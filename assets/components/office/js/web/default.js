Office = {
	initialize: function() {

		if(!jQuery().ajaxForm) {
			document.write('<script src="'+OfficeConfig.jsUrl+'lib/jquery.form.min.js"><\/script>');
		}
		if(!jQuery().jGrowl) {
			document.write('<script src="'+OfficeConfig.jsUrl+'lib/jquery.jgrowl.min.js"><\/script>');
		}

		$(document).ready(function() {
			$.jGrowl.defaults.closerTemplate = '<div>[ '+OfficeConfig.close_all_message+' ]</div>';
		});
	}
};

Office.initialize();