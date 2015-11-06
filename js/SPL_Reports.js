var splReportConfig = {

	api: {  
		url: '/wordpress/wp-admin/admin-ajax.php'
	, data: { action: 'spl_reports'
					, params: { 
							ajax:true
						, id: $('.spl-report-control').data('spl-report-id') 
						} 
					}
			, type: 'POST'
	}

}