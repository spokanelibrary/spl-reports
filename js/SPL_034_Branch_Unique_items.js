var splReport = {
	config:splReportConfig

	,init: function() {
		_this = this;
		//_this.getReport();
		$('body').on('submit', '.spl-report-control', function(e) {
			
			_this.getReport();
			e.preventDefault();
		});

		this.initUI();
	} 
	,initUI: function() {
		
		
		
	}
	,getReport: function() {
		//console.log(_this.config.api);
		$.ajax({
			url: '/wordpress/wp-admin/admin-ajax.php'
			,data: { action: 'spl_reports'
						,params: { 
							ajax:true
							,id: $('.spl-report').data('spl-report-id') 
					} 
			}
		})
		.done(function(obj) { 
			console.log(obj);
			console.log('done');
		})
		.fail(function() {
			console.log('fail');
		})
		.always(function() {
			console.log('always');
		});
		
	}

}.init();