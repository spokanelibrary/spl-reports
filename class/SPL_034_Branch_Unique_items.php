<?php

class SPL_034_Branch_Unique_items extends SPL_Report {

	/*
	var $params;
	var $config;

	function __construct($params=null, $config=null) {
		$this->params = $params;
		$this->config = $config;
	}
	*/
	function __construct() {
		parent::__construct();
	}

	public function getReportData() {
		return $this->params;
	}

}

?>