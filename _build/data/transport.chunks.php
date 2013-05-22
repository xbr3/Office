<?php

$chunks = array();

$tmp = array(
	'tpl.Office.auth.login' => array(
		'file' => 'auth.login'
		,'description' => ''
	)
	,'tpl.Office.auth.logout' => array(
		'file' => 'auth.logout'
		,'description' => ''
	)
	,'tpl.Office.auth.activate' => array(
		'file' => 'auth.activate'
		,'description' => ''
	)
	,'tpl.Office.profile.form' => array(
		'file' => 'profile.form'
		,'description' => ''
	)
	,'tpl.Office.profile.activate' => array(
		'file' => 'profile.activate'
		,'description' => ''
	)

);

foreach ($tmp as $k => $v) {
	/* @avr modChunk $chunk */
	$chunk = $modx->newObject('modChunk');
	$chunk->fromArray(array(
		'id' => 0
		,'name' => $k
		,'description' => @$v['description']
		,'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.'.$v['file'].'.tpl')
		,'static' => 1
		,'source' => 1
		,'static_file' => 'core/components/office/elements/chunks/chunk.'.$v['file'].'.tpl'
	),'',true,true);

	$chunks[] = $chunk;
}

unset($tmp);
return $chunks;