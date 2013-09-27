<?php
/**
 * Resolve system settings
 * @var array $options
 */
if ($object->xpdo) {
	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
			/** @var modX $modx */
			$modx =& $object->xpdo;

			/** @var modSystemSetting $setting */
			if ($setting = $modx->getObject('modSystemSetting', 'allow_multiple_emails')) {
				$setting->set('value', 0);
				$setting->save();
			}
			break;

		case xPDOTransport::ACTION_UPGRADE:
			break;
	}
}
return true;