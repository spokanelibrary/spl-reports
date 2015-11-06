var splReport = {
	config:splReportConfig

, init: function() {
		_self = this;

		$.ajax({ 
			url: _self.config.api.endpoint
		, data: { action: 'spl_reports'
						, params: { ajax:true, id: 34, endpoint: splReportConfig.api } 
						}
				, type: 'POST'
				
		})
		.done(function(obj) {  
			console.log(obj);
		})
		.fail(function() {
		})
		.always(function() {
	});
} 

}.init();