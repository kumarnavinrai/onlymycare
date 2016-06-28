<?php global $theme; ?>

<div id="sidebar-primary">
    <?php wp_list_pages('title_li=<h2>Pages</h2>'); ?>
    <?php
        if(!dynamic_sidebar('sidebar_primary')) {
            /**
            * The primary sidebar widget area. Manage the widgets from: wp-admin -> Appearance -> Widgets 
            */
            $theme->hook('sidebar_primary');
        }
        $theme->hook("sidebar_primary_after");
    ?>
    
</div><!-- #sidebar-primary -->


<div id="sidebar-secondary">

    <?php
        if(!dynamic_sidebar('sidebar_secondary')) {
            /**
            * The secondary sidebar widget area. Manage the widgets from: wp-admin -> Appearance -> Widgets 
            */
            $theme->hook('sidebar_secondary');
        }
    ?>
    
</div><!-- #sidebar-secondary -->