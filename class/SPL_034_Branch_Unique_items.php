<?php

class SPL_034_Branch_Unique_items extends SPL_Report {

	var $data;

	function __construct() {
		$js = plugins_url('js/'.get_class().'.js', dirname(__FILE__));
		
		$this->data = $js;
	}

}

?>