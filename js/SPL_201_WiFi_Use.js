/*
 *	Rename file to match api/html 
 *	this file will be autoloaded
 *	and setUi() autorun
 *
 *	Used for report-specific mods
 */
var splReportUI = {

	setUI: function() {
		$datebegin = $('.datebegin');

		var defaultDate = new Date();
		defaultDate.setMonth(defaultDate.getMonth() - 12);
		$datebegin.datepicker( 'update', defaultDate );
	}

}.setUI();
