<?php
/*
Template for slideshows
*/

// The color of the "More" button is determined by the color of the h1 tag
if( isset( $wipOptions['designKey'] ) &&  $wipOptions['designKey'] != '' )
	$design = substr( stripslashes( $wipOptions['designKey'] ), 3, 1 );
else
	$design = 'b';

foreach( $cType as $cType_value ) {


// "Posts" content type
	if( substr( $cType_value, 0, 10 ) == 'cTypePosts' ) {
		$limit = $dynamicMeta[$cType_value.'_number'];
		$category = $dynamicMeta[$cType_value.'_category'];
		$count = 0;
		$page = 1;
		$postList = new WP_Query( array( 'cat'=>$category, 'posts_per_page'=>$limit, 'paged'=>$page ) );						

		// Loop through posts
		while( $postList->have_posts() && $count < $limit ) {
			$toDisplay = $postList->next_post(); // Grab post object
			$imageData = wp_get_attachment_image_src( get_post_thumbnail_id( $toDisplay->ID ), "slideshow-wsu" ); // Grab featured image

			// Verify presence of featured image
			if( $imageData ) {
				$imageUrl = $imageData[0]; // Get image url
				$excerpt = $toDisplay->post_excerpt; // Get post excerpt

				// Verify image meets requirements to be shown
				if( $imageData[1] == 775 && $imageData[2] == 250 && $excerpt != '' ) {
					$count++;
					?>
						<div style="background-image:url(<?php echo $imageUrl; ?>);">
							<table>
								<tr>
                	<td style="width:56%;"></td>
									<td style="height:240px; padding:0 0 10px 0; position:relative; text-align:right; vertical-align:bottom; width:42%;">
										<?php
                    	echo $excerpt;
											echo '<a href="' . get_permalink( $toDisplay->ID ) . '" title="' . $toDisplay->post_title . '"><img src="' . get_stylesheet_directory_uri() . '/images/more-button-' . $design . '.png" width="83" height="28" alt="Read more" /></a>';
										?>
									</td>
                  <td style="width:2%;"></td>
								</tr>
							</table>
						</div>
					<?php
					// If limit isn't hit, make a new query and grab the next post
					if( !$postList->have_posts() && $count < $limit ) {
						// Increase page, get new set of posts
						$page++;
						$postList = new WP_Query( array( 'cat'=>$category, 'posts_per_page'=>$limit, 'paged'=>$page ) );
					}
				}
			}
		}
	}


// "Feed" content type. Simplepie is bundled with WordPress - documentation at http://www.simplepie.org/wiki/
	if( substr( $cType_value, 0, 9 ) == 'cTypeFeed' ) {
		// Get the feed items to show based on the URL and number of items selected by the user.
		if( function_exists('fetch_feed') ) {
			// Change the default feed cache recreation period to 1 hour
			add_filter( 'wp_feed_cache_transient_lifetime' , 'feedCache_hour' );
			
			$feed = fetch_feed($dynamicMeta[$cType_value.'_url']);
			
			remove_filter( 'wp_feed_cache_transient_lifetime' , 'feedCache_hour' );
				
			if( is_wp_error( $feed ) ) {
				echo 'This feed cannot be parsed.';
			} else {
				$count = 0;
				$limit = $dynamicMeta[$cType_value.'_number'];
				$feedItems = $feed->get_items();
	
				// Number conversions for display limit control
				if( $limit != '-1' )
					$limit = intval( $limit ); // Widget is set to limit
				else
					$limit = $feed->get_item_quantity(); // Widget set to unlimited
	
				foreach( $feedItems as $item ) {
					if( $count < $limit ) {
						$description = $item->get_description(); // Grab description
						$imageElement = $item->get_item_tags('', 'image');
						$image = $imageElement[0]['data'];
						$headlineElement = $item->get_item_tags('', 'headline');
						$headline = $headlineElement[0]['data'];
						if( $image != '' )
							$imageSize = getimagesize( $image ); // Get image size
						
						if( $image != '' && $headline != '' && $imageSize && $imageSize[0] > 700 && $imageSize[1] == 250 ) {
							// Increase count of displayed items
							$count++;
							?>
								<div style="background-image:url(<?php echo esc_url( $image ); ?>);height:250px;width:100%;">
									<table>
										<tr>
											<td style="width:56%;"></td>
											<td style="height:240px; padding:0 0 10px 0; position:relative; text-align:right; vertical-align:bottom; width:42%;">
												<?php
													echo wp_kses_post( $headline );
													echo '<a href="' . esc_url( $item->get_permalink() ) . '" title="' . esc_html( $item->get_title() ) . '" target="_blank"><img src="' . get_stylesheet_directory_uri() . '/images/more-button-' . $design . '.png" width="83" height="28" alt="Read more" /></a>';
												?>
											</td>
											<td style="width:2%;"></td>
										</tr>
									</table>
								</div>
							<?php
						}
					} else {
						break; // Display limit reached, break out of foreach
					}
				}
			}
		}
	}


// "Links" content type
	if( substr( $cType_value, 0, 10 ) == 'cTypeLinks' ) {
		$link = get_bookmarks( 'category=' . $dynamicMeta[$cType_value.'_category'] . '&limit=' . $dynamicMeta[$cType_value.'_number'] . '&orderby=link_id&order=DESC' );
		foreach( $link as $link_value ) {
			if( $link_value->link_image != '' ) {
				$image = $link_value->link_image;
				$imageSize = getimagesize( $image );

				// Verify link meets all requirements to be shown on the slideshow
				if( $imageSize && $imageSize[0] == 775 && $imageSize[1] == 250 ) {
					?>
						<div style="background-image:url(<?php echo esc_url( $link_value->link_image ); ?>);">
							<table>
								<tr>
									<td style="width:56%;"></td>
									<td style="height:240px; padding:0 0 10px 0; position:relative; text-align:right; vertical-align:bottom; width:42%;">
										<?php
											if( $link_value->link_notes && $link_value->link_description == '' ) {
												echo wp_kses_post( $link_value->link_notes );
											} else {
												echo '<h1>' . esc_html( $link_value->link_name ) . '</h1>';
												echo '<h4>' . esc_html( $link_value->link_description ) . '</h4>';
											}

											echo '<a href="' . esc_url( $link_value->link_url ) . '" title="' . esc_html( $link_value->link_name ) . '" target="' . $link_value->link_target . '"><img src="' . get_stylesheet_directory_uri() . '/images/more-button-' . $design . '.png" width="83" height="28"></a>';
										?>
									</td>
									<td style="width:2%;"></td>
								</tr>
							</table>
						</div>
					<?php
				}
			}
		}
	}


}
?>