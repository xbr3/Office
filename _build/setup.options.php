<?php
/**
 * Build the setup options form.
 */
$exists = false;
$output = null;
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_INSTALL:

	case xPDOTransport::ACTION_UPGRADE:
		$exists = $modx->getObject('transport.modTransportPackage', array('package_name' => 'HybridAuth'));
		break;

	case xPDOTransport::ACTION_UNINSTALL: break;
}

if (!$exists) {
	switch ($modx->getOption('manager_language')) {
		case 'ru':
			$output = 'Этот компонент может работать c <b>HybridAuth</b> для социальной авторизации.<br/><br/>Могу я автоматически скачать и установить его?';
			break;
		default:
			$output = 'This component can work with <b>HybridAuth</b> for social authorization.<br/><br/>Can i automaticly download and install it?';
	}

}

return $output;