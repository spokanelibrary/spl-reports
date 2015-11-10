<?php

class SPL_Report {

	var $params;
	var $config;
	var $apikey;
	var $output;
	var $endpoint = 'https://app.spokanelibrary.org/v3/spl-reports/';

	function __construct($params=null, $config=null) {
		$this->params = $params;
		$this->config = $config;
		$this->apikey = getenv('SPL_KEY');
	}

	public function getReport() {
		//$html = $this->getReportNavbar();
		if ( empty($this->params['id']) ) {
			$html = $this->getReportNavbar();
			$html .= $this->getReportMenu();
			$this->output = $html;
		} else {
			$class = $this->getReportClass();
			if ( is_object($class) && $class->path ) {
				include $class->path;
				$report = new $class->name($this->params, $this->config);
				if ( $this->params['ajax'] ) {
					if ( wp_verify_nonce( $_REQUEST['nonce'], 'spl-report-nonce-'.$this->params['id'] ) ) {
						$this->output = $report->processData($report->getReportData());
					}
				} else {
					$report->loadJs();
					$html .= '<div class="spl-report"
										data-spl-report-nonce="'.wp_create_nonce( 'spl-report-nonce-'.$this->params['id'] ).'"
										data-spl-report-id="'.$this->params['id'] .'">'.PHP_EOL;
					$html .= $report->getTmpl();	
					$html .= PHP_EOL.'</div>'.PHP_EOL;

					$this->output = $html;
				}
			} else {
				$html .= $this->getReportError('Report not found');
				$this->output = $html;
			}	
		}
	} 

	protected function getReportMenu() {
		$db = $this->getReportDB();
		$html = null;

		$html .= '<pre>'.print_r($db, true).'</pre>';

		$html .= '
		<h3>Reports Dashboard</h3>
		<div class="row">
			<div class="col-md-3">
			Stats Dashboard
			</div>
			<div class="col-md-3">
			Updated Reports
			</div>
			<div class="col-md-3">
			Apps and Tools
			</div>
			<div class="col-md-3">
			Other Reporting Sources
			</div>
		</div>
		
		<h3>Old (Intra) reports</h3>
		<div class="row">
			<div class="col-md-3">
			...
			</div>
			<div class="col-md-3">
			...
			</div>
			<div class="col-md-3">
			...
			</div>
			<div class="col-md-3">
			...
			</div>
		</div>
		';

		return $html;
	}

	protected function getReportNavbar() {
		$db = $this->getReportDB();
		$html = null;

		//$html .= 'navbar';

		return $html;
	}

	private function getReportDB() {
		$db = new stdClass();

		$apps  new stdClass();
		$dash = new stdClass();
		$intra = new stdClass();
		$other = new stdClass();
		$reports = new stdClass();

		$db->apps = $apps;
		$db->dash = $dash;
		$db->intra = $intra;
		$db->other = $other;
		$db->reports = $reports;

		return $db;
	}

	protected function getReportError( $msg='Unknown error' ) {
		if ( $this->params['ajax'] ) {
			$error = array('Error'=>$msg);
		} else {
			$error = null;
			$error .= '<div class="alert alert-danger">'.PHP_EOL;
			$error .= '<b>Error:</b> '.$msg.PHP_EOL;
			$error .= '</div>'.PHP_EOL;
		}
		
		return $error;
	}

	protected function getReportData() {
		$params['apikey'] = $this->apikey;
		$params['params'] = $this->params;
		if ( !isset( $this->api ) ) {
			$this->api = $this->params['id'];
		}
		$data = $this->curlProxy($this->endpoint.$this->api
													, $params);

		return json_decode($this->processData($data->response));
	}

	protected function processData($data=null) {
		return $data;
	}

	protected function getReportClass() {
		$class = new stdClass();
		$files = scandir( plugin_dir_path( __FILE__ ) );
    foreach ($files as $file) {
      // ignore directories and hidden files
      if(0 !== stripos($file, '.')) {
        //$class->scan = $this->params['id'];
        if (substr_count($file, $this->params['id'])) {
          $class->path = plugin_dir_path( __FILE__ ).$file;
          // trim off file extension
          $class->name = stristr($file, '.', true);
        }
      }
    }

		return $class;
	}

	protected function loadJs() {
		wp_enqueue_script( 'spl-reports-config', plugins_url('js/SPL_Reports.js', dirname(__FILE__)) );
		wp_enqueue_script( 'spl-reports-dynatable', plugins_url('js/jquery.dynatable.js', dirname(__FILE__)) );
		wp_enqueue_style( 'spl-reports-dynatable-css', plugins_url('css/jquery.dynatable.css', dirname(__FILE__)) );
		wp_enqueue_script( get_class(), plugins_url('js/'.get_class($this).'.js', dirname(__FILE__)) );
	}

	protected function getTmpl() {
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