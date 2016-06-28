<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } }
?>
<div class="grid_7"><!--featured post-->
    <div class="box">
    
		<?php $my_query = new WP_Query('showposts=1&category_name='.$ub_fcat); while ($my_query->have_posts()) : $my_query->the_post(); ?>
   			        <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php echo shorten_text(get_the_title(), 46); ?></a></h2>                   
                        <div class="featuredthumb">
                        <?php if (imagesrc()) { ?>
                        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><img src="<?php bloginfo('template_directory'); ?>/thumbs.php?src=<?php echo imagesrc(); ?>&h=100&w=100&zc=1" alt="<?php the_title(); ?>" class="ths"/></a>
                        <?php } ?> 
                        </div>
          				<p><?php limits(280, 'Read More of this Featured Post &raquo;'); ?></p>
        <?php endwhile; ?>

    </div>
</div>