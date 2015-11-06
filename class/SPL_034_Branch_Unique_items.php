<?php

class SPL_034_Branch_Unique_items extends SPL_Report {

	
	function __construct($params, $config) {
		//$this->params = $params;
		//$this->config = $config;
		parent::__construct;
	}

	public function getReportData() {
		//return 'test';
		return $this->params;
	}

}

?>