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

		$('body').on('change', '.spl-cgroup-selector', function(e) {
			var cgroup = $(this).val();
			$('.spl-cgroup').removeClass('in').children.('input[type=checkbox]').prop('checked', false);
			$('#spl-cgroup-'+cgroup).addClass('in'); //.children('input[type=checkbox]').prop('checked', true);
		});

		$.ajax(
			_this.api
		)
		.done(function(obj) { 
			//console.log(obj);
			if ( obj.controls ) {
				console.log(obj.controls);
				tmpl = Handlebars.compile( $('.spl-report-controls-tmpl').html() );
				$('.spl-report-controls').html( tmpl({ controls:obj.controls }));
			}
			// reveal first cgroup
			$('#spl-cgroup-'+obj.controls.cgroups[0].code).addClass('in');

		})
		.fail(function() {
		})
		.always(function() {
		});
		
	}

}.setUI();

