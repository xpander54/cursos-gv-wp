<?php

// Load WP bootstrap
require_once('bootstrap.php');

// Set title
$title = "Stream Video";

// load defaults
$def_options = get_option('StreamVideoSettings');

$tbsur = get_bloginfo('url');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title>
<?php bloginfo('name') ?>
&rsaquo;<?php echo wp_specialchars( $title ); ?>&#8212; WordPress</title>
<link rel="stylesheet" type="text/css" media="screen" title="no title" charset="utf-8" href="<?php echo plugins_url('/stream-video-player/button/config.css'); ?>?ver=<?php echo $StreamVideoVersion; ?>"/>
<link rel="stylesheet" type="text/css" media="screen" title="no title" charset="utf-8" href="<?php echo plugins_url('/stream-video-player/button/generator.css'); ?>?ver=<?php echo $StreamVideoVersion; ?>"/>
<link rel="stylesheet" type="text/css" media="screen" title="no title" charset="utf-8" href="<?php echo $tbsur ?>/wp-includes/js/thickbox/thickbox.css?ver=<?php echo $StreamVideoVersion; ?>"/>
<!--[if lte IE 7]>
<link rel="stylesheet" id="ie-css" type="text/css" media="all" href="<?php echo $tbsur; ?>/wp-admin/css/ie.css?ver=<?php echo $StreamVideoVersion; ?>" />
<![endif]-->
</head>
<body>
<div class="wrap">
  <h2><?php echo $title; ?></h2>
  <div class="note">
    <?php _e('Learn how to encode &quot;stream-ready&quot; videos with multi-platform free-tools and<br />how to use this plug-in by ', 'stream-video-player'); ?>
    <a href="http://www.rodrigopolo.com/about/wp-stream-video/how-to" target="_blank">
    <?php _e('clicking here', 'stream-video-player'); ?>
    </a>. </div>
  <?php
		$selfhost = selfHost();
		$crossdomain = $selfhost.'/crossdomain.xml';
		$thisfile = getSelfUri();
		$file = getRelPa($thisfile,$crossdomain);
		if(!file_exists($file)){
			?>
  <div class="error below-h2" id="notice">
    <p><strong>
      <?php _e('WARNING:', 'stream-video-player');?>
      </strong>
      <?php _e('You don\'t have your crossdomain.xml file in the root folder of your web server, this file is required if you want to enable the &quot;embed&quot; option and to prevents issues loading content, however you can copy the crossdomain.xml included in this plug-in', 'stream-video-player');?>
      <br />
      <br />
      <?php _e('From:', 'stream-video-player');?>
      <br />
      <a href="<?php echo plugins_url('/stream-video-player/crossdomain.xml'); ?>" target="_blank"><?php echo plugins_url('/stream-video-player/crossdomain.xml'); ?></a><br />
      <br />
      <?php _e('To:', 'stream-video-player');?>
      <br />
      <a href="<?php echo $crossdomain; ?>" target="_blank"><?php echo $crossdomain; ?></a><br />
      <br />
      <?php _e('After copyng this file whis &quot;warning&quot; message will not appear, read more information about the crossdomain.xml', 'stream-video-player');?>
      <a href="http://rodrigopolo.com/about/wp-stream-video/faq#flash-cross-domain-policy" target="_blank">
      <?php _e('clicking here', 'stream-video-player');?>
      </a>.</p>
  </div>
  <?php 
}
?>
  <div class="note">(<span class="req">*</span>)
    <?php _e('indicates required field', 'stream-video-player'); ?>
  </div>
  <fieldset>
    <legend><?php _e('Stream Player Tag Atributes', 'stream-video-player'); ?></legend>
    
    
    <div class="col1">
      <label class="info" title="<?php _e('Base URL to save writing over and over again the full URL for the flv, img, mp4, hd and captions tags', 'stream-video-player'); ?>" for="base"><?php _e('Base URL:', 'stream-video-player'); ?></label>
    </div>
    <div class="col3">
      <input type="button" class="button wb" name="bbase" id="bbase" value="Media Library"/>
    </div>
    <div class="col6">
      <input type="text" size="18" name="base" id="base"/>
    </div>
    
    <div class="col1">
      <label class="info" title="<?php _e('Set the media provider, &quot;http&quot; is for pseudo-streming and is enable by default, choose &quot;video&quot; for progressively downloaded FLV, MP4, and AAC audio, choose &quot;sound&quot; for progressively downloaded MP3 files, choose &quot;image&quot; for JPG/GIF/PNG images, choose &quot;youtube&quot; for videos from Youtube and &quot;rtmp&quot; for FLV/MP4/MP3 files played from an RTMP server.', 'stream-video-player'); ?>" for="provider"><?php _e('Media provider:', 'stream-video-player'); ?></label>
    </div>
    <div class="col4">
      <select name="provider" id="provider">
        <?php
					// to load defaults
					foreach ($def_options[3][4]['op'] as $value) {
						$sel = ($def_options[3][4]['v']==$value)?' selected="selected"':'';
						$vname = ($value=='http')?$value.' : pseudo-streaming':$value;
						echo '<option value="'.$value.'"'.$sel.'>'.$vname.'</option>';
					}
					?>
      </select>
    </div>
    <div class="clear">&nbsp;</div>
    
	<div class="col1">
      <label class="info" title="<?php _e('The absolute path to your file OR YouTube address', 'stream-video-player'); ?>" for="flv"><?php _e('Video:', 'stream-video-player'); ?></label>
      <span class="req">*</span>
	</div>
    <div class="col3">
      <input type="button" class="button wb" name="bflv" id="bflv" value="Media Library"/>
    </div>
    <div class="col6">
      <input type="text" size="18" name="flv" id="flv"/>
    </div>
    
    <div class="col1">
      <label class="info" title="<?php _e('The absolute path to your image preview', 'stream-video-player'); ?>" for="img"><?php _e('Image Preview (jpg or png):', 'stream-video-player'); ?></label>
      <span class="req">*</span>
	</div>
    <div class="col3">
      <input type="button" class="button wb" name="bimg" id="bimg" value="Media Library"/>
    </div>
    <div class="col6">
      <input type="text" size="18" name="img" id="img"/>
    </div>
    
    <div class="col1">
      <label class="info" title="<?php _e('The absolute path to your HD video', 'stream-video-player'); ?>" for="hd"><?php _e('HD Video for Player:', 'stream-video-player'); ?></label>
    </div>
    <div class="col3">
      <input type="button" class="button wb" name="bhd" id="bhd" value="Media Library"/>
    </div>
    <div class="col6">
      <input type="text" size="18" name="hd" id="hd"/>
    </div>
    
    <div class="col1"><label class="info" title="<?php _e('The absolute path to your MP4 video for iOS and Android', 'stream-video-player'); ?>" for="mp4"><?php _e('iPhone-iPad Video (.mp4):', 'stream-video-player'); ?></label>
    </div>
    <div class="col3">
      <input type="button" class="button wb" name="bmp4" id="bmp4" value="Media Library"/>
    </div>
    <div class="col6">
      <input type="text" size="18" name="mp4" id="mp4"/>
    </div>
    
    <div class="col1"><label class="info" title="<?php _e('The absolute path to your OGV video for FireFox', 'stream-video-player'); ?>" for="ogv"><?php _e('OGV Video (.ogv):', 'stream-video-player'); ?></label>
    </div>
    <div class="col3">
      <input type="button" class="button wb" name="bogv" id="bogv" value="Media Library"/>
    </div>
    <div class="col6">
      <input type="text" size="18" name="ogv" id="ogv"/>
    </div>
    
    <div class="col1">
      <label class="info" title="<?php _e('The absolute path to your XML captions file', 'stream-video-player'); ?>" for="captions"><?php _e('Captions (.xml):', 'stream-video-player'); ?></label>
    </div>
    <div class="col3">
      <input type="button" class="button wb" name="bcaptions" id="bcaptions" value="Media Library"/>
    </div>
    <div class="col6">
      <input type="text" size="18" name="captions" id="captions"/>
    </div>
    
    <div class="col1">
      <label title="<?php _e('Responsive video aspect ratio', 'stream-video-player'); ?>" class="info" for="bresponsive"><?php _e('Responsive:', 'stream-video-player'); ?></label>
      
	</div>
    <div class="col4">
      <select name="responsive" id="responsive">
        <?php
			// to load defaults
			foreach ($def_options[3][8]['op'] as $value) {
				$sel = ($def_options[3][8]['v']==$value)?' selected="selected"':'';
				echo '<option value="'.$value.'"'.$sel.'>'.ucfirst($value).'</option>';
			}
		?>
      </select>
    </div>
    <div class="clear">&nbsp;</div>
    
    <div class="col1">
      <label class="info" title="<?php _e('Width &times; height in pixels', 'stream-video-player'); ?>"><?php _e('Dimensions:', 'stream-video-player'); ?></label>
      <span class="req">*</span>
	</div>
    <div class="col2">
      <input type="text" maxlength="5" size="5" value="<?php echo $def_options[1][0]['v']; ?>" name="width" id="width"/>&times;<input type="text" maxlength="5" size="5" value="<?php echo $def_options[1][1]['v']; ?>" name="height" id="height"/>
    </div>
    <div class="clear">&nbsp;</div>
    
    <div class="col1">
      <label title="<?php _e('Enable embed the player in other sites', 'stream-video-player'); ?>" class="info" for="embed"><?php _e('Enable Embed:', 'stream-video-player'); ?></label>
    </div>
    <div class="col4">
      <select name="embed" id="embed">
        <option value="true">True</option>
        <option value="false"<?php echo ($def_options[2][3]['v']=='false')?' selected="selected"':''; ?>>False</option>
      </select>
    </div>
    <div class="clear">&nbsp;</div>
    
    <div class="col1">
      <label title="<?php _e('Enable the sharing of the URL of the post where the video is inserted', 'stream-video-player'); ?>" class="info" for="share"><?php _e('Enable URL Share:', 'stream-video-player'); ?></label>
    </div>
    <div class="col4">
      <select name="share" id="share">
        <option value="true">True</option>
        <option value="false"<?php echo ($def_options[2][2]['v']=='false')?' selected="selected"':''; ?>>False</option>
      </select>
    </div>
    <div class="clear">&nbsp;</div>
    
    <div class="col1">
      <label class="info" title="<?php _e('Show the Dock for this player', 'stream-video-player'); ?>" for="dock"><?php _e('Dock:', 'stream-video-player'); ?></label>
    </div>
    <div class="col4">
      <select name="dock" id="dock">
        <?php
			// to load defaults
			foreach ($def_options[1][5]['op'] as $value) {
				$sel = ($def_options[1][5]['v']==$value)?' selected="selected"':'';
				echo '<option value="'.$value.'"'.$sel.'>'.ucfirst($value).'</option>';
			}
		?>
      </select>
    </div>
    <div class="clear">&nbsp;</div>
    
    <div class="col1">
      <label class="info" title="<?php _e('Set the Control Bar position and visibility', 'stream-video-player'); ?>" for="controlbar"><?php _e('Control Bar:', 'stream-video-player'); ?></label>
    </div>
    <div class="col4">
      <select name="controlbar" id="controlbar">
        <?php
			// to load defaults
			foreach ($def_options[1][4]['op'] as $value) {
				$sel = ($def_options[1][4]['v']==$value)?' selected="selected"':'';
				echo '<option value="'.$value.'"'.$sel.'>'.$value.'</option>';
			}
		?>
      </select>
    </div>
    <div class="clear">&nbsp;</div>
    
    <div class="col1">
      <label class="info" title="<?php _e('Skin for this player', 'stream-video-player'); ?>" for="skin"><?php _e('Skin:', 'stream-video-player'); ?></label>
    </div>
    <div class="col4">
      <select name="skin" id="skin">
        <?php
			// to load defaults
			foreach (StreamVideoReadSkins() as $value) {
				$sel = ($def_options[1][2]['v']==$value)?' selected="selected"':'';
				echo '<option value="'.$value.'"'.$sel.'>'.ucfirst($value).'</option>';
			}
		?>
      </select>
    </div>
    <div class="clear">&nbsp;</div>
    
    <div class="col1">
      <label class="info" title="<?php _e('The absolute path to your configuration XML File', 'stream-video-player'); ?>" for="config"><?php _e('Config XML:', 'stream-video-player'); ?></label>
    </div>
    <div class="col3">
      <input type="button" class="button wb" name="bconfig" id="bconfig" value="Media Library"/>
    </div>
    <div class="col6">
      <input type="text" size="18" name="config" id="config"/>
    </div>
    
    <div class="col1">
      <label class="info" title="<?php _e('The absolute path to your XML Playlist file', 'stream-video-player'); ?>" for="playlistfile"><?php _e('Playlist XML:', 'stream-video-player'); ?></label>
    </div>
    <div class="col3">
      <input type="button" class="button wb" name="bplaylistfile" id="bplaylistfile" value="Media Library"/>
    </div>
    <div class="col6">
      <input type="text" size="18" name="playlistfile" id="playlistfile"/>
    </div>
    
    <div class="col1">
      <label class="info" title="<?php _e('Playlist Size (Width or Height depending on the position)', 'stream-video-player'); ?>" for="playlistsize"><?php _e('Playlist Size:', 'stream-video-player'); ?></label>
    </div>
    <div class="col2">
      <input type="text" size="18" name="playlistsize" id="playlistsize" />
    </div>
    <div class="clear">&nbsp;</div>
    
    
    <div class="col1">
      <label class="info" title="<?php _e('Set the playlist position', 'stream-video-player'); ?>" for="playlist"><?php _e('Playlist Position:', 'stream-video-player'); ?></label>
    </div>
    <div class="col4">
      <select name="playlist" id="playlist">
		<option value="false">-----</option>
        <option value="bottom">Bottom</option>
        <option value="over">Over</option>
        <option value="right">Right</option>
      </select>
    </div>
    <div class="clear">&nbsp;</div>
    
    <div class="col1">
      <label class="info" title="<?php _e('Set the repeat behaviour', 'stream-video-player'); ?>" for="repeat"><?php _e('Repeat:', 'stream-video-player'); ?></label>
    </div>
    <div class="col4">
      <select name="repeat" id="repeat">
		<option value="false">-----</option>
        <option value="none">None</option>
        <option value="list">List</option>
        <option value="always">Always</option>
        <option value="single">Single</option>      
      </select>
    </div>
    <div class="clear">&nbsp;</div>
    
    <div class="col1">
      <label class="info" title="<?php _e('LongTail Ads Code', 'stream-video-player'); ?>" for="adscode"><?php _e('Ads Code:', 'stream-video-player'); ?></label>
    </div>
    <div class="col2">
      <input type="text" size="18" name="adscode" id="adscode" value="<?php echo $def_options[3][7]['v']; ?>" alt="<?php echo $def_options[3][7]['v']; ?>"/>
    </div>
    <div class="clear">&nbsp;</div>
    
    <div class="col1">
      <label class="info" title="<?php _e('Google Analytics Account ID like \'UA-123456-1\'', 'stream-video-player'); ?>" for="gapro"><?php _e('Google Analytics:', 'stream-video-player'); ?></label>
    </div>
    <div class="col2">
      <input type="text" size="18" name="gapro" id="gapro" value="<?php echo $def_options[3][6]['v']; ?>" alt="<?php echo $def_options[3][6]['v']; ?>"/>
    </div>
    <div class="clear">&nbsp;</div>
    
    <div class="col1">
      <label class="info" title="<?php _e('The absolute path to your video player logo, if empty takes the default value on the plug-in settings', 'stream-video-player'); ?>" for="logo"><?php _e('Logo (jpg or png):', 'stream-video-player'); ?></label>
    </div>
    <div class="col3">
      <input type="button" class="button wb" name="blogo" id="blogo" value="Media Library"/>
    </div>
    <div class="col6">
      <input type="text" size="18" name="logo" id="logo"/>
    </div>
    
    <div class="col1">
      <label title="<?php _e('Set the desired bandwidth for cache and site bandwidth control', 'stream-video-player'); ?>" class="info" for="bw"><?php _e('Bandwidth:', 'stream-video-player'); ?></label>
      <span class="req">*</span>
	</div>
    <div class="col4">
      <select name="bandwidth" id="bandwidth">
        <?php
			// to load defaults
			foreach ($def_options[3][0]['op'] as $value) {
				$sel = ($def_options[3][0]['v']==$value)?' selected="selected"':'';
				echo '<option value="'.$value.'"'.$sel.'>'.ucfirst($value).'</option>';
			}
		?>
      </select>
    </div>
    <div class="clear">&nbsp;</div>
    
    <div class="col1">
      <label class="info" title="<?php _e('Your video title', 'stream-video-player'); ?>" for="title"><?php _e('Title:', 'stream-video-player'); ?></label>
    </div>
    <div class="col2">
      <input type="text" size="18" name="title" id="title"/>
    </div>
    
    <div class="col1">
      <label class="info" title="<?php _e('Your video volume, from 0 to 100, if empty takes the default value on the plug-in settings', 'stream-video-player'); ?>" for="volume"><?php _e('Volume:', 'stream-video-player'); ?></label>
    </div>
    <div class="col2">
      <input type="text" size="18" name="volume" id="volume"/>
    </div>
    
    <div class="col1">
      <label title="<?php _e('Auto Start the video with the Stream Player', 'stream-video-player'); ?>" class="info" for="autostart"><?php _e('Auto Start:', 'stream-video-player'); ?></label>
    </div>
    <div class="col4">
      <select name="autostart" id="autostart">
        <option value="false">False</option>
        <option value="true"<?php echo ($def_options[2][0]['v']=='true')?' selected="selected"':''; ?>>True</option>
      </select>
    </div>
    <div class="clear">&nbsp;</div>
    
    <div class="col1">
      <label class="info" title="<?php _e('To use another pseudo streaming script on other server, if is empty it takes the default value on the plug-in settings.', 'stream-video-player'); ?>" for="streamer"><?php _e('Streamer:', 'stream-video-player'); ?></label>
    </div>
    <div class="col2">
      <input type="text" size="18" name="streamer" id="streamer"/>
    </div>
    
    <div class="col1">
      <label title="<?php _e('Fixes the HTML overlapping of the Flash Video by adding the parameter [wmode=opaque].', 'stream-video-player'); ?>" class="info" for="opfix"><?php _e('Fix HTML overlapping:', 'stream-video-player'); ?></label>
    </div>
    <div class="col4">
      <select name="opfix" id="opfix">
        <option value="true">True</option>
        <option value="false" selected="selected">False</option>
      </select>
    </div>
    <div class="clear">&nbsp;</div>
    
  </fieldset>
  
  <div class="col1">
    <input type="button" class="button" id="generate" name="generate" value="<?php _e('Generate', 'stream-video-player'); ?>" />
  </div>
</div>
<script type="text/javascript" charset="utf-8" src="<?php echo plugins_url('/stream-video-player/button/jquery.js'); ?>?ver=<?php echo $StreamVideoVersion; ?>"></script> 
<script>
		// <![CDATA[
		jQuery(document).ready(function($){
			try {
				RodrigoPolo.Tag.Generator.initialize();
			} catch (e) {
				//throw "<?php _e("Stream Video: This tag generator isn't going to put the stream video tag in your code.", 'stream-video-player'); ?>";
			}
			$(".button.wb").click(function(e) {
				e.preventDefault();
				var fid = $(this).attr("id");
				curwbf = fid.substring(1);
				var wtitle = $("label[for='"+curwbf+"']").html();
				var url_ml = '<?php echo plugins_url('/stream-video-player/medialibrary.php'); ?>?&wtitle='+encodeURIComponent(wtitle)+'&KeepThis=true&TB_iframe=true&height=320&width=240&modal=true&';
				tb_show('Un Titulo', url_ml, false);
			});
		});
		var curwbf;
		function chfld(v){
			if(curwbf == 'base'){
				// remove the file name because wee need a path
				v = (v.substring(0, v.lastIndexOf('/')))+'/';
			}
			// If base url is defined
			var bbase = jQuery("#base").val();
			var fv = [];
			if(bbase.length > 0 && curwbf != 'base'){
				fv = v.split(bbase);
				if(fv[0].length > 0){
					v = fv[0];
				}else if(fv[1].length > 0){
					v = fv[1];
				}
			}
			jQuery("#"+curwbf).val(v);
		}
		
var tb_pathToImage = "<?php echo $tbsur; ?>/wp-includes/js/thickbox/loadingAnimation.gif";
var tb_closeImage = "<?php echo $tbsur; ?>/wp-includes/js/thickbox/tb-close.png";
var thickboxL10n = {};
		
		// ]]>
	</script> 
<script type="text/javascript" charset="utf-8" src="<?php echo plugins_url('/stream-video-player/button/svb.js'); ?>?ver=<?php echo $StreamVideoVersion; ?>"></script> 
<script type="text/javascript" charset="utf-8" src="<?php echo $tbsur; ?>/wp-includes/js/thickbox/thickbox.js?ver=<?php echo $StreamVideoVersion;?>"></script>
</body>
</html>