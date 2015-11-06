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
  //add_action( 'wp_ajax_my_backend_action', 'my_backend_action_callback' );
  // Add other back-end action hooks here
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


function wp_spl_reports($params=null) {
	$view = get_query_var('spl-reports');
	if ( !empty($view) ) {
  	$view = explode('/', $view);
  }

  
  if ( $view[0] ) {
  	if ( 'id' == $view[0] && isset($view[1]) ) {
  		$config['id'] = $view[1];
  	} else {
  		$config['id'] = $view[0];
  	}
  }
  //return $config;
	if ( $config ) {
		return spl_reports($config, $params);
	}
	
}
add_shortcode('spl_reports', 'wp_spl_reports');

function spl_reports($config=null, $params=null) {
	
	
	$report = null;
	if ( $config ) {
		$report = new SPL_Report($config, $params);
	}

	if ( is_object($report) ) {
		return $report->output();
	} 	
}

function spl_reports_ajax() {
	wp_send_json( spl_reports($_REQUEST['params']) );
	wp_die();
} 

function spl_reports_ajax_anon() {
	wp_send_json( spl_reports($_REQUEST['params']) );
	wp_die();
} 

?>