<?php
// URI Functions
function selfHost(){
	$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
	$pt = strtolower($_SERVER["SERVER_PROTOCOL"]);
	$protocol = substr($pt, 0, strpos($pt, "/")).$s;
	return $protocol."://".$_SERVER['SERVER_NAME'];
}

// (config y stream)
function getSelfUri(){
	$proto = ( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://';
	return $proto.$_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
}


// To match a relative path
function getRelPa($scr,$fil){
	$scr = str_replace("http://www.", "http://", $scr);
	$fil = str_replace("http://www.", "http://", $fil);
	$scr_a = explode('/',$scr);
	$fil_a = explode('/',$fil);
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


// FileTree Functions
// get file extension (funct)
function getExt($file){
	return strtolower(substr($file, strrpos($file, '.') + 1));
}

// Check if it is a valid file for Flash Player (func)
function isValidFile($file){
	$extensions = explode(',','flv,f4v,f4p,f4a,f4b,xml,jpg,jpeg,mp4,m4a,m4v,png,mp3,ogv');
	$ext = getExt($file);
	if (in_array($ext, $extensions)){
		return true;
	}else{
		return false;
	}
}

// Read the directory (func, media lib)
function readDirR($dir = "./",$base_path='./',$mp='') {
	

	if($listing = opendir($dir)){
		$return = array ();
		while(($entry = readdir($listing)) !== false) {
			if ($entry != "." && $entry != ".." && substr($entry,0,1) != '.') {
				$dir = preg_replace("/^(.*)(\/)+$/", "$1", $dir);
				$item = $dir . "/" . $entry;
				$isfile = is_file($item);
				$dirend = ($isfile)?'':'/';
				
				$path_to_file = $dir . "/" . $entry . $dirend;
				$path_to_file = str_replace($mp, $base_path, $path_to_file);
				
				$link = '<a rel="'.getExt($entry).'" href="'.$path_to_file.'">' . $entry . '</a>';
				if ($isfile && isValidFile($entry)) {
					$return[] = $link;
				}
				elseif (is_dir($item)) {
					$return[$link] = readDirR($item,$base_path,$mp);
				} else {}
			} else {}
		}
	
		return $return;
	}else{
		die('Can\'t read directory.');
	}
}

// Convert the array to UL-LI (func, media lib)
function makeULLI($array) {
	$return = "<ul>";
	if (is_array($array) && count($array) > 0) {
		foreach ($array as $k => $v) {
			if (is_array($v) && count($v) > 0) {
				$return .= "<li><span class=\"folder\">" . $k . "</span>". makeULLI($v) . "</li>";
			} else if(count($v)>0){
				$return .= "<li><span class=\"file\">" . $v . "</span></li>";
			}
		}
	} else {}
	$return .= "</ul>";
	return $return;
}

?>