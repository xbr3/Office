<?php
switch ($modx->event->name) {

	case 'OnLoadWebDocument':
		if ($modx->user->isAuthenticated()) {
			if (!$modx->user->active || $modx->user->Profile->blocked) {
				$modx->runProcessor('security/logout');
				$modx->sendRedirect($modx->makeUrl($modx->getOption('site_start'),'','','full'));
			}
			else if ($page_id = $modx->getOption('office_profile_page_id', null, false, true)) {
				if ($modx->resource->id != $page_id && $modx->resource->parent != $page_id && @$_GET['action'] != 'auth/logout') {
					$required = array_map('trim', explode(',', $modx->getOption('office_profile_required_fields', null)));
					if (!empty($required)) {
						$user = array_merge($modx->user->toArray(), $modx->user->Profile->toArray());
						foreach ($required as $field) {
							if (isset($user[$field]) && trim($user[$field]) == '') {
								$modx->sendRedirect($modx->makeUrl($page_id,'','','full'));
								return;
							}
						}
					}
				}
			}
		};
	break;

}