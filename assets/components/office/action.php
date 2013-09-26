<?php

if (empty($_REQUEST['action'])) {
	die('Access denied');
}
else {
	$action = $_REQUEST['action'];
}

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/index.php';

$modx->getService('error','error.modError');
$modx->getRequest();
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;

if (!empty($_REQUEST['pageId']) && $resource = $modx->getObject('modResource', $_REQUEST['pageId'])) {
	$ctx = $resource->get('context_key');
}
else {
	$ctx = !empty($_REQUEST['ctx']) ? $_REQUEST['ctx'] : 'web';
}
if ($ctx != 'web') {
	$modx->switchContext($ctx);
	$modx->user = null;
	$modx->getUser($ctx);
}

/* @var Office $Office */
define('MODX_ACTION_MODE', true);
$Office = $modx->getService('office','Office', MODX_CORE_PATH . 'components/office/model/office/', array());
if ($modx->error->hasError() || !($Office instanceof Office)) {die('Error');}
$Office->initialize($ctx);

unset($_REQUEST['action']);
if (!$response = $Office->loadAction($action, $_REQUEST)) {
	$response = $modx->toJSON(array(
		'success' => false
		,'message' => $modx->lexicon('office_err_action_nf')
	));
}

exit($response);