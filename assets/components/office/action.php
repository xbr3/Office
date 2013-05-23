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

$ctx = !empty($_REQUEST['ctx']) ? $_REQUEST['ctx'] : 'web';
if ($ctx != 'web') {$modx->switchContext($ctx);}

//ini_set('display_errors',1);
//ini_set('error_reporting',-1);

/* @var Office $Office */
define('MODX_ACTION_MODE', true);
$Office = $modx->getService('office','Office',$modx->getOption('office.core_path',null,$modx->getOption('core_path').'components/office/').'model/office/',array());
if ($modx->error->hasError() || !($Office instanceof Office)) {die('Error');}
$Office->initialize($ctx);

if (!$response = $Office->loadAction($action, $_REQUEST)) {
	$response = $modx->toJSON(array(
		'success' => false
		,'message' => $modx->lexicon('office_err_action_nf')
	));
}

exit($response);