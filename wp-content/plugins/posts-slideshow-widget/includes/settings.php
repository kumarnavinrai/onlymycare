<?php
/*
 * Posts Slideshow Widget WordPress plugin global options page
 *
 * @Author: Vladimir Garagulya
 * @package posts-slideshow-widget
 *
 */

?>
<div class="wrap">
  <div class="icon32" id="icon-options-general"><br/></div>
  <h2><?php _e('Posts Slideshow Widget - Settings', 'posts_slideshow_widget'); ?></h2>
  <hr/>
  
  <form method="post" action="options-general.php?page=posts-slideshow-widget.php" >
  
    <table>
      <tr>
        <td><label for="speed"><?php _e('Slideshow Speed:', 'posts_slideshow_widget'); ?></label></td>
        <td><input type="text" name="speed" id="speed" value="<?php echo esc_attr($speed); ?>" size="4"/> <?php _e( 'seconds', 'posts_slideshow_widget' ); ?></td>
      </tr>
      <tr>
        <td><label for="max_quant"><?php _e('Max quant of images:', 'posts_slideshow_widget'); ?></label></td>
        <td><input type="text" name="max_quant" id="max_quant" value="<?php echo esc_attr($max_quant); ?>" size="4"/> </td>
      </tr>
      <tr>
        <td><label for="default_quant"><?php _e('Default quant of images:', 'posts_slideshow_widget'); ?></label></td>
        <td><input type="text" name="default_quant" id="max_quant" value="<?php echo esc_attr($default_quant); ?>" size="4"/> </td>
      </tr>
      <tr>
        <td><label for="width"><?php _e('Slider width:', 'posts_slideshow_widget'); ?></label></td>
        <td><input type="text" name="width" id="width" value="<?php echo esc_attr($width); ?>" size="4"/> </td>
      </tr>
      <tr>
        <td><label for="width"><?php _e('Slider height:', 'posts_slideshow_widget'); ?></label></td>
        <td><input type="text" name="height" id="height" value="<?php echo esc_attr($height); ?>" size="4"/> </td>
      </tr>
      <tr>
        <td colspan="2"><input type="checkbox" name="post_title" id="post_title" value="1" <?php echo $post_title_checked; ?> /> <label for="post_title"><?php _e('Show post title', 'posts_slideshow_widget'); ?></label> 
        </td>
      </tr>
      <tr>
        <td><label for="post_title_background_color"><?php _e('Post title backgound color:', 'posts_slideshow_widget'); ?></label></td>
        <td><input type="text" name="post_title_background_color" id="post_title_background_color" class="posts_slideshow-widget-color-field" value="<?php echo esc_attr($post_title_background_color); ?>" size="10"/> </td>
      </tr>
      <tr>
        <td><label for="post_title_color"><?php _e('Post title color:', 'posts_slideshow_widget'); ?></label></td>
        <td><input type="text" name="post_title_color" id="post_title_color" class="posts_slideshow-widget-color-field" value="<?php echo esc_attr($post_title_color); ?>" size="10"/> </td>
      </tr>
      <tr>
        <td><label for="nav_bar_color"><?php _e('Navigation bar backgound color:', 'posts_slideshow_widget'); ?></label></td>
        <td><input type="text" name="nav_bar_color" id="nav_bar_color" class="posts_slideshow-widget-color-field" value="<?php echo esc_attr($nav_bar_color); ?>" size="10"/> </td>
      </tr>
    </table>
    <?php wp_nonce_field('posts_slideshow_widget'); ?>   
    <p class="submit">
      <input type="submit" class="button-primary" name="posts_slideshow_widget_update" value="<?php _e('Update') ?>" />
    </p>  

  </form>  
  Credits by: <?php echo $this->lib->credits(); ?>
</div>

<script  type="text/javascript">
  
  jQuery(document).ready(function($){
    $('.posts_slideshow-widget-color-field').wpColorPicker();
  });

</script>
