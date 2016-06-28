<?php

if ( ! class_exists( 'Facebook' ) )
	require_once( dirname( __FILE__ ) . '/facebook.php' );

/**
 * Override default Facebook PHP SDK behaviors with WordPress-friendly features
 *
 * @since 1.0
 */
class FacebookConnect_WP extends Facebook {
	/**
	 * Override Facebook PHP SDK cURL function with WP_HTTP
	 * Facebook PHP SDK is POST-only
	 *
	 * @since 1.0
	 * @todo add file upload support if we care
	 * @param string $url request URL
	 * @param array $params parameters used in the POST body
	 * @param CurlHandler $ch Initialized curl handle. unused: here for compatibility with parent method parameters only
	 * @return string HTTP response body
	 */
	protected function makeRequest( $url, $params, $ch=null ) {
		global $wp_version;

		if ( empty( $url ) || empty( $params ) )
			throw new FacebookApiException( array( 'error_code' => 400, 'error' => array( 'type' => 'makeRequest', 'message' => 'Invalid parameters and/or URI passed to makeRequest' ) ) );

		$params = array(
			'redirection' => 0,
			'httpversion' => '1.1',
			'timeout' => 60,
			'user-agent' => apply_filters( 'http_headers_useragent', 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ) . '; facebook-php-' . self::VERSION . '-wp' ),
			'headers' => array( 'Expect:' ),
			'sslverify' => false, // warning: might be overridden by 'https_ssl_verify' filter
			'body' => http_build_query( $params, '', '&' )
		);

		$response = wp_remote_post( $url, $params );
		if ( is_wp_error( $response ) )
			throw new FacebookApiException( array( 'error_code' => $response->get_error_code(), 'error_msg' => $response->get_error_message() ) );
		else if ( wp_remote_retrieve_response_code( $response ) != '200' )
			throw new FacebookApiException( array( 'error_code' => wp_remote_retrieve_response_code( $response ), 'error' => array( 'type' => 'WP_HTTP', 'message' => 'HTTP Status not OK' ) ) );

		return wp_remote_retrieve_body( $response );
	}

	public function get_usertokentype(){
		$token = $this->getAccessToken();
		return $this->usertoken;
	}
  /**
   * Provides the implementations of the inherited abstract
   * methods.  The implementation uses user meta to maintain
   * a store for authorization codes, user ids, CSRF states, and
   * access tokens.
   */
  protected function setPersistentData($key, $value){
    
	    if (!in_array($key, self::$kSupportedKeys)) {
	      self::errorLog('Unsupported key passed to setPersistentData.');
	      return;   
	    }
		
		$user_id = get_current_user_id();
		$override = apply_filters( 'fb_setPersistentData', false, $user_id, $meta_key, $meta_value, $prev_value );
		if ( false !== $override )
		return $override;

		return update_user_meta( $user_id, $meta_key, $meta_value, $prev_value );
	}

  protected function getPersistentData($key, $default = false){
    
    if (!in_array($key, self::$kSupportedKeys)) {
      self::errorLog('Unsupported key passed to getPersistentData.');
      return $default;
    }
	
	  $user_id = get_current_user_id();
	  $override = apply_filters( 'fb_getPersistentData', false, $user_id, $meta_key, $single );
	  if ( false !== $override )
			return $override;
	
	  return get_user_meta( $user_id, $meta_key, $single );
	}

  protected function clearPersistentData($key) {
    if (!in_array($key, self::$kSupportedKeys)) {
      self::errorLog('Unsupported key passed to clearPersistentData.');
      return;
    }

    $user_id = get_current_user_id();
	$override = apply_filters( 'fb_clearPersistentData', false, $user_id, $meta_key, $meta_value );
	if ( false !== $override )
		return $override;

	return delete_user_meta( $user_id, $meta_key, $meta_value );
  }

  protected function clearAllPersistentData() {
    foreach (self::$kSupportedKeys as $key) {
      $this->clearPersistentData($key);
    }
  }
}
?>