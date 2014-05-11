<?php

// Load bootstrap
require_once('bootstrap.php');

// Upload Default is wp-content/uploads
$opt_upload_path = get_option('upload_path');
$opt_upload_path = ((empty($opt_upload_path))?'wp-content/uploads':$opt_upload_path);

$svp_uppath = get_bloginfo( 'wpurl' ).'/'.$opt_upload_path;

$upload_dir = realpath(ABSPATH.$opt_upload_path);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<title>TreeView</title>
<link rel="stylesheet" href="button/jquery.treeview.css" />
<script type="text/javascript" src="<?php echo plugins_url('/stream-video-player/button/jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php echo plugins_url('/stream-video-player/button/jquery.treeview.js'); ?>"></script>
<script type="text/javascript" src="<?php echo plugins_url('/stream-video-player/button/medialib.js'); ?>"></script>
</head>
<body>
<div id="wbtitle"><?php echo htmlentities(utf8_decode($_GET['wtitle']), ENT_QUOTES); ?></div>
<div id="flbrow"><?php
if(!file_exists($upload_dir)){
	_e('You need to upload something in to your media library.', 'stream-video-player');
}else{
?>
  <ul id="browser" class="filetree">
    <li><span class="folder" id="uplds"><a href="<?php echo $svp_uppath; ?>">Uploads</a></span> <?php echo makeULLI(readDirR($upload_dir,$svp_uppath,$upload_dir)); ?>
  </ul>
<?php
}
?>
</div>

<div id="ftr"> <span id="fl"><?php _e('Select a file', 'stream-video-player'); ?></span> <span id="fr">
  <input type="submit" id="Login" value="&nbsp;&nbsp;Ok&nbsp;&nbsp;" onclick="self.parent.tb_remove();" />
  </span> </div>
</body>
</html>