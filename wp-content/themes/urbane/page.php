<?php get_header(); ?>

<div class="grid_7">
    <div class="box_nobg">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        
        <div class="post" id="post-<?php the_ID(); ?>">   
			        <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>                    
       				<div class="postcontent"><?php the_content(); ?></div>
        			<div class="clear"></div>
        </div><!--post-->
        
<?php endwhile; endif; ?>

    </div>
</div>

<?php include (TEMPLATEPATH . '/pagebar.php'); ?>

<?php include (TEMPLATEPATH . '/bottom.php'); ?>
<?php get_footer(); ?>

