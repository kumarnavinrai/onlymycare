<?php
global $post;
global $fb_old_comments_path;
// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'fbconnect_comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>
		<p class="nocomments">This post is password protected. Enter the password to view comments.</p>
	<?php
		return;
	}
	/*if ( ! comments_open() ){
	?>
	<p class="nocomments"><?php _e( 'Comments are closed.' ); ?></p>
	<?php 
	return;
	}*/
	
if (FBCONNECT_CANVAS=="web"){
	$width = "550";
	if (fb_get_option('fb_fbcomments_width') != ""){
		$width = fb_get_option('fb_fbcomments_width');
	}
}else{
	$width = "490";
	if (fb_get_option('fb_fbcomments_canvas_width') != ""){
		$width = fb_get_option('fb_fbcomments_canvas_width');
	}
}

$commentscolor="";
if (fb_get_option('fb_fbcomments_color')!=""){
	$commentscolor = 'colorscheme="dark"';
}
$commentsopen="";
if ( ! comments_open() ){
	$commentsopen='canpost="false" showform="false"';
}

$commentsxid = "";
if (fb_get_option('fb_fbcomments_usexid')=="href"){
	$commentsxid ='href="'.get_permalink($post->ID).'"';
}else{
	$commentsxid ='migrated=1 xid="'.$post->ID.'"';
}
?>
<div id="fbcommentscontainer">
<?php 
if (!fb_get_option('fb_hide_wpcomments') && fb_get_option('fb_show_fbcomments')){
?>
<div class="fbTabs">
<ul class="tabNavigation">
    <li><a id="fbFirstCommentsA" class="selected" href="#fbFirstComments" onclick="fb_showTabComments('fbFirstComments');return false;"><?php _e('Comments', 'fbconnect'); ?></a></li>
    <li><a id="fbSecondCommentsA" href="#fbSecondComments" onclick="fb_showTabComments('fbSecondComments');return false;"><?php _e('Facebook comments', 'fbconnect'); ?></a></li>
</ul>
<div id="fbFirstComments" class="fb_commentstab">
	<?php require( $fb_old_comments_path ); ?>
</div>
<div id="fbSecondComments" style="display:none;" class="fb_commentstab">
<?php 
//<fb:comments migrated=1 xid="echo $post->ID;" showform="false" canpost="false"> </fb:comments>
?>
<fb:comments <?php echo $commentscolor;?> <?php echo $commentsopen;?> numposts="<?php echo fb_get_option('fb_fbcomments_numposts'); ?>" width=<?php echo $width;?> <?php echo $commentsxid;?>> </fb:comments>
</div>
</div>
<?php
}elseif (fb_get_option('fb_show_fbcomments')){
?>
<fb:comments <?php echo $commentscolor;?> <?php echo $commentsopen;?> numposts="<?php echo fb_get_option('fb_fbcomments_numposts'); ?>" width=<?php echo $width;?> <?php echo $commentsxid;?>> </fb:comments>
<?php
}elseif (!fb_get_option('fb_hide_wpcomments')){
	require( $fb_old_comments_path );
}
?>
</div>