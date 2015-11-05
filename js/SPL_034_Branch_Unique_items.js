$.ajax({ 
	url: '/wordpress/wp-admin/admin-ajax.php'
	, data: { action: 'spl_reports'
			, params: { 'spl-reports': 1 } 
			}
			//, type: 'POST'
			
	})
	.done(function(obj) {  
		console.log(obj);
	})
	.fail(function() {
	})
	.always(function() {
});