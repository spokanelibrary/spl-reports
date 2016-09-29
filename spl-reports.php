<?php

/**
 * @package SPL_Reports
 * @version 0.1
 */

/*
Plugin Name: SPL Reports
Plugin URI: http://www.spokanelibrary.org/
Description: Hooks the <code>[spl_reports]</code> shortcode to show a variety of reports.
Author: Sean Girard
Author URI: http://seangirard.com
Version: 0.1
*/

include __DIR__.'/class/SPL_Report.php';

if ( is_admin() ) {
  add_action( 'wp_ajax_spl_reports', 'spl_reports_ajax' );
  add_action( 'wp_ajax_nopriv_spl_reports', 'spl_reports_ajax_anon' );
  //add_action( 'wp_ajax_my_backend_action', 'spl_reports_ajax_admin' );
} 

// remember to visit permalinks page to flush cache
function add_spl_reports_rewrite_rules() {   
	$url = 'reports';
	$page = 'reports';
	$query = 'spl-reports'; // you must register in query vars
	// grab the entire trailing string
  add_rewrite_rule(  
      $url."/(.*)?$",
      'index.php?pagename='.$page.'&'.$query.'=$matches[1]',  
      'top');
}
add_action( 'init', 'add_spl_reports_rewrite_rules' );

function add_spl_reports_query_vars($vars) {
  $spl_vars = array(
			'spl-reports'
  );
  return array_merge($spl_vars, $vars);
}
add_filter('query_vars', 'add_spl_reports_query_vars');

function wp_spl_reports($config=null) {
	$view = get_query_var('spl-reports');
	if ( !empty($view) ) {
  	$view = explode('/', $view);
  }

  if ( $view[0] ) {
  	if ( 'id' == $view[0] && isset($view[1]) ) {
  		$params['id'] = $view[1];
  	} else {
  		$params['id'] = $view[0];
  	}
  }
	
	return spl_reports($params, $config);
}
add_shortcode('spl_reports', 'wp_spl_reports');

function spl_reports($params=null, $config=null) {
	$report = new SPL_Report($params, $config);
	$report->getReport();

	if ( !spl_reports_restricted($params, $config) ) { 
		if ( is_object($report) ) {
			return $report->output();
		} else {
			return 'Uh oh. Error loading report.';
		}
	} else {
		return '<h4 class="text-danger">Access to this page is restricted.</h4>';
	}
}

function spl_reports_restricted($params=null, $config=null) {
	$restrict = false;

	$user = wp_get_current_user();
	if ( 207 == $params['id'] || 'new-employee' == $params['id'] ) {
		if ( !in_array($user->data->user_login, array('administrator', 'lwrixon')) ) {
			$restrict = true;	
		}
	}


	if ( 211 == $params['id'] ) {
		if ( !in_array($user->data->user_login
						, array('administrator'
								, 'sgirard')) ) {
			$restrict = true;	
		}
	}


	return $restrict;
}

function spl_reports_ajax() {
	wp_send_json( spl_reports($_REQUEST['params']) );
	wp_die();
} 

function spl_reports_ajax_anon() {
	spl_reports_ajax();
}
/*
function spl_reports_ajax_admin() {
	spl_reports_ajax();
} 
*/

?>