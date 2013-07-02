Ext.namespace('miniShop2.combo');

miniShop2.combo.Status = function(config) {
	config = config || {};

	Ext.applyIf(config,{
		name: 'status'
		,id: 'minishop2-combo-status'
		,hiddenName: 'status'
		,displayField: 'name'
		,valueField: 'id'
		,fields: ['id','name']
		,pageSize: 10
		,emptyText: _('ms2_combo_select_status')
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'minishop2/getStatus'
			,combo: true
			,addall: config.addall || 0
			,order_id: config.order_id || 0
		}
		,listeners: miniShop2.combo.listeners_disable
	});
	miniShop2.combo.Status.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.combo.Status,MODx.combo.ComboBox);
Ext.reg('minishop2-combo-status',miniShop2.combo.Status);