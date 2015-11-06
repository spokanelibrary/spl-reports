var splReportConfig = {

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
	,getReport: function() {
		$.ajax(
			_this.api
		)
		.done(function(obj) { 
			console.log(obj);
		})
		.fail(function() {
		})
		.always(function() {
		});
		
	}


}.init();