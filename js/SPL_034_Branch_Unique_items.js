$.ajax({ 
	url: '/wp-admin/admin/admin-ajax.php'
	, data: { params: { action: 'my_frontend_action' } }
	})
	.done(function(obj) {  
		console.log(obj);
	})
	.fail(function() {
	})
	.always(function() {
});