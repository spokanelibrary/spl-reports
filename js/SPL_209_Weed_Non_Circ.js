/*
 *	Rename file to match api/html 
 *	this file will be autoloaded
 *	and setUi() autorun
 *
 *	Used for report-specific mods
 */
var splReportUI = {

	

	setUI: function() {
		//console.log('retreiving control params');
		var vals = {init:true};
		_this.api.data.params.vals = vals;
		//console.log(_this.api.data.params);

		$.ajax(
			_this.api
		)
		.done(function(obj) { 
			//console.log(obj);
			if ( obj.controls ) {
				console.log(obj.controls);
				tmpl = Handlebars.compile( $('.spl-report-controls-tmpl').html() );
				$('.spl-report-controls').html( tmpl({ controls:obj.controls }));
			
				$('body').on('change', '.spl-cgroup-selector', function(e) {
					$('.spl-cgroup').removeClass('in');
					console.log($(this).val());
					//$('#spl-cgroup-'+$(this).value())
				});
			}
			/*

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
			*/
		})
		.fail(function() {
		})
		.always(function() {
		});
		
	}

}.setUI();

