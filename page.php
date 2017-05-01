<?php
/*
Template for displaying Pages
*/

$wipOptions = get_option( 'wipOptions' );
$layoutMeta = $wipLayout->the_meta();
$dynamicMeta = $wipDynamic->the_meta();
$redirect = get_post_meta( get_the_ID(), 'redirect', true );
$campaignProgress = get_post_meta( get_the_ID(), 'campaignprogress', true );

// If there is custom redirect metadata on this page 
if( $redirect ) {
    wp_redirect( $redirect );
	die();
} else {
	get_header();

	while( have_posts() ) {
		the_post();

		// Split the content at the More tag if any of the multiple column layout options are selected, or of the page is dynamic
		if(
				( isset( $layoutMeta['layout'] ) &&
					( $layoutMeta['layout'] != '' || $layoutMeta['layout'] != '0' )
				) ||
				( isset( $layoutMeta['page_type'] ) && $layoutMeta['page_type'] == 'dynamic' )
		) {
			// Double angle brackets are necessary for the pattern to match.
			$content = preg_split( "<<!--more-->>", $post->post_content );
		}

		// Do slideshow stuff if this page has one and the site is in the WSU theme
		if( wp_get_theme()->Name != 'WIP' &&
				(
					isset( $wipLayoutMeta['page_type'] ) && $wipLayoutMeta['page_type'] == 'dynamic' &&
					isset( $wipLayoutMeta['slideshow'] ) && $wipLayoutMeta['slideshow'] == 'show' &&
					isset( $wipDynamicMeta['wipHomeArray'] ) && $wipDynamicMeta['wipHomeArray'] != ''
				)
		) {
			?>
        <div id="home">
          <div id="slideshow">
						<?php
              $cType = explode( ",", $dynamicMeta['wipHomeArray'] );
              include( get_stylesheet_directory() . "/inc/dynamic_slideshow_display.php" );
            ?>
          </div>
        </div>
			<?php
		}

		// Show the title across all columns if a two equal, three, or four column layout and if the author hasn't opted to hide it
		if( !isset( $layoutMeta['page_title'] ) &&
				( isset( $layoutMeta['layout'] ) &&
					( $layoutMeta['layout'] != '0' && $layoutMeta['layout'] != '1' )
				)
		) {
			?>
				<h1 id="pagetitle"><?php the_title(); ?></h1>
			<?php
		}

		// The "main" div is shown no matter what
		?>
      <div id="main">
        <?php
          // Show the title if a full or sidebar layout and if the author hasn't opted to hide it
          if( !isset( $layoutMeta['page_title'] ) &&
							( !isset( $layoutMeta['layout'] ) ||
								( isset( $layoutMeta['layout'] ) &&
									( $layoutMeta['layout'] == '0' || $layoutMeta['layout'] == '1' )
								)
							)
					) {
						?>
							<h1><?php the_title(); ?></h1>
						<?php
          }

          // "Show publication Date on pages" if option is selected
          if( $wipOptions['pagePubDate'] ) {
            ?>
            	<h6><?php the_time('F j, Y'); ?></h6>
            <?php
          }

          // "Show author on pages" if option is selected
          if( $wipOptions['pageAuthor'] ) {
            ?>
            	<p><strong>By <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a></strong></p>
            <?php
          }

          // If this is a dynamic page, include the dynamic display template
          if( isset( $dynamicMeta['wipMainArray'] ) &&
							( isset( $layoutMeta['page_type'] ) && $layoutMeta['page_type'] == 'dynamic' )
					) {
						$cType = explode( ",", $dynamicMeta['wipMainArray'] );
						// possibly include a conditional here - if none of the dynamic region arrays contain a cTypePage, output some content?
						include( get_template_directory() . "/inc/dynamic_display.php" );
					} else { // Otherwise, determine which layout is being used and output content appropriately
						if( !isset( $layoutMeta['layout'] ) || $layoutMeta['layout'] == '' || $layoutMeta['layout'] == '0' )
							the_content();
						else
							echo apply_filters( 'the_content', $content[0] );
					}

          // If the theme option for "Show AddThis share tools on pages" has been set
          if( $wipOptions['pageShare'] ) {
            ?>
              <div class="addthis_toolbox addthis_default_style">
                <a class="addthis_button_preferred_1"></a>
                <a class="addthis_button_preferred_2"></a>
                <a class="addthis_button_preferred_3"></a>
                <a class="addthis_button_preferred_4"></a>
                <a class="addthis_button_compact"></a>
                <a class="addthis_counter addthis_bubble_style"></a>
              </div>
            <?php
          }
        ?>
      </div><!-- #main -->
		<?php
			// If a multiple-column layout is selected, display the "secondary" div.
			if( isset( $layoutMeta['layout'] ) &&
					( $layoutMeta['layout'] == '1' || $layoutMeta['layout'] == '2' || $layoutMeta['layout'] == '3' || $layoutMeta['layout'] == '4' )
			) {
				?>
					<div id="secondary">
						<?php
    						if(is_front_page()) :
								     // Add All In One Calendar Events widget
              		    		     if( is_active_sidebar( 'upcomingevents' ) ) {
         						      dynamic_sidebar( 'upcomingevents' );
					               }
								 endif; 
   						 // For the A&F site - campaign progress meter
							if( isset( $campaignProgress ) && $campaignProgress != '' ) {
								?>
                  <div id="cougmeter_container">
                    <div id="cougmeter_progress"></div>
                    <img src="http://alumni.cahnrs.wsu.edu/files/2011/10/meter-w.png" width="193" height="207">
                    <div id="arrow">$<span id="dollaramount">0</span> Million</div>
                </div>
                <?php
							}

							if( isset( $dynamicMeta['wipSecondaryArray'] ) &&
									( isset( $layoutMeta['page_type'] ) && $layoutMeta['page_type'] == 'dynamic' )
							) {
								$cType = explode( ",", $dynamicMeta['wipSecondaryArray'] );
								include( get_template_directory() . "/inc/dynamic_display.php" );
							} else {
								if( preg_match( '/<!--more(.*?)?-->/', $post->post_content ) )
									echo apply_filters( 'the_content', $content[1] );
							}
						?>
					</div><!-- #secondary -->
				<?php
			}


			// If the Three Column layout is selected, display the "additional" div.
			if( isset( $layoutMeta['layout'] ) &&
					( $layoutMeta['layout'] == '4' || $layoutMeta['layout'] == '5' )
			) {
				?>
				<div id="additional">
					<?php
					if( isset( $dynamicMeta['wipAdditionalArray'] ) &&
							( isset( $layoutMeta['page_type'] ) && $layoutMeta['page_type'] == 'dynamic' )
					) {
						$cType = explode( ",", $dynamicMeta['wipAdditionalArray'] );
						include( get_template_directory() . "/inc/dynamic_display.php" );
					} else {
						if( preg_match( '/<!--more(.*?)?-->/', $post->post_content ) )
							echo apply_filters( 'the_content', $content[2] );
					}
					?>
				</div><!-- #additional -->
				<?php
			}


			// If the Four Column layout is selected, display the Fourth div.
			if( isset( $layoutMeta['layout'] ) && $layoutMeta['layout'] == '5' ) {
				?>
					<div id="fourth">
						<?php
							if( isset( $dynamicMeta['wipFourthArray'] ) &&
									( isset( $layoutMeta['page_type'] ) && $layoutMeta['page_type'] == 'dynamic' )
							) {
								$cType = explode( ",", $dynamicMeta['wipFourthArray'] );
								include( get_template_directory() . "/inc/dynamic_display.php" );
							} else {
								if( preg_match( '/<!--more(.*?)?-->/', $post->post_content ) )
									echo apply_filters( 'the_content', $content[3] );
							}
						?>
					</div><!-- #fourth -->
				<?php
			}

	}
	
	get_footer();

}
?>
