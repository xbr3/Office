<?php

/* @var Office $Office */
$Office = $modx->getService('office','Office',$modx->getOption('office_core_path',null,$modx->getOption('core_path').'components/office/').'model/office/',$scriptProperties);
if (!($Office instanceof Office)) return '';
$Office->initialize($modx->context->key);

$output = null;

if (!empty($_GET['action'])) {
	$output = $Office->loadAction(trim($_GET['action']), array_merge($_REQUEST, $scriptProperties));
}

if (empty($output)) {
	if ((empty($action) || $action == 'menu') && !empty($Office->controllers)) {
		/* @var officeDefaultController $controller */
		foreach ($Office->controllers as $controller) {
			$output .= $controller->getMenu($scriptProperties);
		}
	}
	else if (!empty($action)) {
		$output = $Office->loadAction($action, $scriptProperties);
	}
}

return $output;