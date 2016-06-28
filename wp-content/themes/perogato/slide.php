<script type="text/javascript">
var $jx = jQuery.noConflict(); 
$jx(function() {
 $jx(".mygallery").jCarouselLite({
 btnNext: ".next",
        btnPrev: ".prev",
		visible: 1,
		easing: "backout",
	    speed: 1000
    });

});
</script>

<div id="slidearea">

<div id="gallerycover">
<div class="mygallery">

	<ul>
			<?php 
			$gldcat = get_option('pero_gldcat'); 
			$gldct = get_option('pero_gldct');
			$my_query = new WP_Query('category_name=' .$gldcat. '&showposts=' .$gldct. '');
			while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
			?>
    <li>
	<div class="mytext">
			<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>

			<?php the_excerpt(); ?> 

           
			<?php if ($preview = get_post_meta($post->ID, 'preview', $single = true)) { ?>
			<img class="slidim" src="<?php bloginfo('stylesheet_directory'); ?>/timthumb.php?src=<?php echo $preview; ?>&amp;h=180&amp;w=400&amp;zc=1" alt=""/> 
			<?php } else { ?>
			<img src="<?php bloginfo('template_directory'); ?>/images/place1.jpg"  />
			<?php } ?> 
		    </div>   	
	 </li>
			<?php endwhile; ?>
     </ul>

    <div class="clear"></div>  
	
</div>

</div>



   <a href="#" class="prev"></a>
   <a href="#" class="next"></a>   
</div>