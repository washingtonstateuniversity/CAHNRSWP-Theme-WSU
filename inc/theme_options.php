<?php
/*
Allow site owners to define the look and feel of their site
*/

function wipRegisterSettings() {
	register_setting( 'wipThemeOptions', 'wipOptions', 'wipValidateOptions' );
}
add_action( 'admin_init', 'wipRegisterSettings' );


function wipThemeOptions() {
	add_theme_page( 'Theme Options', 'Theme Options', 'edit_theme_options', 'theme_options', 'wipThemeOptions_page' );
}
add_action( 'admin_menu', 'wipThemeOptions' );


// The content of the options page
function wipThemeOptions_page() {

	// Check whether the form has just been submitted
	if( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;

	$settings = get_option( 'wipOptions' );

	?>
	<div class="wrap">
		<div class='icon32' id='icon-themes'><br></div>
		<h2>Theme Options</h2>
		<?php
			// Options saved
			if( $_REQUEST['settings-updated'] ) {
				?>
					<div class="updated fade"><p><strong>Options saved</strong></p></div>
				<?php
				// Get CSS file by design key
				// It would be preferable to check the just saved "design key" value against the previous one so that the stylesheet is downloaded only if they are different.
				$designKey = substr($settings['designKey'], 0, -1);
				try {

					$uploadDirectory = wp_upload_dir();
					chdir( $uploadDirectory['basedir'] );

					$defaultCSS = fopen( 'default.css', 'wb' );

					// Save "default" CSS from central
					$cssDesign = fopen( 'http://designer.wsu.edu/template/css2.aspx?key=' . $designKey . '1', 'rb' );
					$contents = '';
					while( !feof( $cssDesign ) ) {
						$contents .= fread( $cssDesign, 8192 );
					}
					fwrite( $defaultCSS, $contents );
					fclose( $defaultCSS );
					fclose( $cssDesign );

				} catch( Exception $e ) {
					?>
						<div class="error"><p><strong>Unable to save WSU Designer CSS files. Please retry saving options.</strong></p></div>
					<?php
				}
			}
		?>
		<form method="post" action="options.php">
		<?php
			settings_fields( 'wipThemeOptions' );
		?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">WSU CSS design key</th>
				<td>Generate at <a href="http://identity.wsu.edu/designer/Default.aspx" target="_blank">http://identity.wsu.edu/designer/Default.aspx</a>. Using the "default" layout is highly recommended (example: 0eab28zs011).<br />
					<input id="designKey" name="wipOptions[designKey]" type="text" value="<?php	if( isset( $settings['designKey'] ) ) esc_attr_e( $settings['designKey'] ); ?>"	maxlength="11" size="11" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">AddThis ID</th>
				<td>Register at <a href="http://www.addthis.com/" target="_blank">http://www.addthis.com/</a> if you want to be able to track how visitors are using your site's share tools.<br />
					<input id="addThis" name="wipOptions[addThis]" type="text" value="<?php	if( isset( $settings['AddThis'] ) ) esc_attr_e( $settings['addThis'] ); ?>"	size="11" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Google analytics ID</th>
				<td>This site has built-in stats, but you can insert your ID here if you also want to track site use with Google Analytics.<br />
					<input id="googleAnalytics" name="wipOptions[googleAnalytics]" type="text" value="<?php if( isset( $settings['googleAnalytics'] ) )	esc_attr_e( $settings['googleAnalytics'] ); ?>" size="11" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Toolbar</th>
				<td>If using a custom graphic for the toolbar background, upload your 775x20 pixel image via the Media Library and paste the path in this field.<br />
					<input id="tbBgImg" name="wipOptions[tbBgImg]" type="text" value="<?php if( isset( $settings['tbBgImg'] ) )	esc_attr_e( $settings['tbBgImg'] ); ?>" style="width:400px;" /><br />
					<label for="tbBreadcrumb"><input type="checkbox" id="tbBreadcrumb" name="wipOptions[tbBreadcrumb]" value="1" <?php if( isset( $settings['tbBreadcrumb'] ) ) checked( true, $settings['tbBreadcrumb'] ); ?> /> Show breadcrumb navigation</label><br />
					<label for="tbPrintLink"><input type="checkbox" id="tbPrintLink" name="wipOptions[tbPrintLink]" value="1" <?php if( isset( $settings['tbPrintLink'] ) ) checked( true, $settings['tbPrintLink'] ); ?> /> Show Print link</label><br />
					<label for="shareLink"><input type="checkbox" id="shareLink" name="wipOptions[tbShareLink]" value="1" <?php if( isset( $settings['tbShareLink'] ) ) checked( true, $settings['tbShareLink'] ); ?> /> Show AddThis Share link</label><br />
					<label for="tbSearch"><input type="checkbox" id="tbSearch" name="wipOptions[tbSearch]" value="1" <?php if( isset( $settings['tbSearch'] ) ) checked( true, $settings['tbSearch'] ); ?> /> Show site search form</label>
				</td>
			</tr>
      <tr valign="top">
				<th scope="row">Navigation Menu</th>
				<td><label for="disableJS"><input type="checkbox" id="disableJS" name="wipOptions[disableJS]" value="1" <?php if( isset( $settings['disableJS'] ) ) checked( true, $settings['disableJS'] ); ?> /> Disable WSU navigation javascript</label>
				</td>
			</tr>
			<?php
      	// If the "Reading Settings" > "Front page displays" is set to "Your latest posts", show this option
				if( get_option( 'show_on_front' ) == 'posts' ) {
					global $category, $slideCount;
					$category = get_categories();
					$slideCount = array( '1', '2', '3', '4', '5', '6', '7', '8', '9', '10' );
					?>
						<tr valign="top">
							<th scope="row">Homepage Slideshow</th>
							<td>The slideshow cycles through the featured images of posts from the selected category.<br />
								<label for="indexSlide"><input type="checkbox" id="indexSlide" name="wipOptions[indexSlide]" value="1" <?php  if( isset( $settings['indexSlide'] ) ) checked( true, $settings['indexSlide'] ); ?> /> Add Slideshow</label><br />
								Category <select id="slidesCategory" name="wipOptions[slidesCategory]">
									<option value="">(Select)</option>
									<?php 
										foreach( $category as $category_value ) {
											?>
												<option value="<?php echo $category_value->term_id; ?>"<?php  if( isset( $settings['slidesCategory'] ) ) selected( $settings['slidesCategory'], $category_value->term_id ); ?>><?php echo $category_value->cat_name; ?></option>
											<?php
										}
									?>
								</select><br />
								Number of Slides <select id="slideCount" name="wipOptions[slideCount]">
									<?php
										foreach( $slideCount as $slideCount_value ) {
											?>
												<option value="<?php echo $slideCount_value; ?>"<?php  if( isset( $settings['slideCount'] ) ) selected( $settings['slideCount'], $slideCount_value ); ?>><?php echo $slideCount_value; ?></option>
											<?php
										}
									?>
								</select>
							</td>
						</tr>
					<?php
				}
			?>
			<tr valign="top">
				<th scope="row">Posts, Pages, and Archives settings</th>
				<td>
					<div style="float:left;margin-right:5%;width:20%;">
						Show publication date on<br />
						<label for="postPubDate"><input type="checkbox" id="postPubDate" name="wipOptions[postPubDate]" value="1" <?php checked( true, $settings['postPubDate'] ); ?> /> Posts</label><br />
						<label for="pagePubDate"><input type="checkbox" id="pagePubDate" name="wipOptions[pagePubDate]" value="1" <?php checked( true, $settings['pagePubDate'] ); ?> /> Pages</label><br />
						<label for="loopPubDate"><input type="checkbox" id="loopPubDate" name="wipOptions[loopPubDate]" value="1" <?php checked( true, $settings['loopPubDate'] ); ?> /> Archives</label>
					</div>
					<div style="float:left;margin-right:5%;width:20%;">
						Show author on<br />
						<label for="postAuthor"><input type="checkbox" id="postAuthor" name="wipOptions[postAuthor]" value="1" <?php checked( true, $settings['postAuthor'] ); ?> /> Posts</label><br />
						<label for="pageAuthor"><input type="checkbox" id="pageAuthor" name="wipOptions[pageAuthor]" value="1" <?php checked( true, $settings['pageAuthor'] ); ?> /> Pages</label><br />
						<label for="loopAuthor"><input type="checkbox" id="loopAuthor" name="wipOptions[loopAuthor]" value="1" <?php checked( true, $settings['loopAuthor'] ); ?> /> Archives</label>
					</div>
					<div style="float:left;margin-right:5%;width:20%;">
						Enable AddThis share tools on<br />
						<label for="postShare"><input type="checkbox" id="postShare" name="wipOptions[postShare]" value="1" <?php checked( true, $settings['postShare'] ); ?> /> Posts</label><br />
						<label for="pageShare"><input type="checkbox" id="pageShare" name="wipOptions[pageShare]" value="1" <?php checked( true, $settings['pageShare'] ); ?> /> Pages</label>
					</div>
					<div style="float:left;margin-right:5%;width:20%;">
						Show categories and tags on<br />
						<label for="postTaxonomy"><input type="checkbox" id="postTaxonomy" name="wipOptions[postTaxonomy]" value="1" <?php checked( true, $settings['postTaxonomy'] ); ?> /> Posts</label><br />
						<label for="loopTaxonomy"><input type="checkbox" id="loopTaxonomy" name="wipOptions[loopTaxonomy]" value="1" <?php checked( true, $settings['loopTaxonomy'] ); ?> /> Archives</label>
					</div>
					<div style="clear:both;padding-top:12px;">
						Show post comment count on<br />
						<label for="loopComments"><input type="checkbox" id="loopComments" name="wipOptions[loopComments]" value="1" <?php checked( true, $settings['loopComments'] ); ?> /> Archives</label>
					</div>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Secondary div background image</th>
				<td>If you want a background image for the Secondary div in the two equal column layout, upload the image via the Media Library and paste its absolute URL in this field.<br />
					<input id="secondaryBgImg" name="wipOptions[secondaryBgImg]" type="text" value="<?php if( isset( $settings['secondaryBgImg'] ) ) esc_attr_e( $settings['secondaryBgImg'] ); ?>" style="width:400px;" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Footer Information</th>
				<td>Physical address.<br />
					<input id="footerInfo" name="wipOptions[footerInfo]" type="text" value="<?php	if( isset( $settings['footerInfo'] ) ) esc_attr_e( $settings['footerInfo'] ); ?>" size="50" /><br />
					Phone number.<br />
					<input id="footerPhone" name="wipOptions[footerPhone]" type="text" value="<?php if( isset( $settings['footerPhone'] ) )	esc_attr_e( $settings['footerPhone'] ); ?>" size="50" /><br />
					"Contact" link (prefix with <strong>mailto:</strong> if using an email address).<br />
					<input id="footerContact" name="wipOptions[footerContact]" type="text" value="<?php if( isset( $settings['footerContact'] ) ) esc_attr_e( $settings['footerContact'] ); ?>" size="50" /><br />
          <br />
          Second physical address (optional)<br />
					<input id="footerInfo" name="wipOptions[footerInfoSecond]" type="text" value="<?php if( isset( $settings['footerInfoSecond'] ) )	esc_attr_e( $settings['footerInfoSecond'] ); ?>" size="50" /><br />
					Second phone number (optional).<br />
					<input id="footerPhone" name="wipOptions[footerPhoneSecond]" type="text" value="<?php if( isset( $settings['footerPhoneSecond'] ) )	esc_attr_e( $settings['footerPhoneSecond'] ); ?>" size="50" /><br />
					Second "contact" link (prefix with <strong>mailto:</strong> if using an email address) (optional).<br />
					<input id="footerContact" name="wipOptions[footerContactSecond]" type="text" value="<?php	if( isset( $settings['footerContactSecond'] ) ) esc_attr_e( $settings['footerContactSecond'] ); ?>" size="50" />
				</td>
			</tr>
		</table>
		<p class="submit"><input type="submit" class="button-primary" value="Save Options" /></p>
	</form>
</div>
<?php
}

function wipValidateOptions( $input ) {
	// Strip HTML from the fields
	$input['designKey'] = wp_filter_nohtml_kses( $input['designKey'] );
	$input['addThis'] = wp_filter_nohtml_kses( $input['addThis'] );
	$input['googleAnalytics'] = wp_filter_nohtml_kses( $input['googleAnalytics'] );
	$input['tbBgImg'] = wp_filter_nohtml_kses( $input['tbBgImg'] );
	$input['secondaryBgImg'] = wp_filter_nohtml_kses( $input['secondaryBgImg'] );
	$input['footerInfo'] = wp_filter_nohtml_kses( $input['footerInfo'] );
	$input['footerPhone'] = wp_filter_nohtml_kses( $input['footerPhone'] );
	$input['footerContact'] = wp_filter_nohtml_kses( $input['footerContact'] );
	$input['footerInfoSecond'] = wp_filter_nohtml_kses( $input['footerInfoSecond'] );
	$input['footerPhoneSecond'] = wp_filter_nohtml_kses( $input['footerPhoneSecond'] );
	$input['footerContactSecond'] = wp_filter_nohtml_kses( $input['footerContactSecond'] );
	if( get_option( 'show_on_front' ) == 'posts' ) {
		$input['slidesCategory'] = wp_filter_nohtml_kses( $input['slidesCategory'] );
		$input['slideCount'] = wp_filter_nohtml_kses( $input['slideCount'] );
	}
	
	// Verify that the input is a boolean value if a checkbox has been checked, otherwise void it
	$checkbox = array( 'shareTools', 'tbPrintLink', 'tbBreadcrumb', 'tbShareLink', 'tbSearch', 'disableJS', 'postPubDate', 'pagePubDate', 'loopPubDate', 'postAuthor', 'pageAuthor', 'loopAuthor', 'postShare', 'pageShare', 'postTaxonomy', 'loopTaxonomy', 'loopComments' );
	if( get_option( 'show_on_front' ) == 'posts' ) {
		array_push($checkbox, 'indexSlide');
	}
	
	foreach( $checkbox as $checkbox_value ) {
		if( ! isset( $input[$checkbox_value] ) )
			$input[$checkbox_value] = null;
		$input[$checkbox_value] = ( $input[$checkbox_value] == 1 ? 1 : 0 );
	}

	return $input;
}
?>