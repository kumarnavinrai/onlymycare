<?php
/**
 * Posts Slideshow Widget plugin cache supporting class
 * 
 * @author Vladimir Garagulya	
 * @package posts_slideshow_widget 
 * 
 * file path processing code from timthumb library are used partially
 * 
 */

class Posts_Slideshow_Cache_It {
  private $lib = null;
  private $cache_dir = '';
  private $doc_root = '';
  private $debug = WP_DEBUG;
  private $thumb_prefix = '';
  
  
  function __construct($cache_dir = '', $lib = null, $thumb_prefix = '') {

    if ($lib !== null) {
      $this->lib = $lib;
    }

    if (empty($thumb_prefix)) {
      $this->thumb_prefix = 'cached_';
    } else {
      $this->thumb_prefix = $thumb_prefix;
    }
    $this->check_cache_dir($cache_dir);
    $this->calc_doc_root();
    
  }

  
  /**
   * returns file name of main plugin file
   * 
   */
  private function get_plugin_main_file() {
    
    if (!empty($this->lib)) {
      return $this->lib->get_plugin_main_file();
    } else {
      return '';
    }
    
  }
  // end of get_plugin_main_file()
  
  
  /**
   * Output message using external library, if it's defined
   * @param string $message
   */
  private function show_message($message) {
   
    if ($this->lib!==null) {
      $message = __FILE__ .':<br /> '. $message;
      $this->lib->show_debug_message($message);
    }
    
  }
  // end of show_message()
  
  
  /**
   * Calculate document root for current site
   * 
   */
  private function calc_doc_root() {
    $doc_root = @$_SERVER['DOCUMENT_ROOT'];
    if (!isset($doc_root)) {
      if (isset($_SERVER['SCRIPT_FILENAME'])) {
        $doc_root = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
      }
    }
    if (!isset($doc_root)) {
      if (isset($_SERVER['PATH_TRANSLATED'])) {
        $doc_root = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
      }
    }
    if ($doc_root && $_SERVER['DOCUMENT_ROOT'] != '/') {
      $doc_root = preg_replace('/\/$/', '', $doc_root);
    }
    $this->doc_root = $doc_root;
  }
  // end of calc_doc_root()

  
  /**
   * 
   * @param string $path
   * @return string
   */
  private function real_path( $path ) {
		
    //try to remove any relative paths
		$remove_relatives = '/\w+\/\.\.\//';
		while( preg_match($remove_relatives,$path) ) {
		  $path = preg_replace( $remove_relatives, '', $path );
		}
		//if any remain use PHP realpath to strip them out, otherwise return $path
		//if using realpath, any symlinks will also be resolved
		return preg_match('#^\.\./|/\.\./#', $path) ? realpath($path) : $path;
    
	}
  // end of real_path()
  
  
  /**
   * Returns image local path built on the base of image URL and document root
   * 
   * @param string $src - image URL
   * @return string|boolean - false in case of failure, image local path - in case of success
   * 
   */
  private function get_image_local_path( $src ) {
		
    if( !$this->doc_root ) {
      // doc root should be defined
      $this->show_message('Could not define Server Document Root. Request for file processing is stopped.');
      return '';
    }
    
    $src = ltrim($src, '/'); //strip off the leading '/'

		//Try src under docRoot
		if (file_exists($this->doc_root . '/' . $src)) {
      $real = $this->real_path($this->doc_root . '/' . $src);
      if (stripos($real, $this->doc_root) === 0) {
        return $real;
      }
    }
    
		//Check absolute paths and then verify the real path is under doc root
		$absolute = $this->real_path('/' . $src);
    if ($absolute && file_exists($absolute)) {
      if (stripos($absolute, $this->doc_root) === 0) {
        return $absolute;
      }
    }
		
		$base = $this->doc_root;
		
		// account for Windows directory structure
		if (strstr($_SERVER['SCRIPT_FILENAME'], ':')) {
      $sub_directories = explode('\\', str_replace($this->doc_root, '', $_SERVER['SCRIPT_FILENAME']));
    } else {
      $sub_directories = explode('/', str_replace($this->doc_root, '', $_SERVER['SCRIPT_FILENAME']));
    }

    foreach ($sub_directories as $sub) {
      $base .= $sub . '/';

      if (file_exists($base . $src)) {
        $real = $this->real_path($base . $src);
        if (stripos($real, $this->real_path($this->doc_root)) === 0) {
          return $real;
        }
      }
    }
    
    return false;
  }
  // end of get_local_image_path()

  
  /**
   * Check if local cache directory is available and writable
   * @param string $cache_dir - path to plugin local cache directory
   * 
   */
  private function check_cache_dir($cache_dir) {

    if (empty($cache_dir)) {
      $cache_dir = '.'. DIRECTORY_SEPARATOR . 'cache';
    }

    if (!is_dir($cache_dir)) {
      @mkdir($cache_dir);
      if (!is_dir($cache_dir)) {
        $this->show_message('Could not create cache directory ' . $cache_dir . ' Caching is switched off.');
        return false;
      }

      if (!@touch($this->cache_dir . '/index.html')) {
        $this->show_message('Cache directory is not writable' . ' ' . $cache_dir . ' Caching is switched off.');
        return false;
      }
    }

    $this->cache_dir = $cache_dir;
    
  }
  // end of check_cache_dir()
  
  
  /**
   * Clear widget image cache for post with ID=$post_id
   * @param integer $post_id - post ID
   */
  public function remove_image($post_id) {
    
    if (empty ($this->cache_dir)) { // cache is switched off
      return false;
    }
    
    $file_name_template = $this->thumb_prefix . $post_id;
    $this->scan_dir( $this->cache_dir, $file_name_template, array($this, 'remove_file') );
    
  }
  // end of remove_image()
  
  
  /**
   * returns full URL for cached file, by its file name
   * 
   * @param string $file_name - base file name
   * @return string - file URL
   * 
   */
  private function cached_file_url($file_name) {
    
    if (strpos($this->cache_dir, POSTS_SLIDESHOW_WIDGET_PLUGIN_DIR) !== false) {
      $relative_path = substr($this->cache_dir, strlen(POSTS_SLIDESHOW_WIDGET_PLUGIN_DIR));
    } else {
      $relative_path = $this->cache_dir;
    }

    $image_url = plugins_url($relative_path . DIRECTORY_SEPARATOR . $file_name, POSTS_SLIDESHOW_WIDGET_PLUGIN_FILE);
    return $image_url;
    
  }
  // end of cached_file_url()

  
  /**
   * Reduce image and place it to cache directory. If reduced image exists already, then return its URL
   * In case of any error of imposibility to reduce and cache image the original image URL is returned
   * 
   * @param integer $post_id - post ID
   * @param string $image_url - image URL
   * @param integer $width  - reduced image width
   * @param integer $height - reduced image height
   * @return string - image URL
   */
  public function get_image($post_id, $image_url, $width, $height) {
    if ($width===0 || $height===0 || empty($this->cache_dir) ) {
      // no new size - is nothing to do
      return $image_url;
    }
        
    $my_host = strtolower( preg_replace('/^www\./i', '', $_SERVER['HTTP_HOST']) );
		$url_parts = parse_url($image_url);
    if(strlen($url_parts['path']) <= 3){
			$this->show_message('Wrong image URL');
			return $image_url;
		}
    
    $url_parts['host'] = strtolower( preg_replace('/^www\./i', '', $url_parts['host'] ) );				    
    if ( $url_parts['host'] !== $my_host ) {
       $this->show_message("It's a request for an external URL: " . $image_url .' - do not caching!');
       return $image_url;
    }
    
    $path_parts = pathinfo($url_parts['path']);    
    $thumb_file_name = $this->thumb_prefix . $post_id .'_'. $width .'_'. $height .'.'. $path_parts['extension'];
    
    // if file exists in cache, return URL to it 
    if (file_exists($this->cache_dir .DIRECTORY_SEPARATOR. $thumb_file_name)) {
      return $this->cached_file_url($thumb_file_name);
    }
        
    // Image should be cached.     
    $local_path = $this->get_image_local_path($url_parts['path']);
    if (empty($local_path)) {
      return $image_url;  // stop caching
    }
                
    $image_data = getimagesize($local_path);
		$orig_type = $image_data[2];
		$mime_type = $image_data['mime'];

		if (!preg_match('/^image\/(?:gif|jpg|jpeg|png)$/i', $mime_type)) {
      $this->show_message('Image is not a valid gif, jpg or png.');
      return '';
    }
    
    if ( $width > 0 && $height > 0 && is_array($image_data) ) {
      if ($image_data[0] <= $width && $image_data[1] <= $height) {
        $img_file = $local_path;
        copy($local_path, $this->cache_dir .DIRECTORY_SEPARATOR. $thumb_file_name);
      } else {
        $image = wp_get_image_editor( $local_path ); 
        if ( !is_wp_error( $image ) ) {
          $image->resize( $width, $height, true );
          $image->save($this->cache_dir .DIRECTORY_SEPARATOR. $thumb_file_name);
          return $this->cached_file_url($thumb_file_name);
        }
      }
    } else {
      return $image_url;
    }
  }
  // end of get_image()

  /**
   * 
   * @param string $file_name
   * 
   */
  public function remove_file($file_name)  {
    
    unlink($file_name);
    
  }
  // end of remove_file()
  

/**
 *  Recursive scan of folder for files matching search key with execution of action against every match
 * 
 * @param string $path
 * @param type $recursive
 * @param type $search_key
 * @param function $action reference to PHP function to call in order fulfill needed action
 * 
 */  
private function scan_dir( $path, $search_key, $action_callback ) {

  if (!is_callable($action_callback)) {
    $this->show_message('scan_dir(): Invalid callback - '. print_r($action_callback, true) );
    return;
  }
  $dir = @opendir($path);
	if ($dir) {
		while($file_name = readdir($dir)) {
			if ($file_name == '.' || $file_name == '..') {
        continue;
      }
			$file_name = $path . '/' . $file_name;
			if ( is_dir($file_name) )  {
				continue;
			}
			if (is_file($file_name)) {        
        $path_parts = pathinfo($file_name);
        if (strpos($path_parts['basename'], $search_key)===0) { // file name beginning from search key is found          
            call_user_func($action_callback, $file_name);
        }
			}
		}
		closedir($dir);
 	}
}
// end of scan_dir()  


}
// end of class Posts_Slideshow_Cache_It

?>
