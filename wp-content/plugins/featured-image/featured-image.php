<?php
/**
 * @package Featured Image
 * @author Mervin Praison
 * @version 1.0
 */
/*
    Plugin Name: Featured Image
    Plugin URI: http://mervin.info/wordpress-featured-image
    Description: Provides you with a featured image shortcode [ featured-img ] and Featured Image widget. Very Easy to implement. 
    Author: Mervin Praison
    Version: 1.0
    License: GPL
    Author URI: http://mervin.info/
    Last change: 16.03.2012
*/
/**
* Example for use inside the loop: <?php if ( function_exists('get_featured_img') ) get_featured_img(); ?>
 */


function getting_featured_img() {
$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );

$mpfeatureimg = " <img src='" ;
$mpfeatureimg .= $image[0];
$mpfeatureimg .= "' />";

return $mpfeatureimg;
}

add_shortcode('featured-img', 'getting_featured_img');

function get_featured_img() {
echo getting_featured_img();
}

wp_register_sidebar_widget(
    'mp_featuredimg_1',        // your unique widget id
    'Featured Image',          // widget name
    'get_featured_img',  // callback function
    array(                  // options
        'description' => 'Displays featured image on each individual post/page.'
    )
);
?>