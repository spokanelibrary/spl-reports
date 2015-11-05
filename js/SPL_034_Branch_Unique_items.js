$.ajax({ 
	url: '/wordpress/wp-admin/admin-ajax.php'
	, data: { action: 'spl_reports' }
			//, type: 'POST'
			, params: { 'spl-reports':'test' }
	})
	.done(function(obj) {  
		console.log(obj);
	})
	.fail(function() {
	})
	.always(function() {
});