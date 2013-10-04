<?php
switch ($modx->event->name) {

	case 'OnHandleRequest':
		if (!empty($_REQUEST['action']) && in_array(urldecode($_REQUEST['action']), array('auth/login', 'auth/logout'))) {
			$params = array();
			foreach ($_REQUEST as $k => $v) {
				$params[$k] = urldecode($v);
			}
			if (!$modx->loadClass('office', MODX_CORE_PATH . 'components/office/model/office/', false, true)) {return;}
			$config = !empty($_SESSION['Office']['Auth'][$modx->context->key])
				? $_SESSION['Office']['Auth'][$modx->context->key]
				: array();
			$Office = new Office($modx, $config);

			$Office->loadAction($params['action'], array_merge($params, $config));
		}
		elseif ($modx->context->key != 'web' && !$modx->user->id) {
			if ($user = $modx->getAuthenticatedUser($modx->context->key)) {
				$modx->user = $user;
				$modx->getUser($modx->context->key);
			}
		}
		break;
}