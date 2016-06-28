<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } }
?>  
  <div id="TabbedPanels1" class="TabbedPanels">
    <ul class="TabbedPanelsTabGroup">
      <li class="TabbedPanelsTab" tabindex="0">Subscribe</li>
      <li class="TabbedPanelsTab" tabindex="0">Popular Posts</li>
      <li class="TabbedPanelsTab" tabindex="0">Recent Posts</li>
    </ul>
    <div class="TabbedPanelsContentGroup">
     	
              <div class="TabbedPanelsContent">
                  <div id="subscribe">
                  <div class="subscribetext">Subscribe to our newsletter & updates!</div>
                  <div class="subscriberss"><a href="http://feeds.feedburner.com/<?php echo $ub_feedburner; ?>" class="subtext">Subscribe via RSS feed</a></div>
                  <div class="twitter"><a href="http://www.twitter.com/<?php echo $ub_twitter; ?>" class="twittext">Follow us on Twitter!</a></div>
                  <div class="subscribemail">OR, subscribe via Email</div>
                  <form method="get" id="subscribeform" action="http://www.feedburner.com/fb/a/emailverify" target="popupwindow" onsubmit="window.open('http://www.feedburner.com', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
                  <input class="subscribeinput" value="" name="email" id="sub" type="text"><input type="hidden" value="http://feeds.feedburner.com/~e?ffid=<?php echo $ub_feed_id; ?>" name="url"/><input type="hidden" value="<?php bloginfo('name'); ?>" name="title"/><input type="hidden" name="loc" value="en_US"/> <input class="subscribesubmit" value="Sign up" type="submit">
                  </form>
                  </div>
              </div>
      
              <div class="TabbedPanelsContent">
                  <ul>
                  <?php popular_posts(); ?>
                  </ul>
              </div>
     		 
              <div class="TabbedPanelsContent">
                  <ul>
                  <?php get_archives('postbypost', '6', 'custom', '<li>', '</li>'); ?>
                  </ul>
              </div>
    </div>
  </div>