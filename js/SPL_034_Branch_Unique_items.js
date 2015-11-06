var splReport = {
	config:splReportConfig

	,init: function() {
		_this = this;
		//_this.getReport();
		$('body').on('submit', '.spl-report-control', function(e) {
			e.preventDefault();
			_this.getReport();
		});

		this.initUI();
	} 
	,initUI: function() {
		
		
		
	}
	,getReport: function() {
		//console.log(_this.config.api);
		$.ajax(
			_this.config.api
		)
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