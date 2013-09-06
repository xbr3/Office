<?php

$settings = array();

$tmp = array(
	'frontend_css' => array(
		'xtype' => 'textfield',
		'value' => '[[+cssUrl]]main/default.css',
	),
	'extjs_css' => array(
		'xtype' => 'textfield',
		'value' => '',
	),
	'frontend_js' => array(
		'xtype' => 'textfield',
		'value' => '[[+jsUrl]]main/default.js',
	),


	'auth_page_id' => array(
		'xtype' => 'numberfield',
		'value' => 0,
		'area' => 'office_auth',
	),
	'auth_frontend_css' => array(
		'xtype' => 'textfield',
		'value' => '',
		'area' => 'office_auth',
	),
	'auth_frontend_js' => array(
		'xtype' => 'textfield',
		'value' => '[[+jsUrl]]auth/default.js',
		'area' => 'office_auth',
	),


	'profile_page_id' => array(
		'xtype' => 'numberfield',
		'value' => 0,
		'area' => 'office_profile',
	),
	'profile_required_fields' => array(
		'xtype' => 'textfield',
		'value' => 'fullname',
		'area' => 'office_profile',
	),
	'profile_frontend_css' => array(
		'xtype' => 'textfield',
		'value' => '[[+cssUrl]]profile/default.css',
		'area' => 'office_profile',
	),
	'profile_frontend_js' => array(
		'xtype' => 'textfield',
		'value' => '[[+jsUrl]]profile/default.js',
		'area' => 'office_profile',
	),


	'ms2_frontend_css' => array(
		'xtype' => 'textfield',
		'value' => '[[+cssUrl]]minishop2/default.css',
		'area' => 'office_ms2',
	),
	'ms2_frontend_js' => array(
		'xtype' => 'textfield',
		'value' => '[[+jsUrl]]minishop2/default.js',
		'area' => 'office_ms2',
	),
	'ms2_date_format' => array(
		'xtype' => 'textfield',
		'value' => '%d.%m.%y <small>%H:%M</small>',
		'area' => 'office_ms2',
	),
	'ms2_order_grid_fields' => array(
		'xtype' => 'textarea',
		'value' => 'num,status,cost,weigh,tdelivery,payment,createdon,updatedon',
		'area' => 'office_ms2',
	),
	'ms2_order_form_fields' => array(
		'xtype' => 'textarea',
		'value' => 'num,cart_cost,delivery_cost,weight,payment,delivery',
		'area' => 'office_ms2',
	),
	'ms2_order_address_fields' => array(
		'xtype' => 'textarea',
		'value' => 'receiver,phone,index,country,region,city,metro,street,building,room,comment',
		'area' => 'office_ms2',
	),
	'ms2_order_product_fields' => array(
		'xtype' => 'textarea',
		'value' => 'product_pagetitleproduct_articleweightpricecountcost',
		'area' => 'office_ms2',
	),
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => 'office_'.$k
			,'namespace' => 'office'
			,'area' => 'office_main'
			,'value' => ''
		), $v
	),'',true,true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;