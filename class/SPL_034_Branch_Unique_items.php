<?php

class SPL_034_Branch_Unique_items extends SPL_Report {

	var $params;
	var $config;

	function __construct() {
	
	}

	function getReportData() {
		return parent->$params;
	}

}

?>