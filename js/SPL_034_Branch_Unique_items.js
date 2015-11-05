$.ajax({ 
	url: '/wordpress/wp-admin/admin-ajax.php'
	, data: { action: 'nopriv_my_frontend_action' }
			, params: { }
	})
	.done(function(obj) {  
		console.log(obj);
	})
	.fail(function() {
	})
	.always(function() {
});