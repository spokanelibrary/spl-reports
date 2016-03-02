/*
 *	Rename file to match api/html 
 *	this file will be autoloaded
 *	and setUi() autorun
 *
 *	Used for report-specific mods
 */
var splReportUI = {

	setUI: function() {
		var defaultDate = new Date();
		defaultDate.setMonth(defaultDate.getMonth() - 1);
		$datepicker.datepicker( 'update', defaultDate );
	}

}.setUI();
