<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>" />	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php if (is_search()) { ?>
	   <meta name="robots" content="noindex, nofollow" /> 
	<?php } ?>

	<title>
		   <?php
		      if (function_exists('is_tag') && is_tag()) {
		         single_tag_title("Tag Archive for &quot;"); echo '&quot; - '; }
		      elseif (is_archive()) {
		         wp_title(''); echo ' Archive - '; }
		      elseif (is_search()) {
		         echo 'Search for &quot;'.wp_specialchars($s).'&quot; - '; }
		      elseif (!(is_404()) && (is_single()) || (is_page())) {
		         wp_title(''); echo ' - '; }
		      elseif (is_404()) {
		         echo 'Not Found - '; }
		      if (is_home()) {
		         bloginfo('name'); echo ' - '; bloginfo('description'); }
		      else {
		          bloginfo('name'); }
		      if ($paged>1) {
		         echo ' - page '. $paged; }
		   ?>
	</title>
	
	<link rel="shortcut icon" href="/favicon.ico">
	
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
	
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

	<?php if ( is_singular() ) wp_enqueue_script('comment-reply'); ?>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	
	 <div id="page-wrap"> 
		
		<div class="wrapper clear-fix">
		<header class="main">
			<div class="logo">

				<a href="http://www.gabyvaldes.com">
					<!-- <img src="images/logo-head.png" >
					</img> -->

					<img class="left" src="<?php bloginfo('template_url'); ?>/images/logo-head.png" alt="Logo Gaby Valdes"/>
				</a>

			</div>

			<div class="main head-menu">

				<nav class="main">
					<ul>
						<li>
							<a href="http://www.gabyvaldes.com">
								Inicio
							</a>
						</li>
						<li>
							<a href="http://www.gabyvaldes.com/blog/reflexiones/">
								Reflexiones
							</a>
						</li>
						<li>
							<a href="/">
								Productos
							</a>
						</li>
						<li>
							<a href="http://www.gabyvaldes.com/contacto">
								Contacto
							</a>
						</li>
					</ul>
				</nav>

			</div>
			
		</header>

		<!-- <div id="header">
			<h1><a href="<?//php echo get_option('home'); ?>/"><?//php bloginfo('name'); ?></a></h1>
			<div class="description"><?//php bloginfo('description'); ?></div>
		</div> -->