<div class="grid_3">
    <div class="box">
	<ul class="sidebar">
	<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('Home - Sidebar left') ) : else : ?>

        <li>
        <h2><?php _e('Categories'); ?></h2>
        <ul><li>
        <form action="<?php bloginfo('url'); ?>/" method="get" style="padding:0;margin:0;">
		<?php
        $select = wp_dropdown_categories('show_option_none=Select Category&show_count=1&orderby=name&echo=0');
        $select = preg_replace("#<select([^>]*)>#", "<select$1 onchange='return this.form.submit()'>", $select);
        echo $select;
        ?>
        <noscript><input type="submit" value="View" /></noscript>
        </form>
        </li></ul>
        </li>
        
        <li>
        <h2><?php _e('Archives'); ?></h2>
        <ul><li>
        <select name=\"archive-dropdown\" onChange='document.location.href=this.options[this.selectedIndex].value;' id="arch">
        <option value=\"\"><?php echo attribute_escape(__('Select Month')); ?></option>
        <?php wp_get_archives('type=monthly&format=option&show_post_count=1'); ?> </select>
        </li></ul>
        </li>
        
        <li>
        <h2><?php _e('Search'); ?></h2>
        <ul><li>
		<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
    	<input type="text" value="<?php the_search_query(); ?>" name="s" id="s" />
   		<input type="submit" id="searchsubmit" value="Search" />
		</form>
        </li></ul>
        </li>
        
	<?php endif; ?>
	</ul>
    </div>
</div>