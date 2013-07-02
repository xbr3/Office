Ext.onReady(function() {
	miniShop2.config.connector_url = OfficeConfig.actionUrl;

	var grid = new miniShop2.panel.Orders();
	grid.render('office-minishop2-grid');
});
