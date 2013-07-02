<?php

$chunks = array();

$tmp = array(
	'auth' => array(
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
	)
	,'profile' => array(
		'tpl.Office.profile.form' => array(
			'file' => 'profile.form'
			,'description' => ''
		)
		,'tpl.Office.profile.activate' => array(
			'file' => 'profile.activate'
			,   'description' => ''
		)
	)
	,'extras' => array(
		'tpl.Office.extras.outer' => array(
			'file' => 'extras.outer'
			,'description' => ''
		)
	)
	,'zpayment' => array(
		'tpl.Office.zp.register' => array(
			'file' => 'zp.register'
			,'description' => ''
		)
		,'tpl.Office.zp.operations' => array(
			'file' => 'zp.operations'
			,'description' => ''
		)
	)
	,'minishop2' => array(
		'tpl.Office.ms2.outer' => array(
			'file' => 'ms2.outer'
			,'description' => ''
		)
	)
);

foreach ($tmp as $controller => $values) {
	if (in_array($controller, $BUILD_CONTROLLERS)) {
		foreach ($values as $k => $v) {
			/* @avr modChunk $chunk */
			$chunk = $modx->newObject('modChunk');
			$chunk->fromArray(array(
				'id' => 0
				,'name' => $k
				,'description' => @$v['description']
				,'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.'.$v['file'].'.tpl')
				,'static' => BUILD_CHUNK_STATIC
				,'source' => 1
				,'static_file' => 'core/components/'.PKG_NAME_LOWER.'/elements/chunks/chunk.'.$v['file'].'.tpl'
			),'',true,true);
			$chunks[] = $chunk;
		}
	}
}

unset($tmp);
return $chunks;