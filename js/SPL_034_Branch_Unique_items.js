var splReport = {
	config:splReportConfig

, init: function() {
		_self = this;

		//this.getReport();
} 
, getReport: function() {
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