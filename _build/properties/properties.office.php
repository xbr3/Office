<?php
/**
 * Properties for the Office snippet.
 *
 * @package office
 * @subpackage build
 */

$properties = array();

$tmp = array(
	'tplOuter' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Office.outer'
	)
	,'tplMenuRow' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Office.menu.row'
	)
	,'controllers' => array(
		'type' => 'textfield'
		,'value' => 'auth'
	)
	,'action' => array(
		'type' => 'textfield'
		,'value' => 'menu'
	)

);

foreach ($tmp as $k => $v) {
	$properties[] = array_merge(array(
			'name' => $k
			,'desc' => 'office_prop_'.$k
			,'lexicon' => 'office:properties'
		), $v
	);
}

return $properties;