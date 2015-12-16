<?php
/*
Page Not Found template.
*/

get_header();
?>
<div id="main">
	<h1>Page not Found</h1>
	<p></p>
	<p>The CAHNRS and Extension web presence is under reconstruction. We apologize for the inconvenience and appreciate your patience during this transition. The content you are looking is most likely in a new location, so please try using the navigation or the search form below to find it.</p>
<p><?php get_search_form(); ?></p>
    <p>Or maybe you were looking for information in one main sections of our site.</p>
      <?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'depth' => 1, 'fallback_cb' => false ) ); ?>
    
    <p>Alternatively, you can go to  
    <?php    
    echo '<a href="' . get_bloginfo( 'url' ) . '">Home page</a> &raquo; ';
?></p>
    <p>One last thing, if you're feeling so kind, please 
<?php
$wipOptions = get_option( 'wipOptions' );
               if( $wipOptions['footerContact'] )
				echo ' <a href="' . esc_url( $wipOptions['footerContact'] ) . '">Contact Us</a>'; // The theme options "Footer" >
?>    
 
     about this error, so that I can fix it. Thanks!</p>
</div><!-- #main -->
<?php 
get_footer();
?>