/*
 *	Rename file to match api/html 
 *	this file will be autoloaded
 *	and setUi() autorun
 *
 *	Used for report-specific mods
 */
var splReportUI = {

	function getParameterByName( name ){
	    var regexS = "[\\?&]"+name+"=([^&#]*)", 
	  regex = new RegExp( regexS ),
	  results = regex.exec( window.location.search );
	  if( results == null ){
	    return "";
	  } else{
	    return decodeURIComponent(results[1].replace(/\+/g, " "));
	  }
	}

	setUI: function() {
		var id = getParameterByName('id');;
		if (id) {
			console.log(id);
		}
	}

}.setUI();
