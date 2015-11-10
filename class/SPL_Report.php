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
		$html = null;
		if ( empty($this->params['id']) ) {
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

	protected function getReportMenu($class='link') {
		$db = $this->getReportDB();
		$html = null;

		//$html .= '<pre>'.print_r($db, true).'</pre>';

		$html .= '
		<div class="panel spl-hero-intranet spl-hero-brand-blue-f">
			<div class="panel-heading">
				<h4 class="">Newer Reports</h4>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-6 col-md-3">
						<h4>Converted Reports</h4>
						'.$this->getReportList($db->reports, $class).'
					</div>
					<div class="col-sm-6 col-md-3">
						<h4>Dashboard Reports</h4>
						'.$this->getReportList($db->dash, $class).'
					</div>
					<div class="clearfix visible-sm"></div>
					<div class="col-sm-6 col-md-3">
						<h4>Apps and Tools</h4>
						'.$this->getReportList($db->apps, $class).'
					</div>
					<div class="col-sm-6 col-md-3">
						<h4>Other Reports</h4>
					</div>
				</div>
			</div>
		</div>

		<div class="panel spl-hero-intranet spl-hero-brand-gray-b">
			<div class="panel-heading">
				<h4>Older reports (intra)</h4>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-6 col-md-3">
					...
					</div>
					<div class="col-sm-6 col-md-3">
					...
					</div>
					<div class="clearfix visible-sm"></div>
					<div class="col-sm-6 col-md-3">
					...
					</div>
					<div class="col-sm-6 col-md-3">
					...
					</div>
				</div>
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

	protected function getReportList($list, $class='link') {
		$html .= null;
		if ( $list ) {
			foreach ( $list as $menu ) {
				$html .= '<p>';
				$html .= '<div class="btn-group">';
				$html .= '<button type="button" class="btn btn-lg btn-'.$class.' dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
				$html .= $menu->label.' <span class="caret"></span></button>';
				if ( is_array($menu->list) ) {
					$html .= '<ul class="dropdown-menu">';
					foreach ( $menu->list as $i=>$item ) {
						if ( $item['divider'] ) {
							$html .= '<li role="separator" class="divider"></li>';
						} else {
							$html .= '<li><a href="'.$item[1].'">'.$item[0].'</a></li>';
						}
					}	
					$html .= '</ul>';
				}
				$html .= '</div>';
				$html .= '</p>';
			}
		}

		return $html;
	}

	private function getReportDB() {
		$db = new stdClass();

		$apps = new stdClass();
		$dash = new stdClass();
		$intra = new stdClass();
		$other = new stdClass();
		$reports = new stdClass();

		/*
		 *	Converted Reports
		 */
		$reports->coll->label = 'Collections';
		$reports->coll->list[] = array('Unique Items by Branch'
													,'./34/');

		$reports->ss->label = 'Support Services';
		$reports->ss->list[] = array('New Items with Holds'
													,'./11/');

		/*
		 *	Apps & Tools
		 */
		$apps->nmr->label = 'New Material Requests';
		$apps->nmr->list[] = array('NMR Staff Portal'
													,'http://dash.spokanelibrary.org/connect/request');
		$apps->nmr->list[] = array('NMR on our website'
													,'http://www.spokanelibrary.org/request');

		$apps->cfr->label = 'Content Filter Requests';
		$apps->cfr->list[] = array('View &amp; Manage Filter Requests'
													,'http://dash.spokanelibrary.org/connect/unblock');
		$apps->cfr->list[] = array('Process Filter Requests in Firewall'
													,'https://199.237.16.1/login');

		$apps->circ->label = 'Circulation';
		$apps->circ->list[] = array('Process New Library Cards'
													,'http://dash.spokanelibrary.org/connect/libcard');

		/*
		 *	Dashboard Reports
		 */

		$dash->circ->label = 'Circulation';
		$dash->circ->list[] = array('Circulation Monthly: Totals by Collection'
													,'http://dash.spokanelibrary.org/stats/circulation-monthly');
		$dash->circ->list[] = array('Circulation Daily: by Hour'
													,'http://dash.spokanelibrary.org/stats/circulation-daily');
		$dash->circ->list[] = array('Circulation Hourly: in Date Range'
													,'http://dash.spokanelibrary.org/stats/circulation-hourly-date-range');
		$dash->circ->list[] = array('divider'=>true);
		$dash->circ->list[] = array('X1 Circulation History'
													,'http://dash.spokanelibrary.org/stats/circulation-x1-history');
		$dash->circ->list[] = array('divider'=>true);
		$dash->circ->list[] = array('Circulation Snapshot: Right Now!'
													,'http://dash.spokanelibrary.org/stats/circulation-snapshot');
		$dash->circ->list[] = array('Circulation Snapshot Averages: Live in the Past!'
													,'http://dash.spokanelibrary.org/stats/circulation-snapshot-average');
		$dash->circ->list[] = array('divider'=>true);
		$dash->circ->list[] = array('Circulation Turnover by Call Type'
													,'http://dash.spokanelibrary.org/stats/circulation-monthly-call-type');

		$dash->coll->label = 'Collections';
		$dash->coll->list[] = array('Collection Balancing'
													,'http://dash.spokanelibrary.org/stats/collection-balance');
		$dash->coll->list[] = array('&nbsp;&nbsp;Lost in Transfer'
													,'http://dash.spokanelibrary.org/stats/collection-transfer');
		$dash->coll->list[] = array('divider'=>true);
		$dash->coll->list[] = array('New Material Requests (monthly)'
													,'http://dash.spokanelibrary.org/stats/collection-nmr');
		
		$dash->cust->label = 'Customers';
		$dash->cust->list[] = array('EnvisionWare Gates &amp; Events'
													,'http://dash.spokanelibrary.org/stats/envisionware-gates-events');
		$dash->cust->list[] = array('EnvisionWare PC Activity'
													,'http://dash.spokanelibrary.org/stats/envisionware-pc-activity');
		$dash->cust->list[] = array('divider'=>true);
		$dash->cust->list[] = array('Customer Fines &amp; Fees Waived'
													,'http://dash.spokanelibrary.org/stats/customer-fines-waived');
		$dash->cust->list[] = array('New Cards Issued'
													,'http://dash.spokanelibrary.org/stats/new-cards');
		$dash->cust->list[] = array('Hold Activity: Picked Up / Expired'
													,'http://dash.spokanelibrary.org/stats/hold-activity-weekly');
		$dash->cust->list[] = array('Item &amp; Request Limits'
													,'http://dash.spokanelibrary.org/stats/item-request-limits');
		

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