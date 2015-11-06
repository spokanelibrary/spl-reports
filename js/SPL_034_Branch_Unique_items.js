var splReport = {
	config:splReportConfig

, init: function() {
		_self = this;

		$.ajax({ 
			url: _self.config.api.endpoint
		, data: { action: 'spl_reports'
						, params: { ajax:true, id: self.config.rid, endpoint: _self.config.api } 
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