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

		$(document).on('submit', selector, function(e) {
			$(this).ajaxSubmit({
				url: OfficeConfig.actionUrl
				,dataType: 'json'
				,beforeSubmit: function(data) {
					Office.Message.close();
					$(selector + ' .message').text('');
					data.push({name: 'action', value:'Profile/Update'});
					data.push({name: 'pageId', value: OfficeConfig.pageId});
				}
				,success: function(response) {
					var i;
					if (response.success) {
						Office.Message.success(response.message);
						if (response.data) {
							for (i in response.data) {
								if (response.data.hasOwnProperty(i)) {
									$(selector + ' [name="'+i+'"]').val(response.data[i]);
								}
							}
						}
					}
					else {
						Office.Message.error(response.message, false);
						if (response.data) {
							for (i in response.data) {
								if (response.data.hasOwnProperty(i)) {
									$(selector + ' [name="'+i+'"]').parent().find('.message').text(response.data[i]);
								}
							}
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