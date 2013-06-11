<?php

$settings = array();

$tmp = array(
	'frontend_css' => array(
		'xtype' => 'textfield'
		,'value' => '[[+cssUrl]]main/default.css'
	)
	,'frontend_js' => array(
		'xtype' => 'textfield'
		,'value' => '[[+jsUrl]]main/default.js'
	)
	,'page_id' => array(
		'xtype' => 'numberfield'
		,'value' => 0
	)
	,'zp_interface' => array(
		'xtype' => 'numberfield'
		,'value' => 0
		,'area' => 'office_zbillng'
	)
	,'zp_api_url' => array(
		'xtype' => 'textfield'
		,'value' => 'https://z-payment.com/api/billing/'
		,'area' => 'office_zpayment'
	)
	,'zp_account' => array(
		'xtype' => 'textfield'
		,'value' => 'ZP00000000'
		,'area' => 'office_zpayment'
	)
	,'zp_password' => array(
		'xtype' => 'text-password'
		,'value' => 'yourpassword'
		,'area' => 'office_zpayment'
	)
	,'zp_money_format' => array(
		'xtype' => 'textfield'
		,'value' => '[2, ".", " "]'
		,'area' => 'office_zpayment'
	)
	,'zp_activation_type' => array(
		'xtype' => 'textfield'
		,'value' => 'MAIL_CODE'
		,'area' => 'office_zpayment'
	)
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