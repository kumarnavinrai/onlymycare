<div class="right">

<?php include (TEMPLATEPATH . '/tab.php'); ?>	

<div class="twifol"> 
<h3 class="sidetil"> Latest Tweets</h3>
</div>

<div id="twitter_div">

<?php
$twit = get_option('pero_twit'); 
include('twitter.php');?>
<?php if(function_exists('twitter_messages')) : ?>
       <?php twitter_messages("$twit") ?>
       <?php endif; ?>

</div>


<?php include (TEMPLATEPATH . '/sponsors.php'); ?>	

<div class="sidebar">

	<ul>
<?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar() ) : ?>    
		<div class="sidebox">
		<li>
			<h3 class="sidetitl">Pages</h3>
			<ul>
			<?php wp_list_pages('title_li='); ?>
			</ul>
		</li>
	</div>
	
			<div class="sidebox">
		<li>
			<h3 class="sidetitl">Pages</h3>
			<ul>
			<?php wp_list_categories('title_li='); ?>
			</ul>
		</li>
	</div>
		
	<?php endif; ?>
	</ul>

</div>


</div>