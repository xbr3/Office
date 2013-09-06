<?php

$properties = array();

$tmp = array(
	'tplProfile' => array(
		'type' => 'textfield',
		'value' => 'tpl.Office.profile.form',
	),
	'tplActivate' => array(
		'type' => 'textfield',
		'value' => 'tpl.Office.profile.activate',
	),

	'profileFields' => array(
		'type' => 'textfield',
		'value' => 'email:50,fullname:50,phone:12,mobilephone:12,dob:10,gender,address,country,city,state,zip,fax,photo,comment,website',
	),
	'requiredFields' => array(
		'type' => 'textfield',
		'value' => 'email,fullname',
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