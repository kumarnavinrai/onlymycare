<?php

global $theme;

$themater_tweets_defaults = array(
    'title' => 'Recent Tweets',
    'number' => '5',
    'username' => 'twitter',
    'link_title' => 'true',
    'refresh' => '30',
    'profile_image' => 'false',
    'bird' => 'true',
    'retweets' => 'true',
    'replies' => '',
    'date' => 'true'
);
  
$theme->options['widgets_options']['tweets'] = is_array($theme->options['widgets_options']['tweets'])
    ?  array_merge($themater_tweets_defaults, $theme->options['widgets_options']['tweets'])
    : $themater_tweets_defaults;
    
add_action('widgets_init', create_function('', 'return register_widget("ThematerTweets");'));

class ThematerTweets extends WP_Widget 
{
    function __construct() 
    {
        $widget_options = array('description' => __('Advanced widget for displaying the recent tweets', 'themater') );
        $control_options = array( 'width' => 400);
		$this->WP_Widget('themater_tweets', '&raquo; Twitter Widget', $widget_options, $control_options);
        wp_enqueue_script( 'chirp', THEMATER_URL . '/js/chirp.min.js');
    }

    function widget($args, $instance)
    {
        
        
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        
            ?>
                <ul class="widget-container"><li class="tweets-widget">
                
                <?php  if ( $title ) {  ?> <h3 class="widgettitle"><?php 
                    if($instance['link_title']) {
                        printf("%s$title%s", "<a href=\"http://twitter.com/$instance[username]\" target=\"_blank\" title=\"$title\">", "</a>");
                    } else {
                        echo $title; 
                    }
                ?></h3> <?php }  ?>
                    <script>Chirp({
                      user:'<?php echo $instance[username]; ?>',
                      max:<?php echo $instance[number]; ?>,
                      retweets:<?php $get_retweets = $instance[retweets] == 'true' ? 'true' : 'false';  echo $get_retweets; ?>,
                      replies:<?php $get_replies = $instance[replies] == 'true' ? 'true' : 'false';  echo $get_replies; ?>,
                      cacheExpire: 1000 * 60 * <?php echo $instance[refresh]; ?>,
                      templates: {
                      	base: '<ul class="chirp">{{tweets}}</ul>',
                      	tweet: '<li<?php if($instance['bird']) { echo ' class="tweets-bird"'; } ?>><p><?php if($instance[profile_image] == 'true') { ?><a href="http://twitter.com/{{user.screen_name}}" title="{{user.name}} — {{user.description}}"><img src="{{user.profile_image_url}}" style="float:left; margin: 0 5px 5px 0;"></a> <?php }?>{{html}}<?php if($instance[date] == 'true') { ?><br /><time style="font-style:italic; text-decoration:underline;"><a href="http://twitter.com/{{user.screen_name}}/statuses/{{id_str}}">{{time_ago}}</a></time><?php }?></p></li>'
                      }
                    })</script>
                    </li></ul>
        <?php
    }
    

    function update($new_instance, $old_instance) 
    {				
    	$instance = $old_instance;
    	$instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = strip_tags($new_instance['number']);
        $instance['username'] = str_replace(array('http://twitter.com/', 'http://www.twitter.com/', 'www.twitter.com', 'twitter.com'), array('','','',''), strip_tags($new_instance['username']) );
        $instance['link_title'] = strip_tags($new_instance['link_title']);
        $instance['refresh'] = strip_tags($new_instance['refresh']);
        $instance['profile_image'] = strip_tags($new_instance['profile_image']);
        $instance['bird'] = strip_tags($new_instance['bird']);
        $instance['retweets'] = strip_tags($new_instance['retweets']);
        $instance['replies'] = strip_tags($new_instance['replies']);
        $instance['date'] = strip_tags($new_instance['date']);
        return $instance;
    }
    
    function form($instance) 
    {	
        global $theme;
        
		$instance = wp_parse_args( (array) $instance, $theme->options['widgets_options']['tweets'] );
        
        ?>
            <div class="tt-widget">
                <table width="100%">
                    <tr>
                        <td class="tt-widget-label" width="20%"><label for="<?php echo $this->get_field_id('title'); ?>">Title:</label></td>
                        <td class="tt-widget-content" width="80%"><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" /></td>
                    </tr>
                    
                    <tr>
                        <td class="tt-widget-label"><label for="<?php echo $this->get_field_id('username'); ?>">Twitter Username:</label></td>
                        <td class="tt-widget-content"><input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo esc_attr($instance['username']); ?>" /></td>
                    </tr>
                    
                    <tr>
                        <td class="tt-widget-label">Display:</td>
                        <td class="tt-widget-content">
                            latest <input style="width: 50px;" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($instance['number']); ?>" /> tweets, refresh every 
                            <input style="width: 50px;" id="<?php echo $this->get_field_id('refresh'); ?>" name="<?php echo $this->get_field_name('refresh'); ?>" type="text" value="<?php echo esc_attr($instance['refresh']); ?>" /> minutes
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="tt-widget-label">Misc Options:</td>
                        <td class="tt-widget-content">
                            <input type="checkbox" name="<?php echo $this->get_field_name('link_title'); ?>"  <?php checked('true', $instance['link_title']); ?> value="true" /> Link the widget title to your Twitter profile hmepage.
                            <br /><input type="checkbox" name="<?php echo $this->get_field_name('profile_image'); ?>"  <?php checked('true', $instance['profile_image']); ?> value="true" /> Show Your Profile Image
                            <br /><input type="checkbox" name="<?php echo $this->get_field_name('bird'); ?>"  <?php checked('true', $instance['bird']); ?> value="true" /> Display the bird icon.
                            <br /><input type="checkbox" name="<?php echo $this->get_field_name('retweets'); ?>"  <?php checked('true', $instance['retweets']); ?> value="true" /> Show Retweets
                            <br /><input type="checkbox" name="<?php echo $this->get_field_name('replies'); ?>"  <?php checked('true', $instance['replies']); ?> value="true" />  Show Replies
                            <br /><input type="checkbox" name="<?php echo $this->get_field_name('date'); ?>"  <?php checked('true', $instance['date']); ?> value="true" /> Show the date/time
                        </td>
                    </tr>
                    
                </table>
            </div>
        
        <?php 
    }
    
} 
?>