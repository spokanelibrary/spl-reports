<?php

class SPL_034_Branch_Unique_items extends SPL_Report {

	
	function __construct($caller) {
		$this->caller = $caller;
		//$this->params = $params;
		//$this->config = $config;
	}

	public function getReportData() {
		//return 'test';
		return $this->caller->params;
	}

}

?>