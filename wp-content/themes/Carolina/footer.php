    <div class="span-24">
	<div id="footer">Copyright &copy; <a href="<?php bloginfo('home'); ?>"><strong><?php bloginfo('name'); ?></strong></a>  - <?php bloginfo('description'); ?></div>
    <?php /* 
                    All links in the footer should remain intact. 
                    These links are all family friendly and will not hurt your site in any way. 
                    Warning! Your site may stop working if these links are edited or deleted 
                    
                    You can buy this theme without footer links online at http://newwpthemes.com/buy/?theme=carolina 
                */ ?>
    <div id="credits">Powered by <a href="http://wordpress.org/"><strong>WordPress</strong></a> | Designed by: <a href="http://freewpthemes.co">wordpress themes free</a> | Thanks to <a href="http://allpremiumthemes.com">Premium WordPress Themes</a>, <a href="http://allpremiumthemes.com">Premium Themes</a> and <a href="http://freewpthemes.co">WordPress Themes</a></div>
</div>
</div>
</div>
<?php
	 wp_footer();
	echo get_theme_option("footer")  . "\n";
?>
</body>
</html>