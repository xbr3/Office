<?php
/**
 * Properties for the Office snippet.
 *
 * @package office
 * @subpackage build
 */

$properties = array();

$tmp = array(
	'action' => array(
		'type' => 'textfield'
		,'value' => 'Auth'
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