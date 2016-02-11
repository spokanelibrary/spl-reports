<?php

class SPL_Report_Stub extends SPL_Report {

	/*
	 *	overload api endpoint, if necessary
	 */
	//var $api = '';

	// This file is also required to trigger .js loading

	/*
	 *	overload data processing, if necessary
	 */
	protected function processData($data=null) {	
		return parent::processData($data);
	}

}

?>