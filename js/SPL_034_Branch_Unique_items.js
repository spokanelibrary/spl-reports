$.ajax({ 
	url: '/wordpress/wp-admin/admin-ajax.php'
	, data: { params: { action: 'wp_ajax_my_frontend_action' } }
	})
	.done(function(obj) {  
		console.log(obj);
	})
	.fail(function() {
	})
	.always(function() {
});