
<div class="clear"></div>
<script type="text/javascript">
var $jx = jQuery.noConflict(); 
$jx(document).ready(function() {
	$jx('#tabzine> ul').tabs({ fx: { height: 'toggle', opacity: 'toggle' } });

});
	</script>
<div id="tabzine" class="widgets">
    <ul class="tabnav">
        <li class="pop"><a href="#searc"> SEARCH </a></li>
        <li class="fea"><a href="#recent"> RECENT </a></li>
		<li class="rec"><a href=" #popular"> POPULAR  </a></li>
    </ul>

<div id="searc" class="tabdiv">
  
   			<form method="get" id="searchform" action="<?php bloginfo('home'); ?>" >
			<input id="s" type="text" name="s" value="" onblur="if(this.value == '') {this.value = 'Site Search ';}" onfocus="if(this.value == 'Site Search') {this.value = '';}"  />
			</form>
			
		<div class="subsbutton">  <a href="<?php bloginfo('rss2_url'); ?>" ><img src="<?php bloginfo('stylesheet_directory'); ?>/images/feed.png" alt="bookmark" /></a></div>
		<div class="subsbutton">  <a href="http://del.icio.us/post?url=<?php bloginfo('siteurl');?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/delicious.png" alt="bookmark" /></a></div>
		<div class="subsbutton"> <a href="http://www.digg.com/submit?phase=2&amp;url=<?php bloginfo('siteurl');?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/digg.png" alt="bookmark"  /></a></div>
		<div class="subsbutton"> <a href="http://www.facebook.com/login.php"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/facebook.png" alt="bookmark"  /></a></div>
		<div class="subsbutton"> <a href="http://www.youtube.com/"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/youtube.png" alt="bookmark"  /></a></div>

<div class="clear"></div>
			
</div>
            
<div id="recent" class="tabdiv">
	<?php get_archives('postbypost', 8); ?>
</div>
			
 <div id="popular" class="tabdiv">
<ul><?php popular_posts(); ?></ul>
</div>

</div>