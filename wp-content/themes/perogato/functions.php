<?php
	require_once(TEMPLATEPATH . '/controlpanel.php'); 
if ( function_exists('register_sidebar') )
    register_sidebar(array(
    'before_widget' => '<div class="sidebox">',
    'after_widget' => '</div>',
	'before_title' => '<h3 class="sidetitl">',
    'after_title' => '</h3>',
    ));
	
	 function popular_posts($no_posts = 6, $before = '<li>', $after = '</li>', $show_pass_post = false, $duration='') {
global $wpdb;
$request = "SELECT ID, post_title, COUNT($wpdb->comments.comment_post_ID) AS 'comment_count' FROM $wpdb->posts, $wpdb->comments";
$request .= " WHERE comment_approved = '1' AND $wpdb->posts.ID=$wpdb->comments.comment_post_ID AND post_status = 'publish'";
if(!$show_pass_post) $request .= " AND post_password =''";
if($duration !="") { $request .= " AND DATE_SUB(CURDATE(),INTERVAL ".$duration." DAY) < post_date ";
}
$request .= " GROUP BY $wpdb->comments.comment_post_ID ORDER BY comment_count DESC LIMIT $no_posts";
$posts = $wpdb->get_results($request);
$output = '';
if ($posts) {
foreach ($posts as $post) {
$post_title = stripslashes($post->post_title);
$comment_count = $post->comment_count;
$permalink = get_permalink($post->ID);
$output .= $before . ' <a href="' . $permalink . '" title="' . $post_title.'">' . $post_title . '</a> ' . $after;
}
} else {
$output .= $before . "None found" . $after;
}
echo $output;
} 
	
?>