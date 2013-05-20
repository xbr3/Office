<?php
/**
 * Resolve creating db tables
 */
if ($object->xpdo) {
	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
			/* @var modX $modx */
			$modx =& $object->xpdo;
			$modelPath = $modx->getOption('office_core_path',null,$modx->getOption('core_path').'components/office/').'model/';
			//$modx->addPackage('office', $modelPath);

			//$manager = $modx->getManager();
			//$manager->createObjectContainer('OfficeItem');
		break;

		case xPDOTransport::ACTION_UPGRADE:
		break;
	}
}
return true;