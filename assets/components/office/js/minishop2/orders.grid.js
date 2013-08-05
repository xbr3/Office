OfficeMS2.panel.Orders = function(config) {
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
	OfficeMS2.panel.Orders.superclass.constructor.call(this,config);
};
Ext.extend(OfficeMS2.panel.Orders,MODx.Panel);
Ext.reg('minishop2-panel-orders',OfficeMS2.panel.Orders);


OfficeMS2.grid.Orders = function(config) {
	config = config || {};

	Ext.applyIf(config,{
		id: 'minishop2-grid-orders'
		,url: OfficeMS2.config.connector_url
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
		]
		,listeners: {
			rowDblClick: function(grid, rowIndex, e) {
				var row = grid.store.getAt(rowIndex);
				this.viewOrder(grid, e, row);
			}
		}
	});
	OfficeMS2.grid.Orders.superclass.constructor.call(this,config);
	this._makeTemplates();
	this.on('click', this.onClick, this);
};
Ext.extend(OfficeMS2.grid.Orders,MODx.grid.Grid,{
	windows: {}

	,_makeTemplates: function() {
		this.tplActions = new Ext.XTemplate(''
			+'<div class="row-details-column">'
			+'<a href="#" class="controlBtn details" onclick="return false;" title="'+_('office_ms2_menu_details')+'">'+_('office_ms2_menu_details')+'</a>'
			+'</div>'
			,{compiled: true}
		);
	}

	,_renderActions: function(v,md,rec) {
		return this.tplActions.apply(rec.data);
	}

	/*
	,getMenu: function() {
		var m = [];
		m.push({
			text: _('office_ms2_menu_details')
			,handler: this.viewOrder
		});
		this.addContextMenuItem(m);
	}
	*/

	,onClick: function(e) {
		var t = e.getTarget();
		var elm = t.className.split(' ')[0];
		if (elm == 'controlBtn') {
			var action = t.className.split(' ')[1];
			this.menu.record = this.getSelectionModel().getSelected();
			switch (action) {
				case 'details': this.viewOrder(this,e); break;
			}
		}
	}

	,getColumns: function() {
		var all = {
			id: {width: 50, hidden: true}
			//,user_id: {width: 50, hidden: true}
			,createdon: {width: 75, sortable: true, renderer: OfficeMS2.utils.formatDate}
			,updatedon: {width: 75, sortable: true, renderer: OfficeMS2.utils.formatDate}
			,num: {width: 50, sortable: true}
			,cost: {width: 75, sortable: true}
			,cart_cost: {width: 75, sortable: true}
			,delivery_cost: {width: 75, sortable: true}
			,weight: {width: 50, sortable: true}
			,status: {width: 75, sortable: true}
			,delivery: {width: 75, sortable: true}
			,payment: {width: 75, sortable: true}
			//,address: {width: 50, sortable: true}
			//,context: {width: 50, sortable: true}
			,customer: {width: 150, sortable: true}
			,receiver: {width: 150, sortable: true}
			,details: {width: 50, renderer: {fn:this._renderActions,scope:this}}
		};

		var columns = [];
		for(var i=0; i < MODx.config.order_grid_fields.length; i++) {
			var field = MODx.config.order_grid_fields[i];
			if (all[field]) {
				Ext.applyIf(all[field], {
					header: _('office_ms2_' + field)
					,dataIndex: field
				});
				columns.push(all[field]);
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

		var mask = new Ext.LoadMask(this.getEl());
		mask.show();
		MODx.Ajax.request({
			url: OfficeMS2.config.connector_url
			,params: {
				action: 'minishop2/getOrder'
				,id: id
			}
			,listeners: {
				success: {fn:function(r) {
					mask.hide();
					var w = Ext.getCmp('minishop2-window-order-details');
					if (w) {w.hide().getEl().remove();}

					w = MODx.load({
						xtype: 'minishop2-window-order-details'
						,id: 'minishop2-window-order-details'
						,record: r.object
						,order_id: id
						,listeners: {
							success: {fn:function() {this.refresh();},scope:this}
							,hide: {fn: function() {this.getEl().remove();}}
						}
					});
					w.fp.getForm().reset();
					w.fp.getForm().setValues(r.object);
					w.show(e.target);
				},scope:this}
				,failure: function() {
					mask.hide();
				}
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
Ext.reg('minishop2-grid-orders',OfficeMS2.grid.Orders);



OfficeMS2.window.ViewOrder = function(config) {
	config = config || {};

	this.ident = config.ident || 'meuitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('office_ms2_menu_details')
		,id: this.ident
		,width: 750
		,labelAlign: 'top'
		,resizable: false
		,maximizable: false
		,collapsible: false
		,fields: {
			xtype: 'modx-tabs'
			,activeTab: config.activeTab || 0
			,bodyStyle: { background: 'transparent'}
			,deferredRender: false
			,autoHeight: true
			,stateful: true
			,stateId: 'minishop2-window-order-details'
			,stateEvents: ['tabchange']
			,getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};}
			,items: this.getTabs(config)
		}
		,buttons: [{text: _('close'),scope: this,handler: function() {this.hide();}}]
	});
	OfficeMS2.window.ViewOrder.superclass.constructor.call(this,config);

};
Ext.extend(OfficeMS2.window.ViewOrder,MODx.Window, {

	getTabs: function(config) {
		var tabs = [{
			title: _('office_ms2_order')
			,hideMode: 'offsets'
			,bodyStyle: 'padding:5px 0;'
			,defaults: {msgTarget: 'under',border: false}
			,items: this.getOrderFields(config)
		},{
			xtype: 'minishop2-grid-order-products'
			,title: _('office_ms2_order_products')
			,order_id: config.order_id
		}];

		var address = this.getAddressFields(config);
		if (address.length > 0) {
			tabs.push({
				layout: 'form'
				,title: _('office_ms2_address')
				,hideMode: 'offsets'
				,bodyStyle: 'padding:5px 0;'
				,defaults: {msgTarget: 'under',border: false}
				,items: address
			});
		}

		return tabs;
	}

	,getOrderFields: function(config) {
		var fields = [{
			xtype: 'hidden'
			,name: 'id'
		},{
			layout: 'column'
			,defaults: {msgTarget: 'under',border: false}
			,style: 'padding:15px 5px;text-align:center;'
			,items: [{
				columnWidth: .5
				,layout: 'form'
				,items: [{xtype: 'displayfield', name: 'fullname', fieldLabel: _('office_ms2_customer'), anchor: '100%', style: 'font-size:1.1em;'}]
			},{
				columnWidth: .5
				,layout: 'form'
				,items: [{xtype: 'displayfield', name: 'cost', fieldLabel: _('office_ms2_order_cost'), anchor: '100%', style: 'font-size:1.1em;'}]
			}]
		}];

		var all = {
			num: {style: 'font-size:1.1em;'}
			,weight: {},createdon: {},updatedon: {},cart_cost: {},delivery_cost: {},status: {},delivery: {},payment: {}
		};

		var tmp = [];
		for (var i=0; i < MODx.config.order_form_fields.length; i++) {
			var field = MODx.config.order_form_fields[i];
			if (all[field]) {
				Ext.applyIf(all[field], {
					xtype: 'displayfield'
					,name: field
					,fieldLabel: _('office_ms2_' + field)
				});
				all[field].anchor = '100%';
				tmp.push(all[field]);
			}
		}

		if (tmp.length > 0) {
			var add = {
				layout:'column'
				,xtype: 'fieldset'
				,style: 'padding:15px 5px;text-align:center;'
				,defaults: {msgTarget: 'under',border: false}
				,items: [
					{columnWidth: .33,layout: 'form',items: []}
					,{columnWidth: .33,layout: 'form',items: []}
					,{columnWidth: .33,layout: 'form',items: []}
				]
			};
			for (i=0; i < tmp.length; i++) {
				field = tmp[i];
				add.items[i % 3].items.push(field);
			}
			fields.push(add);
		}

		fields.push({xtype: 'minishop2-grid-order-logs',order_id: config.order_id});
		return fields;
	}

	,getAddressFields: function(config) {
		var all = {receiver: {},phone: {},index: {},country: {},region: {},metro: {},building: {},city: {},street: {},room: {}};
		var fields = [];
		var tmp = [];
		for (var i=0; i < MODx.config.order_address_fields.length; i++) {
			var field = MODx.config.order_address_fields[i];
			if (all[field]) {
				Ext.applyIf(all[field], {
					xtype: 'displayfield'
					,name: 'addr_' + field
					,fieldLabel: _('office_ms2_' + field)
				});
				all[field].anchor = '100%';
				tmp.push(all[field]);
			}
		}

		if (tmp.length > 0) {
			var add = {
				layout:'column'
				,defaults: {msgTarget: 'under',border: false}
				,items: [
					{columnWidth: .5,layout: 'form',items: []}
					,{columnWidth: .5,layout: 'form',items: []}
				]
			};
			for (i=0; i < tmp.length; i++) {
				field = tmp[i];
				add.items[i % 2].items.push(field);
			}
			fields.push(add);

			if (MODx.config.order_address_fields.in_array('comment')) {
				fields.push({xtype: 'displayfield', name: 'addr_comment', fieldLabel: _('office_ms2_comment'), anchor: '100%'});
			}
		}

		return fields;
	}

	,loadDropZones: function() {}
});
Ext.reg('minishop2-window-order-details',OfficeMS2.window.ViewOrder);




/*------------------------------------*/
OfficeMS2.grid.Logs = function(config) {
	config = config || {};

	Ext.applyIf(config,{
		id: this.ident
		,url: OfficeMS2.config.connector_url
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
			{header: _('office_ms2_timestamp'),dataIndex: 'timestamp', sortable: true, renderer: OfficeMS2.utils.formatDate, width: 100}
			,{header: _('office_ms2_action'),dataIndex: 'action', width: 100}
			,{header: _('office_ms2_entry'),dataIndex: 'entry', width: 100}
		]
	});
	OfficeMS2.grid.Logs.superclass.constructor.call(this,config);
};
Ext.extend(OfficeMS2.grid.Logs,MODx.grid.Grid, {
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
Ext.reg('minishop2-grid-order-logs',OfficeMS2.grid.Logs);


OfficeMS2.grid.Products = function(config) {
	config = config || {};

	Ext.applyIf(config,{
		id: this.ident
		,url: OfficeMS2.config.connector_url
		,baseParams: {
			action: 'minishop2/getOrderProducts'
			,order_id: config.order_id
		}
		,fields: MODx.config.order_product_fields
		,pageSize: Math.round(MODx.config.default_per_page / 2)
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,columns: this.getColumns()
	});
	OfficeMS2.grid.Products.superclass.constructor.call(this,config);
};
Ext.extend(OfficeMS2.grid.Products,MODx.grid.Grid, {

	productLink: function(val,cell,row) {
		if (!val) {return '';}
		var url = row.data['url'];

		return '<a href="' + url + '" target="_blank" class="ms2-link">' + val + '</a>'
	}

	,getColumns: function() {
		var fields = {
			id: {hidden: true, sortable: true, width: 40}
			,product_pagetitle: {header: _('office_ms2_product'), width: 100, renderer: this.productLink}
			,product_weight: {header: _('office_ms2_product_weight'), width: 50}
			,product_price: {header: _('office_ms2_product_price'), width: 50}
			,article: {width: 50}
			,weight: { sortable: true, width: 50}
			,price: {sortable: true, width: 50}
			,count: {sortable: true, width: 50}
			,cost: {width: 50}
			,options: {width: 100}
		};

		var columns = [];
		for(var i=0; i < MODx.config.order_product_fields.length; i++) {
			var field = MODx.config.order_product_fields[i];
			if (fields[field]) {
				Ext.applyIf(fields[field], {
					header: _('office_ms2_' + field)
					,dataIndex: field
				});
				columns.push(fields[field]);
			}
			else if (/^option_/.test(field)) {
				columns.push(
					{header: _(field.replace(/^option_/, 'office_ms2_')), dataIndex: field, width: 50}
				);
			}
			else if (/^product_/.test(field)) {
				columns.push(
					{header: _(field.replace(/^product_/, 'office_ms2_')), dataIndex: field, width: 75}
				);
			}
		}

		return columns;
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
Ext.reg('minishop2-grid-order-products',OfficeMS2.grid.Products);