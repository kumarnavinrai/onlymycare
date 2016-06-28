<style>
.cvdiv a{
 color:#9C0356;
 text-decoration:none;
 font-weight: bold;
 
}
.c_videos_sidebar{
 border: solid 1px #9C0356;
 margin-bottom:5px;
 background-color:#9C0356;
 -moz-border-radius:5px;  /* for Firefox */
 -webkit-border-radius:5px; /* for Webkit-Browsers */
 border-radius:5px; /* regular */
 /*opacity:0.5;*/ /* Transparent Background 50% */
}
.c_videos_sidebar_inner{
  background-color:#FFFFFF;
  -moz-border-radius:5px;  /* for Firefox */
  -webkit-border-radius:5px; /* for Webkit-Browsers */
  border-radius:5px; /* regular */
 /*opacity:0.5;*/ /* Transparent Background 50% */
}
.c_view_all_sidebar{
  height:25px;
  background-color:#9C0356;
  color:#ffffff;
  text-align:right;
  padding-top:3px;
  font-weight:bold;
}
.c_view_all_sidebar a {
  height:25px;
  background-color:#9C0356;
  color:#ffffff;
  text-align:right;
  padding-top:3px;
  font-weight:bold;
  text-decoration:none;
}

.showLinkInFancyBoxUL li {
	list-style-type: none;
	margin: 0 0 16 5px;
	padding: 10px 0 3px 0;
	text-decoration:none;
	color:#9C0356;
	font-weight:bold;
	border-bottom: solid 1px #9C0356;
}

.showLinkInFancyBoxUL li a {
	list-style-type: none;
	margin: 0 0 0 0px;
	text-decoration:none;
	color:#9C0356;
	font-weight:bold;
	
}

.c_videos_sidebar_inner ul{
	margin: 0px;
	list-style-type: none;
	padding:5px;
}

</style>
<?php global $theme; ?>
<div id="sidebar-primary">

<!--sidebar primary news sidebar starts here -->
<div class="c_videos_sidebar">
<h3 class="widgettitle">Health News</h3>
<div class="c_videos_sidebar_inner">

    <?php 
    wp_reset_postdata();
    $order = 'description';
    //$cat_ids = $wpdb->get_col("SELECT DISTINCT term_taxonomy_id FROM $wpdb->term_taxonomy where description  LIKE '%news%' ORDER BY rand()"); // query users

$cat_ids = $wpdb->get_col("SELECT DISTINCT term_id FROM $wpdb->terms where name  LIKE '%news%' ORDER BY rand()"); // query users


    //$cat_ids = $wpdb->get_col("SELECT DISTINCT term_taxonomy_id FROM $wpdb->term_taxonomy LEFT JOIN $wpdb->terms ON $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id  where $wpdb->terms.name  LIKE '%news%' ORDER BY rand()"); // query users
  
    ?>
<!-- this php line is to stop displaying news category info on home pahe and rather display  -->	
<!-- rss news aggregate pligin output -->
<?php if(isset($donotdisplaynewscategory)): ?>
<?php
$contentArry = array();
$loopLimit = 4;
$loopVar = 1;
global $post;
$cat_ids = array_unique($cat_ids);

foreach($cat_ids as $cat_id):

/*$args = array(
	'posts_per_page'   => 5,
	'offset'           => 0,
	'category'         => ''.$cat_id.'',
	'orderby'          => 'post_date',
	'order'            => 'DESC',
	'include'          => '',
	'exclude'          => '',
	'meta_key'         => '',
	'meta_value'       => '',
	'post_type'        => 'post',
	'post_mime_type'   => '',
	'post_parent'      => '',
	'post_status'      => 'publish',
	'suppress_filters' => true );*/
	
$args = array( 'posts_per_page' => 5, 'offset'=> 1, 'category' => $cat_id );
//$args = array( 'posts_per_page' => 1, 'offset'=> 1, 'category' => 1140 );
//$posts = get_posts(array('numberposts' => 10000, 'category' => 5));

$myposts = get_posts($args);

foreach( $myposts as $post ) : setup_postdata($post); 
               
	        $content = get_the_content();  
                
                if(!in_array($content,$contentArry)){
                 $contentArry[] = $content;
                
                $posOne = strpos($content,'[');
                $posTwo = strpos($content,']');
                $lenStr = $posTwo - $posOne;  
                $str = substr($content,$posOne,$lenStr);
                $strArr = explode(" ",$str);
                $videoId = explode("=",$strArr[1]);       
                $vStr = strlen($videoId[1]);
                $vStr = $vStr -2;
                $vId = substr($videoId[1],1,$vStr);  
                
           ?>
           <div id="vdiv" class="cvdiv" style="height:165px;color:#9C0356;margin-bottom:2px;padding-top:5px;" >
           <div style="height:120px;text-align:center;" >
          <!-- <a href="<?php the_permalink(); ?>">
           <img width="148" height="113" style="margin-left:6px;" src="http://img.youtube.com/vi/<?php echo $vId; ?>/1.jpg" />
           </a>-->
           <?php
                  if ( has_post_thumbnail($post->ID)) {
		      echo '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( $post->post_title ) . '">';
		      echo get_the_post_thumbnail($post->ID, array('148','113'), 'thumbnail');
		      echo '</a>';
		    		
		 }
	   ?>
           </div>
           <div style="height:30px;border-bottom: 2px dashed #9C0356;text-align:center;margin-top:5px;margin-bottom:5px;padding-bottom:5px;" >
           <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
           </div>           
           </div>
           <?php 
              
              }elseif(in_array($content,$contentArry)){
                 $loopVar--;
              }
           ?>
<?php endforeach; ?>
<?php $loopVar++;
      if($loopVar>$loopLimit)break;
 ?>
<?php endforeach;  ?>
<?php endif; ?>
<?php //do_shortcode('[wp-rss-aggregator]'); ?>
<?php 

	wprss_display_feed_items( $args = array(
		'links_before' => '<ul class="showLinkInFancyBoxUL">',
		'links_after' => '</ul>',
		'link_before' => '<li class="showLinkInFancyBox">',
		'link_after' => '</li>',
		'limit' => '15'
		)); 

 //	wprss_display_feed_items(); 
		
?>
</div>
<div class="c_view_all_sidebar"><a href="http://onlymycare.com/category/health-news/">View All</a></div>
</div> <!-- c_video_sidebar ends here -->
<!--sidebar primary news sidebar ends here -->



<!--sidebar primary video sidebar starts here -->
<div class="c_videos_sidebar">
<h3 class="widgettitle">Latest Videos</h3>
<div class="c_videos_sidebar_inner">
    <?php 
    $order = 'description';
    $cat_ids = $wpdb->get_col("SELECT DISTINCT term_taxonomy_id FROM $wpdb->term_taxonomy where description  LIKE '%video%' ORDER BY rand()"); // query users
    ?>

<?php
$contentArry = array();
$loopLimit = 4;
$loopVar = 1;
global $post;
$cat_ids = array_unique($cat_ids);

foreach($cat_ids as $cat_id):

$args = array( 'posts_per_page' => 1, 'offset'=> 1, 'category' => $cat_id );

$myposts = get_posts( $args );

foreach( $myposts as $post ) : setup_postdata($post); ?>
	
          
          <?php $content = get_the_content();  
                
                if(!in_array($content,$contentArry)){
                 $contentArry[] = $content;
                
                $posOne = strpos($content,'[');
                $posTwo = strpos($content,']');
                $lenStr = $posTwo - $posOne;  
                $str = substr($content,$posOne,$lenStr);
                $strArr = explode(" ",$str);
                $videoId = explode("=",$strArr[1]);       
                $vStr = strlen($videoId[1]);
                $vStr = $vStr -2;
                $vId = substr($videoId[1],1,$vStr);  
                
           ?>
           <div id="vdiv" class="cvdiv" style="height:165px;color:#9C0356;margin-bottom:2px;padding-top:5px;" >
           <div style="height:120px;" >
           <a href="<?php the_permalink(); ?>">
           <img width="148" height="113" style="margin-left:6px;" src="http://img.youtube.com/vi/<?php echo $vId; ?>/1.jpg" />
           </a>
           </div>
           <div style="height:30px;border-bottom: 2px dashed #9C0356;text-align:center;margin-top:5px;margin-bottom:5px;padding-bottom:5px;" >
           <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
           </div>           
           </div>
           <?php 
              
              }elseif(in_array($content,$contentArry)){
                 $loopVar--;
              }
           ?>
<?php endforeach; ?>
<?php $loopVar++;
      if($loopVar>$loopLimit)break;
 ?>
<?php endforeach;  ?>
</div>
<div class="c_view_all_sidebar"><a href="http://onlymycare.com/category/videos-2/">View All</a></div>
</div> <!-- c_video_sidebar ends here -->
<!-- sidebar primary video sidebar ends here -->
<!-------------------------------------------------------------------------------------------------->



 <!-- Begin MailChimp Signup Form -->
<link href="http://cdn-images.mailchimp.com/embedcode/slim-081711.css" rel="stylesheet" type="text/css">
<style type="text/css">
	#mc_embed_signup{ clear:left; font:14px Helvetica,Arial,sans-serif; }
	/* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
	   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
</style>
<div id="mc_embed_signup">
<form action="http://onlymycare.us7.list-manage1.com/subscribe/post?u=4220a5bfb64c4447557a8ebc3&amp;id=f72ed0ce02" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
	<label for="mce-EMAIL">Subscribe to our mailing list</label>
	<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
	<div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
</form>
</div>

<!--End mc_embed_signup-->


<!-- sidebar primary news sidebar ends here -->
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




<script type="text/javascript" src="<?php echo get_site_url(); ?>/wp-includes/js/fancybox1.3.4/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<?php echo get_site_url(); ?>/wp-includes/js/fancybox1.3.4/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo get_site_url(); ?>/wp-includes/js/fancybox1.3.4/jquery.fancybox-1.3.4.css" media="screen" />


<script>

jQuery(document).ready(function() { 

	jQuery('.showLinkInFancyBox a').click(function(event){
		event.preventDefault();
		
		var eventGenerator = jQuery(this);
		
		/*
		jQuery(".showLinkInFancyBox a").fancybox({
		
			'type'	:	'iframe',
			'href'	:	eventGenerator.attr('href')
			
		});
		*/  
		jQuery.fancybox({
		
			'type'	:	'iframe',
			'width' : '90%',
			'height' : '90%',
			'href'	:	eventGenerator.attr('href')
			
		});
		
		
		
	});
 });
</script>