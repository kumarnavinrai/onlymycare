<?php global $theme; ?>
<?php 
//top div styles
/*
style="height:190px;width:110px;padding-top:10px;padding-left:15px;padding-right:15px;border:solid 1px #000000;margin-right:10px;float:left;background-color:#9C0356;"
*/
/*
style="height:150px;width:100px;"
*/
?>   
    <div style="border-bottom:dashed 1px #333333;margin-top:10px;"  <?php post_class('post '); ?> id="post-<?php the_ID(); ?>">
    
       
        
     <!--   <div class="postmeta-primary">

           <!-- <span class="meta_date"><?php echo get_the_date(); ?></span>-->
         <!--  &nbsp;  <span class="meta_categories"><?php the_category(', '); ?></span>-->

            <!--    <?php if(comments_open( get_the_ID() ))  {
                    ?> &nbsp; <span class="meta_comments"><?php comments_popup_link( __( 'No comments', 'themater' ), __( '1 Comment', 'themater' ), __( '% Comments', 'themater' ) ); ?></span><?php
                } ?>
        </div>-->
        
        <div class="entry clearfix" >
            
            <?php
                if(has_post_thumbnail())  {
                    ?><a href="<?php the_permalink(); ?>">
                   <?php the_post_thumbnail(
                        array($theme->get_option('featured_image_width'), $theme->get_option('featured_image_height')),
                        array("class" => $theme->get_option('featured_image_position') . " featured_image")
                    ); ?>
                  <!--  <?php the_post_thumbnail(
                        array(115, 115),
                        array("class" => $theme->get_option('featured_image_position') . " featured_image")
                    ); ?>-->
                    </a><?php  
                }
            ?>
            <h2 class="title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'themater' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2> 
       <!--   <div class="title" style="line-height:10px;" ><a href="<?php the_permalink(); ?>" style="font-size:12px;font-weight:bold;color:#ffffff;" title="<?php printf( esc_attr__( 'Permalink to %s', 'themater' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></div>-->  
            <?php
                //the_content('');
                //the_excerpt('');
                $my_excerpt = get_the_excerpt();
                $len = strlen($my_excerpt);
                $r=substr($my_excerpt,19,$len);
                echo $r;
            ?>
            <?php if($theme->display('read_more')) { ?>
        
        <div class="readmore">
            <a href="<?php the_permalink(); ?>#more-<?php the_ID(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'themater' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php $theme->option('read_more'); ?></a>
        </div>
        <?php } ?>
        

        </div>
        
        
        
    </div><!-- Post ID <?php the_ID(); ?> -->