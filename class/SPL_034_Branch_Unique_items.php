<?php

class SPL_034_Branch_Unique_items extends SPL_Report {

	var $api = '34';

	// override data processing, if necessary
	protected function processData($data=null) {
		
		//parent::processData($data);
		$data['sg'] = 'test';
		return $data;
	}

}

?>