<?php
/*
WSU theme functions and definitions.

Sets up the theme and provides helper functions, some or which are used as custom template tags 
while others are attached to action and filter hooks to change core WordPress functionality.

Functions can be overridden when using a child theme (see http://codex.wordpress.org/Child_Themes).

For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.

*/

// Override the wipMenuSetup function from the WIP theme and register just one menu.
// Include custom header image support while we're at it.

function overridableSetup() {
		
	register_nav_menus( array(
			'featured' => __( 'Featured Navigation (optional)' ),
			'primary' => __( 'Navigation' ),
			'mobile' => __( 'Mobile Navigation (optional)' )
		) );

	// Add support for custom headers.
	$custom_header_support = array(
		'width'                  => 775,
		'height'                 => 85,
		'header-text'            => false,
		'admin-head-callback'    => 'wipCustomHeaderStyle',
		'admin-preview-callback' => 'wipCustomHeaderDiv',
	);

	add_theme_support( 'custom-header', $custom_header_support );

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'crimson' => array(
			'url' => 'http://images.wsu.edu/sids/sid-zr.jpg',
			'thumbnail_url' => 'http://images.wsu.edu/sids-thumbs/sid-zr.jpg',
			'description' => __( 'Graphic Crimson', 'wsu' )
		),
		'darkgray' => array(
			'url' => 'http://images.wsu.edu/sids/sid-zt.jpg',
			'thumbnail_url' => 'http://images.wsu.edu/sids-thumbs/sid-zt.jpg',
			'description' => __( 'Graphic Dark Gray', 'wsu' )
		),
		'gray' => array(
			'url' => 'http://images.wsu.edu/sids/sid-zu.jpg',
			'thumbnail_url' => 'http://images.wsu.edu/sids-thumbs/sid-zu.jpg',
			'description' => __( 'Graphic Gray', 'wsu' )
		),
		'brown' => array(
			'url' => 'http://images.wsu.edu/sids/sid-zv.jpg',
			'thumbnail_url' => 'http://images.wsu.edu/sids-thumbs/sid-zv.jpg',
			'description' => __( 'Graphic Brown', 'wsu' )
		),
		'blue' => array(
			'url' => 'http://images.wsu.edu/sids/sid-zw.jpg',
			'thumbnail_url' => 'http://images.wsu.edu/sids-thumbs/sid-zw.jpg',
			'description' => __( 'Graphic Blue', 'wsu' )
		),
		'aqua' => array(
			'url' => 'http://images.wsu.edu/sids/sid-zx.jpg',
			'thumbnail_url' => 'http://images.wsu.edu/sids-thumbs/sid-zx.jpg',
			'description' => __( 'Graphic Aqua', 'wsu' )
		),
		'green' => array(
			'url' => 'http://images.wsu.edu/sids/sid-zy.jpg',
			'thumbnail_url' => 'http://images.wsu.edu/sids-thumbs/sid-zy.jpg',
			'description' => __( 'Graphic Green', 'wsu' )
		),
		'orange' => array(
			'url' => 'http://images.wsu.edu/sids/sid-zz.jpg',
			'thumbnail_url' => 'http://images.wsu.edu/sids-thumbs/sid-zz.jpg',
			'description' => __( 'Graphic Orange', 'wsu' )
		)
	) );
}


function wipCustomHeaderStyle() {

	if( get_header_image() )
		$image = get_header_image();
	else
		$image = 'http://images.wsu.edu/sids/sid-zr.jpg';

	$wipOptions = get_option( 'wipOptions' );
	?>
	<style type="text/css">
		.appearance_page_custom-header #siteID { background:url(<?php echo esc_url( $image ); ?>) no-repeat; border:none; height:85px; margin:0; width:775px; }
		#siteID h1, #siteID h2 { color:#fff; font-family:"Lucida Grande","Lucida Sans Unicode",Arial,san-serif; }
		#siteID h2 { font-weight:normal; font-size:13px; padding:21px 0px 0px 22px; height:16px; line-height:14px; margin:0px; width:753px; }
		#siteID h1 { padding:0px 0px 0px 22px; font-size:26px; font-weight:bold; letter-spacing:-0.05em; line-height:31px; width:580px; margin:0px; }
		.appearance_page_custom-header .random-header { display:none; }
	</style>
	<?php

}


function wipCustomHeaderDiv() {
?>
	<div id="siteID">
		<h2><?php bloginfo( 'description' ); ?></h2>
		<h1><?php bloginfo( 'name' ); ?></h1>
	</div>
<?php

}


// Fallback function for Featured Nav
function featuredNavFallback() {
echo "<ul>
	<li><a href=\"https://www.applyweb.com/public/inquiry?wsuuinq\">Request Info</a></li>
	<li><a href=\"http://admission.wsu.edu/visits/index.html\">Visit</a></li>
	<li><a href=\"http://admission.wsu.edu/applications/index.html\">Apply</a></li>
	<li><a href=\"http://cahnrs.wsu.edu/alumni/give/\">I Want to Give</a></li>
</ul>";
}


// Override Theme Options
if( ! function_exists( 'themeOptions' ) ) {
	function themeOptions() {
		require( get_stylesheet_directory() . '/inc/theme_options.php' );
	}
}

add_action( 'after_setup_theme', 'themeOptions' );


// Enqueues scripts and styles for front-end
function scriptsStyles() {

	global $wp_query, $wipOptions, $wipDynamicMeta, $wipLayoutMeta;
	$postID = $wp_query->post->ID;
	$wipOptions = get_option( 'wipOptions' );
	$wipDynamicMeta = get_post_meta( $postID, '_dynamic', true );
	$wipLayoutMeta = get_post_meta( $postID, '_layout', true );

	// Loads the main stylesheet
	wp_enqueue_style( 'wsuStyle', get_stylesheet_uri() );
	wp_enqueue_style( 'shadowboxStyle', get_template_directory_uri() . '/css/shadowbox.css' );
	//	wp_enqueue_style( 'calendarhack', get_stylesheet_directory_uri() . '/a1ecalendarfix.css' );
	if(is_front_page() || is_page('calendar') || is_page('upcoming-events')){
		wp_enqueue_style( 'calendarhack', get_stylesheet_directory_uri() . '/a1ecalendarfix.css' );
	};

	// Scripts
	wp_enqueue_script('jquery');

	// Adds JavaScript to pages with the comment form to support sites with threaded comments (when in use).
	if( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	wp_enqueue_script( 'wsujsRespond',   get_template_directory_uri() . '/js/respond.min.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'wsujsInit',      get_template_directory_uri() . '/js/init.js',        array(),           null, true );
	wp_enqueue_script( 'wsujsNavstyle',  get_stylesheet_directory_uri() . '/js/navstyle.js',  array(),           null       );
	wp_enqueue_script( 'wsujsShadowbox', get_template_directory_uri() . '/js/shadowbox.js',   array(),           null       );

	// Enqueue the slideshow js if appropriate
	if(
			( isset( $wipLayoutMeta['page_type'] ) && $wipLayoutMeta['page_type'] == 'dynamic' &&
				isset( $wipLayoutMeta['slideshow'] ) && $wipLayoutMeta['slideshow'] == 'show' &&
				$wipDynamicMeta['wipHomeArray'] != ''
			) ||
			( is_front_page() && isset( $wipOptions['indexSlide'] ) && isset( $wipOptions['slidesCategory'] ) && isset( $wipOptions['slideCount'] ) && !is_paged() )
	) {
		if( isset( $wipLayoutMeta['slideshow_order'] ) && $wipLayoutMeta['slideshow_order'] == 'random' )
			$slideshowRandom = 'true';
		else
			$slideshowRandom = 'false';
			wp_register_script( 'wsujsSlideshow', get_stylesheet_directory_uri() . '/js/slideshow.js', array(), null );
			wp_enqueue_script( 'wsujsSlideshow' );
			wp_localize_script( 'wsujsSlideshow', 'slideshowRandom', $slideshowRandom );
	}

	// If print link is enabled load script
	if( $wipOptions['tbPrintLink'] ) {
		$printCSS = get_stylesheet_directory_uri() . '/css/print.css';
		wp_register_script( 'wsujsPrint', get_template_directory_uri() . '/js/print.js', array(), null );
		wp_enqueue_script( 'wsujsPrint' );
		wp_localize_script( 'wsujsPrint', 'printCSS', $printCSS );
	}

}

add_action( 'wp_enqueue_scripts', 'scriptsStyles' );
/*
function cahnrs_capabilities() {

	$admin = get_role( 'administrator' );
	$admin_remove_caps = array(
		'activate_plugins',
		'delete_plugins',
		'manage_options',
		'switch_themes',
	);
	foreach ( $admin_remove_caps as $admin_remove_cap ) {
		$admin->remove_cap( $admin_remove_cap );
	}

}
add_action( 'init', 'cahnrs_capabilities' );
*/

add_filter( 'mce_buttons_2', 'cahnrswp_add_tinymce_table_plugin' );
/**
 * Add Table controls to tinyMCE editor.
 */
function cahnrswp_add_tinymce_table_plugin( $buttons ) {
   array_push( $buttons, 'table' );
   return $buttons;
}

add_filter( 'mce_external_plugins', 'cahnrswp_register_tinymce_table_plugin' );
/**
 * Register the tinyMCE Table plugin.
 */
function cahnrswp_register_tinymce_table_plugin( $plugin_array ) {
   $plugin_array['table'] = get_stylesheet_directory_uri() . '/tinymce/table-plugin.min.js';
   return $plugin_array;
}
?>