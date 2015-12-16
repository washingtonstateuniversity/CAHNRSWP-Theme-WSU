<?php
/*
Serve up CSS based on what kind of layout is being used
*/

// If this is a category archive, get its ID and make it a variable for use later on
if( is_category() )
	$categoryID = get_query_var( 'cat' );


// Establish some variables to determine the layout of a custom content type post
if( is_singular() && ! is_singular( array ( 'post', 'page', 'attachment' ) ) ) {
	$customContentType = get_post_type();
	$customContentType_object = get_post_type_object( $customContentType );
	//$customContentType_extend = $customContentType_object->wip_layout_options;
	//if( $customContentType_extend == false )
		$standardLayout = $customContentType_object->single_layout;
}


// Establish some variables to determine the layout of a custom content type archive
if( is_post_type_archive() && ( ! is_post_type_archive( array ( 'post', 'page', 'attachment' ) ) || ! is_tax() ) ) {
	$customContentType = get_queried_object()->name;
	$customContentType_object = get_post_type_object( $customContentType );
	$customContentType_extend = $customContentType_object->archive_widget;
	if( $customContentType_extend == false )
		$standardLayout = $customContentType_object->archive_layout;
}


// Establish some variables to determine the layout of a custom taxonomy
if( is_tax() && ! is_post_type_archive() ) {
	$customTaxonomy = get_query_var( 'taxonomy' );
	$customTaxonomy_object = get_taxonomy( $customTaxonomy );
	$customTaxonomy_extend = $customTaxonomy_object->wip_taxonomy_options;
	if( $customTaxonomy_extend == true ) {
		if( is_tax( $customTaxonomy ) )
			$customTaxonomy_termID = get_queried_object()->term_id;
	} else {
		$standardLayout = $customTaxonomy_object->archive_layout;
	}
}


// Styles to apply for a given layout
$fullWidth = "#secondary, #additional { display:none; }\n	#main { padding:20px 0 0 2.2%; width:76.3%; }\n";
$sideBar = "#main { padding:20px 2.8% 0 2.2%; width:51.6%; }\n #wrapper #content #secondary { margin-left:0; padding:20px 2.2% 0 2.2%; width:19.7%; }\n #additional { display:none; }\n";
$twoColumns = "#main { padding-left:2.2%; width:37%; }\n #wrapper #content #secondary { background:none !important; clear:none; margin:0; padding:20px 0 0 2.3%; width:37%; }\n #additional { display:none; }\n";
$threeColumns = "#main { clear:none; padding:20px 0 0 2.2%; width:23.9%; }\n	#wrapper #content #secondary { background:none !important; clear:none; margin:0; padding:20px 0 0 2.2%; width:23.9%; }\n #additional { clear:none; display:inline; padding:20px 0 0 2.2%; width:23.9%; }\n";
$fourColumns = "#main { clear:none; padding:20px 0 0 2.2%; width:17.4%; }\n	#wrapper #content #secondary { background:none !important; clear:none; margin:0; padding:20px 0 0 2.2%; width:17.4%; }\n #additional { clear:none; display:inline; padding:20px 0 0 2.2%; width:17.4%; }\n #fourth { background-color:white; clear:none; display:inline; float:left; font-size:0.75em; padding:20px 0 0 2.2%; width:17.4%; }\n";

echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . $uploadDirectory['baseurl'] . "/default.css\" media=\"all\" />\n";

echo "<style type=\"text/css\" media=\"screen\">\n	";

// Slideshow
if(
		( isset( $wipLayoutMeta['page_type'] ) && $wipLayoutMeta['page_type'] == 'dynamic' &&
			isset( $wipLayoutMeta['slideshow'] ) && $wipLayoutMeta['slideshow'] == 'show'
			&& $wipDynamicMeta['wipHomeArray'] != '' ) ||
		( is_front_page() &&
			( isset( $wipOptions['indexSlide'] ) &&
				$wipOptions['indexSlide'] ) &&
				isset( $wipOptions['slidesCategory'] ) &&
				isset( $wipOptions['slideCount'] ) &&
				!is_paged()
			)
		)
{
	?>
		#home { display:block; height:275px; text-align:center; width:775px; }
		#home #slideshow { height:250px; }
		#home #slideshow div, #home #slideshow div table { height:250px; width:100% !important; }
		#home #slideshow div table { background:url("<?php bloginfo( 'stylesheet_directory' ); ?>/images/slideshow-overlay.png") bottom right no-repeat transparent; border:none; border-collapse:collapse; border-spacing:0; }
		#home #slideshow div table tr { border:none; }
		#content #home h1 { font-family:"Times New Roman",Times,serif; font-size:24px; font-style:italic; font-weight:bold; line-height:24px; margin:0; max-height:92px; overflow:hidden; padding:0 5px 5px; }
		#content #home h4 { font-family:"Lucida Grande","Lucida Sans Unicode",Arial,san-serif; font-size:14px; font-weight:bold; margin:0; max-height:87px; overflow:hidden; padding:0 5px 5px; }
		#content #home ul#slidethmbs { display:inline-block; height:26px; margin:0 auto; padding:0; text-align:left; width:auto; }
		#content #home ul#slidethmbs li { display:block; float:left; height:25px; list-style:none; margin:0 10px; width:72px; }
		#content #home ul#slidethmbs li a { padding:1px; display:block; }
		#content #home ul#slidethmbs li.activeSlide a { background:#981e32; }
		#content #home ul#slidethmbs li a img { display:block; }
<?php
}

// If a header image has been set
if( get_header_image() )
	echo "#siteID { background-image:url(" . get_header_image() . "); }\n";

// If a custom toolbar background image has been set
if( $wipOptions['tbBgImg'] )
	echo "#wrapper #toolbar{ background:url(" . $wipOptions['tbBgImg'] . ") no-repeat; }\n";

echo "@media only screen and (min-width:769px) {\n";

// If in the "Two equal column" layout and a custom background image for the Secondary div has been set
if( ( isset( $wipLayoutMeta['layout'] ) && $wipLayoutMeta['layout'] == '2' ) && ( $wipOptions['secondaryBgImg'] ) )
	echo "#wrapper #content #secondary{ background:url(" . $wipOptions['secondaryBgImg'] . ") no-repeat !important; margin-top:30px !important; }\n";

// A big ridiculous conditional to determine which layout styles to apply
if( is_singular() ) { 
	if(
			( is_singular( array ( 'post', 'page' ) ) && ( isset( $wipLayoutMeta['layout'] ) && $wipLayoutMeta['layout'] != '' ) ) ||
			( ( is_singular( $customContentType ) /*&& $customContentType_extend == true*/ ) && ( isset( $wipLayoutMeta['layout'] ) && $wipLayoutMeta['layout'] != '' ) )
	) {		
		if( $wipLayoutMeta['layout'] == '0' ) echo $fullWidth;
		if( $wipLayoutMeta['layout'] == '1' ) echo $sideBar;
		if( $wipLayoutMeta['layout'] == '2' ) echo $twoColumns;
		if( $wipLayoutMeta['layout'] == '4' ) echo $threeColumns;
		if( $wipLayoutMeta['layout'] == '5' ) echo $fourColumns;
	} else if( is_singular( array ( 'post', 'page' ) ) && !isset( $wipLayoutMeta['layout'] ) ) {
		echo $fullWidth;
	} else if( is_singular( $customContentType ) ) { // A custom content type with a standard layout
		if( $standardLayout ) {
			if( $standardLayout == 'full' ) echo $fullWidth;
			if( $standardLayout == 'sidebar' ) echo $sideBar;
			if( $standardLayout == 'two' ) echo $twoColumns;
			if( $standardLayout == 'three' ) echo $threeColumns;
			if( $standardLayout == 'four' )	echo $fourColumns;
		}	else {
			echo $fullWidth;
		}
	} else {
		echo $fullWidth;
	}
} else if( is_home() ) {
	if( is_active_sidebar( 'index' ) )
		echo $sideBar;
	else
		echo $fullWidth;
} else if( is_archive() ) {
	if(
			( ( is_archive() && is_active_sidebar( 'archive' ) ) && ! is_category() && ! is_tag() ) ||
			( is_category() && ( ! empty( $catDesc ) || is_active_sidebar( 'category_archive' ) || is_active_sidebar( 'category_' . $categoryID . '_widget' ) ) ) ||
			( is_tag() && ( ! empty( $tagDesc ) || is_active_sidebar( 'tag_archive' ) ) ) ||
			is_author()
		)
	{
		echo $sideBar;
	} else if( is_post_type_archive() ) {
		if( ( is_post_type_archive( $customContentType ) && $customContentType_extend == true ) && is_active_sidebar( $customContentType . '_archive' ) ) {
			echo $sideBar;
		}
		else if( is_post_type_archive( $customContentType ) ) {
			if( $standardLayout ) {
				if( $standardLayout == 'full' ) echo $fullWidth;
				if( $standardLayout == 'sidebar' ) echo $sideBar;
				if( $standardLayout == 'two' ) echo $twoColumns;
				if( $standardLayout == 'three' ) echo $threeColumns;
				if( $standardLayout == 'four' ) echo $fourColumns;
			} else {
				echo $fullWidth;
			}
		} else {
			echo $fullWidth;
		}
	} else if( is_tax() ) {
		if( is_tax( $customTaxonomy ) && ( $customTaxonomy_extend == true ) && ( term_description() || is_active_sidebar( $customTaxonomy . '_archive' ) || is_active_sidebar( $customTaxonomy . '_' . $customTaxonomy_termID . '_archive' ) ) ) {
			echo $sideBar;
		} else if( is_tax( $customTaxonomy ) ) {
			if( $standardLayout ) {
				if( $standardLayout == 'full' ) echo $fullWidth;
				if( $standardLayout == 'sidebar' ) echo $sideBar;
				if( $standardLayout == 'two' ) echo $twoColumns;
				if( $standardLayout == 'three' ) echo $threeColumns;
				if( $standardLayout == 'four' ) echo $fourColumns;
			} else {
				echo $fullWidth;
			}
		} else {
			echo $fullWidth;
		}
	} else {
		echo $fullWidth;
	}
} else {
	echo $fullWidth;
}

echo "}\n";

if( $wipLayoutMeta['layout'] == '1' )
	echo "@media only screen and (min-width:986px) {\n #main { padding:20px 0 0 22px; width:495px; }\n #wrapper #content #secondary { margin:25px 0 0 25px; padding:20px 22px 0 22px; width:189px; }\n }";
	
if( $wipLayoutMeta['layout'] == '2' )
	echo "@media only screen and (min-width:986px) {\n #main { padding:20px 0 0 22px; width:355px; }\n #wrapper #content #secondary { padding:20px 22px 0 22px; width:354px; }\n }";

// Title stuff
if( is_singular() &&
		(
			isset( $wipLayoutMeta['layout'] ) &&
			(
				$wipLayoutMeta['layout'] != '0' &&
				$wipLayoutMeta['layout'] != '1'
			)
		)
	)
	echo "#main, #wrapper #content #secondary, #additional, #fourth { padding-top:0; }\n";

echo "</style>\n";
?>