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

	public function __construct() {
        parent::__construct();
    }

	public function getReportData() {
		$this->test = 'test';
		return $this->params;
	}

}

?>