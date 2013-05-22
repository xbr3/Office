Office.Profile = {

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
		$(document).on('submit', selector, function(e) {
			$(this).ajaxSubmit({
				url: OfficeConfig.actionUrl
				,beforeSubmit: function(data) {
					Office.Message.close();
					$(selector + ' .message').text('');
					data.push({name: 'action', value:'Profile/Update'});
					data.push({name: 'pageId', value: OfficeConfig.pageId});
				}
				,success: function(response) {
					var data = $.parseJSON(response);
					if (data.success) {
						Office.Message.success(data.message);
					}
					else {
						Office.Message.error(data.message, false);
					}

					if (data.data) {
						for (var i in data.data) {
							$(selector + ' [name="'+i+'"]').parent().find('.message').text(data.data[i]);
						}
					}
				}
			});
			return false;
		});

		return true;
	}

};

Office.Profile.initialize('#office-profile-form');