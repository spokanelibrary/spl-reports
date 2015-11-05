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


if ( is_admin() ) {
  add_action( 'wp_ajax_spl_reports', 'spl_reports_ajax' );
  add_action( 'wp_ajax_nopriv_spl_reports', 'spl_reports_ajax_anon' );
  //add_action( 'wp_ajax_my_backend_action', 'my_backend_action_callback' );
  // Add other back-end action hooks here
} 


// remember to visit permalinks page to flush cache
function add_spl_reports_rewrite_rules() {   

	// rpa pages
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

require_once 'class/SPL_Report.php';

function wp_spl_reports($params) {

	spl_reports($params);

}

function spl_reports($params=null) {
	$report = null;

	$view = get_query_var('spl-reports');
	if ( !empty($view) ) {
  	$view = explode('/', $view);
    if ( !empty($view) ) {
    	$report = new SPL_Report($view, $params);
		}
	}

	if ( is_object($report) ) {
		return $report->output();
	} 
}

function spl_reports_ajax() {
	//echo 'test';
	wp_send_json( array('auth'=>'yes') );
	wp_die(); 
} 

function spl_reports_ajax_anon() {
	//echo 'test';
	wp_send_json( array('auth'=>'no') );
	wp_die(); 
} 

add_shortcode('spl_reports', 'wp_spl_reports');

?>