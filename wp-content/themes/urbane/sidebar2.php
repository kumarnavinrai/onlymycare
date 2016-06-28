<div class="grid_3">
    <div class="box">
	<ul class="sidebar">
	<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('Home - Sidebar Right') ) : else : ?>
        
        <li>
        <h2><?php _e('Links'); ?></h2>
            <ul>
             <?php get_links(2, '<li>', '</li>', '', TRUE, 'url', FALSE); ?>
             </ul>
        </li>

	<?php endif; ?>
	</ul>
    </div>
</div>
<div class="clear"></div>