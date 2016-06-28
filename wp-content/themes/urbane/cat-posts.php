<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } }
?>
<div class="grid_6">
    <div class="box">
		<?php $cat1=$ub_lcat; $my_query = new WP_Query('showposts=1&category_name='.$cat1); while ($my_query->have_posts()) : $my_query->the_post(); ?>
   			        <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php echo shorten_text(get_the_title(), 42); ?></a></h3>                   
                        <div class="cat1thumb">
                        <?php if (imagesrc()) { ?>
                        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><img src="<?php bloginfo('template_directory'); ?>/thumbs.php?src=<?php echo imagesrc(); ?>&h=180&w=180&zc=1" alt="<?php the_title(); ?>" class="ths"/></a>
                        <?php } ?> 
                        </div>
          				<p><?php limits(500, '<span class="readmore">Read More &raquo;</span>'); ?>
						<?php $category_id = get_cat_ID( $cat1 ); ?>
                        <span class="catname" style="margin-bottom:3px;"><a href="<?php bloginfo('url'); ?>/?cat=<?php echo $category_id; ?>&feed=rss2" title="RSS Feed for <?php echo $cat1; ?>"><?php echo $cat1; ?></a></span>
                        <br clear="all" />
                        </p>
        <?php endwhile; ?>
    </div>
</div>
<div class="grid_3">
    <div class="box">
		<?php $cat2=$ub_mcat; $my_query = new WP_Query('showposts=1&category_name='.$cat2); while ($my_query->have_posts()) : $my_query->the_post(); ?>
   			        <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php echo shorten_text(get_the_title(), 16); ?></a></h3>                   
                        <div class="cat2thumb">
                        <?php if (imagesrc()) { ?>
                        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><img src="<?php bloginfo('template_directory'); ?>/thumbs.php?src=<?php echo imagesrc(); ?>&h=80&w=170&zc=1" alt="<?php the_title(); ?>" class="ths"/></a>
                        <?php } ?> 
                        </div>
          				<p><?php limits(200, '<span class="readmore">Read More &raquo;</span>'); ?>
						<?php $category_id = get_cat_ID( $cat2 ); ?>
                        <span class="catname"><a href="<?php bloginfo('url'); ?>/?cat=<?php echo $category_id; ?>&feed=rss2" title="RSS Feed for <?php echo $cat2; ?>"><?php echo $cat2; ?></a></span>
                        <br clear="all" />
                        </p>
        <?php endwhile; ?>
    </div>
</div>
<div class="grid_3">
    <div class="box">
		<?php $cat3=$ub_rcat; $my_query = new WP_Query('showposts=1&category_name='.$cat3); while ($my_query->have_posts()) : $my_query->the_post(); ?>
   			        <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php echo shorten_text(get_the_title(), 16); ?></a></h3>                   
                        <div class="cat2thumb">
                        <?php if (imagesrc()) { ?>
                        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><img src="<?php bloginfo('template_directory'); ?>/thumbs.php?src=<?php echo imagesrc(); ?>&h=80&w=170&zc=1" alt="<?php the_title(); ?>" class="ths"/></a>
                        <?php } ?> 
                        </div>
          				<p><?php limits(200, '<span class="readmore">Read More &raquo;</span>'); ?>
						<?php $category_id = get_cat_ID( $cat3 ); ?>
                        <span class="catname"><a href="<?php bloginfo('url'); ?>/?cat=<?php echo $category_id; ?>&feed=rss2" title="RSS Feed for <?php echo $cat3; ?>"><?php echo $cat3; ?></a></span>
                        <br clear="all" />
                        </p>
        <?php endwhile; ?>
    </div>
</div>
<div class="clear"></div>