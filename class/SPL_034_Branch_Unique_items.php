<?php

class SPL_034_Branch_Unique_items extends SPL_Report {

	var $params;
	var $config;

	function __construct($params, $config) {
		$this->params = $params;
		$this->config = $config;
	}

	function getReportData() {
		return $this->$params;
	}

}

?>