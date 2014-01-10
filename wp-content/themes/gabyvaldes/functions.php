<?php
	
	// Add RSS links to <head> section
	automatic_feed_links();
	
	/* Load jQuery
	if ( !is_admin() ) {
	   wp_deregister_script('jquery');
	  // wp_register_script('jquery', ("http:////ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"), false);
       wp_register_script('jquery', ("http:////ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"), false);
	   wp_enqueue_script('jquery');
	}
*/

	function catch_first_post_image() {
      global $post, $posts;
      $first_img = '';
      ob_start();
      ob_end_clean();
      $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
      $first_img = $matches [1] [0];

      if(empty($first_img)){ //Defines a default image
        $first_img = "/images/default.jpg";
      }
      return $first_img;
    }


	// Clean up the <head>
	function removeHeadLinks() {
    	remove_action('wp_head', 'rsd_link');
    	remove_action('wp_head', 'wlwmanifest_link');
    }
    add_action('init', 'removeHeadLinks');
    remove_action('wp_head', 'wp_generator');
    
	// Declare sidebar widget zone
    if (function_exists('register_sidebar')) {
    	register_sidebar(array(
    		'name' => 'Sidebar Widgets',
    		'id'   => 'sidebar-widgets',
    		'description'   => 'These are widgets for the sidebar.',
    		'before_widget' => '<div id="%1$s" class="widget %2$s">',
    		'after_widget'  => '</div>',
    		'before_title'  => '<h2>',
    		'after_title'   => '</h2>'
    	));
    }

?>