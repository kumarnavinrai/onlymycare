<?php global $cur_cat_id; 
$cur_cat_id = get_cat_id( single_cat_title("",false) ); 
$_SESSION['cur_cat_id']= $cur_cat_id;
?>
<?php global $theme; get_header(); ?>
<script src="<?php echo get_bloginfo('template_directory'); ?>/lib/js/jquery.roundabout.js"></script>
<!-- Add fancyBox main JS and CSS files -->
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/lib/fancybox/source/jquery.fancybox.js?v=2.1.4"></script>
<!--<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/lib/fancybox/fancyboxscript.js">--></script>
<link rel="stylesheet" type="text/css" href="<?php echo get_bloginfo('template_directory'); ?>/lib/fancybox/source/jquery.fancybox.css?v=2.1.4" media="screen" />
<script>
   jQuery(document).ready(function() {
      jQuery('ul.roundabout-holder').roundabout({
               btnNext: ".next",
               btnPrev: ".previous"
       });
     /*jQuery('.c_temp_link').fancybox(function(e){
        
        e.preventDefault();

        type: "iframe"
        //href: "http://google.com"
        });*/
   });

 </script>
<script>
$(window).load(function(){
	
	jQuery(".c_post_link").click(function(e) {
		e.preventDefault();  var link_name = jQuery(this).attr('url'); ;
		jQuery.fancybox.open({
			href : link_name,
			type : 'iframe',
                        width : '90%',
                        height : '90%',
			padding : 5
		});
	});
	
})
</script>	

<style>
   .roundabout-holder {
      list-style: none;
      padding: 0;
      margin: 0;
      height: 400px;
      width: 650px;
   }
   .roundabout-moveable-item {
      height: 350px;
      width: 300px;
      cursor: pointer;
      background-color: #FFFFFF !important;
      border: 1px solid #999;
   }
   .roundabout-in-focus {
      cursor: auto;
   }
</style> 

    <div id="main">
    
        <?php $theme->hook('main_before'); ?>
    
        <div id="content">
        
            <?php $theme->hook('content_before'); ?>
        
            <h2 class="page-title"><?php printf( __( 'Category Archives: <span>%s</span>', 'themater' ), single_cat_title( '', false ) ); ?></h2>
            
            <?php 
                $is_post_wrap = 0; 
                    if (have_posts()) : while (have_posts()) : the_post();
                     
                     /**
                     * The default post formatting from the post.php template file will be used.
                     * If you want to customize the post formatting for your category pages:
                     * 
                     *   - Create a new file: post-category.php
                     *   - Copy/Paste the content of post.php to post-category.php
                     *   - Edit and customize the post-category.php file for your needs.
                     * 
                     * Learn more about the get_template_part() function: http://codex.wordpress.org/Function_Reference/get_template_part
                     */
                    
                     $is_post_wrap++;
                        if($is_post_wrap == '1') {
                            ?><div class="post-wrap clearfix"><?php
                        }
                        get_template_part('post', 'category');
                        
                        if($is_post_wrap == '2') {
                            $is_post_wrap = 0;
                            ?></div><?php
                        }
                endwhile;
                
                else :
                    get_template_part('post', 'noresults');
                endif; 
                    
                    if($is_post_wrap == '1') {
                        ?></div><?php
                    } 
                
               
            ?>
            <?php if(function_exists('wp_simple_pagination')) { ?>
             <div>
            <?php         wp_simple_pagination();  ?>
            </div>
            <?php } ?>
 
<!--<ul class="roundabout-holder">
    <li class="roundabout-moveable-item"><h1>New Article from face book will be comming here.</h1></li>                
    <li class="roundabout-moveable-item"><h1>Wait for it upill tommrow.</h1></li>
    <li class="roundabout-moveable-item"><h1>It is 11:30 pm i am going to sleep</h1></li>
    <li class="roundabout-moveable-item"><h1>I am saying good night to you</h1></li>
    <li class="roundabout-moveable-item"><h1>Keep Smiling!!!! For ever.</h1></li>
    <li class="roundabout-moveable-item"><h1>I LOVE YOU LUCKY :-) NAVIN</h1></li>
</ul>--> 
<a href="#"><span class="previous" style="float:left"><img src="<?php echo get_bloginfo('template_directory'); ?>/images/white_previous.png" height="35" width="25" /></span></a>
<a href="#"><span class="next" style="float:right"><img src="<?php echo get_bloginfo('template_directory'); ?>/images/white_next.png" height="35" width="25" /></span></a>
            <?php 
             // Custom widget Area Start
             if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Widget Area 4') ) : ?>
            <div>
            <?php get_sidebars('footer_4');  ?>  
            </div>
            <?php endif; // Custom widget Area End ?>
<a href="#"><span class="previous" style="float:left"><img src="<?php echo get_bloginfo('template_directory'); ?>/images/previous.png" height="15" width="25" /></span></a>
<a href="#"><span class="next" style="float:right"><img src="<?php echo get_bloginfo('template_directory'); ?>/images/next.png" height="15" width="25" /></span></a>

<!--<a class="c_post_link" url="http://wp.dekhfashion.com" href="#">Iframe</a>-->

            <?php $theme->hook('content_after'); ?>
        
        </div><!-- #content -->
    
        <?php get_sidebars(); ?>
        
        <?php $theme->hook('main_after'); ?>
        
    </div><!-- #main -->
    
<?php get_footer(); ?>