<?php global $theme; get_header(); ?>
<!-- post-homepage.php array declaration -->
<?php $_SESSION['catTocheck'] = array(); ?>
    <div id="main">
    
        <?php $theme->hook('main_before'); ?>

        <div id="content">
        <?php 
        	  $rand1=rand(1, 100);
        	  $rand2=rand(1, 100);
        ?>
            <?php if($rand1>$rand2){ ?>
            <?php /* dynamic_content_gallery(); */ } ?>
            <?php if($rand1<$rand2){ ?>
            <?php /* $theme->hook('content_before'); */ } ?>
            <?php if($rand1==$rand2){ ?>
            <?php /* $theme->hook('content_before'); */ } ?>
            <div style="background: none repeat scroll 0 0 #9C0356;margin: 10px 0 10px;padding: 10px;font-size:20px;color:#ffffff;font-family:'Oswald',sans-serif;font-weight:bold;line-height: 20px;text-decoration: none; text-align:center;">Latest on Onlymycare.com</div>
            <?php // query_posts(array('posts_per_page' => 100)); ?>
            <?php $args = array( 'posts_per_page' => 40, 'orderby' => 'rand' );
                  $rand_posts = get_posts( $args ); 
                  
                  ?>
            <?php $i=0;
            
                if ($rand_posts):  //print_r($rand_posts);
                foreach ($rand_posts as $post) : 
                setup_postdata($post);   
                //the_post();
                    /**
                     * The default post formatting from the post.php template file will be used.
                     * If you want to customize the post formatting for your homepage:
                     * 
                     *   - Create a new file: post-homepage.php
                     *   - Copy/Paste the content of post.php to post-homepage.php
                     *   - Edit and customize the post-homepage.php file for your needs.
                     * 
                     * Learn more about the get_template_part() function: http://codex.wordpress.org/Function_Reference/get_template_part
                     */

                    get_template_part('post', 'homepage');
                    $i++; //echo $i;
                    //if($i==15)break;
                    if(count($_SESSION['catTocheck'])==10)break;
                endforeach;
                
                else :
                    get_template_part('post', 'noresults');
                endif; 
                // Reset Query
               // wp_reset_query();
              //  get_template_part('navigation');
            ?>
            <div>
            <?php /** code for wp navigation simple pagination plugin **/ ?>
            <?php if(function_exists('wp_simple_pagination')) { ?>
             <div>
            <?php        wp_simple_pagination();  ?>
            </div>
            <?php } ?>
            <?php /** code for wp navigation simple pagination plugin **/ ?>
            </div>
            <div>
            <?php //if(function_exists('TCHPCSCarousel')){ /*echo TCHPCSCarousel();*/ } ?>
            <?php //if ( function_exists('show_skitter') ) { /*show_skitter();*/ } ?>
            <?php /*echo do_shortcode('[frontslider]');*/ ?>
            <?php //echo do_shortcode('[widget id="recent-posts-flexslider-2"]'); ?>
            <?php //if (function_exists('slideshow')) { /*slideshow($output = true, $gallery_id = false, $post_id = 2291, $params = array());*/ } ?>
            </div>
            <?php $theme->hook('content_after'); ?>
        
        </div><!-- #content -->
    
        <?php get_sidebars(); ?>
        
        <?php $theme->hook('main_after'); ?>
        
    </div><!-- #main -->
<!--category color for latest posts for home page -->
<style>
.vnewsticker, a.vnewsticker {
    color: #000000 !important;
    font-family: verdana,arial,sans-serif !important;
    font-size: 16px !important;
    font-weight: bold !important;
    padding-right: 10px !important;
    text-decoration: none !important;
    align:left !important;
}
 .st_tip{
    margin-bottom:10px;
    font-size: 13px;
    font-weight: bold;
   }
#dfcg-openGallery{
    background-color: #9C0356 !important;
}
#dfcg-text {
    background-color: #9C0356 !important;
}
#dfcg-fullsize {
    background: none !important;
    border: 0 !important;
    height: 250px;
    overflow: hidden;
    padding: 0;
    position: relative;
    width: 460px;
    z-index: 1;
}
#cat_at_home_page_id a{
color:#9C0356 !important;
font-weight:bold;
text-decoration:none;
}
.slideInfoZone h2{
color:#ffffff !important;
font-size:13px !important;
font-weight:bold !important;
}

.slideInfoZone p{
color:#ffffff !important;
font-size:13px !important;
font-weight:bold !important;
}
</style>   
<?php get_footer(); ?>
