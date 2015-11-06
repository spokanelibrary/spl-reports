var splReport = {
	config:splReportConfig

, init: function() {
		_self = this;

		$.ajax(_self.config.api)
		.done(function(obj) {  
			console.log(obj);
		})
		.fail(function() {
		})
		.always(function() {
	});
} 

}.init();