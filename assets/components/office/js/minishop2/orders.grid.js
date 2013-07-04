miniShop2.panel.Orders = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		layout: 'form'
		,defaults: { border: false ,autoHeight: true }
		,hideMode: 'offsets'
		,border: false
		,items: [{
			xtype: 'minishop2-grid-orders'
			,cls: 'main-wrapper'
			,border: false
			,title: ''
			,preventRender: true
			,width: '97%'
		}]
	});
	miniShop2.panel.Orders.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.panel.Orders,MODx.Panel);
Ext.reg('minishop2-panel-orders',miniShop2.panel.Orders);


miniShop2.grid.Orders = function(config) {
	config = config || {};

	Ext.applyIf(config,{
		id: 'minishop2-grid-orders'
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'minishop2/getOrders'
		}
		,fields: MODx.config.order_grid_fields
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,columns: this.getColumns()
		,tbar: ['->', {
				xtype: 'minishop2-combo-status'
				,id: 'tbar-minishop2-combo-status'
				,width: 200
				,addall: true
				,listeners: {
					select: {fn: this.filterByStatus, scope:this}
				}
			}
			/*,'->',{
				xtype: 'textfield'
				,name: 'query'
				,width: 200
				,id: 'minishop2-orders-search'
				,emptyText: _('ms2_search')
				,listeners: {
					render: {fn:function(tf) {tf.getEl().addKeyListener(Ext.EventObject.ENTER,function() {this.FilterByQuery(tf);},this);},scope:this}
				}
			},{
				xtype: 'button'
				,id: 'minishop2-orders-clear'
				,text: _('ms2_search_clear')
				,listeners: {
					click: {fn: this.clearFilter, scope: this}
				}
			}*/
		]
		,listeners: {
			rowDblClick: function(grid, rowIndex, e) {
				var row = grid.store.getAt(rowIndex);
				this.viewOrder(grid, e, row);
			}
		}
	});
	miniShop2.grid.Orders.superclass.constructor.call(this,config);
	this.changed = false;
};
Ext.extend(miniShop2.grid.Orders,MODx.grid.Grid,{
	windows: {}

	,getMenu: function() {
		var m = [];
		m.push({
			text: _('ms2_menu_details')
			,handler: this.viewOrder
		});
		this.addContextMenuItem(m);
	}

	,getColumns: function() {
		var fields = {
			id: {header: _('ms2_id'),dataIndex: 'id',width: 50, hidden: true}
			//,user_id: {header: _('ms2_user_id'),dataIndex: 'user_id',width: 50, hidden: true}

			,createdon: {header: _('ms2_createdon'),dataIndex: 'createdon',width: 75, sortable: true, renderer: miniShop2.utils.formatDate}
			,updatedon: {header: _('ms2_updatedon'),dataIndex: 'updatedon',width: 75, sortable: true, renderer: miniShop2.utils.formatDate}

			,num: {header: _('ms2_num'),dataIndex: 'num',width: 50, sortable: true}
			,cost: {header: _('ms2_cost'),dataIndex: 'cost',width: 75, sortable: true}
			,cart_cost: {header: _('ms2_cart_cost'),dataIndex: 'cart_cost',width: 75, sortable: true}
			,delivery_cost: {header: _('ms2_delivery_cost'),dataIndex: 'delivery_cost',width: 75, sortable: true}

			,weight: {header: _('ms2_weight'),dataIndex: 'weight',width: 50, sortable: true}
			,status: {header: _('ms2_status'),dataIndex: 'status',width: 75, sortable: true}
			,delivery: {header: _('ms2_delivery'),dataIndex: 'delivery',width: 75, sortable: true}
			,payment: {header: _('ms2_payment'),dataIndex: 'payment',width: 75, sortable: true}
			//,address: {header: _('ms2_address'),dataIndex: 'address',width: 50, sortable: true}
			//,context: {header: _('ms2_context'),dataIndex: 'context',width: 50, sortable: true}

			,customer: {header: _('ms2_customer'),dataIndex: 'customer',width: 150, sortable: true}
			,receiver: {header: _('ms2_receiver'),dataIndex: 'receiver',width: 150, sortable: true}
		};

		var columns = [];
		for(var i=0; i < MODx.config.order_grid_fields.length; i++) {
			var field = MODx.config.order_grid_fields[i];
			if (fields[field]) {
				columns.push(fields[field]);
			}
		}

		return columns;
	}
/*
	,FilterByQuery: function(tf, nv, ov) {
		var s = this.getStore();
		s.baseParams.query = tf.getValue();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,clearFilter: function(btn,e) {
		var s = this.getStore();
		s.baseParams.query = '';
		Ext.getCmp('minishop2-orders-search').setValue('');
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}
*/
	,filterByStatus: function(cb) {
		this.getStore().baseParams['status'] = cb.value;
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,viewOrder: function(btn,e,row) {
		if (typeof(row) != 'undefined') {this.menu.record = row.data;}
		var id = this.menu.record.id;

		MODx.Ajax.request({
			url: miniShop2.config.connector_url
			,params: {
				action: 'minishop2/getOrder'
				,id: id
			}
			,listeners: {
				success: {fn:function(r) {
					var w = Ext.getCmp('minishop2-window-order-details');
					if (w) {w.hide().getEl().remove();}

					w = MODx.load({
						xtype: 'minishop2-window-order-details'
						,id: 'minishop2-window-order-details'
						,record:r.object
						,listeners: {
							success: {fn:function() {this.refresh();},scope:this}
							,hide: {fn: function() {this.getEl().remove();}}
						}
					});
					w.fp.getForm().reset();
					w.fp.getForm().setValues(r.object);
					w.show(e.target,function() {w.setPosition(null,100)},this);
				},scope:this}
			}
		});
	}

	,_loadStore: function() {
		this.store = new Ext.data.JsonStore({
			url: this.config.url
			,baseParams: this.config.baseParams || { action: this.config.action || 'getList' }
			,fields: this.config.fields
			,root: 'results'
			,totalProperty: 'total'
			,remoteSort: this.config.remoteSort || false
			,storeId: this.config.storeId || Ext.id()
			,autoDestroy: true
		});
	}
});
Ext.reg('minishop2-grid-orders',miniShop2.grid.Orders);



miniShop2.window.ViewOrder = function(config) {
	config = config || {};

	this.ident = config.ident || 'meuitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('ms2_menu_details')
		,id: this.ident
		,width: 750
		,labelAlign: 'top'
		//,url: miniShop2.config.connector_url
		//,action: ''
		,fields: {
			xtype: 'modx-tabs'
			//,border: true
			,activeTab: config.activeTab || 0
			,bodyStyle: { background: 'transparent'}
			,deferredRender: false
			,autoHeight: true
			,stateful: true
			,stateId: 'minishop2-window-order-details'
			,stateEvents: ['tabchange']
			,getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};}
			,items: [{
				title: _('ms2_order')
				,hideMode: 'offsets'
				,bodyStyle: 'padding:5px 0;'
				,defaults: {msgTarget: 'under',border: false}
				,items: this.getOrderFields(config)
			},{
				xtype: 'minishop2-grid-order-products'
				,title: _('ms2_order_products')
				,order_id: config.record.id
			},{
				layout: 'form'
				,title: _('ms2_address')
				,hideMode: 'offsets'
				,bodyStyle: 'padding:5px 0;'
				,defaults: {msgTarget: 'under',border: false}
				,items: this.getAddressFields(config)
			}]
		}
		,buttons: [{text: _('close'),scope: this,handler: function() {this.hide();}}]
	});
	miniShop2.window.ViewOrder.superclass.constructor.call(this,config);

};
Ext.extend(miniShop2.window.ViewOrder,MODx.Window, {

	getOrderFields: function(config) {
		return [{
			xtype: 'hidden'
			,name: 'id'
		},{
			layout: 'column'
			,defaults: {msgTarget: 'under',border: false}
			,style: 'padding:15px 5px;text-align:center;'
			,items: [{
				columnWidth: .5
				,layout: 'form'
				,items: [{xtype: 'displayfield', name: 'fullname', fieldLabel: _('ms2_user'), anchor: '100%', style: 'font-size:1.1em;'}]
			},{
				columnWidth: .5
				,layout: 'form'
				,items: [{xtype: 'displayfield', name: 'cost', fieldLabel: _('ms2_order_cost'), anchor: '100%', style: 'font-size:1.1em;'}]
			}]
		},{
			xtype: 'fieldset'
			,layout: 'column'
			,style: 'padding:15px 5px;text-align:center;'
			,defaults: {msgTarget: 'under',border: false}
			,items: [{
				columnWidth: .33
				,layout: 'form'
				,items: [
					{xtype: 'displayfield', name: 'num', fieldLabel: _('ms2_num'), anchor: '100%', style: 'font-size:1.1em;'}
					,{xtype: 'displayfield', name: 'weight', fieldLabel: _('ms2_weight'), anchor: '100%'}
				]
			},{
				columnWidth: .33
				,layout: 'form'
				,items: [
					{xtype: 'displayfield', name: 'cart_cost', fieldLabel: _('ms2_cart_cost'), anchor: '100%'}
					,{xtype: 'displayfield', name: 'delivery', fieldLabel: _('ms2_delivery'), anchor: '100%'}
				]
			},{
				columnWidth: .33
				,layout: 'form'
				,items: [
					{xtype: 'displayfield', name: 'delivery_cost', fieldLabel: _('ms2_delivery_cost'), anchor: '100%'}
					,{xtype: 'displayfield', name: 'payment', fieldLabel: _('ms2_payment'), anchor: '100%'}
				]
			}]
		},{
			//html: '<h2>' + _('ms2_order_log') + '</h2>'
		},{
			xtype: 'minishop2-grid-order-logs',order_id: config.record.id
		}];
	}

	,getAddressFields: function(config) {
		return [
			{
				layout: 'column'
				,defaults: {msgTarget: 'under',border: false}
				,items: [{
					columnWidth: .5
					,layout: 'form'
					,items: [
						{xtype: 'displayfield', name: 'addr_receiver', fieldLabel: _('ms2_receiver'), anchor: '100%'}
						,{xtype: 'displayfield', name: 'addr_phone', fieldLabel: _('ms2_phone'), anchor: '100%'}
						,{xtype: 'displayfield', name: 'addr_index', fieldLabel: _('ms2_index'), anchor: '100%'}
						,{xtype: 'displayfield', name: 'addr_country', fieldLabel: _('ms2_country'), anchor: '100%'}
						,{xtype: 'displayfield', name: 'addr_region', fieldLabel: _('ms2_region'), anchor: '100%'}
					]
				},{
					columnWidth: .5
					,layout: 'form'
					,items: [
						{xtype: 'displayfield', name: 'addr_metro', fieldLabel: _('ms2_metro'), anchor: '100%'}
						,{xtype: 'displayfield', name: 'addr_building', fieldLabel: _('ms2_building'), anchor: '100%'}
						,{xtype: 'displayfield', name: 'addr_city', fieldLabel: _('ms2_city'), anchor: '100%'}
						,{xtype: 'displayfield', name: 'addr_street', fieldLabel: _('ms2_street'), anchor: '100%'}
						,{xtype: 'displayfield', name: 'addr_room', fieldLabel: _('ms2_room'), anchor: '100%'}
					]
				}]
			}
			,{xtype: 'displayfield', name: 'addr_comment', fieldLabel: _('ms2_comment'), anchor: '100%'}
		];
	}

	,loadDropZones: function() {}
});
Ext.reg('minishop2-window-order-details',miniShop2.window.ViewOrder);




/*------------------------------------*/
miniShop2.grid.Logs = function(config) {
	config = config || {};

	Ext.applyIf(config,{
		id: this.ident
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'minishop2/getLog'
			,order_id: config.order_id
			,type: 'status'
		}
		,fields: ['timestamp','action','entry']
		,pageSize: Math.round(MODx.config.default_per_page / 6)
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,columns: [
			{header: _('ms2_timestamp'),dataIndex: 'timestamp', sortable: true, renderer: miniShop2.utils.formatDate, width: 100}
			,{header: _('ms2_action'),dataIndex: 'action', width: 100}
			,{header: _('ms2_entry'),dataIndex: 'entry', width: 100}
		]
	});
	miniShop2.grid.Logs.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.grid.Logs,MODx.grid.Grid, {
	_loadStore: function() {
		this.store = new Ext.data.JsonStore({
			url: this.config.url
			,baseParams: this.config.baseParams || { action: this.config.action || 'getList' }
			,fields: this.config.fields
			,root: 'results'
			,totalProperty: 'total'
			,remoteSort: this.config.remoteSort || false
			,storeId: this.config.storeId || Ext.id()
			,autoDestroy: true
		});
	}
});
Ext.reg('minishop2-grid-order-logs',miniShop2.grid.Logs);


miniShop2.grid.Products = function(config) {
	config = config || {};

	this.exp = new Ext.grid.RowExpander({
		expandOnDblClick: true
		,tpl : new Ext.Template('<p class="desc">{options}</p>')
		,renderer : function(v, p, record){return record.data.options != '' && record.data.options != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';}
	});

	Ext.applyIf(config,{
		id: this.ident
		,url: miniShop2.config.connector_url
		,baseParams: {
			action: 'minishop2/getOrderProducts'
			,order_id: config.order_id
		}
		,fields: ['pagetitle','article','weight','count','price','cost','options','url']
		,pageSize: Math.round(MODx.config.default_per_page / 2)
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,plugins: this.exp
		,columns: [this.exp
			,{header: _('ms2_id'),dataIndex: 'id', hidden: true, sortable: true, width: 40}
			,{header: _('ms2_product_pagetitle'),dataIndex: 'pagetitle', width: 100, renderer: this.productLink}
			,{header: _('ms2_product_article'),dataIndex: 'article', width: 50}
			,{header: _('ms2_product_weight'),dataIndex: 'weight', sortable: true, width: 50}
			,{header: _('ms2_product_price'),dataIndex: 'price', sortable: true, width: 50}
			,{header: _('ms2_count'),dataIndex: 'count', sortable: true, width: 50}
			,{header: _('ms2_cost'),dataIndex: 'cost', width: 50}
		]
	});
	miniShop2.grid.Products.superclass.constructor.call(this,config);
};
Ext.extend(miniShop2.grid.Products,MODx.grid.Grid, {

	productLink: function(val,cell,row) {
		if (!val) {return '';}
		var url = row.data['url'];

		return '<a href="' + url + '" target="_blank" class="ms2-link">' + val + '</a>'
	}

	,_loadStore: function() {
		this.store = new Ext.data.JsonStore({
			url: this.config.url
			,baseParams: this.config.baseParams || { action: this.config.action || 'getList' }
			,fields: this.config.fields
			,root: 'results'
			,totalProperty: 'total'
			,remoteSort: this.config.remoteSort || false
			,storeId: this.config.storeId || Ext.id()
			,autoDestroy: true
		});
	}
});
Ext.reg('minishop2-grid-order-products',miniShop2.grid.Products);