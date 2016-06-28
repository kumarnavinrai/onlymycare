<?php global $theme; ?>
    
    <div style="height:170px;width:140px;padding-top:10px;padding-left:15px;padding-right:15px;border:solid 1px #000000;margin-right:30px;float:left;background-color:#ffffff;" <?php post_class('post '); ?> id="post-<?php the_ID(); ?>">
    
      <!--  <h2 class="title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'themater' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2> -->
        
     <!--   <div class="postmeta-primary">

           <!-- <span class="meta_date"><?php echo get_the_date(); ?></span>-->
         <!--  &nbsp;  <span class="meta_categories"><?php the_category(', '); ?></span>-->

            <!--    <?php if(comments_open( get_the_ID() ))  {
                    ?> &nbsp; <span class="meta_comments"><?php comments_popup_link( __( 'No comments', 'themater' ), __( '1 Comment', 'themater' ), __( '% Comments', 'themater' ) ); ?></span><?php
                } ?>
        </div>-->
        <!-- video play button start -->
       <a href="<?php the_permalink(); ?>">
                    <span class="wrap_playimg">
                    <span class="playimg">
                  <!--  <img src="<?php echo get_bloginfo('template_url'); ?>/images/play_smaller.jpg" width="64" height="64" /> -->
                    </span>
                    
                    </span>
                    
                                  
                </a>
         <!-- video play ends here -->       
        <div class="entry clearfix" style="height:150px;width:180px;">
            
            <?php
                if(has_post_thumbnail())  {
                    ?><a href="<?php the_permalink(); ?>">
                    <!--<?php the_post_thumbnail(
                        array($theme->get_option('featured_image_width'), $theme->get_option('featured_image_height')),
                        array("class" => $theme->get_option('featured_image_position') . " featured_image")
                    ); ?>-->
                    <?php the_post_thumbnail(
                        array(115, 115),
                        array("class" => $theme->get_option('featured_image_position') . " featured_image")
                    ); ?>
                    </a><?php  
                }elseif(!(has_post_thumbnail()))  {
                
                	$content = get_the_content();  
	                
	                if(!in_array($content,$contentArryVideo)){
		                $contentArryVideo[] = $content;
		                
		                $posOne = strpos($content,'[');
		                $posTwo = strpos($content,']');
		                $lenStr = $posTwo - $posOne;  
		                $str = substr($content,$posOne,$lenStr);
		                $strArr = explode(" ",$str);
		                $videoId = explode("=",$strArr[1]);       
		                $vStr = strlen($videoId[1]);
		                $vStr = $vStr -2;
		                $vId = substr($videoId[1],1,$vStr); 
	                
	                }
                
                
                ?>
               
                <a href="<?php the_permalink(); ?>">
                    
                    <img src="http://img.youtube.com/vi/<?php echo $vId; ?>/1.jpg" width="140" height="113" />
                                  
                </a>
               
                <?php
                }
            ?>
          <div class="title" style="line-height:13px;margin-top:7px;width:140px;" >
          <a href="<?php the_permalink(); ?>" style="font-size:14px;font-weight:bold;color:#9C0356;" title="<?php printf( esc_attr__( 'Permalink to %s', 'themater' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
          </div>  
            <?php
                //the_content('');
                /*the_excerpt('');*/
            ?>

        </div>
        
       <!-- <?php if($theme->display('read_more')) { ?>
        <div class="readmore">
            <a href="<?php the_permalink(); ?>#more-<?php the_ID(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'themater' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php $theme->option('read_more'); ?></a>
        </div>
        <?php } ?>-->
        
    </div><!-- Post ID <?php the_ID(); ?> -->
<!-- yutube play button css starts -->
 <style type="text/css">
.wrap_playimg { position: relative;  }
.playimg {
position: absolute;
display: block;
background: url('<?php echo get_bloginfo('template_url'); ?>/images/youtube-play-button.png') no-repeat;
height: 50px;
width: 70px;
top: 40px;
left: 46px;
}
</style>
<!-- youtube play button css ends -->
    