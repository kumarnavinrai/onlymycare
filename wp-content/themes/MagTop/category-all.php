<style>
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
.c_view_all_sidebar a {
  height:25px;
  background-color:#9C0356;
  color:#ffffff;
  text-align:right;
  padding-top:3px;
  font-weight:bold;
  text-decoration:none;
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
<?php global $theme; get_header(); ?>

    <div id="main">
    
        <?php $theme->hook('main_before'); ?>
    
        <div id="content">
        
            <?php $theme->hook('content_before'); ?>
        
            <h2 class="page-title" style="color:#9C0356;" ><?php printf( __( '<span>%s</span>', 'themater' ), single_cat_title( '', false ) ); ?></h2>
            
            <?php  $i=0; $youMustSeeThis = 0;
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
                    if($i<8){ 
                    get_template_part('post', 'category');
                    }else{ ?>
                  <?php if($youMustSeeThis == 0){ ?>
                  <div style="margin-top:450px;margin-bottom:20px;background:none repeat scroll 0 0 #9C0356;color:#ffffff;font-size:17px;font-weight:bold;width:593px;height:25px;padding-top:5px;padding-left:5px;letter-spacing:2px;" >YOU MUST SEE THESE</div>
                  <?php $youMustSeeThis=1;  } ?>
                    <?php get_template_part('post', 'normal');
                    }
                    $i++;
                endwhile;
                
                else :
                    get_template_part('post', 'noresults');
                endif; ?>
                
                 <?php if($youMustSeeThis == 0){ ?>
                 <div style="margin-top:450px;margin-bottom:20px;background:none repeat scroll 0 0 #9C0356;color:#ffffff;font-size:17px;font-weight:bold;width:593px;height:25px;padding-top:5px;padding-left:5px;letter-spacing:2px;" >ARTICLES FROM SAME CATEGORY</div>
                 <?php $youMustSeeThis=1;  } ?>
                <?php
                //if posts less than 24 then pic post from parent category starts here
                if($i<24){
					$numberOfPosts = 24-$i;
					$category = get_the_category();
					$parent_cat = $category[0]->category_parent;
					
					$allCatObject = get_the_category($parent_cat);
					
					$args = array( 'posts_per_page' => 1, 'offset'=> 1, 'category' => $parent_cat );
			
					$myposts = get_posts( $args ); 
			
					$categories=  get_categories('child_of='.$parent_cat.'');
				   
					foreach($categories as $cat){
						if($cat->cat_ID != $parent_cat){
							$allCatIds[] = $cat->cat_ID;
						}
					}
			
					$allCatIds = implode(',',$allCatIds); 
			
					//get_template_part('navigation');
					$my_query = new WP_Query('showposts='.$numberOfPosts.'&cat='.$allCatIds.'');

					while ($my_query->have_posts()) : $my_query->the_post(); 

					get_template_part('post', 'normal'); 

					endwhile; 
				}  //if posts less than 24 then pic post from parent category ends here ?> 
            
            <div style="clear:both;"></div>
            <?php /** code for wp navigation simple pagination plugin **/ ?>
            <?php if(function_exists('wp_simple_pagination')) { ?>
             <div>
            <?php         wp_simple_pagination();  ?>
            </div>
            <?php } ?>
            <?php /** code for wp navigation simple pagination plugin **/ ?>

            <?php $theme->hook('content_after'); ?>
        
        </div><!-- #content -->
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
		
        <?php /*get_sidebars();*/ ?>
        <?php include ('sidebar-category.php'); ?>
        
        <?php $theme->hook('main_after'); ?>
        
    </div><!-- #main -->
    
<?php get_footer(); ?>