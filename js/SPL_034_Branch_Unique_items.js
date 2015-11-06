var splReport = {
	_self: this
	
	,config:splReportConfig

	,init: function() {
		//var _self = this;
		_self.initUI();
	} 
	,initUI: function() {
		$('body').on('submit', '.spl-report-control', function(e) {
			e.preventDefault();
			_self.getReport();
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

}.init();