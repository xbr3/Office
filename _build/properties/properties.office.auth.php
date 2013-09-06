<?php

$properties = array();

$tmp = array(
	'tplLogin' => array(
		'type' => 'textfield',
		'value' => 'tpl.Office.auth.login',
	),
	'tplLogout' => array(
		'type' => 'textfield',
		'value' => 'tpl.Office.auth.logout',
	),
	'tplActivate' => array(
		'type' => 'textfield',
		'value' => 'tpl.Office.auth.activate',
	),

	'linkTTL' => array(
		'type' => 'textfield',
		'value' => 600,
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

	'HybridAuth' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'providers' => array(
		'type' => '',
		'value' => '',
	),
	'providerTpl' => array(
		'type' => 'textfield',
		'value' => 'tpl.HybridAuth.provider',
	),
	'activeProviderTpl' => array(
		'type' => 'textfield',
		'value' => 'tpl.HybridAuth.provider.active',
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