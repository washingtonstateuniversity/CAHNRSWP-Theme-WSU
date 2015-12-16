<?php
/*
Displays everything from <div id="localfooter"> to the closing html tag
*/

$wipOptions = get_option( 'wipOptions' );
?>
        <div id="localfooter">
          <a href="<?php bloginfo( 'url' ); ?>/"><?php bloginfo('name'); ?></a>,
          <?php
            if( $wipOptions['footerInfo'] )
							echo esc_html( $wipOptions['footerInfo'] ); // The theme options "Footer" > "Physical Address" field
            if( $wipOptions['footerPhone'] )
							echo ', <a href="tel:+1' . esc_attr( $wipOptions['footerPhone'] ) . '">' . esc_html( $wipOptions['footerPhone'] ) . '</a>'; // The theme options "Footer" > "Phone Number" field
            if( $wipOptions['footerContact'] )
							echo ', <a href="' . esc_url( $wipOptions['footerContact'] ) . '">Contact Us</a>'; // The theme options "Footer" > "Contact..." field
            if( $wipOptions['footerInfoSecond'] ) {
							echo '<br /><br />';
							echo esc_html( $wipOptions['footerInfoSecond'] ); // The theme options "Footer" > "Second physical address" field
						}
						if( $wipOptions['footerPhoneSecond'] )
							echo ', <a href="tel:+1' . esc_attr( $wipOptions['footerPhoneSecond'] ) . '">' . esc_html( $wipOptions['footerPhoneSecond'] ) . '</a>'; // The theme options "Footer" > "Second phone number" field
            if( $wipOptions['footerContactSecond'] )
							echo ', <a href="' . esc_url( $wipOptions['footerContactSecond'] ) . '">Contact Us</a>'; // The theme options "Footer" > "Second contact..." field
          ?>
        </div><!-- #footer -->
			</div><!-- #content  -->
		</div><!-- #wrapper -->
    <div id="wsufooter">
      <a href="http://publishing.wsu.edu/copyright/WSU.html">&copy; <?php echo date('Y'); ?></a> <a href="http://wsu.edu">Washington State University</a> | <a href="http://access.wsu.edu/">Accessibility</a> | <a href="http://policies.wsu.edu/">Policies</a> | <a href="http://publishing.wsu.edu/copyright/WSU.html">Copyright</a> | <?php wp_loginout(); ?>
    </div><!-- #wsufooter -->
		<?php
			// If either of the "Show AddThis... " options are selected, add the Javascript for it
      if( $wipOptions['postShare'] || $wipOptions['pageShare'] || $wipOptions['tbShareLink'] ) {
				?>
					<script type="text/javascript" src="http://s7.addthis.com/js/300/addthis_widget.js#pubid=<?php if( $wipOptions['addThis'] ) echo urlencode( stripslashes( $wipOptions['addThis'] ) ); ?>"></script>
          <script type="text/javascript">
            var addthis_config = {
              services_compact: 'email, facebook, twitter, google, more',
              services_exclude: 'print',
              ui_click: true,
							ui_508_compliant: true,
							data_track_clickback: true
            }
          </script>
				<?php
      }
			
			// If the "Google Analytics ID" field has been filled in, add the Javascript for it
			if( $wipOptions['googleAnalytics'] ) {
				?>
        <script>
					var _gaq=[['_setAccount','<?php echo esc_js( $wipOptions['googleAnalytics'] ); ?>'],['_trackPageview']];
					(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
					g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
					s.parentNode.insertBefore(g,s)}(document,'script'));
				</script>
				<?php
			}
			
			wp_footer();
		?>
	</body>
</html>