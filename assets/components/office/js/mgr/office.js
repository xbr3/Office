var Office = function(config) {
	config = config || {};
	Office.superclass.constructor.call(this,config);
};
Ext.extend(Office,Ext.Component,{
	page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {},view: {}
});
Ext.reg('office',Office);

Office = new Office();