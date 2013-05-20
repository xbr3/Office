<?php
/**
 * Add snippets to build
 * 
 * @package office
 * @subpackage build
 */

$snippets = array();

$tmp = array(
	'Office' => array(
		'file' => 'office'
		,'description' => 'Displays Items.'
	)
);

foreach ($tmp as $k => $v) {
	/* @avr modSnippet $snippet */
	$snippet = $modx->newObject('modSnippet');
	$snippet->fromArray(array(
		'id' => 0
		,'name' => $k
		,'description' => @$v['description']
		,'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.'.$v['file'].'.php')
		//,'static' => 1
		//,'static_file' => 'core/components/office/elements/snippets/'.$v['file'].'.php'
	),'',true,true);

	$properties = include $sources['build'].'properties/properties.'.$v['file'].'.php';
	$snippet->setProperties($properties);

	$snippets[] = $snippet;
}

unset($tmp, $properties);
return $snippets;