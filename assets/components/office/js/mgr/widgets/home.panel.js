Office.panel.Home = function(config) {
	config = config || {};
	Ext.apply(config,{
		border: false
		,baseCls: 'modx-formpanel'
		,items: [{
			html: '<h2>'+_('office')+'</h2>'
			,border: false
			,cls: 'modx-page-header container'
		},{
			xtype: 'modx-tabs'
			,bodyStyle: 'padding: 10px'
			,defaults: { border: false ,autoHeight: true }
			,border: true
			,activeItem: 0
			,hideMode: 'offsets'
			,items: [{
				title: _('office_items')
				,items: [{
					html: _('office_intro_msg')
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'office-grid-items'
					,preventRender: true
				}]
			}]
		}]
	});
	Office.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(Office.panel.Home,MODx.Panel);
Ext.reg('office-panel-home',Office.panel.Home);
