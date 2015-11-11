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

	protected function getReportMenu() {
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
						'.$this->getReportList($db->reports).'
					</div>
					<div class="col-sm-6 col-md-3">
						<h4>Dashboard Reports</h4>
						'.$this->getReportList($db->dash).'
					</div>
					<div class="clearfix visible-sm"></div>
					<div class="col-sm-6 col-md-3">
						<h4>Apps &amp; Tools</h4>
						'.$this->getReportList($db->apps).'
					</div>
					<div class="col-sm-6 col-md-3">
						<h4>Other Reports</h4>
						'.$this->getReportList($db->other).'
					</div>
				</div>
			</div>
		</div>

		<div class="panel spl-hero-intranet spl-hero-brand-gray-b">
			<div class="panel-heading">
				<h4>Older reports (intra)</h4>
			</div>
			<div class="panel-body">
				<p>
					<b>Note:</b> I am still populating this list.
				</p>
				<div class="row">
					<div class="col-sm-6 col-md-3">
						<h4>Circulation</h4>
						'.$this->getReportList($db->intra->circ).'
					</div>
					<div class="col-sm-6 col-md-3">
						<h4>Collection Development</h4>
						'.$this->getReportList($db->intra->coll).'
					</div>
					<div class="clearfix visible-sm"></div>
					<div class="col-sm-6 col-md-3">
						<h4>Business &amp; Finance</h4>
						'.$this->getReportList($db->intra->fin).'
					</div>
					<div class="col-sm-6 col-md-3">
						<h4>Serials</h4>
						'.$this->getReportList($db->intra->serial).'
						<h4>Youth Services</h4>
						'.$this->getReportList($db->intra->ya).'
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
				//$html .= '<p>';
				//$html .= '<div class="btn-group">';
				//$html .= '<button type="button" class="btn btn-lg btn-'.$class.' dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
				//$html .= $menu->label.' <span class="caret"></span></button>';
				if ( is_array($menu->list) ) {
					//$html .= '<ul class="dropdown-menu">';
					$html .= '<ul class="nav nav-pills nav-stacked">';
					foreach ( $menu->list as $i=>$item ) {
						if ( $item['divider'] ) {
							//$html .= '<li role="separator" class="divider"></li>';
						} else {
							$html .= '<li><a href="'.$item[1].'">'.$item[0].'</a></li>';
						}
					}	
					$html .= '</ul>';
				}
				//$html .= '</div>';
				//$html .= '</p>';
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
		$reports->coll->label = 'Collection Development';
		$reports->coll->list[] = array('Unique Items by Branch'
													,'./34/');

		$reports->ss->label = 'Support Services';
		$reports->ss->list[] = array('New Items with Holds'
													,'./11/');

		/*
		 *	Other Reports
		 */
		$other->circ->label = 'Circulation';
		$other->circ->list[] = array('Fee Waive Summary by Customer'
																,'http://web.spokanelibrary.org/reports/176');
		$other->circ->list[] = array('Item Borrower History (George Only)'
																,'http://web.spokanelibrary.org/reports/177');
		$other->circ->list[] = array('Borrowers Forced to Debt Collection'
																,'http://web.spokanelibrary.org/reports/184');

		$other->cust->label = 'Customers';
		$other->cust->list[] = array('WiFi Use Count'
																,'http://web.spokanelibrary.org/spl-wireless-track/');
		$other->cust->list[] = array('Summer Reading Signup'
																,'http://web.spokanelibrary.org/reports/107');

		$other->ss->label = 'Support Services';
		$other->ss->list[] = array(' Support Services Stats Summary Monthly'
																,'http://web.spokanelibrary.org/reports/153');


		$other->fin->label = 'Business & Finance';
		$other->fin->list[] = array('EnvisionWare Tableau Server'
																,'http://10.14.51.103/');


		/*
		 *	Apps & Tools
		 */
		$apps->circ->label = 'Circulation';
		$apps->circ->list[] = array('Process New Library Cards'
													,'http://dash.spokanelibrary.org/connect/libcard');

		$apps->ss->label = 'Support Services';
		$apps->ss->list[] = array('Process OneClick MARC Records'
													,'http://web.spokanelibrary.org/oneclick/');
		$apps->ss->list[] = array('Process OverDrive Metadata'
													,'http://web.spokanelibrary.org/overdrive/');
		
		$apps->nmr->label = 'New Material Requests';
		$apps->nmr->list[] = array('NMR Staff Portal'
													,'http://dash.spokanelibrary.org/connect/request');
		//$apps->nmr->list[] = array('NMR on our website'
		//											,'http://www.spokanelibrary.org/request');

		$apps->cfr->label = 'Content Filter Requests';
		$apps->cfr->list[] = array('View &amp; Manage Filter Requests'
													,'http://dash.spokanelibrary.org/connect/unblock');
		$apps->cfr->list[] = array('Process Filter Requests in Firewall'
													,'https://199.237.16.1/login');
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

		$dash->coll->label = 'Collection Development';
		$dash->coll->list[] = array('Collection Balancing'
													,'http://dash.spokanelibrary.org/stats/collection-balance');
		$dash->coll->list[] = array('Lost in Transfer'
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
		
		$dash->fin->label = 'Business & Finance';
		$dash->fin->list[] = array('Balance Sheet (beta)'
																,'http://dash.spokanelibrary.org/stats/balance-sheet');


		/*
		 *	Intra Reports
		 */

		$intra->circ->borr->label = 'Borrowers';
		$intra->circ->borr->list[] = array('Cost of all materials currently out to customer'
																,'http://web.spokanelibrary.org/reports/091');
		$intra->circ->borr->list[] = array('Outreach cards by location'
																,'http://web.spokanelibrary.org/reports/066');


		$intra->circ->bus->label = 'Business Office';
		$intra->circ->bus->list[] = array('Daily Fee Payment Detail'
																,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=45');
		$intra->circ->bus->list[] = array('Waives Detail'
																,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=128');

		$intra->circ->circ->label = 'Circulation';
		$intra->circ->circ->list[] = array('Circulation by login (daily/hourly) - working?'
																,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=86');
		$intra->circ->circ->list[] = array('Circulation by outreach sites (monthly) - crashes db?'
																,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=68');
		$intra->circ->circ->list[] = array('Circulation summary for Outreach'
																,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=67');
		$intra->circ->circ->list[] = array('Courtesy email reminders list'
																,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=90');
		$intra->circ->circ->list[] = array('Customers with an "edc" block'
																,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=122');
		$intra->circ->circ->list[] = array('Item status check'
																,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=33');
		$intra->circ->circ->list[] = array('System Notice Lookup'
																,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=165');
		$intra->circ->circ->list[] = array('Zip codes of checked out items'
																,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=35');


		$intra->circ->clean->label = 'Cleanup';
		$intra->circ->clean->list[] = array('Items with public services notes'
																,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=21');
		$intra->circ->clean->list[] = array('Lost in transit (by checkin location)'
																,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=42');
		$intra->circ->clean->list[] = array('Lost in transit (by collection)'
																,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=43');
		$intra->circ->clean->list[] = array('Older material in new collection codes'
																,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=41');

		$intra->circ->ca->label = 'Collection Agency';
		$intra->circ->ca->list[] = array('Patrons going to debt collection soon'
																,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=46');

		$intra->circ->search->label = 'Search Lists';
		$intra->circ->search->list[] = array('Complete trace search list searching at the site'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=97');
		$intra->circ->search->list[] = array('Create new trace search list'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=95');
		$intra->circ->search->list[] = array('Current trace search list (full)'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=96');
		$intra->circ->search->list[] = array('Finalize trace search list searching for all sites'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=98');
		$intra->circ->search->list[] = array('Trace search list log'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=99');
		$intra->circ->search->list[] = array('View current search list'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=101');

		$intra->circ->ill->label = 'ILL';
		$intra->circ->ill->list[] = array('ILL Cards Expiration Update'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=144');
		$intra->circ->ill->list[] = array('ILL cards with money owed'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=145');


		$intra->coll->bib->label = 'Bib Records';

		$intra->coll->circ->label = 'Circulation';

		$intra->coll->clean->label = 'Clean Up';

		$intra->coll->cd->label = 'Collection Development';

		$intra->coll->holds->label = 'Holds';

		$intra->coll->items->label = 'Item Records';

		$intra->coll->new->label = 'New Materials';

		$intra->coll->noncirc->label = 'Non-Circ';

		$intra->coll->labels->label = 'Label Requests';

		$intra->coll->subjects->label = 'Subject Headings';




		$intra->fin->borr->label = 'Bib Records';

		$intra->fin->budget->label = 'Budget';

		$intra->fin->bus->label = 'Business Office';

		$intra->fin->cleanup->label = 'Cleanup';

		$intra->fin->ca->label = 'Collection Agency';

		$intra->fin->borr->label = 'Internal Audit';




		$intra->serial->circ->label = 'Circulation';

		$intra->serial->items->label = 'Item Records';




		$intra->ya->circ->label = 'Circulation';

		$intra->ya->cd->label = 'Collection Development';



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