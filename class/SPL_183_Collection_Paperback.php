<?php

class SPL_Report_Stub extends SPL_Report {

	/*
	 *	overload api endpoint, if necessary
	 */
	//var $api = '';

	/*
	 *	overload data processing, if necessary
	 */
	protected function processData($data=null) {	
		return parent::processData($data);
	}

}

?>