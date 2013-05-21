Office = {
	initialize: function() {

		if(!jQuery().ajaxForm) {
			document.write('<script src="'+OfficeConfig.jsUrl+'main/lib/jquery.form.min.js"><\/script>');
		}
		if(!jQuery().jGrowl) {
			document.write('<script src="'+OfficeConfig.jsUrl+'main/lib/jquery.jgrowl.min.js"><\/script>');
		}

		$(document).ready(function() {
			$.jGrowl.defaults.closerTemplate = '<div>[ '+OfficeConfig.close_all_message+' ]</div>';
		});
	}

};

Office.Message = {
	success: function(message) {
		if (message) {
			$.jGrowl(message, {theme: 'office-message-success'});
		}
	}
	,error: function(message) {
		if (message) {
			$.jGrowl(message, {theme: 'office-message-error', sticky: true});
		}
	}
	,info: function(message) {
		if (message) {
			$.jGrowl(message, {theme: 'office-message-info'});
		}
	}
	,close: function() {
		$.jGrowl('close');
	}
};

Office.initialize();