<?php
/**
 * Loads system settings into build
 *
 * @package office
 * @subpackage build
 */
$settings = array();

$tmp = array(
	'some_setting' => array(
		'xtype' => 'combo-boolean'
		,'value' => true
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