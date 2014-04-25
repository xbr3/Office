<?php
switch ($modx->event->name) {

	case 'OnHandleRequest':
		$actions = array('auth/login', 'auth/logout', 'remote/login', 'remote/logout');

		if (!empty($_REQUEST['action']) && in_array(urldecode($_REQUEST['action']), $actions)) {
			$params = array();
			foreach ($_REQUEST as $k => $v) {
				$params[$k] = urldecode($v);
			}
			if (!$modx->loadClass('office', MODX_CORE_PATH . 'components/office/model/office/', false, true)) {return;}

			list($controller, $action) = explode('/', $params['action']);
			$config = !empty($_SESSION['Office'][ucfirst($controller)][$modx->context->key])
				? $_SESSION['Office'][ucfirst($controller)][$modx->context->key]
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

	case 'OnWebAuthentication':
		$modx->event->_output = !empty($_SESSION['Office']['Auth']['verified']);
		break;

	case 'OnUserSave':
		if (!empty($user) && !empty($mode) && $mode == 'new') {
			if (!$user->get('remote_key')) {
				$user->set('remote_key', $user->get('id'));
				$user->save();
			}
		}
		break;
}