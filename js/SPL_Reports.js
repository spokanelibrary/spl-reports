var splReport = {

	api: {  
		url: '//staff.spokanelibrary.org/wordpress/wp-admin/admin-ajax.php'
		,data: { action: 'spl_reports'
						,nonce: $('.spl-report').data('spl-report-nonce') 
						,params: { 
							ajax: true
							,id: $('.spl-report').data('spl-report-id') 
					} 
		}
		,type: 'POST'
		,dataType: 'json'
	}

	,init: function() {
		_this = this;
		this.initUI();
	}
	,initUI: function() {
		$('body').on('submit', '.spl-report-control', function(e) {
			if ( $(this).data('noajax') ) {
			} else {
				e.preventDefault();
				_this.getReport();
			}
		});

		if ( $('#spl-report-json').length > 0 ) { 
      var $report = $('#spl-report-json');
      //if ( $account && $account.text().length > 0 ) {
      if ( $report && $report.html().length > 0 ) {
        //var user = JSON.parse($account.text());
        var report = JSON.parse($report.html());
      	console.log(report);
        _this.showReport(report);
      }
    }

		
		$datepicker = $('.date');
		if ( $datepicker.length ) {
			$datepicker.datepicker({
	        //startDate: "+1d",
	        //endDate: "+1y",
	        defaultDate: 'now',
	        //daysOfWeekDisabled: "0",
	        autoclose: true
	    });
	    //$datepicker.datepicker( 'update', new Date() );
		}
		

	}
	,getVals: function() {
		var vals = {};
		var form = $('form.spl-report-control').serializeArray();
		//return form;
		$.each(form, function() {
			if ( this.name.indexOf("[]" > -1) ) { 
				//name = this.name.replace('[]', '');
				console.log(this.name);
				//vals[name][] = this.value || '';
			} else {
				//vals[this.name] = this.value || '';
			}

			vals[this.name] = this.value || '';
		});
		return vals;
	}
	,getReport: function() {
		this.api.data.params.vals = this.getVals();
		$('.spl-report-result').html('<div class="alert alert-info">Please wait&hellip;</div>');
		$.ajax(
			_this.api
		)
		.done(function(obj) { 
			_this.showReport(obj);
		})
		.fail(function() {
		})
		.always(function() {
		});
		
	}
	,showReport: function(report) {
		//console.log(result);
		this.tmpl = Handlebars.compile( $('.spl-report-tmpl').html() );
		$('.spl-report-result').html( this.tmpl({ report:report }));


		$('.dynatable').dynatable({
		
			features: {
		    paginate: false,
		    sort: true,
		    pushState: false,
		    search: false,
		    recordCount: false,
		    perPageSelect: false
		  }
		});
		

	}

}.init();