<?php

class officeAuthController extends officeDefaultController {
	public $checkAuth = false;

	function __construct(Office $office, array $config = array()) {
		$this->modx = & $office->modx;
		$this->office = & $office;

		$this->config = array_merge(array(
			'tplLogin' => 'tpl.Office.login.login'
			,'tplLogout' => 'tpl.Office.login.logout'
			,'tplReset' => 'tpl.Office.login.reset'
			,'tplProfile' => 'tpl.Office.login.profile'
		), $config);
	}


	public function getForm() {
		if (!$this->modx->user->isAuthenticated()) {
			return $this->modx->getChunk($this->config['tplLogin']);
		}
		else {
			$user = $this->modx->user->toArray();
			$profile = $this->modx->user->getOne('Profile')->toArray();
			return $this->modx->getChunk($this->config['tplLogout'], array_merge($profile, $user));
		}
	}

}

return 'officeAuthController';