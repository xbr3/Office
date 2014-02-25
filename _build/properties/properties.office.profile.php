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
		'value' => 'username:50,email:50,fullname:50,phone:12,mobilephone:12,dob:10,gender,address,country,city,state,zip,fax,photo,comment,website',
	),
	'requiredFields' => array(
		'type' => 'textfield',
		'value' => 'username,email,fullname',
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

	'avatarParams' => array(
		'type' => 'textfield',
		'value' => '{"w":200,"h":200,"zc":0,"bg":"ffffff","f":"jpg"}',
	),
	'avatarPath' => array(
		'type' => 'textfield',
		'value' => 'images/users/',
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