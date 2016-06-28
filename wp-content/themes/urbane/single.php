<?php get_header(); ?>
<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } }
?>
<div class="grid_7">
    <div class="box_nobg">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        
        <div class="post" id="post-<?php the_ID(); ?>">   
			        <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>             
                    <div class="date">This article was posted on <?php the_time('M d Y'); ?></div>  
       				<div class="postcontent"><?php the_content(); ?></div>
        			<div class="clear"></div>
                    <div class="meta">
                            <div class="tags"><?php the_tags('', ', ', '<br />'); ?></div>
                            <div class="cats"><?php the_category(', ') ?></div>
                    </div><!--meta-->
                    <?php comments_template(); ?>
        </div><!--post-->
        
<?php endwhile; endif; ?>

    </div>
</div>

<?php include (TEMPLATEPATH . '/pagebar.php'); ?>

<?php include (TEMPLATEPATH . '/bottom.php'); ?>
<?php get_footer(); ?>

