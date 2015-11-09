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
			e.preventDefault();
			_this.getReport();
		});
	}
	,getVals: function() {
		var vals = {};
		var form = $('form.spl-report-control').serializeArray();
		$.each(form, function() {
			vals[this.name] = this.value || '';
		});
		return vals;
	}
	,getReport: function() {
		this.api.data.params.vals = this.getVals();
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
	}

}.init();