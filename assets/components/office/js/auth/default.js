Office.Auth = {

	initialize: function(selector) {
		var elem = $(selector);
		if (!elem.length) {return false;}

		// Disable elements during ajax request
		$(document).ajaxStart(function() {
			elem.find('button, a').attr('disabled', true);
		})
		.ajaxStop(function() {
			elem.find('button, a').attr('disabled', false);
		});

		// Sign up
		$(document).on('submit', selector + ' form', function(e) {
			var email = $(this).find('input[name="email"]').val();
			$.post(OfficeConfig.actionUrl, {action: 'auth/sendlink', email: email}, function(response) {
				Office.Message.close();
				var data = $.parseJSON(response);
				if (data.success) {
					Office.Message.success(data.message);
					$(selector + ' form [name="email"]').val('');
					$(selector).modal('hide');
				}
				else {
					Office.Message.error(data.message);
				}
			});
			return false;
		});

		return true;
	}

};

Office.Auth.initialize('#office-auth-form');