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
				if ( class_exists($class->name) ) {
					$report = new $class->name($this->params, $this->config);
				
					if ( $this->params['ajax'] ) {
						if ( wp_verify_nonce( $_REQUEST['nonce'], 'spl-report-nonce-'.$this->params['id'] ) ) {
							$this->output = $report->processData($report->getReportData());
						}
					} else {
						$report->loadJs();
						//$html .= '<div class="row">'.PHP_EOL;
						//$html .= '<div class="col-sm-12">'.PHP_EOL;
						$html .= '<div class="spl-report"
											data-spl-report-nonce="'.wp_create_nonce( 'spl-report-nonce-'.$this->params['id'] ).'"
											data-spl-report-id="'.$this->params['id'] .'">'.PHP_EOL;
						
						$html .= '<pre>'.print_r($this->getReportTemplateClass(), true).'</pre>';

						$html .= $report->getTmpl();	
						$html .= PHP_EOL.'</div>'.PHP_EOL;
						//$html .= PHP_EOL.'</div>'.PHP_EOL;
						//$html .= PHP_EOL.'</div>'.PHP_EOL;

						$this->output = $html;
					}
				} else {
					$html .= $this->getReportError('Report not loaded');
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
		<blockquote>
			<b>This is a master list of all reports currently in use.</b> 
			If you don\'t see something listed here, or if you see an obsolete or non-functional report, please send in a <a href="mailto:itsupport@spokanelibrary.org">Tech Request</a>! 
			Right now this is mostly a collection of links to older reporting sources. 
			I will be porting <b>all</b> of our reports to the staff intranet in the coming months. 
			Reports already converted are listed under <b>Converted Reports</b>.
			<small>sean</small>
		</blockquote>
		<div class="panel spl-hero-intranet spl-hero-brand-blue-f">
			<div class="panel-heading">
				<h4 class="">Newer Reports</h4>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-6 col-md-3">
						<h4>Converted Reports</h4>
						'.$this->getReportList($db->reports).'
						<h4>ToDo Reports</h4>
						'.$this->getReportList($db->todo).'
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
					<b>Note:</b> I have already weeded a few things here that I am pretty sure are obsolete. -sg
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

	protected function getReportTemplateClass() {
    $class = new stdClass();

    $files = scandir( plugin_dir_path( __DIR__ ) . 'html'  );
    foreach ($files as $file) {
      // ignore directories and hidden files
      if(0 !== stripos($file, '.')) {
        //$class->scan = $this->params['id'];
        if ( substr_count(strtolower(str_ireplace('_', '-', $file)), strtolower($this->params['id'])) ) {
          $class->path = plugin_dir_path( __FILE__ ).$file;
          // trim off file extension
          $class->name = stristr($file, '.', true);
        }
      }
    }


    return $class;
  }

	protected function getReportClass() {
		$class = new stdClass();
		$files = scandir( plugin_dir_path( __FILE__ ) );
    foreach ($files as $file) {
      // ignore directories and hidden files
      if(0 !== stripos($file, '.')) {
        //$class->scan = $this->params['id'];
        if ( substr_count(strtolower(str_ireplace('_', '-', $file)), strtolower($this->params['id'])) ) {
          $class->path = plugin_dir_path( __FILE__ ).$file;
          // trim off file extension
          $class->name = stristr($file, '.', true);
        }
      }
    }

		return $class;
	}

	protected function loadJs() {
		wp_enqueue_script( 'spl-reports-datepicker', plugins_url('js/vendor/bootstrap-datepicker.js', dirname(__FILE__)) );
		wp_enqueue_style( 'spl-reports-datepicker-css', plugins_url('css/vendor/datepicker.min.css', dirname(__FILE__)) );
		
		wp_enqueue_script( 'spl-reports-dynatable', plugins_url('js/vendor/jquery.dynatable.js', dirname(__FILE__)) );
		wp_enqueue_style( 'spl-reports-dynatable-css', plugins_url('css/vendor/jquery.dynatable.css', dirname(__FILE__)) );
		

		wp_enqueue_script( 'spl-reports-config', plugins_url('js/SPL_Reports.js', dirname(__FILE__)) );
		if ( file_exists(plugins_url('js/'.get_class($this).'.js')) ) {	
			wp_enqueue_script( get_class(), plugins_url('js/'.get_class($this).'.js', dirname(__FILE__)) );
		}
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

	private function getReportDB() {
		$db = new stdClass();

		$apps = new stdClass();
		$dash = new stdClass();
		$todo = new stdClass();
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

		$todo->create->label = 'Reports Needed';
		$todo->create->list[] = array('Non-res tieout'
													,'./');

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
		$apps->cfr->list[] = array('View &amp; Manage Content Filter Requests'
													,'http://dash.spokanelibrary.org/connect/unblock');
		$apps->cfr->list[] = array('Process Content Filter Requests in Firewall'
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
		//$intra->circ->circ->list[] = array('Zip codes of checked out items'
		//														,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=35');


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
		$intra->coll->bib->list[] = array('1XX, 6XX, 7XX text not proper authority cross-reference'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=129');
		$intra->coll->bib->list[] = array('Bibs potentially marked erroneously as "staff-only"'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=4');
		$intra->coll->bib->list[] = array('Bibs potentially marked with an incorrect bib status'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=15');
		$intra->coll->bib->list[] = array('Bibs where the 092 and 100 fields do not match'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=17');
		$intra->coll->bib->list[] = array('Bibs which may need to be marked "staff-only"'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=16');
		$intra->coll->bib->list[] = array('Bibs with no items'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=9');
		$intra->coll->bib->list[] = array('Bibs without a MARC 092 field'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=19');

		
		$intra->coll->circ->label = 'Circulation';
		$intra->coll->circ->list[] = array('Percentage of missing and lost items'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=27');
		$intra->coll->circ->list[] = array('Turnover by collection'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=139');

		
		$intra->coll->clean->label = 'Clean Up';
		$intra->coll->clean->list[] = array('8-digit barcodes by location and collection'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=160');
		$intra->coll->clean->list[] = array('Bibs with a cutter beginning with a number'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=163');
		$intra->coll->clean->list[] = array('Drama cleanup'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=1');
		$intra->coll->clean->list[] = array('Items with support services notes'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=22');
		$intra->coll->clean->list[] = array('JTAutoentry: Bibs to mark staff_only'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=133');
		$intra->coll->clean->list[] = array('JTAutoentry: Bibs to unmark staff_only'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=132');
		$intra->coll->clean->list[] = array('Juvenile/Easy Bib/Item Call Number Discrepency'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=130');
		$intra->coll->clean->list[] = array('MARC 245a is NULL'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=131');
		$intra->coll->clean->list[] = array('Possible itype/collection code irregularities'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=148');
		$intra->coll->clean->list[] = array('Spine label & item call number disagreement'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=149');
		$intra->coll->clean->list[] = array('YA collection code not in agreement with call number'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=146');
		

		$intra->coll->cd->label = 'Collection Development';
		//$intra->coll->cd->list[] = array(''
		//															,'');

		$intra->coll->holds->label = 'Holds';
		$intra->coll->holds->list[] = array('Bibs in Support Services with possible hold problems'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=5');
		$intra->coll->holds->list[] = array('Holds ratio'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=10');
		$intra->coll->holds->list[] = array('Titles with possible un-fillable holds'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=49');

		$intra->coll->items->label = 'Item Records';
		$intra->coll->cd->list[] = array('Staff-only items in circulation'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=84');
		$intra->coll->cd->list[] = array('Viable items by location'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=159');

		$intra->coll->new->label = 'New Materials';
		$intra->coll->new->list[] = array('Bibs with items created 6+ months ago and in item_status "on order"'
															,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=50');
		$intra->coll->new->list[] = array('New books to regular collection script'
															,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=169');

		$intra->coll->noncirc->label = 'Non-Circ';
		$intra->coll->noncirc->list[] = array('Biography non-circulation'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=39');
		$intra->coll->noncirc->list[] = array('Fiction non-circulation'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=38');
		$intra->coll->noncirc->list[] = array('Miscellaneous non-circulation'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=40');
		$intra->coll->noncirc->list[] = array('Non-fiction non-circulation'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=37');

		$intra->coll->labels->label = 'Label Requests';
		$intra->coll->labels->list[] = array('Approved call number labels'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=80');
		$intra->coll->labels->list[] = array('New call number label request'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=79');
		$intra->coll->labels->list[] = array('Unapproved call number label requests (av)'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=74');
		$intra->coll->labels->list[] = array('Unapproved call number label requests (print)'
																	,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=78');
		
		$intra->coll->subjects->label = 'Subject Headings';
		$intra->coll->subjects->list[] = array('Adult records with juvenile fiction subject headings'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=2');
		$intra->coll->subjects->list[] = array('Adult records with juvenile literature subject headings'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=3');
		$intra->coll->subjects->list[] = array('Juvenile fiction subfield errors'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=6');
		$intra->coll->subjects->list[] = array('Juvenile non-fiction subfield errors'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=7');
		$intra->coll->subjects->list[] = array('Potential MARC 651 Guidebook problems'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=12');
		$intra->coll->subjects->list[] = array('Young adult fiction subfield errors'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=8');
		
		
		$intra->fin->borr->label = 'Bib Records';
		$intra->fin->borr->list[] = array('Customers with credit balances in Horizon'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=59');
		$intra->fin->borr->list[] = array('Library cards with staff btypes'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=58');
		
		
		$intra->fin->budget->label = 'Budget';
		$intra->fin->budget->list[] = array('Budget Expenditures and circulation by Dewey range'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=158');
		$intra->fin->budget->list[] = array('Budget Expenditures by Location'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=157');
		$intra->fin->budget->list[] = array('Budget Transfer'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=13');
		$intra->fin->budget->list[] = array('Collection values by collection'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=26');
		$intra->fin->budget->list[] = array('Collection values by location'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=25');
		

		
		$intra->fin->bus->label = 'Business Office';
		$intra->fin->bus->list[] = array('A/R'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=55');
		$intra->fin->bus->list[] = array('Bounced checks'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=56');
		$intra->fin->bus->list[] = array('Daily Payment Detail (Business Office)'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=135');
		$intra->fin->bus->list[] = array('Deleted item search'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=60');
		$intra->fin->bus->list[] = array('Deleted Items List'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=175');
		$intra->fin->bus->list[] = array('Item holdings with values'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=57');
		$intra->fin->bus->list[] = array('Monthly pay waive'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=52');
		$intra->fin->bus->list[] = array('Waives Detail (Business Office)'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=140');
		$intra->fin->bus->list[] = array('Weekly Pay/Waive Summary'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=44');
		

		$intra->fin->cleanup->label = 'Cleanup';
		$intra->fin->cleanup->list[] = array('Statement Anomolies'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=118');
		
		$intra->fin->ca->label = 'Collection Agency';
		$intra->fin->ca->list[] = array('All customers in collection'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=120');
		$intra->fin->ca->list[] = array('Customers with money owing $50.00 and 50 days'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=123');
		$intra->fin->ca->list[] = array('Total owed by customers in collection'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=121');
		$intra->fin->ca->list[] = array('Weekly collection agency summary'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=119');

		$intra->fin->audit->label = 'Internal Audit';
		$intra->fin->audit->list[] = array('Library cards renewed with money owing (Weekly)'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=63');
		$intra->fin->audit->list[] = array('Staff card waives'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=61');
		$intra->fin->audit->list[] = array('Weekly blocks deleted'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=62');
				


		$intra->serial->circ->label = 'Circulation';
		$intra->serial->circ->list[] = array('Periodicals routing list'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=83');
		
		$intra->serial->items->label = 'Item Records';
		$intra->serial->items->list[] = array('Periodical copy list'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=81');
		$intra->serial->items->list[] = array('Periodical copy list Curr Rec\'d Only'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=171');
		$intra->serial->items->list[] = array('Serial Weirdness'
																		,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=141');



		//$intra->ya->circ->label = 'Circulation';

		$intra->ya->cd->label = 'Collection Development';
		$intra->ya->cd->list[] = array('Dewey - date (Easy Non-fiction)'
														,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=32');
		$intra->ya->cd->list[] = array('Dewey - date (Juvenile Non-fiction)'
														,'http://intra.spokanelibrary.org/reports/detail.asp?report_id=31');
		


		$db->apps = $apps;
		$db->dash = $dash;
		$db->todo = $todo;
		$db->intra = $intra;
		$db->other = $other;
		$db->reports = $reports;

		return $db;
	}

}

?>