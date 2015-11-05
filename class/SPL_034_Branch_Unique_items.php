<?php

class SPL_034_Branch_Unique_items extends SPL_Report {

	var $data;

	function __construct() {

		$this->data = $this->loadJs();

		$this->data = $this->getHtml();
	}

}

?>