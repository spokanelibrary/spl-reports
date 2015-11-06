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
		$.ajax({
			_this.config.api
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