<?php
switch ($modx->event->name) {

	case 'OnHandleRequest':
		if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], array('auth/login', 'auth/logout'))) {
			if (!$modx->loadClass('office', MODX_CORE_PATH . 'components/office/model/office/', false, true)) {return;}
			$config = !empty($_SESSION['Office']['Auth'][$modx->context->key])
				? $_SESSION['Office']['Auth'][$modx->context->key]
				: array();
			$Office = new Office($modx, $config);

			$config = array_merge($_REQUEST, $config);
			$Office->loadAction($_REQUEST['action'], $config);
		}
		elseif ($modx->context->key != 'web' && !$modx->user->id) {
			if ($user = $modx->getAuthenticatedUser($modx->context->key)) {
				$modx->user = $user;
				$modx->getUser($modx->context->key);
			}
		}
		break;
}