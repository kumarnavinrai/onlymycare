<?php global $theme; ?>

    <div <?php post_class('post post-single clearfix'); ?> id="post-<?php the_ID(); ?>">
        
        <div style="height:20px;float:right;">
        <?php
        //echo do_shortcode('[widget id="wptextresize-2"]');
        wpTextResizeControls(0);
        ?>
        </div>
    
        <h2 class="title"><?php the_title(); ?></h2>
        <div style="height:25px;width:640px;padding-top:5px;padding-bottom:5px;border-bottom:solid 1px #9C0356;border-top:solid 1px #9C0356;margin-bottom:4px;">
            
            <div style="height:20px;float:left;">
            <?php
            //echo do_shortcode('[widget id="wptextresize-2"]');
            if(function_exists('pf_show_link')){ echo pf_show_link(); }
            ?>
            </div>
            <div style="height:20px;float:right;margin-right:10px;">
            <?php
             echo do_shortcode("[widget id='facebook-like-2']");
            ?>
            </div>
            <div style="height:20px;float:right;margin-right:10px;">
            <?php
             echo do_shortcode("[followbutton username='onlymycare' count='false' lang='en' theme='light']");
            ?>
            </div>
            <div style="height:20px;float:right;margin-right:10px;">
	            <script src="//platform.linkedin.com/in.js" type="text/javascript">
	 		lang: en_US
		    </script>
		    <script type="IN/Share" data-url="in.linkedin.com/pub/onlymycare-care/78/9a2/5a3/"></script>
            </div>
            <div style="height:20px;float:right;margin-right:10px;">
	        <!-- Place this tag where you want the share button to render. -->
		<div class="g-plus" data-action="share" data-annotation="none" data-href="https://plus.google.com/?hl=en&amp;partnerid=gplp0"></div>
		
		<!-- Place this tag after the last share tag. -->
		<script type="text/javascript">
		  (function() {
		    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		    po.src = 'https://apis.google.com/js/plusone.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();
		</script>
            </div>
            
        </div>
        

        <!--<div class="postmeta-primary">
    
          <span class="meta_date"><?php echo get_the_date(); ?></span> 
           &nbsp; <span class="meta_categories"><?php the_category(', '); ?></span>
    
                <?php if(comments_open( get_the_ID() ))  {
                    ?> &nbsp; <span class="meta_comments"><?php comments_popup_link( __( 'No comments', 'themater' ), __( '1 Comment', 'themater' ), __( '% Comments', 'themater' ) ); ?></span><?php
                }
                
                if(is_user_logged_in())  {
                    ?> &nbsp; <span class="meta_edit"><?php edit_post_link(); ?></span><?php
                } ?> 
        </div>-->
        
        <div class="entry clearfix">
            
            <?php
                /*if(has_post_thumbnail())  {
                    the_post_thumbnail(
                        array($theme->get_option('featured_image_width_single'), $theme->get_option('featured_image_height_single')),
                        array("class" => $theme->get_option('featured_image_position_single') . " featured_image")
                    );
                }*/
            ?>
            <div class="c_post_content">
            <?php
                the_content('');
                wp_link_pages( array( 'before' => '<p><strong>' . __( 'Pages:', 'themater' ) . '</strong>', 'after' => '</p>' ) );
            ?>
            </div>
        </div>
        
        <?php if(get_the_tags()) {
                ?><div class="postmeta-secondary"><span class="meta_tags"><?php the_tags('', ', ', ''); ?></span></div><?php
            }
        ?> 
        
    
    </div><!-- Post ID <?php the_ID(); ?> -->
    
    <?php 
        if(comments_open( get_the_ID() ))  {
            comments_template('', true); 
        }
    ?>