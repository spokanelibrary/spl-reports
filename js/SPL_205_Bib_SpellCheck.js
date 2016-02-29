/*
 *	Rename file to match api/html 
 *	this file will be autoloaded
 *	and setUi() autorun
 *
 *	Used for report-specific mods
 */
var splReportUI = {

	

	setUI: function() {
		console.log('retreiving char sets');
		$.ajax(
			_this.api
		)
		.done(function(obj) { 
			//_this.showReport(obj);
			console.log(obj);
		})
		.fail(function() {
		})
		.always(function() {
		});
		
	}

}.setUI();
