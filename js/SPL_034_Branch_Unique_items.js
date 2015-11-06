var splReport = {
	config:splReportConfig

	,init: function() {
		_this = this;
		//_this.getReport();
		this.initUI();
	} 
	,initUI: function() {
		
		$('body').on('submit', '.spl-report-control', function(e) {
			_this.getReport();
			e.preventDefault();
		});
		
	}
	,getReport: function() {
		$.ajax(
		_this.config.api
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