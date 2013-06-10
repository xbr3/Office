Office.ZPayment = {

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
				,beforeSubmit: function(data) {
					Office.Message.close();
					$(selector + ' .message').text('');
					data.push({name: 'pageId', value: OfficeConfig.pageId});
				}
				,success: function(response) {
					var data = $.parseJSON(response);
					if (data.success) {
						Office.Message.success(data.message);
						var create = $('#office-zpayment-form-create');
						if (create.is(':visible')) {
							create.hide();
							$('#office-zpayment-form-activate').show();
						}
					}
					else {
						Office.Message.error(data.message, false);
					}

					if (data.reload) {
						document.location = document.location;
					}
					else {
						if (data.data.errors) {
							for (var i in data.data.errors) {
								$(selector + ' [name="'+i+'"]').parent().find('.message').text(data.data.errors[i]);
							}
						}
						if (data.data.values) {
							for (var i in data.data.values) {
								$(selector + ' [name="'+i+'"]').val(data.data.values[i]);
							}
						}
					}
				}
			});
			return false;
		});

		return true;
	}

	,sendCode: function() {
		$.post(OfficeConfig.actionUrl, {action: 'ZPayment/createPurse'}, function(data) {
			if (data.success) {
				Office.Message.success(data.message);
			}
			else {
				Office.Message.error(data.message, false);
			}
		}, 'json');
	}

	,getBalance: function(selector) {
		if (!selector) {selector = '#office-zpayment-user-balance';}
		var elem = $(selector);
		if (!elem.length) {return false;}

		$.post(OfficeConfig.actionUrl, {action: 'ZPayment/getBalance'}, function(data) {
			if (data.success && data.data) {
				//Office.Message.success(data.message);
				elem.text(data.data.balance);
			}
			else {
				Office.Message.error(data.message, false);
			}
		}, 'json');
	}

	,getHistory: function(selector) {
		if (!selector) {selector = '#office-zpayment-user-history';}
		var elem = $(selector);
		if (!elem.length) {return false;}

		$.post(OfficeConfig.actionUrl, {action: 'ZPayment/getHistory'}, function(data) {
			if (data.success && data.data) {
				//Office.Message.success(data.message);
				$(elem).text(data.data);
			}
			else {
				Office.Message.error(data.message, false);
			}
		}, 'json');
	}

};

Office.ZPayment.initialize('#office-zpayment-form-create');
Office.ZPayment.initialize('#office-zpayment-form-activate');

$(document).on('click', '#office-zpayment-code-link', function(e) {
	Office.ZPayment.sendCode();
	return false;
});