<?php
switch ($modx->event->name) {

	case 'OnLoadWebDocument':
		if ($modx->user->isAuthenticated()) {
			if (!$modx->user->active || $modx->user->Profile->blocked) {
				$modx->runProcessor('security/logout');
				$modx->sendRedirect($modx->makeUrl($modx->getOption('site_start'),'','','full'));
			}
			else if ($page_id = $modx->getOption('office_profile_page_id')) {
				if (!empty($page_id)
					&& trim($modx->user->Profile->fullname) == ''
					&& $modx->resource->id != $page_id
					&& $modx->resource->parent != $page_id
					&& @$_GET['action'] != 'auth/logout'
				) {
					$modx->sendRedirect($modx->makeUrl($page_id,'','','full'));
				}
			}
		};
	break;

}