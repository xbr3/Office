Ext.onReady(function() {
	MODx.load({ xtype: 'office-page-home'});
});

Office.page.Home = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		components: [{
			xtype: 'office-panel-home'
			,renderTo: 'office-panel-home-div'
		}]
	}); 
	Office.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(Office.page.Home,MODx.Component);
Ext.reg('office-page-home',Office.page.Home);