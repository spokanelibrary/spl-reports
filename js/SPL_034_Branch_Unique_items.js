var splReport = {
	config:splReportConfig

	,init: function() {
		_self = this;
		this.initUI();
	} 
	,initUI: function() {
		$('body').on('submit', '.spl-report-control', function(e) {
			e.preventDefault();
			console.log(test);
		});
	}
	,getReport: function() {
		$.ajax(
		_self.config.api
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