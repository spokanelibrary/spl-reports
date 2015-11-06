<?php

class SPL_034_Branch_Unique_items extends SPL_Report {

	
	function __construct($parent) {
		$this->parent = $parent;
		//$this->params = $params;
		//$this->config = $config;
	}

	public function getReportData() {
		//return 'test';
		return $this->parent->$params;
	}

}

?>