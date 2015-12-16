<?php
/*
Displays everything up to <div id="main">
*/

// A couple variables used all the time
$wipOptions = get_option( 'wipOptions' );
$uploadDirectory = wp_upload_dir();

// Post- and page-specific variables
if( is_single() || is_page() ) {
	$wipLayoutMeta = get_post_meta( $post->ID, '_layout', true );
	$wipHeadMeta = get_post_meta( $post->ID, 'head', true );
	$wipBodyMeta = get_post_meta( $post->ID, 'body', true );
	if ( is_page() )
		$wipDynamicMeta = get_post_meta( $post->ID, '_dynamic', true );
} else {
	$wipDynamicMeta = false;
	$wipLayoutMeta = false;
	$wipHeadMeta = false;
	$wipBodyMeta = false;
}

// Taxonomy-specific variables
$catDesc = category_description();
$tagDesc = tag_description();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
		<title><?php
			wp_title( '' );
			if( wp_title( '', false ) )
				echo ' - ';
			bloginfo( 'name' );
			echo ' | ';
			bloginfo( 'description' );
		?></title>
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		<?php
			include( get_stylesheet_directory() . "/css/layout.php" );

			wp_head();

			// Include custom header stuff if there is any (haven't thoroughly tested this escaping)
			if( $wipHeadMeta ) {
				$allowedHeaderTags = array(
					'link' => array(
						'href' => array(),
						'rel' => array(),
						'type' => array(),
						'media' => array()
					),
					'style' => array(
						'type' => array(),
						'media' => array()
					),
					'script' => array(
						'type' => array(),
						'src' => array()
					)
				);
				echo wp_kses( $wipHeadMeta, $allowedHeaderTags );
			}
			
			// If navigation override is in place
			$categories = get_the_category();
			foreach( $categories as $category ) {
				$categoryID = $category->cat_ID;
				if( get_tax_meta( $categoryID, 'navOverride' ) )
					echo '<script language="JavaScript" type="text/javascript">var navcurrentpage="' . get_tax_meta( $categoryID, 'navOverride' ) . '";</script>';
				if( ( is_category( $categoryID ) || in_category( $categoryID ) ) && get_tax_meta( $categoryID, 'customCSS' ) )
					echo '<link rel="stylesheet" href="' . get_tax_meta( $categoryID, 'customCSS' ) . '" type="text/css" media="all" />';
			}
		?>
		<script type="text/javascript"> Shadowbox.init(); </script>
	</head>
	<body<?php if( $wipBodyMeta ) echo ' ' . esc_attr( $wipBodyMeta ); /* Include custom body attribute stuff if there is any*/ ?>>

<?php if ( ! defined( 'CAHNRSANALYTICS' ) ): ?>
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-PC9VFJ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PC9VFJ');</script>
<!-- End Google Tag Manager -->
<?php endif;?>
		<div id="wrapper">

			<div id="globalnav">
				<ul>
					<li><a href="http://index.wsu.edu/">A-Z Index</a></li>
					<li><a href="http://www.about.wsu.edu/statewide/">Statewide</a></li>
					<li><a href="https://portal.wsu.edu/">zzusis</a></li>
					<li><a href="http://www.wsu.edu/">WSU Home</a></li>
					<li>
						<form name="wsusearch" method="get" action="http://search.wsu.edu/Default.aspx" id="globalnavsearchform" _lpchecked="1">
							<input name="cx" type="hidden" value="013644890599324097824:kbqgwamjoxq" />
							<input name="cof" type="hidden" value="FORID:11" />
							<input name="sa" type="hidden" value="Search" />
							<input name="fp" type="hidden" value="true" />
							<input name="q" type="text" value="Search WSU Web/People" onclick="erasetextboxwsu();" onblur="checktextboxwsu();" /><a href="#" onclick="document.wsusearch.submit(); return false;"><img border="0" alt="Submit" id="searchbuttonimg" src="http://images.wsu.edu/global/global-search-arrow.jpg" align="top"></a></form>
					</li>
				</ul>
			</div><!-- #globalnav -->

			<div id="logo">
				<a href='http://www.wsu.edu'><img src='http://images.wsu.edu/global/bg-logo3.jpg' alt="WSU Logo"/></a>
			</div><!-- #logo -->

			<div id="siteID">
				<h2 id="mobile">WSU CAHNRS</h2>
				<h2><?php bloginfo( 'description' ); ?></h2>
				<h1><a href="<?php bloginfo( 'url' ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
			</div><!-- #siteID -->

			<div id="toolbar">
				<?php
					// If the "Show breadcrumb" option is selected
					if( $wipOptions['tbBreadcrumb'] ) {
						echo '<p id="breadcrumb">';

						// If this is the home page, display just the site name - otherwise make it a link.
						if( is_front_page() )
							bloginfo( 'name' );
						else
							echo '<a href="' . get_bloginfo( 'url' ) . '">' . get_bloginfo( 'name' ) . '</a> &raquo; ';

						// The rest of the breadcrumb stuff
						if( function_exists( 'breadcrumbs' ) )
							breadcrumbs( array( 'singular_post_taxonomy' => 'category' ) );

						echo '</p>';
					}
				?>
				<ul>
					<?php
						// If the "Show AddThis Share link" option is selected
						if( $wipOptions['tbShareLink'] )
							echo '<li><a class="addthis_button">Share</a></li>';

						// If the "Show Print link" option is selected
						if( $wipOptions['tbPrintLink'] )
							echo '<li><a href="javascript:popPrintPage();">Print</a></li>';

						if( $wipOptions['tbSearch'] ) {
							?><li>
								<form name="sitesearch" method="get" id="sitesearch" action="<?php bloginfo( 'url' ); ?>/" onsubmit="this.submit();return false;">
									<input type="text" name="s" id="sitesearch_input" value="Search" onclick="erasetextboxlocal();" onblur="checktextboxlocal();" /><input type="submit" value="Search" id="sitesearch_submit" />
								</form>
							</li><?php
						}
					?>
				</ul>
			</div><!-- #toolbar -->

			<div id="content">

				<div id="mobilenav">

					<form name="menu">
						<select name="compoundnav" id="compoundnav" onchange="location=document.menu.compoundnav.options[document.menu.compoundnav.selectedIndex].value;" value="go">
							<option value="network" selected="selected">Navigation</option>
							<?php
								$locations = get_nav_menu_locations();
								if( isset( $locations['mobile'] ) && $locations['mobile'] != '' )
									$menu = wp_get_nav_menu_object( $locations['mobile'] );
								else
									$menu = wp_get_nav_menu_object( $locations['primary'] );
									
								$menuItem = wp_get_nav_menu_items($menu->term_id);
	
								echo '<optgroup label="' . get_bloginfo( 'name' ) . '">';
	
								foreach( ( array ) $menuItem as $key => $menuItem_value ) {
									$title = $menuItem_value->title;
									$url = $menuItem_value->url;
									echo '<option value="' . $url . '">' . $title . '</option>';
								}
	
								echo '</optgroup>';
							?>
							<optgroup label="Washington State University">
								<option value="http://index.wsu.edu/">A-Z Index</option>
								<option value="http://www.about.wsu.edu/statewide/">Statewide</option>
								<option value="https://portal.wsu.edu/">zzussis</option>
								<option value="http://www.wsu.edu/">WSU Home</option>
								<option value="https://www.applyweb.com/public/inquiry?wsuuinq">Request Info</option>
								<option value="http://admission.wsu.edu/visits/index.html">Visit</option>
								<option value="http://admission.wsu.edu/applications/index.html">Apply</option>
								<option value="http://cahnrsalumni.wsu.edu/give/">I Want to Give</option>
							</optgroup>
						</select>
					</form>

					<div id="sitenav_toggle">
						<a href="#nav">Menu</a>
					</div>

					<div id="compoundsearch_toggle">
						<a href="#compoundsearch">Search</a>
					</div>

					<div id="compoundsearch">
						<form action="<?php bloginfo( 'url' ); ?>/" class="msearch" method="get" name="sitesearchm" onsubmit="this.submit();return false;">
							<input class="msearch_input" name="s" type="text" value="Search" onclick="erasetextboxlocalm();" onblur="checktextboxlocalm();" /><input class="msearch_submit" type="submit" value="Search" />
						</form>
						<form action="http://search.wsu.edu/Default.aspx" class="msearch" method="get" name="wsusearchm" onsubmit="this.submit();return false;">
							<input name="cx" value="013644890599324097824:kbqgwamjoxq" type="hidden" />
							<input name="cof" value="FORID:11" type="hidden" />
							<input name="sa" value="Search" type="hidden" />
							<input name="fp" value="true" type="hidden" />
							<input class="msearch_input" name="q" type="text" value="Search WSU Web/People" onclick="erasetextboxwsu();" onblur="checktextboxwsu();" /><input class="msearch_submit" type="submit" value="Search" />
						</form>
					</div><!-- #compoundsearch -->

				</div><!-- #mobilenav -->

				<div id="nav">

					<div id="featured">
						<?php
							// The Navigation menu
							wp_nav_menu( array( 'theme_location' => 'featured', 'fallback_cb' => 'featuredNavFallback' ) );
						?>
					</div><!-- #featured -->

					<?php
						// The Navigation menu
						wp_nav_menu( array( 'theme_location' => 'primary', 'fallback_cb' => false ) );

						// The "Navigation Column" widget, if active
						if( is_active_sidebar( 'navigation' ) ) {
							echo '<div style="margin-top:25px;">';
							dynamic_sidebar( 'navigation' );
							echo '</div>';
						}
					?>

				</div><!-- #nav -->

				<?php
					if( ! $wipOptions['disableJS'] )
						echo '<script type="text/javascript"> initNav(); </script>';
				?>