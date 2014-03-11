<?php

$properties = array();

$tmp = array(
	'tplLogin' => array(
		'type' => 'textfield',
		'value' => 'tpl.Office.remote.login',
	),
	'tplLogout' => array(
		'type' => 'textfield',
		'value' => 'tpl.Office.remote.logout',
	),

	'groups' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'rememberme' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'loginContext' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'addContexts' => array(
		'type' => 'textfield',
		'value' => '',
	),

	'loginResourceId' => array(
		'type' => 'textfield',
		'value' => 0,
	),
	'logoutResourceId' => array(
		'type' => 'textfield',
		'value' => 0,
	),

	'updateUser' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'createUser' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'remote' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'key' => array(
		'type' => 'textfield',
		'value' => '',
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