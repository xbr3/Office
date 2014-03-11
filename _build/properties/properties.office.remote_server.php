<?php

$properties = array();

$tmp = array(
	'hosts' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'key' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'authId' => array(
		'type' => 'numberfield',
		'value' => 0,
	),

);

foreach ($tmp as $k => $v) {
	$properties[] = array_merge(array(
			'name' => $k,
			'desc' => 'office_prop_'.$k,
			'lexicon' => 'office:properties',
		), $v
	);
}

return $properties;