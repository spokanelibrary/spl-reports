<?php

class SPL_034_Branch_Unique_items extends SPL_Report {

	var $data;

	function __construct() {
		$js = plugins_url('js/', dirname(__FILE__));
		$filename = $js.get_class();
		
		$this->data = $filename;
	}

}

?>