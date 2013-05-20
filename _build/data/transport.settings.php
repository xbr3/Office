<?php

$settings = array();

$tmp = array(
	'frontend_css' => array(
		'xtype' => 'textfield'
		,'value' => '[[+cssUrl]]web/default.css'
		,'area' => 'office_main'
	)
	,'frontend_js' => array(
		'xtype' => 'textfield'
		,'value' => '[[+jsUrl]]web/default.js'
		,'area' => 'office_main'
	)
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => 'off_'.$k
			,'namespace' => 'office'
		), $v
	),'',true,true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;