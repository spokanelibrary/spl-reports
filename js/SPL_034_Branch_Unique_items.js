$.ajax({ 
	url: '/wordpress/wp-admin/admin-ajax.php'
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