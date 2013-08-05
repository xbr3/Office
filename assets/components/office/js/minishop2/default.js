Ext.onReady(function() {
	OfficeMS2.config.connector_url = OfficeConfig.actionUrl;

	var grid = new OfficeMS2.panel.Orders();
	grid.render('office-minishop2-grid');

	var preloader = document.getElementById('office-preloader');
	if (preloader) {
		preloader.parentNode.removeChild(preloader);
	}
});