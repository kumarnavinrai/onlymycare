<?php global $theme; ?>
    <?php $categories = get_the_category(); $catId = $categories[0]->term_taxonomy_id;
    $catName = $categories[0]->cat_name; ?> 
    <!-- videos cat check starts here -->
    <?php if ((!(preg_match("/Videos/i", strtolower($catName)))) && (!(preg_match("/Video/i", strtolower($catName)))) && (!(preg_match("/news/i", strtolower($catName))))) { ?>
    <!-- Category if starts here -->
    <?php if(!in_array($catId,$_SESSION['catTocheck'])){  ?>
    <?php $_SESSION['catTocheck'][] = $catId; ?>
    <div <?php post_class('post clearfix'); ?> id="post-<?php the_ID(); ?>" style="border-bottom: dashed 2px #9C0356;margin-bottom:6px !important; " >
    
        
        
      <!--  <div class="postmeta-primary">

                  <?php if(comments_open( get_the_ID() ))  {
                    ?> &nbsp; <span class="meta_comments"><?php //comments_popup_link( __( 'No comments', 'themater' ), __( '1 Comment', 'themater' ), __( '% Comments', 'themater' ) ); ?></span><?php
                } ?> 
        </div> -->
        
        <div class="entry clearfix">
            
            <?php
                if(has_post_thumbnail())  {
                    ?><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(
                        array($theme->get_option('featured_image_width'), $theme->get_option('featured_image_height')),
                        array("class" => $theme->get_option('featured_image_position') . " featured_image")
                    ); ?></a><?php  
                }
            ?>
            <!--title -->
            <div style="font-size:16px; color:#9C0356; font-family:'Oswald',sans-serif;line-height: 16px; font-weight:bold; text-decoration: none; "> 
            <a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'themater' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark" style="text-decoration: none; color:#9C0356;"><?php the_title(); ?></a>
            </div>
           <div> 
            <?php
                //the_content('');
                //the_excerpt('');
                $my_excerpt = get_the_excerpt();
               // $my_excerpt = substr($my_excerpt,19,150);
                echo $my_excerpt." ....";
            ?>
            </div>
           <!-- category -->
           <!-- <span class="meta_date"><?php //echo get_the_date(); ?></span> 
           &nbsp; -->  
           
        <!--  <div class="meta_categories" id="cat_at_home_page_id" style="float:right;color:#9C0356;" ><?php the_category(', '); ?></div> -->
        <?php
        
        // Get the URL of this category
    	$catNow = get_the_category();
	
    	// Get the URL of this category
    	$category_link = get_category_link($catNow[0]->cat_ID);
	?>
	<div class="meta_categories" id="cat_at_home_page_id" style="float:right;color:#9C0356;" ><a href="<?php echo esc_url( $category_link ); ?>"><?php  echo $catNow[0]->cat_name; ?></a></div>
        
        </div>
        
    <!--    <?php if($theme->display('read_more')) { ?>
        <div class="readmore">
            <a href="<?php the_permalink(); ?>#more-<?php the_ID(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'themater' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php $theme->option('read_more'); ?></a>
        </div>
        <?php } ?> -->
        
    </div><!-- Post ID <?php the_ID(); ?> 
    <?php } ?>
    <!-- category check if ends here -->
    <?php }  ?>
    <!-- videos category check ends here -->
    