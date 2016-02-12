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

		var date = new Date();
		var dateBegin = new Date(date.getFullYear(), date.getMonth(), 1);
		$datebegin.datepicker( 'update', dateBegin );
	}

}.setUI();
