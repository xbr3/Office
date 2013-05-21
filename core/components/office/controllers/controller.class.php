<?php

interface officeControllerInterface {

	public function initialize($ctx = 'web');

	public function getMenu();

	public function getDefaultAction();

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

	public function getDefaultAction() {
		return 'defaultAction';
	}

	/* This method returns an error of the cart
	 *
	 * @param string $message A lexicon key for error message
	 * @param array $data.Additional data, for example cart status
	 * @param array $placeholders Array with placeholders for lexicon entry
	 *
	 * @return array|string $response
	 * */
	public function error($message = '', $data = array(), $placeholders = array()) {
		$response = array(
			'success' => false
			,'message' => $this->modx->lexicon($message, $placeholders)
			,'data' => $data
		);

		return $this->config['json_response'] ? $this->modx->toJSON($response) : $response;
	}


	/* This method returns an success of the cart
	 *
	 * @param string $message A lexicon key for success message
	 * @param array $data.Additional data, for example cart status
	 * @param array $placeholders Array with placeholders for lexicon entry
	 *
	 * @return array|string $response
	 * */
	public function success($message = '', $data = array(), $placeholders = array()) {
		$response = array(
			'success' => true
			,'message' => $this->modx->lexicon($message, $placeholders)
			,'data' => $data
		);

		return $this->config['json_response'] ? $this->modx->toJSON($response) : $response;
	}

}