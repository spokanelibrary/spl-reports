$.ajax({ 
	url: '/wordpress/wp-admin/admin-ajax.php'
	, data: { action: 'my_frontend_action' }
			, type: 'POST'
			, params: { }
	})
	.done(function(obj) {  
		console.log(obj);
	})
	.fail(function() {
	})
	.always(function() {
});