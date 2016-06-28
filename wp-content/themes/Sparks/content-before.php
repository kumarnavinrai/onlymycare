<?php global $theme; get_header(); ?>

    <div id="main" class="span-24">
    
        <div id="primary-sidebar-wrap" class="span-4">
        
            <?php get_sidebars('primary'); ?>
    
        </div><!-- #primary-sidebar-wrap -->
        
        <div id="content-wrap" class="span-13">
        
        <?php $theme->hook('content_before'); ?>