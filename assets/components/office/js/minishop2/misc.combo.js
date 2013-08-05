Ext.namespace('OfficeMS2.combo');

OfficeMS2.combo.Status = function(config) {
	config = config || {};

	Ext.applyIf(config,{
		name: 'status'
		,id: 'minishop2-combo-status'
		,hiddenName: 'status'
		,displayField: 'name'
		,valueField: 'id'
		,fields: ['id','name']
		,pageSize: 10
		,emptyText: _('office_ms2_combo_select_status')
		,url: OfficeMS2.config.connector_url
		,baseParams: {
			action: 'minishop2/getStatus'
			,combo: true
			,addall: config.addall || 0
			,order_id: config.order_id || 0
		}
		,listeners: OfficeMS2.combo.listeners_disable
	});
	OfficeMS2.combo.Status.superclass.constructor.call(this,config);
};
Ext.extend(OfficeMS2.combo.Status,MODx.combo.ComboBox);
Ext.reg('minishop2-combo-status',OfficeMS2.combo.Status);