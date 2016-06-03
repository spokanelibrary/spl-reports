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
			$('.spl-cgroup').removeClass('in')
			$('.spl-cgroup input[type=checkbox]').prop('checked', false);
			$('#spl-cgroup-'+cgroup).addClass('in');
			$('#spl-cgroup-'+cgroup+' input[type=checkbox]').prop('checked', true);
		
			$('#spl-cgroup-deselect-all').removeClass('disabled');
			$('#spl-cgroup-select-all').addClass('disabled');

			$('.spl-report-result').html('');
		});

		$('body').on('click', '#spl-cgroup-select-all', function(e) {
			$(this).addClass('disabled');
			$('#spl-cgroup-deselect-all').removeClass('disabled');
			var cgroup = $('.spl-cgroup-selector').val();
			$('#spl-cgroup-'+cgroup+' input[type=checkbox]').prop('checked', true);

		});

		$('body').on('click', '#spl-cgroup-deselect-all', function(e) {
			$(this).addClass('disabled');
			$('#spl-cgroup-select-all').removeClass('disabled');
			$('.spl-cgroup input[type=checkbox]').prop('checked', false);
		});

		$.ajax(
			_this.api
		)
		.done(function(obj) { 
			//console.log(obj);
			if ( obj.controls ) {
				//console.log(obj.controls);
				tmpl = Handlebars.compile( $('.spl-report-controls-tmpl').html() );
				$('.spl-report-controls').html( tmpl({ controls:obj.controls }));
				
				// reveal first cgroup
				$('#spl-cgroup-'+obj.controls.cgroups[0].code).addClass('in');
				$('#spl-cgroup-'+obj.controls.cgroups[0].code+' input[type=checkbox]').prop('checked', true);
			}			
		})
		.fail(function() {
		})
		.always(function() {
		});
		
	}

}.setUI();

