var splReport = {
	config:splReportConfig

	,init: function() {
		_self = this;
		this.initUI();
	} 
	,initUI: function() {
		_self = this;
		$('body').on('submit', '.spl-report-control', function(e) {
			e.preventDefault();
			_self.getReport();
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