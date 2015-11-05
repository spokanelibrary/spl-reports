<?php

class SPL_034_Branch_Unique_items extends SPL_Report {

	var $data;

	function __construct() {
		$classpath = get_class();
		//$js = plugins_url('js/'.$classpath.'.js', dirname(__FILE__));
		$html = dirname(__DIR__).'/html/'.$classpath.'.html';

		//wp_enqueue_script( get_class(), $js );

		$this->loadJs();

		$this->data = file_get_contents($html);
	}

}

?>