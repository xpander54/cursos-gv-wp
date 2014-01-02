<?php

/**
 * Setup the Theme Admin Settings Page
 * 
 * Add "Theme Options" link to the "Appearance" menu
 * 
 */
function techism_theme_page() {
	add_theme_page( __( 'techism Theme Options', 'techism' ), __( 'Theme Options', 'techism' ), 'edit_theme_options', 'techism_options', 'techism_admin_options_page' );
}
// Load the Admin Options page
add_action( 'admin_menu', 'techism_theme_page' );

function techism_register_settings() {
	register_setting( 'techism_theme_options', 'techism_theme_options', 'techism_validate_theme_options' );
}

add_action( 'admin_init', 'techism_register_settings' );

function techism_admin_options_page() { ?>
	<div class="wrap">	
		<?php techism_admin_options_page_tabs(); ?>
		<?php if ( isset( $_GET['settings-updated'] ) ) : ?>
			<div class='updated'><p><?php _e( 'Theme settings updated successfully.', 'techism' ); ?></p></div>
		<?php endif; ?>
		<form action="options.php" method="post">
			<?php settings_fields( 'techism_theme_options' ); ?>
			<?php do_settings_sections('techism_options'); ?>
			<p>&nbsp;</p>
			<?php $tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'general' ); ?>
			<?php if($tab=='general'):?>			
				<input name="techism_theme_options[submit-<?php echo $tab; ?>]" type="submit" class="button-primary" value="<?php _e( 'Save Settings', 'techism' ); ?>" />
				<input name="techism_theme_options[reset-<?php echo $tab; ?>]" type="submit" class="button-secondary" value="<?php _e( 'Reset Defaults', 'techism' ); ?>" />
			<?php endif; ?>
		</form>
	</div>

<?php	
}

function techism_admin_options_page_tabs( $current = 'general' ) { 
	$current = ( isset ( $_GET['tab'] ) ? $_GET['tab'] : 'general' );
	$tabs = array(
		'general' => __( 'General', 'techism' ),
		'about' => __( 'About', 'techism' ),
	);
	$links = array();
	foreach( $tabs as $tab => $name )
		$links[] = "<a class='nav-tab" . ( $tab == $current ? ' nav-tab-active' : '' ) ."' href='?page=techism_options&tab=$tab'>$name</a>";
	echo '<div id="icon-themes" class="icon32"><br /></div>';
	echo '<h2 class="nav-tab-wrapper">';
	foreach ( $links as $link )
		echo $link;
	echo '</h2>';
}

function techism_admin_options_init() {
	global $pagenow;
	if( 'themes.php' == $pagenow && isset( $_GET['page'] ) && 'techism_options' == $_GET['page'] ) {
		$tab = ( isset ( $_GET['tab'] ) ? $_GET['tab'] : 'general' );
		switch ( $tab ) {
			case 'general' :
				techism_general_settings_sections();
				break;
			case 'about' :
				techism_about_settings_sections();
				break;
		}
	}
}

add_action( 'admin_init', 'techism_admin_options_init' );

function techism_general_settings_sections() { 
	add_settings_section( 'techism_slider_options', __( 'Slider Settings', 'techism' ), 'techism_slider_options', 'techism_options' );
}

function techism_about_settings_sections() {
	add_settings_section( 'techism_about_support', __( 'About Author / Support', 'techism' ), 'techism_about_support', 'techism_options' );
}

function techism_slider_options(){
	add_settings_field( 'techism_slider_disable', __( 'Disable Slider on Homepage', 'techism' ), 'techism_slider_disable', 'techism_options', 'techism_slider_options' );
	add_settings_field( 'techism_slider_cat', __( 'Select Category', 'techism' ), 'techism_slider_cat', 'techism_options', 'techism_slider_options' );
	add_settings_field( 'techism_slider_post_no', __( 'Number of Posts', 'techism' ), 'techism_slider_post_no', 'techism_options', 'techism_slider_options' );
}
function techism_about_support() {
echo "Thanks for using our theme. Please make a comment ";
}

function techism_slider_disable() { ?>

	<label class="description">
		<input name="techism_theme_options[slider_disable]" type="checkbox" value="<?php echo techism_get_option( 'slider_disable' ); ?>" <?php checked( techism_get_option( 'slider_disable' ) ); ?> />
		<span><?php _e( 'Disable Slider', 'techism' ); ?></span>
	</label><br />
<?php
	}

function techism_slider_cat() { 
	$categories = get_categories( array( 'hide_empty' => 0, 'hierarchical' => 0 ) ); ?>
	<select name="techism_theme_options[slider_cat]">
		<option value="-1" <?php selected( techism_get_option( 'slider_cat' ), -1 ); ?>>&mdash;</option>
		<?php foreach( $categories as $category ) : ?>
			<option value="<?php echo $category->cat_ID; ?>" <?php selected( techism_get_option( 'slider_cat' ), $category->cat_ID ); ?>><?php echo $category->cat_name; ?></option>
		<?php endforeach; ?>
	</select>
	
	<?php
	}
	

function techism_slider_post_no() { ?>
	<label class="description">
		<input name="techism_theme_options[slider_post_no]" type="text" value="<?php echo techism_get_option( 'slider_post_no' ); ?>" />
		<span><?php _e( 'Number of Post to show in slider', 'techism' ); ?></span>
	</label>
	

<?php
} 
function techism_validate_theme_options( $input ) { 
	if( isset( $input['submit-general'] ) || isset( $input['reset-general'] ) ) {
		$input['slider_disable'] = ( isset( $input['slider_disable'] ) ? true : false );
		$input['slider_post_no']= absint($input['slider_post_no']) ? $input['slider_post_no'] : 3  ;	
		
		if( -1 != $input['slider_cat'] ) {
			$valid = 0;
			$categories = get_categories( array( 'hide_empty' => 0, 'hierarchical' => 0 ) );
			foreach( $categories as $category ) {
				if( $input['slider_cat'] == $category->cat_ID )
					$valid = 1;
			}
			if( ! $valid )
				$input['slider_cat'] = techism_get_option( 'slider_cat' );
		}
		/* need to remove layout options from here */
		} /*elseif( isset( $input['submit-layout'] ) || isset( $input['reset-layout'] ) ) {
		$input['site_margin_left'] = balanceTags( $input['site_margin_left'] );
		$input['site_margin_right'] = balanceTags( $input['site_margin_right'] );
		$input['content_area_width'] = balanceTags( $input['content_area_width'] );
		$input['widget_area_width'] = balanceTags( $input['widget_area_width'] );
	}*/
	if( isset( $input['reset-general'] ) || isset( $input['reset-layout'] )  ) {
		$default_options = techism_default_options();
		foreach( $input as $name => $value )
			if( 'reset-general' != $name  && 'reset-layout' != $name )
				$input[$name] = $default_options[$name];
	}
	$input = wp_parse_args( $input, get_option( 'techism_theme_options', techism_default_options() ) );
	return $input;
}
?>