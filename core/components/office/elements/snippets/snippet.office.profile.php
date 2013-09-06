<?php
/** @var array $scriptProperties */
if (!empty($_REQUEST['action']) && strtolower($_REQUEST['action']) == 'auth/logout') {
	$scriptProperties['action'] = 'Auth/Logout';
}
else {
	$scriptProperties['action'] = 'Profile';
}

return $modx->runSnippet('Office', $scriptProperties);