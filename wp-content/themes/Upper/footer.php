<?php global $theme; ?>

    <div id="footer-wrap" class="span-24">
        
        <div id="footer">
        
            <div id="copyrights">
                <?php
                    if($theme->display('footer_custom_text')) {
                        $theme->option('footer_custom_text');
                    } else { 
                        ?> &copy; <?php echo date('Y'); ?>  <a href="<?php bloginfo('url'); ?>/"><?php bloginfo('name'); ?></a>. <?php _e('All Rights Reserved.', 'themater');
                    }
                ?> 
            </div>
            
            <?php /* 
                    All links in the footer should remain intact. 
                    These links are all family friendly and will not hurt your site in any way. 
                    Warning! Your site may stop working if these links are edited or deleted 
                    
                    You can buy this theme without footer links online at http://fthemes.com/buy/?theme=upper 
                */ ?>
            
            <div id="credits">
                Powered by <a href="http://wordpress.org/">WordPress</a> | Designed by: <a href="http://wpcorner.com">Wordpress Templates</a> | Thanks to <a href="http://wickedtour.net/wicked-pittsburgh">Wicked Pittsburgh</a>, <a href="http://footballschedule.org/lsu-tigers-football-schedule.php">LSU Football Schedule</a> and <a href="http://concerttour.org/britney-spears-tour">Britney Spears Tour</a>
            </div><!-- #credits -->
            
        </div><!-- #footer -->
        
    </div><!-- #wrap-footer -->

</div><!-- #container -->

</div><!-- #wrapper -->

<?php wp_footer(); ?>
<?php $theme->hook('html_after'); ?>
</body>
</html>