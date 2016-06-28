<?php global $theme; ?>
<!--<div id="sidebar-primary">
   
    <?php
       /* if(!dynamic_sidebar('sidebar_primary')) {
            /**
            * The primary sidebar widget area. Manage the widgets from: wp-admin -> Appearance -> Widgets 
            */
            /*$theme->hook('sidebar_primary');*/
       /* } */
        /*$theme->hook("sidebar_primary_after");*/
    ?>
    
</div>--><!-- #sidebar-primary -->


<div id="sidebar-secondary">

    <?php
        echo do_shortcode('[widget id="search-5"]');
        echo "<div style='height:7px;clear:both'></div>";
        echo do_shortcode('[widget id="paypal_donations-4"]');
        echo "<div style='height:7px;clear:both'></div>";
        echo do_shortcode('[widget id="arpw_widget-4"]');
        echo "<div style='height:7px;clear:both'></div>";
        
        //if(!dynamic_sidebar('sidebar_secondary')) {
            /**
            * The secondary sidebar widget area. Manage the widgets from: wp-admin -> Appearance -> Widgets 
            */
            //$theme->hook('sidebar_secondary');
        //}
    ?>
    
    <!-- category side bar div -->
        <div style="width:280px;float:right;">
        <!-- video starts -->
                <div class="video_category">
		<!-- c_video_sidebar starts here -->
		<div class="c_videos_sidebar">
		<h3 class="widgettitle">Latest Videos</h3>
		<div class="c_videos_sidebar_inner">
		    <?php
		    $order = 'description';
		    $cat_ids = $wpdb->get_col("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy where description  LIKE '%video%' ORDER BY rand()"); // query users
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
		           <div style="/*height:120px;*/height:120px;width:150px;margin-right:10px;float:left;" >
		           <a href="<?php the_permalink(); ?>">
		           <img width="148" height="113" style="margin-left:6px;" src="http://img.youtube.com/vi/<?php echo $vId;  ?>/1.jpg" />
		           </a>
		           </div>
		           <div style="/*height:30px;border-bottom: 2px dashed #9C0356;text-align:center;margin-top:5px;margin-bottom:5px;padding-bottom:5px;*/height:120px;width:276px;border-bottom: 2px dashed #9C0356;text-align:center;margin-top:5px;margin-bottom:5px;padding-bottom:5px;" >
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
		</div> 
		<!-- c_video_sidebar ends here -->
		</div>
		<!-- video ends here -->
		</div>
		 <!-- category side bar div -->
    
    
</div><!-- #sidebar-secondary -->
<style>

.c_view_all_sidebar a {
  height:25px;
  background-color:#9C0356;
  color:#ffffff;
  text-align:right;
  padding-top:3px;
  font-weight:bold;
  text-decoration:none;
}

#wp_rp_first{
 background: url("<?php bloginfo('template_url'); ?>/images/background.png") repeat scroll 0 0 transparent !important;
 padding:4px;

}
.c_post_content{
    background-color: #FFFFFF;
    border: 1px solid #BDBDBD;
    padding: 8px;
}


.clearfix p a img {
    background-color: #FFFFFF;
    border: 1px solid #BDBDBD;
    padding: 4px;
}
#content {
    overflow: hidden !important;
    float: left !important;
    width: 630px !important;
    margin-left: 0px !important;
}

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

.video_category{    
    border-left: 1px solid #D6D6D6;
    float: right;
    margin-left: 10px;
    overflow: hidden;
    padding-left: 3px;
    width: 280px;
	
}
</style>
