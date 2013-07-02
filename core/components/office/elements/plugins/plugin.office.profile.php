<?php
switch ($modx->event->name) {

	case 'OnLoadWebDocument':
		if ($modx->user->isAuthenticated()) {
			if (!$modx->user->active || $modx->user->Profile->blocked) {
				$modx->runProcessor('security/logout');
				$modx->sendRedirect($modx->makeUrl($modx->getOption('site_start'),'','','full'));
			}

			$page_id = $modx->getOption('office_profile_page_id');
			if (trim($modx->user->Profile->fullname) == '' && !empty($page_id) && $modx->resource->id != $page_id && $modx->resource->parent != $page_id) {
				$modx->sendRedirect($modx->makeUrl($page_id,'','','full'));
			}
		};
	break;

}