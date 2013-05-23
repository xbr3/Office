<?php
/* @var Office $Office */
$Office = $modx->getService('office','Office',$modx->getOption('office_core_path',null,$modx->getOption('core_path').'components/office/').'model/office/',$scriptProperties);
if (!($Office instanceof Office)) return '';

$output = null;

// We can change action to received via $_GET only if snippet was called with the same controller
if (!empty($_GET['action']) && !empty($action)) {
	$request = explode('/', strtolower(trim($_GET['action'])));
	$default = explode('/', strtolower(trim($action)));
	if ($request[0] == $default[0]) {
		$action = $_GET['action'];
		$scriptProperties = array_merge($_REQUEST, $scriptProperties);
	}
}

$Office->initialize($modx->context->key, $scriptProperties);
if (!empty($action)) {
	$output = $Office->loadAction($action, $scriptProperties);
}

return $output;