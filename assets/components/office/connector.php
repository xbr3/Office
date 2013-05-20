<?php
/**
 * Office Connector
 *
 * @package office
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('office_core_path',null,$modx->getOption('core_path').'components/office/');
require_once $corePath.'model/office/office.class.php';
$modx->office = new Office($modx);

$modx->lexicon->load('office:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->office->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));