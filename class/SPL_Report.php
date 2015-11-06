<?php

class SPL_Report {

	var $config;
	var $params;
	var $output;

	var $apikey;
	
	var $summary = null;

	function __construct($config=null, $params=null) {
		
		$this->apikey = getenv('SPL_KEY');

		$this->config = $config
		$this->params = $params;

		$this->getReport();

	}

	protected function getReport() {
		
		
		$path = plugin_dir_path( __FILE__ );
		include $path .'SPL_034_Branch_Unique_items.php';
		$report = new SPL_034_Branch_Unique_items();
		
		$this->output = print_r($this->config, true);
		//$this->output = $report->data;
		
	} 

	protected function loadJs() {
		wp_enqueue_script( get_class(), plugins_url('js/'.get_class($this).'.js', dirname(__FILE__)) );
	}

	protected function getHtml() {
		$html = file_get_contents( dirname(__DIR__).'/html/'.get_class($this).'.html' );
		return $html;
	}

	public function output() {
		return $this->output;
	}

	public static function curlProxy($url, $params, $method='post', $auth=null) {
	  $result = new stdClass();
	  $result->response = false;

	  // create a new cURL resource
	  $ch = curl_init();

	  if ( 'post' == $method ) {
	    // setup for an http post
	    curl_setopt($ch, CURLOPT_POST, 1);
	    // 'cause cURL doesn't like multi-dimensional arrays
	    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
	  } elseif ( 'get' == $method ) {
	    if ( is_array($params) ) {
	      $url .= '?' . http_build_query($params);
	    }
	  } elseif ( 'delete' == $method ) {
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	  } elseif ( 'put' == $method ) {
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
	  }


	  // set URL and other appropriate options
	  curl_setopt($ch, CURLOPT_URL, $url);
	  //curl_setopt($ch, CURLOPT_HEADER, false);

	  // follow redirects
	  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	  // set auth params
	  if ( is_array($auth) ) {
	    //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);  
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); // CURLAUTH_ANY
	    curl_setopt($ch, CURLOPT_USERPWD, $auth['user'] . ':' . $auth['pass']);
	  }

	  // set returntransfer to true to prevent browser echo
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	  $ua = $_SERVER['HTTP_USER_AGENT']; // optional
	  if (isset($ua)) {
	      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
	  }

	  // grab URL
	  $result->response = curl_exec($ch);

	  // grab http response code
	  $result->httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	   
	  // close cURL resource, and free up system resources
	  curl_close($ch);

	  return $result;
	}

}





?>