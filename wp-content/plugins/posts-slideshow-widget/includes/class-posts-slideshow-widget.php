<?php
/**
 * Posts Slideshow Widget plugin widget class
 * 
 * @author Vladimir Garagulya	
 * @package posts_slideshow_widget 
 * 
 */

class Posts_Slideshow_Widget extends WP_Widget {
	
  private $lib = null;
  private $animation_available = array('slide', 'fade');
  static private $widget_number = 0;
  
  /**
	 * Register widget with WordPress.
	 */
	public function __construct() {
    
    global $psw_library;        
    
		parent::__construct(
	 		'posts_slideshow_widget', // Base ID
			'Posts Slideshow', // Name
			array( 'description' => __( 'Recent posts featured images slideshow', 'posts_slideshow_widget'), 'classname'=>'Posts_Slideshow_Widget'), 
      array('width' => 350, 'height' => 350)  // Args
		);
    
    $this->lib = $psw_library;    
    
	}

  /**
   * find taxonomy for selected post type
   * 
   * @param type $post_type 
   * 
   */
  private function get_post_type_taxonomy($post_type) {

    $taxonomy = new stdClass();
    if ($post_type === 'posts') {
      $taxonomy->name = 'category';
      $taxonomy->label = _e('Categories', 'posts_slideshow_widget');
    } else {
      foreach (get_taxonomies(array(), 'objects') as $tax) {
        if (in_array($post_type, (array) $tax->object_type, true) && $tax->hierarchical) {
          $taxonomy->name = $tax->name;
          $taxonomy->label = $tax->label;
          break;
        }
      }
    }

    return $taxonomy;
  }
  // end of get_post_type_taxonomy()
  
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
    
// initialize widget parameters    
    $post_type = $instance['post_type'];
    $category = $instance['category'];
    $tag = $instance['tag'];
    $animation = $instance['animation'];
    $speed = 1000 * $instance['speed'];
    $quant = $instance['quant'];
    $width = $instance['width'];
    $height = $instance['height'];
    $show_post_titles = $instance['show_post_titles'];
    $post_title_background_color = $instance['post_title_background_color'];
    $post_title_color = $instance['post_title_color'];
    $nav_bar_color = $instance['nav_bar_color'];
    
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
	
// select posts for slideshow    
    $args = array('numberposts'=>$quant);
    $args['post_status'] = 'publish';
    $args['post_type'] = $post_type;
    if ($category) {
      $taxonomy = $this->get_post_type_taxonomy($post_type);
      $tax_query = array(
                    array(
                      'taxonomy' => $taxonomy->name,
                      'field' => 'term_taxonomy_id',
                      'terms' => $category
                          ));
      $args['tax_query'] = $tax_query;
    }
    if ($tag) {
      $args['tag'] = $tag;
    }
    
    $posts = get_posts($args);
    $this->widget_number++;
    $slideshow_div_id = 'posts_slideshow_'.$this->widget_number;
    
?>
<div style="width:<?php echo $width; ?>px;">
      <section class="slider">
        <div id="<?php echo $slideshow_div_id; ?>" class="flexslider" >
<?php 
  if (count($posts)) {    
?>
          <ul class="slides">
<?php
  $first_flag = true;
  foreach ( $posts as $post ) {
    $permalink = get_permalink( $post->ID );
    if ($first_flag) {
      $image_url = $this->lib->get_post_image($post, false, $width, $height);     
    } else {
      $image_url = plugins_url( 'images'.DIRECTORY_SEPARATOR.'place-holder.png', POSTS_SLIDESHOW_WIDGET_PLUGIN_FILE );
    }
?>
            <li>
<?php
      if ($show_post_titles) {
?>
              <div class="flex-slide-container">        
                <div class="flex-caption" style="background-color: <?php echo $post_title_background_color; ?>;color: <?php echo $post_title_color; ?>;">
                  <div><?php echo $post->post_title;?></div>
                </div>
<?php
      }
?>
                <a href="<?php echo $permalink;?>" title="<?php echo $post->post_title;?>">
                  <img src="<?php echo $image_url; ?>" alt="<?php echo $post->post_title;?>" style="border:none;box-shadow:none;border-radius:0;"/>
                </a>
              </div>
            </li>            
<?php
  }
?>
          </ul>
<?php
  } else {
    _e('No posts found. Please change selection criteria.','posts_slideshow_widget');
  } // if (count($post))
?>
          <div id="<?php echo $slideshow_div_id.'_control_nav'; ?>" class="flex-control-nav flex-control-paging" style="background-color:<?php echo $nav_bar_color;?>; z-index:100;">
            <ul>
<?php
    for ($i=0; $i<count($posts); $i++) {
       echo '<li><a>'.$i.'</a></li>';
    }
?>
            </ul>            
          </div>
        </div>
      </section>
</div>
<?php 
  if (count($posts)) {    
?>
<script type="text/javascript">
	$jq = jQuery.noConflict();
	$jq(window).ready(function() {
  
     $jq('<?php echo '#'.$slideshow_div_id; ?>').flexslider({
        animation: '<?php echo $this->animation_available[$animation];?>',
        slideshowSpeed: <?php echo $speed; ?>,
        controlNav: true,
        manualControls: $jq('#<?php echo $slideshow_div_id.'_control_nav'; ?> li a'),
        slideshow: true,
        start: function(slider){
          $jq('body').removeClass('loading');
           var images = new Array();
<?php
// that's done for slider load speed increase. We define as static <li> just the 1st image. 
// Rest images are added to the slider just after the 1st one is shown.     
        for ($i=1; $i<count($posts); $i++) {    
          $real_image = $this->lib->get_post_image($post, false, $width, $height);
?>
          images[<?php echo ($i-1);?>] = '<?php echo $real_image;?>';
<?php
        }
?>
          // change src of img to the real images URL
          var i = 0;
          $jq("div#<?php echo $slideshow_div_id;?> img").each(function(i){
            var src = $jq(this).attr('src');
            if (src.indexOf('place-holder.png')!==-1) {
              $jq(this).attr('src', images[i]);
              i++;
            }
          });
          
        }  // start
      });    
    
  });
  
</script>  
<?php
  } // if (count($post))
		
    echo $after_widget;
	}  // end of widget()

  
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
    
    if (is_numeric($new_instance['animation']) && $new_instance['animation']>=0 && $new_instance['animation']<count($this->animation_available)) {
      $instance['animation'] = $new_instance['animation'];    
    } else {
      $instance['animation'] = 0;
    }
    
    $instance['post_type'] = strip_tags($new_instance['post_type']);    
    if (empty($instance['post_type'])) {
      $instance['post_type'] = 'post';
    }    
    
    if (is_numeric($new_instance['category'])) {
      $instance['category'] = $new_instance['category'];
    } else {
      $instance['category'] = 0;
    }    
         
    $instance['tag'] = strip_tags($new_instance['tag']);
    
    if (is_numeric($new_instance['speed']) && $new_instance['speed']>0) {
      $instance['speed'] = $new_instance['speed'];
    } else {
      $instance['speed'] = $this->lib->get_option('speed');
    }
    $max_quant = $this->lib->get_option('max_quant');
    if (is_numeric($new_instance['quant']) && $new_instance['quant']>0 && $new_instance['quant']<$max_quant) {
      $instance['quant'] = $new_instance['quant'];
    } else {
      $instance['quant'] = $max_quant;
    }
    if (is_numeric($new_instance['width']) && $new_instance['width']>0) {
      $instance['width'] = $new_instance['width'];
    } else {
      $instance['width'] = $this->lib->get_option('width', 250);
    }
    if (is_numeric($new_instance['height']) && $new_instance['height']>0) {
      $instance['height'] = $new_instance['height'];
    } else {
      $instance['height'] = $this->lib->get_option('height', 250);
    }
    if (is_numeric($new_instance['show_post_titles'])) {
      $instance['show_post_titles'] = $new_instance['show_post_titles'];
    } else {
      $instance['show_post_titles'] = 0;
    }

    $instance['post_title_background_color'] = $new_instance['post_title_background_color'];
    $instance['post_title_color'] = $new_instance['post_title_color'];
    $instance['nav_bar_color'] = $new_instance['nav_bar_color'];

		return $instance;
	}  // end of update()

  
	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'posts_slideshow_widget' );
		}
		
    if (isset($instance['animation'])) {
      $animation = (int) $instance['animation'];
    } else {
      $animation = 0;
    }

    if (isset($instance['post_type'])) {
      $post_type = $instance['post_type'];
    } else {
      $post_type = 'post';
    }
    $post_types = get_post_types( array('show_ui'=>true), 'names', 'and' );
    unset($post_types['page']); unset($post_types['attachment']);
        
    if (isset($instance['category'])) {
      $category = (int) $instance['category'];
    } else {
      $category = 0;
    }
    
    if (isset($instance['tag'])) {
      $tag = $instance['tag'];
    } else {
      $tag = '';
    }
    
    if ( isset( $instance[ 'speed' ] ) ) {
			$speed = (int) $instance[ 'speed' ];
		} else {
			$speed = $this->lib->get_option('speed');
		} 
		if ( isset( $instance[ 'quant' ] ) ) {
			$quant = (int) $instance[ 'quant' ];
		} else {
			$quant = (int) $this->lib->get_option('default_quant', 3);
		}
    
    if ( isset( $instance[ 'width' ] ) ) {
			$width = (int) $instance[ 'width' ];
		} else {
			$width = (int) $this->lib->get_option('width', 250);
		}
    
    if ( isset( $instance[ 'height' ] ) ) {
			$height = (int) $instance[ 'height' ];
		} else {
			$height = (int) $this->lib->get_option('height', 250);
		}
    
    $post_title = (int) $this->lib->get_option('post_title', 1);  // if 1, then show post title - default value
    if ( isset( $instance[ 'show_post_titles' ] ) ) {
			$show_post_titles = (int) $instance[ 'show_post_titles' ];
      if ($instance[ 'show_post_titles' ]) {
        $spt_checked = 'checked="checked"';
      } else {
        $spt_checked = '';
      }
		} else if ( $post_title ) {
      $show_post_titles = 1;
      $spt_checked = 'checked="checked"';        
    } else {
			$show_post_titles = 0;
      $spt_checked = '';
		}

    if ( isset( $instance['post_title_background_color'] ) ) {
      $post_title_background_color = $instance['post_title_background_color'];
    } else {
      $post_title_background_color = $this->lib->get_option('post_title_background_color', '#CCCCCC');
    }
    $post_title_background_color_id = $this->get_field_id( 'post_title_background_color' );
    
    if ( isset( $instance['post_title_color'] ) ) {
      $post_title_color = $instance['post_title_color'];
    } else {
      $post_title_color = $this->lib->get_option('post_title_color', '#FFFFFF');
    }
    $post_title_color_id = $this->get_field_id( 'post_title_color' );
    
    if ( isset( $instance['nav_bar_color'] ) ) {
      $nav_bar_color = $instance['nav_bar_color'];
    } else {
      $nav_bar_color = $this->lib->get_option('nav_bar_color', '#CCCCCC');
    }
    $nav_bar_color_id = $this->get_field_id( 'nav_bar_color' );
    
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
    <table>
<?php
  if (count($post_types)>1) {
?>
      <tr>
        <td><label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post Type:', 'posts_slideshow_widget' ); ?></label></td>
        <td>           
          <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" style="width:150px;" onchange="jQuery('<?php echo '#'.$this->get_field_id('savewidget'); ?>').trigger('click'); ">              
<?php
  foreach ($post_types as $post_type_name) {
    if ($post_type===$post_type_name) {
      $post_type_selected = 'selected="selected"';
    } else {
      $post_type_selected = '';
    }
?>    
            <option value="<?php echo $post_type_name; ?>" <?php echo $post_type_selected; ?> ><?php echo ucfirst($post_type_name); ?></option>
<?php      
  }
?>      
    </select>          
        </td>
      </tr>
<?php
  }
?>
      <tr>
        <td><label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category:', 'posts_slideshow_widget' ); ?></label> </td>
        <td>
<?php 
  $taxonomy = $this->get_post_type_taxonomy($post_type);
  $args = array('taxonomy' => $taxonomy->name,
                'name' => $this->get_field_name('category'), 
                'id'=> $this->get_field_id('category'), 
                'selected' => $category, 
                'orderby' => 'name' , 
                'hierarchical' => 1, 
                'show_option_all' => 'All '.$taxonomy->label, 
                'hide_empty' => '0');
          wp_dropdown_categories($args); 
?>
        </td>
      </tr>
      <tr>
        <td><label for="<?php echo $this->get_field_id( 'tag' ); ?>"><?php _e( 'Tags list (comma separated)', 'posts_slideshow_widget' ); ?>:</label></td>
        <td><input class="widefat" type="text" id="<?php echo $this->get_field_id( 'tag' ); ?>" name="<?php echo $this->get_field_name( 'tag' ); ?>" value="<?php echo $tag; ?>" /></td>
      </tr>
      <tr>
        <td><label for="<?php echo $this->get_field_id( 'animation' ); ?>"><?php _e( 'Animation:', 'posts_slideshow_widget' ); ?></label> </td>
        <td>
          <select id="<?php echo $this->get_field_id('animation'); ?>" name="<?php echo $this->get_field_name('animation'); ?>" >          
<?php
  foreach ($this->animation_available as $ind=>$animation_mode) {
    if ($ind===$animation) {
      $selected = 'selected="selected"';
    } else {
      $selected = '';
    }
?>    
            <option value="<?php echo $ind; ?>" <?php echo $selected; ?> ><?php echo ucfirst($animation_mode); ?></option>
<?php            
  }
?>                        
          </select>
        </td>
      </tr>      
      <tr>
        <td><label for="<?php echo $this->get_field_id( 'speed' ); ?>"><?php _e( 'Speed:', 'posts_slideshow_widget' ); ?></label> </td>
        <td><input class="widefat" id="<?php echo $this->get_field_id( 'speed' ); ?>" name="<?php echo $this->get_field_name( 'speed' ); ?>" type="text" value="<?php echo esc_attr( $speed ); ?>" /></td>
      </tr>
      <tr>
        <td><label for="<?php echo $this->get_field_id( 'quant' ); ?>"><?php _e( 'Quant of posts:', 'posts_slideshow_widget' ); ?></label> </td>
        <td><input class="widefat" id="<?php echo $this->get_field_id( 'quant' ); ?>" name="<?php echo $this->get_field_name( 'quant' ); ?>" type="text" value="<?php echo esc_attr( $quant ); ?>" /></td>
      </tr>
      <tr>
        <td><label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:', 'posts_slideshow_widget' ); ?></label> </td>
        <td><input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $width ); ?>" /></td>
      </tr>
      <tr>
        <td><label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:', 'posts_slideshow_widget' ); ?></label> </td>
        <td><input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo esc_attr( $height ); ?>" /></td>
      </tr>      
      <tr>
        <td><label for="<?php echo $this->get_field_id( 'show_post_titles' ); ?>"><?php _e( 'Show post titles:', 'posts_slideshow_widget' ); ?></label> </td>
        <td><input class="widefat" id="<?php echo $this->get_field_id( 'show_post_titles' ); ?>" name="<?php echo $this->get_field_name( 'show_post_titles' ); ?>" type="checkbox" value="1" <?php echo $spt_checked;?> /></td>
      </tr>
      <tr>
        <td colspan="2">
          <?php _e( 'Adjust colors:', 'posts_slideshow_widget' ); ?>
        </td>
      </tr>
      <tr>
        <td><label for="<?php echo $post_title_background_color_id; ?>"><?php _e( 'Post title background:', 'posts_slideshow_widget' ); ?></label> </td>
        <td><input class="widefat" id="<?php echo $post_title_background_color_id; ?>" name="<?php echo $this->get_field_name( 'post_title_background_color' ); ?>" type="text" value="<?php echo esc_attr( $post_title_background_color ); ?>" /></td>        
      </tr>
      <tr>
        <td><label for="<?php echo $post_title_color_id; ?>"><?php _e( 'Post title:', 'posts_slideshow_widget' ); ?></label> </td>
        <td><input class="posts_slideshow-widget-color-field" id="<?php echo $post_title_color_id; ?>" name="<?php echo $this->get_field_name( 'post_title_color' ); ?>" type="text" value="<?php echo esc_attr( $post_title_color ); ?>" /></td>        
      </tr>
      <tr>
        <td><label for="<?php echo $nav_bar_color_id; ?>"><?php _e( 'Navigation bar background:', 'posts_slideshow_widget' ); ?></label> </td>
        <td><input class="posts_slideshow-widget-color-field" id="<?php echo $nav_bar_color_id; ?>" name="<?php echo $this->get_field_name( 'nav_bar_color' ); ?>" type="text" value="<?php echo esc_attr( $nav_bar_color ); ?>" /></td>        
      </tr>
    </table>

    <script  type="text/javascript">
  
      jQuery(document).ready(function($){
            
        $('#<?php echo $post_title_background_color_id; ?>').wpColorPicker();
        $('#<?php echo $post_title_color_id; ?>').wpColorPicker();
        $('#<?php echo $nav_bar_color_id; ?>').wpColorPicker();

      });

    </script>
    
		<?php 
	}  // end of form() method
	
}  // end of class Posts_Slideshow_Widget

?>