$.ajax({ 
	url: 'http://staff.spokanelibrary.org/reports/id/1/'
	, data: { params: { }
	})
	.done(function(obj) {  
		console.log(obj);
	})
	.fail(function() {
	})
	.always(function() {
});