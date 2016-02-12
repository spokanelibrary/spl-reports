/*
 *	Rename file to match api/html 
 *	this file will be autoloaded
 *	and setUi() autorun
 *
 *	Used for report-specific mods
 */
var splReportUI = {

	setUI: function() {
		id = $.url.param("id");
		if (id) {
			console.log(id);
		}
	}

}.setUI();
