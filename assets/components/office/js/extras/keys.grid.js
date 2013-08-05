Extras.panel.Keys = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		layout: 'form'
		,defaults: { border: false ,autoHeight: true }
		,hideMode: 'offsets'
		,border: false
		,items: [{
			xtype: 'extras-grid-keys'
			,cls: 'main-wrapper'
			,border: false
			,title: ''
			,preventRender: true
			,width: '97%'
		}]
	});
	Extras.panel.Keys.superclass.constructor.call(this,config);
};
Ext.extend(Extras.panel.Keys,MODx.Panel);
Ext.reg('extras-panel-keys',Extras.panel.Keys);


Extras.grid.Keys = function(config) {
	config = config || {};

	/*
	this.exp = new Ext.grid.RowExpander({
		expandOnDblClick: false
		,tpl : new Ext.Template('<p class="desc">{description}</p>')
		,renderer : function(v, p, record){return record.data.description != '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';}
	});
	*/
	Ext.applyIf(config,{
		id: 'extras-grid-key'
		,url: Extras.config.connector_url
		,baseParams: {
			action: 'extras/getKeys'
		}
		,fields: ['id','key','active','vip','reset','packages','downloads','host','description','createdon','editedon']
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,plugins: this.exp
		,columns: [
			//this.exp
			{header: _('id'), dataIndex: 'id', width: 40}
			,{header: _('extras_key'),dataIndex: 'key', width: 140, renderer: {fn: this._renderKey, scope: this}}
			//,{header: _('extras_vip'), dataIndex: 'vip', width: 50, renderer: this._renderBoolean}
			//,{header: _('extras_active'),dataIndex: 'active',width: 50, renderer: this._renderBoolean}
			//,{header: _('extras_packages'), dataIndex: 'packages', width: 50}
			,{header: _('extras_downloaded'), dataIndex: 'downloads', width: 50}
			,{header: _('extras_host'), dataIndex: 'host', width: 100, renderer: {fn: this._renderReset, scope: this}}
			//,{header: _('extras_createdon'), dataIndex: 'createdon', width: 100}
			,{header: '', width: 60, renderer: {fn: this._renderActions, scope: this}}
			//,{header: _('extras_editedon'), dataIndex: 'editedon', width: 100}
		]
		,tbar: [{
			text: _('extras_key_create')
			,handler: this.createKey
			,scope: this
		}]
		,listeners: {
			rowDblClick: function(grid, rowIndex, e) {
				var row = grid.store.getAt(rowIndex);
				this.updateKey(grid, e, row);
			}
		}
		,viewConfig: {
			forceFit:true
			,enableRowBody:true
			,scrollOffset: 0
			,autoFill: true
			,showPreview: true
			,getRowClass : function(rec){
				return rec.data.active ? 'office-grid grid-row-active' : 'office-grid grid-row-inactive';
			}
		}
	});
	Extras.grid.Keys.superclass.constructor.call(this,config);
	this.store.un('load');
	this._makeTemplates();
	this.on('click', this.onClick, this);
	console.log(this.plugins)
};
Ext.extend(Extras.grid.Keys,MODx.grid.Grid,{
	windows: {}

	,_makeTemplates: function() {
		this.tplActions = new Ext.XTemplate(''
			+'<div class="row-key-column"><ul>'
			+'<li><a href="#" class="controlBtn update" onclick="return false;" title="'+_('extras_update')+'">'+_('extras_update')+'</a></li>'
			+'<li>'
			+'	<tpl if="active==0">'
			+'		<a href="#" class="controlBtn activate" onclick="return false;" title="'+_('office_extras_activate')+'">'+_('office_extras_activate')+'</a></li>'
			+'	</tpl><tpl elseif="active==1">'
			+'		<a href="#" class="controlBtn deactivate" onclick="return false;" title="'+_('office_extras_deactivate')+'">'+_('office_extras_deactivate')+'</a></li>'
			+'	</tpl>'
			+'<li><a href="#" class="controlBtn remove" onclick="return false;" title="'+_('extras_remove')+'">'+_('extras_remove')+'</a></li>'
			+'</ul>'
			,{compiled: true}
		);
		this.tplReset = new Ext.XTemplate(''
			+'{host}'
			+'<tpl if="reset == 0">'
				+'<br/><a href="#" class="controlBtn reset" onclick="return false;" title="'+_('office_extras_reset_host')+'">'+_('office_extras_reset_host')+'</a></li>'
			+'</tpl>'
			,{compiled: true}
		);
		this.tplKey = new Ext.XTemplate(''
			+'{key}'
			+'<tpl if="description">'
			+'	<br/><small class="description">{description}</small>'
			+'</tpl>'
			,{compiled: true}
		);
	}

	,_renderActions: function(v,md,rec) {
		return this.tplActions.apply(rec.data);
	}

	,_renderReset: function(v,md,rec) {
		return this.tplReset.apply(rec.data);
	}

	,_renderKey: function(v,md,rec) {
		return this.tplKey.apply(rec.data);
	}

	,_renderBoolean: function(v) {
		return v
			? '<span style="color: green;">' + _('yes') + '</span>'
			: '<span style="color: brown;">' + _('no') + '</span>'
	}

	,onClick: function(e) {
		var t = e.getTarget();
		var elm = t.className.split(' ')[0];
		if (elm == 'controlBtn') {
			var action = t.className.split(' ')[1];
			this.menu.record = this.getSelectionModel().getSelected();
			switch (action) {
				case 'update': this.updateKey(this,e); break;
				case 'reset': this.resetHost(this,e); break;
				case 'remove': this.removeKey(this,e); break;
				case 'activate': this.activateKey(this,e); break;
				case 'deactivate': this.activateKey(this,e); break;
			}
		}
	}
	/*
	,getMenu: function(grid,index,event) {
		var record = grid.getStore().getAt(index).data;
		var m = [];
		m.push({
			text: _('extras_update')
			,handler: this.updateKey
		});
		if (record.reset == 0) {
			m.push({
				text: _('office_extras_reset_host')
				,handler: this.resetHost
			});
		}
		m.push('-');

		m.push({
			text: _('extras_remove')
			,handler: this.removeKey
		});
		this.addContextMenuItem(m);
	}
	*/

	,createKey: function(btn,e) {
		var mask = new Ext.LoadMask(this.getEl());
		mask.show();
		MODx.Ajax.request({
			url: Extras.config.connector_url
			,params: {
				action: 'extras/generateKey'
				,user_id: MODx.request.id
			}
			,listeners: {
				success: {fn:function(r) {
					mask.hide();
					var w = Ext.getCmp('office-window-key-details');
					if (w) {w.hide().getEl().remove();}

					r.object.id = 0;
					w = MODx.load({
						xtype: 'extras-window-key'
						,id: 'office-window-key-details'
						,record: r.object
						,mode: 'create'
						,title: _('extras_key_create')
						,action: 'extras/createKey'
						,listeners: {
							success: {fn:function() { this.refresh(); },scope:this}
							,hide: {fn:function() { this.destroy(); }}
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

	,updateKey: function(btn,e,row) {
		if (typeof(row) != 'undefined') {this.menu.record = row.data;}
		var id = this.menu.record.id;

		var mask = new Ext.LoadMask(this.getEl());
		mask.show();
		MODx.Ajax.request({
			url: Extras.config.connector_url
			,params: {
				action: 'extras/getKey'
				,id: id
			}
			,listeners: {
				success: {fn:function(r) {
					mask.hide();
					var w = Ext.getCmp('office-window-key-details');
					if (w) {w.hide().getEl().remove();}

					w = MODx.load({
						xtype: 'extras-window-key'
						,id: 'office-window-key-details'
						,record: r.object
						,mode: 'update'
						,action: 'extras/updateKey'
						,listeners: {
							success: {fn:function() { this.refresh(); },scope:this}
							,hide: {fn:function() { this.destroy(); }}
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

	,activateKey: function(btn,e,row) {
		if (typeof(row) != 'undefined') {this.menu.record = row.data;}
		var id = this.menu.record.id;

		var mask = new Ext.LoadMask(this.getEl());
		mask.show();
		MODx.Ajax.request({
			url: Extras.config.connector_url
			,params: {
				action: 'extras/activateKey'
				,id: id
			}
			,listeners: {
				success: {fn:function(r) {
					mask.hide();
					this.refresh();
				},scope:this}
				,failure: function() {
					mask.hide();
				}
			}
		});
	}

	,removeKey: function(btn,e) {
		MODx.msg.confirm({
			title: _('extras_remove')
			,text: _('extras_key_remove_confirm')
			,url: this.config.url
			,params: {
				action: 'extras/removeKey'
				,id: this.menu.record.id
			}
			,listeners: {
				success: {fn:function(r) { this.refresh(); },scope:this}
			}
		});
	}

	,resetHost: function(btn, e) {
		MODx.msg.confirm({
			title: _('office_extras_reset_host')
			,text: _('office_extras_reset_host_confirm')
			,url: this.config.url
			,params: {
				action: 'extras/resetHost'
				,id: this.menu.record.id
			}
			,listeners: {
				success: {fn:function(r) { this.refresh(); },scope:this}
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
Ext.reg('extras-grid-keys',Extras.grid.Keys);

Extras.window.Key = function(config) {
	config = config || {};
	this.ident = config.ident || 'mecitem'+Ext.id();
	Ext.applyIf(config,{
		title: _('extras_key')
		,id: this.ident
		,autoHeight: true
		,width: 600
		,url: Extras.config.connector_url
		,record: config.record
		,action: 'extras/createKey'
		,resizable: false
		,maximizable: false
		,collapsible: false
		,modal: false
		,fields: this.getKeyFields(config)
		/*
		,fields: [{
			xtype: 'modx-tabs'
			,items: [{
				title: _('extras_key')
				,layout: 'form'
				,items: this.getKeyFields(config)
			}]
		}]
		*/
		,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
	});
	Extras.window.Key.superclass.constructor.call(this,config);
};
Ext.extend(Extras.window.Key,MODx.Window,{

	getKeyFields: function(config) {
		return [
			{xtype: 'hidden', name: 'id'}
			,{xtype: 'textfield', name: 'key', fieldLabel: _('extras_key'), anchor: '100%', allowBlank: false, readOnly: true}
			,{xtype: 'textarea', name: 'description', fieldLabel: _('extras_description'), height: 100, anchor: '100%'}
			//,{xtype: 'hidden', name: 'active'}
		];
	}

	,loadDropZones: function() {}

});
Ext.reg('extras-window-key',Extras.window.Key);