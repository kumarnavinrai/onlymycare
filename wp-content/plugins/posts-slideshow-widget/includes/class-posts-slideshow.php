<?php
/**
 * Posts Slideshow Widget plugin main class
 * 
 * @author Vladimir Garagulya	
 * @package posts_slideshow_widget 
 * 
 */

class Posts_Slideshow {
	
	// common code staff, including options data processor
  private $lib = null;
	
	/**
	 * class constructor
	 * 
	 */
	function __construct($library) {
    
    // activation action
    register_activation_hook(POSTS_SLIDESHOW_WIDGET_PLUGIN_FILE, array(&$this, 'setup'));
    
    // deactivation action
    register_deactivation_hook(POSTS_SLIDESHOW_WIDGET_PLUGIN_FILE, array(&$this, 'cleanup'));
    
    /* Add the translation function after the plugins loaded hook. */
    add_action('plugins_loaded', array(&$this, 'load_translation'));
        
    $this->lib = $library;
    
    // update post image url 
    add_action('publish_post', array(&$this->lib, 'get_post_image'));
    
    add_action('admin_init', array(&$this, 'init'));
    
    // add own menu item to the 'Settings' submenu 
    add_action('admin_menu',  array(&$this, 'create_menu'));  
    
    // register own widget
    add_action( 'widgets_init', array(&$this, 'register_widget') );
    
    add_action( 'wp_enqueue_scripts', array(&$this, 'add_frontend_scripts_and_styles') );
    add_action( 'admin_enqueue_scripts', array(&$this, 'add_admin_scripts' ) );
		
	}
	// end of __construct()

	
  public function load_translation() {
    
    load_plugin_textdomain('posts_slideshow_widget','', dirname( plugin_basename( POSTS_SLIDESHOW_WIDGET_PLUGIN_FILE ) ) . DIRECTORY_SEPARATOR .'lang');
    
  }
  // end of load_translation
  
  
	/**
	 * Plugin initialization
	 * 
	 */
	public function init() {
		
    // add a Settings link in the installed plugins page
    add_filter('plugin_action_links', array(&$this, 'plugin_action_links'), 10, 2);
		
	}
	// end of init()

	
	public function plugin_action_links($links, $file) {
		
		$plugin = plugin_basename( POSTS_SLIDESHOW_WIDGET_PLUGIN_DIR . DIRECTORY_SEPARATOR .'posts-slideshow-widget.php' );
		if ( $file == $plugin ) {
        $settings_link = "<a href='options-general.php?page=posts-slideshow-widget.php'>".__('Settings','posts_slideshow_widget')."</a>";
        array_unshift( $links, $settings_link );
    }
		
		return $links;		
	}
	// end of plugin_action_links()
	
	
	public function plugin_row_meta($links, $file) {
		
		return $links;
		
	}
	// end of plugin_row_meta()
	
	
	public function create_menu() {
		if ( function_exists('add_menu_page') ) {        
			add_options_page(__( 'Posts Slideshow Widget Settings', 'posts_slideshow_widget' ), __( 'Posts Slideshow', 'posts_slideshow_widget' ), 'manage_options', 'posts-slideshow-widget.php', array( &$this, 'settings' ));    
		}
	}
	// end of create_menu()
	
	
	function settings() {
    if (isset($_POST['posts_slideshow_widget_update'])) {  // process update from the options form
      $nonce=$_REQUEST['_wpnonce'];
      if (!wp_verify_nonce($nonce, 'posts_slideshow_widget') ) {
        die("Security check");
      }
      
      $speed = $this->lib->get_request_var('speed', 'post', 'int');
      $this->lib->put_option('speed', $speed);
      
      $max_quant = $this->lib->get_request_var('max_quant', 'post', 'int');
      if ($max_quant<10) {
        $max_quant = 10;
      }
      $this->lib->put_option('max_quant', $max_quant);
      
      $default_quant = $this->lib->get_request_var('default_quant', 'post', 'int');
      if ($default_quant<0) {
        $default_quant = 3;
      } else if ($default_quant>$max_quant) {
        $default_quant = $max_quant;
      }
      $this->lib->put_option('default_quant', $default_quant);
      // image width
      $width = $this->lib->get_request_var('width', 'post', 'int');
      $this->lib->put_option('width', $width);
      // image height
      $height = $this->lib->get_request_var('height', 'post', 'int');
      $this->lib->put_option('height', $height);
      // 1 - to show post title
      $post_title = $this->lib->get_request_var('post_title', 'post', 'checkbox');
      $this->lib->put_option('posts_title', $post_title);
      
      $post_title_background_color = $this->lib->get_request_var('post_title_background_color', 'post');
      $this->lib->put_option('post_title_background_color', $post_title_background_color);
      
      $post_title_color = $this->lib->get_request_var('post_title_color', 'post');
      $this->lib->put_option('post_title_color', $post_title_color);
      // navigation bar at the bottom background color
      $nav_bar_color = $this->lib->get_request_var('nav_bar_color', 'post');
      $this->lib->put_option('nav_bar_color', $nav_bar_color);
      
      $this->lib->flush_options();
      $this->lib->show_messages( __('Settings saved', 'posts_slideshow_widget') );
      
    } else { // get options from the options storage
      $speed = $this->lib->get_option('speed', 5);
      $max_quant = $this->lib->get_option('max_quant', 10);
      $default_quant = $this->lib->get_option('default_quant', 3);
      $width = $this->lib->get_option('width', 300);
      $height = $this->lib->get_option('height', 300);
      $post_title = $this->lib->get_option('post_title', 1);
      $post_title_background_color = $this->lib->get_option('post_title_background_color', '#CCCCCC');
      $post_title_color = $this->lib->get_option('post_title_color', '#FFFFFF');
      $nav_bar_color = $this->lib->get_option('nav_bar_color', '#CCCCCC');
    }
      
    if ((int) $post_title === 1) {
      $post_title_checked = 'checked="checked"';
    } else {
      $post_title_checked = '';
    }

    require_once(POSTS_SLIDESHOW_WIDGET_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'includes'. DIRECTORY_SEPARATOR . 'settings.php');
		
	}
	// end of settings()
	
	
	// execute on plugin activation
  public function setup() {
    global $wp_version;

    if (version_compare($wp_version, "3.0", "<")) {
      if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {
        require_once ABSPATH . '/wp-admin/includes/plugin.php';
        deactivate_plugins(__FILE__);
        $exit_msg = __(' requires WordPress 3.0 or newer.', 'edit-post-expire') . ' <a href="http://codex.wordpress.org/Upgrading_WordPress">' . __('Please update!', 'ure') . '</a>';
        wp_die($exit_msg);
      } else {
        return;
      }
    }
    
    // save default values for plugin settings
		$this->lib->put_option('speed', 7);
		$this->lib->put_option('max_quant', 10);
		$this->lib->put_option('default_quant', 3);
    $this->lib->put_option('width', 250);
    $this->lib->put_option('height', 250);
    $this->lib->put_option('post_title', 1);
    $this->lib->flush_options();
    
  }  // end of setup()
  

  // execute on plugin deactivation    
	public function cleanup() {
		
	}
	// end of cleanup()
  
  
  public function register_widget() {
    
    register_widget( 'posts_slideshow_widget' );
    
  }
  // end of register_widget()
	
  
  public function add_frontend_scripts_and_styles() {
    
    	if ( !is_admin() ){
        wp_enqueue_script( 'jquery' );
        
        $flex_slider_path = 'js'. DIRECTORY_SEPARATOR .'flexslider'. DIRECTORY_SEPARATOR;
        $src = plugins_url( $flex_slider_path .'jquery.flexslider-min.js', POSTS_SLIDESHOW_WIDGET_PLUGIN_FILE );
        wp_register_script( 'flexslider', $src );
        wp_enqueue_script( 'flexslider' );
        
        $src = plugins_url($flex_slider_path .'flexslider.css', POSTS_SLIDESHOW_WIDGET_PLUGIN_FILE);
        wp_register_style( 'posts_slideshow-style-flexslider', $src );
        wp_enqueue_style( 'posts_slideshow-style-flexslider' );
        
        $src = plugins_url('css'. DIRECTORY_SEPARATOR .'posts-slideshow-widget.css', POSTS_SLIDESHOW_WIDGET_PLUGIN_FILE);
        wp_register_style( 'posts_slideshow-style-custom', $src );
        wp_enqueue_style( 'posts_slideshow-style-custom' );        
        
    	}

  }
  // end of add_frontend_scripts_and_styles()
  
  /**
   * Add related javascript libraries if we at Appearance->Widgets page at admin back-end.
   * @param type $hook_suffix
   */
  public function add_admin_scripts($hook_suffix) {
    
// first check that $hook_suffix is appropriate for your admin page
    if ($hook_suffix!=='widgets.php' && $hook_suffix!=='settings_page_posts-slideshow-widget') {
      return;
    }
    
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' ); 
  
  }
  // end of add_admin_scripts()
  
}  // end of class Edit_Post_Expire

?>