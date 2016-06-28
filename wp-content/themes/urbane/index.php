<?php get_header(); ?>
<?php include (TEMPLATEPATH . '/featuredpost.php'); ?>
<?php include (TEMPLATEPATH . '/tabs.php'); ?>
<?php include (TEMPLATEPATH . '/cat-posts.php'); ?>
<br />
<?php include (TEMPLATEPATH . '/sidebar1.php'); ?>

<div class="grid_6">
    <div class="box">
<?php query_posts('showposts=2'); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        
        <div class="post" id="post-<?php the_ID(); ?>">   
			        <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>             
                    <div class="date">Posted on <?php the_time('M d Y'); ?></div>        
       				<div class="postcontent"><?php limits(200, ''); ?></div>
        			<div class="clear"></div>
        </div><!--post-->
        
<?php endwhile; endif; ?>

    </div>
    <br />
</div>

<?php include (TEMPLATEPATH . '/sidebar2.php'); ?>
<?php include (TEMPLATEPATH . '/bottom.php'); ?>
<?php get_footer(); ?>

