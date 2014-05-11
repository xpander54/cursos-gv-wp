<?php

/**
 * FLV Streamer for WordPress by Rodrigo Polo, inspired on some xmoov-php main ideas.
 * 
 * URI: http://www.rodrigopolo.com/wp-stream-video
 * 
 * xmoov-php by Eric Lorenzo Benjamin jr. webmaster (AT) xmoov (DOT) com
 * originally inspired by Stefan Richter at flashcomguru.com
 * bandwidth limiting code idea by Terry streamingflvcom (AT) dedicatedmanagers (DOT) com
 *
 * Copyright (C) 2009  Rodrigo J. Polo
 * 
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  any later version.
 * 
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 **/

// Set some limits
/*ini_set('output_buffering', 'Off'); 
ini_set('memory_limit', '128M'); 
ini_set('variables_order', 'EGPCS');
ini_set('register_long_arrays', 'On');
ini_set('max_input_time', '86400');
ini_set('max_execution_time', '86400');
*/

// Turn off all error reporting
error_reporting(0);

// Fix for max execution time
set_time_limit(86400);

 

/**
 *
 * Contants
 *
 **/
 
// Set the cache for the video
define('SP_ALLOW_CACHE',true);
 
// Set band width limit
define('BW_LIMIT',true);

// Kilobytes per time interval
define('BWP_SIZE', 90);

// Time interval for data packets in seconds.
define('BWP_INTERVAL', 0.3);

class flv_streamer{
	
	function __construct($g_fl, $g_st, $g_bw){
		
		// if not url is defined, stop and give a 404 warning
		// if (!isset($_GET[$g_fl]) || !isset($_GET[$g_st])){
		if (!isset($_GET[$g_fl])){
			header('HTTP/1.0 404 Not Found'); 
			die('<h1>404</h1> At least tell me what video to _GET and where to start.');  
		}
		
		// Bandwidth intervals
		$bwl_interval = array('off'=>0, 'high'=>0.2, 'mid'=>0.3, 'low'=>0.6);
		
		// Bandwidth packages sizes
		$bwl_size = array('high'=>90, 'mid'=>40, 'low'=>20);
		
		// Selected packet interval
		$sel_pack_i = (isset($_GET[$g_bw]) && !empty($bwl_interval[$_GET[$g_bw]])) ? $bwl_interval[$_GET[$g_bw]] : BWP_INTERVAL;
		
		// Selected packet size
		$sel_pack_z = ((isset($_GET[$g_bw]) && !empty($bwl_size[$_GET[$g_bw]])) ? $bwl_size[$_GET[$g_bw]] : BWP_SIZE) * 1042;
		
			
		// get seek position
		$start_pos = (isset($_GET[$g_st]))?intval($_GET[$g_st]):0;
			
		// get file name
		$vdo_file = './'.$this->getRelPa($this->getSelfUri(),htmlspecialchars($_GET[$g_fl]));
		
		// file name
		$fname = basename($vdo_file);
			
		// Check if exist, if not, 404
		if(!file_exists($vdo_file)){
			header('HTTP/1.0 404 Not Found'); 
			die('<h1>404</h1> File "' . $vdo_file . '" not found.');  
		}
		
		// File size
		$fsize = filesize($vdo_file) - (($start_pos > 0) ? $start_pos  + 1 : 0);
			
		// Check if the requested file is an FLV
		if(strrchr($fname, '.') != '.flv'){
			header('HTTP/1.1 403 Forbidden');
			die('<h1>403</h1> You cannot acces to "' . $vdo_file . '" because of the file extension.'); 
		}
					
		// Can open ?
		if(!$fpr = fopen($vdo_file, 'rb')){
			header('HTTP/1.1 403 Forbidden');
			die('<h1>403</h1> Streamer cannot acces to "' . $vdo_file . '", Check your file perrmissions with CHMOD.'); 
		}
		
		
		// No chache ?
		if(SP_ALLOW_CACHE){
			session_cache_limiter("nocache");
			header("Expires: Thu, 20 Sep 1980 16:30:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
		}
		
		// Flash Video Header
		header("Content-Type: video/x-flv");
		header("Content-Disposition: attachment; filename=\"" . $fname . "\"");
		header("Content-Length: " . $fsize);
		
		// Flash Video File Format Header
		if($start_pos != 0) {
				echo 'FLV'.pack('C', 1).pack('C', 1).pack('N', 9).pack('N', 9);
		}
		
		// Seek to the file requested start
		fseek($fpr, $start_pos);
		
		// Start the file output
		while(!feof($fpr)){
				// Bandwidth limiting
				if($sel_pack_i > 0 && BW_LIMIT){
						// Start time
						list($us, $s) = explode(' ', microtime());
						$ts = ((float)$us + (float)$s);
						// Echo packet
						echo fread($fpr, $sel_pack_z);
						// End time
						list($us, $s) = explode(' ', microtime());
						$tst = ((float)$us + (float)$s);
						$tdi = $tst - $ts;
						// Wait, when output is slower than packet interval
						if($tdi < (float)$sel_pack_i){
								usleep((float)$sel_pack_i * 1000000 - (float)$tdi * 1000000);
						}
				}else{
						// output file without bandwidth limiting
						echo fread($fpr, filesize($vdo_file)); 
				}
		}
	}
	
	// To know the script self uri
	function getSelfUri(){
		$proto = ( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://';
		return $proto.$_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	}
	
	// To match a relative path
	function getRelPa($scr,$fil){
		
		
		/*if (($pos = strpos($scr, '://')) !== false){ $scr =substr($scr, $pos + 3);}
		if (($pos = strpos($fil, '://')) !== false){ $fil =substr($fil, $pos + 3);}
		if (strpos($scr, 'www.') === 0){ $scr = substr($scr, 4);}
		if (strpos($fil, 'www.') === 0){ $fil = substr($scr, 4);}*/
		
		
		$uria = parse_url($scr);
		$urib = parse_url($fil);
		
		if($uria['host']!=$urib['host']){
			header('HTTP/1.0 404 Not Found'); 
			die('<h1>404</h1>You are requesting a video that does not resides on this server, this streamer.php only works for videos hosted on the same server.');  
			return false;
		}
		
		$scr_a = explode('/',$uria['path']);
		$fil_a = explode('/',$urib['path']);
		$scr_ab = $scr_a;
		foreach($fil_a as $k => $dir){
			if($dir == $scr_a[$k]){
				$d[]=$dir;
				array_shift($scr_ab);
			}else{
				$f[]=$dir;
			}
		}
		$rp='';
		if(count($scr_ab)>1){
			array_shift($scr_ab);
			foreach($scr_ab as $di){
				$rp.='../';
			}
		}
		$rp .= implode('/',$f);
		return $rp;
	}
	
}

new flv_streamer('file','start','bw');
?>