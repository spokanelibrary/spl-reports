<?php

class SPL_034_Branch_Unique_items extends SPL_Report {

	
	function __construct($parent) {
		//$this->params = $params;
		//$this->config = $config;
	}

	public function getReportData() {
		//return 'test';
		return $parent->$params;
	}

}

?>