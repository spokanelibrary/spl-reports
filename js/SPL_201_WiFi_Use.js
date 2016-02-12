/*
 *	Rename file to match api/html 
 *	this file will be autoloaded
 *	and setUi() autorun
 *
 *	Used for report-specific mods
 */
var splReportUI = {

	setUI: function() {
		var date = new Date();

		$datefinish = $('.datefinish');
		var dateFinish = new Date(date.getFullYear(), date.getMonth(), 1);
		$datefinish.datepicker( 'update', dateFinish );
		$datefinish.datepicker( {dateFormat: 'YYYY-MM-DD'} );

		$datebegin = $('.datebegin');
		var dateBegin = new Date(dateFinish.getFullYear(), dateFinish.getMonth()-1, 1);
		$datebegin.datepicker( 'update', dateBegin );
	}

}.setUI();
