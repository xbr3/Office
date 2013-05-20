<?php
/**
 * Properties for the Office snippet.
 *
 * @package office
 * @subpackage build
 */

$properties = array();

$tmp = array(
	'tpl' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Office.item'
	)
	,'sortBy' => array(
		'type' => 'textfield'
		,'value' => 'name'
	)
	,'sortDir' => array(
		'type' => 'list'
		,'options' => array(
			array('text' => 'ASC', 'value' => 'ASC')
			,array('text' => 'DESC', 'value' => 'DESC')
		)
		,'value' => 'ASC'
	)
	,'limit' => array(
		'type' => 'numberfield'
		,'value' => 5
	)
	,'outputSeparator' => array(
		'type' => 'textfield'
		,'value' => "\n"
	)
	,'toPlaceholder' => array(
		'type' => 'combo-boolean'
		,'value' => false
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