<?php

$settings = array();

$tmp = array(
	'frontend_css' => array(
		'xtype' => 'textfield'
		,'value' => '[[+cssUrl]]main/default.css'
		,'area' => 'office_main'
	)
	,'frontend_js' => array(
		'xtype' => 'textfield'
		,'value' => '[[+jsUrl]]main/default.js'
		,'area' => 'office_main'
	)
	,'page_id' => array(
		'xtype' => 'numberfield'
		,'value' => 0
		,'area' => 'office_main'
	)
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => 'office_'.$k
			,'namespace' => 'office'
		), $v
	),'',true,true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;