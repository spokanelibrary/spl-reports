/*
 *	Rename file to match api/html 
 *	this file will be autoloaded
 *	and setUi() autorun
 *
 *	Used for report-specific mods
 */
var splReportUI = {

	

	setUI: function() {
		//console.log('retreiving char sets');
		var vals = {chars:true};
		_this.api.data.params.vals = vals;
		//console.log(_this.api.data.params);

		$.ajax(
			_this.api
		)
		.done(function(obj) { 
			//console.log(obj);
			if (obj.chars) {
				$('#search-list-chars').append(obj.chars);
				$('#search-list-chars-warning').collapse('hide')
				$('#search-list-chars-wrapper').collapse('show');
			} else {
				$('#search-list-chars-warning')
				.removeClass('alert-warning')
				.addClass('alert-danger')
				.html('Unable to load character sets.');
			}
		})
		.fail(function() {
		})
		.always(function() {
		});
		
	}

}.setUI();