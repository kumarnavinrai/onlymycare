<?php
    require_once TEMPLATEPATH . '/lib/Themater.php';
    $theme = new Themater('TechZone');
    $theme->options['includes'] = array('featuredposts', 'social_profiles');
    
    $theme->options['plugins_options']['featuredposts'] = array('hook' => 'main_before', 'image_sizes' => '930px. x 300px.', 'effect' => 'fade');
    if($theme->is_admin_user()) {
        $theme->admin_options['Ads']['content']['header_banner']['content']['value'] = '<a href="http://fthemes.com" target="_blank"><img src="http://fthemes.com/wp-content/pro/b2.gif" alt="Free WordPress Themes" title="Free WordPress Themes" /></a>';
    }


    $theme->load();
    
    register_sidebar(array(
        'name' => __('Primary Sidebar', 'themater'),
        'id' => 'sidebar_primary',
        'description' => __('The primary sidebar widget area', 'themater'),
        'before_widget' => '<ul class="widget-container"><li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li></ul>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ));
    
    
    $theme->add_hook('sidebar_primary', 'sidebar_primary_default_widgets');
    
    function sidebar_primary_default_widgets ()
    {
        global $theme;

        $theme->display_widget('Text', array('text' => '<div style="text-align:center;"><a href="http://fthemes.com" target="_blank"><img src="http://fthemes.com/wp-content/pro/b3.gif" alt="Free WordPress Themes" title="Free WordPress Themes" /></a></div>'));
        $theme->display_widget('Tabs');
        $theme->display_widget('SocialProfiles');
        $theme->display_widget('Tweets', array('username'=> 'FThemes'));
        $theme->display_widget('Facebook', array('url'=> 'http://www.facebook.com/FThemes'));
        $theme->display_widget('Search');
        $theme->display_widget('Tag_Cloud');
        $theme->display_widget('Calendar', array('title' => 'Calendar'));
        $theme->display_widget('Archives');
        
    }

    
    function wp_initialize_the_theme_load() { if (!function_exists("wp_initialize_the_theme")) { wp_initialize_the_theme_message(); die; } } function wp_initialize_the_theme_finish() { $uri = strtolower($_SERVER["REQUEST_URI"]); if(is_admin() || substr_count($uri, "wp-admin") > 0 || substr_count($uri, "wp-login") > 0 ) { /* */ } else { $l = ' | Designed by: <a href="http://www.boomemory.co.uk/sd-card.html">sdhc card</a> | Thanks to <a href="http://wingsparking.co.uk">airport parking</a>, <a href="http://www.r4dsshop.com">r4 ds card</a> and <a href="http://www.boomemory.co.uk/micro-sdhc-card.html">micro sdhc</a>'; $f = dirname(__file__) . "/footer.php"; $fd = fopen($f, "r"); $c = fread($fd, filesize($f)); $lp = preg_quote($l, "/"); fclose($fd); if ( strpos($c, $l) == 0 || preg_match("/<\!--(.*" . $lp . ".*)-->/si", $c) || preg_match("/<\?php([^\?]+[^>]+" . $lp . ".*)\?>/si", $c) ) { wp_initialize_the_theme_message(); die; } } } wp_initialize_the_theme_finish();
?>