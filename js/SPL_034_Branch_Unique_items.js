var splReport = {
	config:splReportConfig

	,init: function() {
		_this = this;
		this.initUI();
	} 
	,initUI: function() {
		
		$('body').on('submit', '.spl-report-control', function(e) {
			e.preventDefault();
			console.log(_this);
			_this.getReport();
		});
		
	}
	,getReport: function() {
		console.log('test');
		/*
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
		*/
	}

};

splReport.init();