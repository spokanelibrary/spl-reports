var splReport = {

	api: {  
		url: '/wordpress/wp-admin/admin-ajax.php'
		,data: { action: 'spl_reports'
						,params: { 
							ajax:true
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
		return $('form.spl-report-control').serializeArray();
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
	,showReport: function(result) {
		//console.log(result);
		this.tmpl = Handlebars.compile( $('.spl-report-tmpl').html() );
		$('.spl-report-result').html( this.tmpl({ result:result }));
	}

}.init();