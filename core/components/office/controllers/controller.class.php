<?php

interface officeControllerInterface {

	public function initialize($ctx = 'web');

	public function getMenu();

}



class officeDefaultController implements officeControllerInterface {
	/* @var modX $modx */
	public $modx;
	/* @var Office $office */
	public $office;
	public $checkAuth = true;


	function __construct(Office $office, array $config = array()) {
		$this->modx = & $office->modx;
		$this->office = & $office;

		$this->config = array_merge(array(
			'json_response' => false
		),$config);
	}


	public function initialize($ctx = 'web') {
		if ($this->checkAuth && !$this->modx->user->isAuthenticated()) {
			return false;
		}
		return true;
	}


	public function getMenu() {
		return array();
	}

}