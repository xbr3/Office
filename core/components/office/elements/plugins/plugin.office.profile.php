<?php
switch ($modx->event->name) {

	case 'OnLoadWebDocument':
		if ($modx->user->isAuthenticated()) {
			if (!$modx->user->active || $modx->user->Profile->blocked) {
				$modx->runProcessor('security/logout');
				$modx->sendRedirect($modx->makeUrl($modx->getOption('site_start'),'','','full'));
			}

			$office = $modx->getOption('office_page_id');
			if (trim($modx->user->Profile->fullname) == '' && $modx->resource->id != $office && $modx->resource->parent != $office) {
				$modx->sendRedirect($modx->makeUrl($office,'','','full'));
			}
		};
	break;
}