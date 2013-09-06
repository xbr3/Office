<?php
/** @var array $scriptProperties */
if (!$modx->loadClass('office', MODX_CORE_PATH . 'components/office/model/office/', false, true)) {return;}
/** @var Office $Office */
$Office = new Office($modx, $scriptProperties);

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

if (!empty($action)) {
	$Office->initialize($modx->context->key, $scriptProperties);
	$output = $Office->loadAction($action, $scriptProperties);
}

return $output;