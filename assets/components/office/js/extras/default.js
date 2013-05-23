Ext.onReady(function() {
	Extras.config.connector_url = OfficeConfig.actionUrl;

	var grid = new Extras.panel.Keys();
	grid.render('office-extras-grid');
});
