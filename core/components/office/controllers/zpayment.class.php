<?php

class officeZPaymentController extends officeDefaultController {

	public function setDefault($config = array()) {
		if (defined('MODX_ACTION_MODE') && MODX_ACTION_MODE && !empty($_SESSION['Office']['ZPayment'])) {
			$this->config = $_SESSION['Office']['ZPayment'];
			$this->config['json_response'] = true;
		}
		else {
			$this->config = array_merge(array(
				'ID_INTERFACE' => $this->modx->getOption('office_zp_interface')
				,'ZP_ACCOUNT' => $this->modx->getOption('office_zp_account')
				,'ACT_PASSWORD' => $this->modx->getOption('office_zp_password')
				,'API_URL' => $this->modx->getOption('office_zp_api_url')

				,'activationType' => $this->modx->getOption('office_zp_activation_type', null, 'MAIL_CODE', true) // Can be PHONE_CODE or MAIL_CODE
				,'tplRegister' => 'tpl.Office.zp.register'
				,'tplOperations' => 'tpl.Office.zp.operations'
			), $config);

			$_SESSION['Office']['ZPayment'] = $this->config;
		}
	}


	public function getLanguageTopics() {
		return array('office:zpayment');
	}


	public function initialize($ctx = 'web') {
		$this->modx->error->errors = array();
		$this->modx->error->message = '';
		return true;
	}


	public function defaultAction() {
		if (!$this->modx->user->isAuthenticated()) {
			$this->modx->sendUnauthorizedPage();
			return '';
		}

		$config = $this->office->makePlaceholders($this->office->config);
		if ($css = trim($this->modx->getOption('office_zpayment_frontend_css', null, '[[+cssUrl]]zpayment/default.css'))) {
			$this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
		}
		if ($js = trim($this->modx->getOption('office_zpayment_frontend_js', null, '[[+jsUrl]]zpayment/default.js'))) {
			$this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));
		}

		if (!$user = $this->getUser()) {
			return $this->modx->getChunk($this->config['tplRegister'], array('mode' => 'new'));
		}
		else {
			/* @var zpUser $user */
			if (!$user->get('active') || !$user->get('zp')) {
				$data = $user->toArray();
				$data['mode'] = 'activate';
				return $this->modx->getChunk($this->config['tplRegister'], $data);
			}
			else {
				$pls = $user->toArray();
				$pls['balance'] = $this->moneyFormat($pls['balance']);
				return $this->modx->getChunk($this->config['tplOperations'], $pls);
			}
		}
	}


	/**
	 * Returns money in user specified format
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public function moneyFormat($value) {
		$f = json_decode($this->modx->getOption('office_zp_money_format', null, '[2, ".", " "]'), true);
		$value = number_format($value, $f[0], $f[1], $f[2]);

		return $value;
	}


	/**
	 * Returns zpUser object if exists
	 *
	 * @return null|object
	 */
	public function getUser($id = 0) {
		if (!$id && $this->modx->user->isAuthenticated()) {
			$id = $this->modx->user->id;
		}
		else {
			return false;
		}
		return $this->modx->getObject('zpUser', array('internalKey' => $id));
	}


	/**
	 * Request to Z-Payment
	 *
	 * @param $action
	 * @param $params
	 *
	 * @return bool|mixed
	 */
	public function request($action, $params, $hash_order = array()) {
		$request = array_merge(array(
			'ID_INTERFACE' => $this->config['ID_INTERFACE']
			,'ZP_ACCOUNT' => $this->config['ZP_ACCOUNT']
		), $params);
		// Generate hash of request
		if (!empty($hash_order)) {
			$tmp = array();
			foreach ($hash_order as $v) {
				$tmp[$v] = @$request[$v];
			}
		}
		else {
			$tmp = $request;
		}

		$request['HASH'] = md5(implode(array_values($tmp)) . $this->config['ACT_PASSWORD']);

		$query = $this->config['API_URL'] . $action . '.php?' . http_build_query($request);
		if (function_exists('curl_init')) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_URL, $query);
			curl_setopt($curl, CURLOPT_TIMEOUT, 1);
			$result = curl_exec($curl);
			curl_close($curl);
		}
		if (empty($result)) {
			$result = @file_get_contents($query);
		}

		if (empty($result) || !$result = simplexml_load_string($result)) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, "[Office] Error executing query.\nRequest: ".$query."\nResponse: ".$result);
			return false;
		}
		else {
			return json_decode(json_encode($result), true);
		}
	}


	/**
	 * Method for verify and create user purse on remote service
	 *
	 * @param $params
	 *
	 * @return array|string
	 */
	public function createPurse($params) {
		if (!$this->modx->user->isAuthenticated()) {
			return $this->error($this->modx->lexicon('office_err_auth'));
		}

		/* @var zpUser $user */
		if (!$user = $this->getUser()) {
			$user = $this->modx->newObject('zpUser');
			$user->fromArray(array(
				'internalKey' => $this->modx->user->id
				,'active' => 0
				,'createdon' => time()
			));
		}
		else if ($user->get('active')) {
			return $this->error('office_zp_err_activated');
		}

		$fields = array('f_name', 's_name', 'm_name', 'phone');
		$errors = $values = array();
		foreach ($fields as $field) {
			if ((!isset($params[$field]) || trim($params[$field]) == '') && !$user->get($field)) {
				$errors[$field] = $this->modx->lexicon('office_zp_err_ns');
				continue;
			}
			else if (isset($params[$field]) && trim($params[$field]) != '') {
				$value = trim($params[$field]);
			}
			else {
				$value = $user->get($field);
			}

			if ($field == 'phone') {
				$value = preg_replace('/[^0-9]/', '', $value);
				if (strlen($value) != 11 || (!in_array($value[0], array(7,8)))) {
					$errors[$field] = $this->modx->lexicon('office_zp_err_value');
				}
				else if ($value[0] == 8) {
					$value = '7' . substr($value, 1);
				}
			}
			else {
				$value = preg_replace('/[^a-zа-яё]/iu', '', $value);
				if (empty($value)) {
					$errors[$field] = $this->modx->lexicon('office_zp_err_value');
				}
			}

			$values[$field] = $value;
		}

		if (!empty($errors)) {
			return $this->error('office_zp_err_form', array('errors' => $errors, 'values' => $values));
		}
		else {
			$user->fromArray($values);
		}

		$params = array(
			'ID_CLIENT' => $this->modx->user->id
			,'PHONE_CLIENT' => $user->get('phone')
			,'EMAIL_CLIENT' => $this->modx->user->get('username')
			,'F_NAME' => $user->get('f_name')
			,'S_NAME' => $user->get('s_name')
			,'M_NAME' => $user->get('m_name')
		);

		if ($result = $this->request('create_purse', $params)) {
			if ($result['Status'] != 1) {
				return $this->error($result['Message']);
			}
			else {
				$user->set('zp', $result['Account']);
				$user->save();
				return $this->success($result['Message']);
			}
		}
		else {
			return $this->error('office_zp_err_request');
		}
	}


	/**
	 * Method for activate user purse on remote service
	 *
	 * @param $params
	 *
	 * @return array|string
	 */
	public function activatePurse($params) {
		if (!$this->modx->user->isAuthenticated()) {
			return $this->error($this->modx->lexicon('office_err_auth'));
		}
		$errors = array();
		if (empty($params['code'])) {
			$errors['code'] = $this->modx->lexicon('office_zp_err_ns');

		}
		else if (!preg_match('/[0-9]{4}/', $params['code'])) {
			$errors['code'] = $this->modx->lexicon('office_zp_err_value');
		}

		if (!empty($errors)) {
			return $this->error('office_zp_err_form', array('errors' => $errors));
		}

		$user = $this->getUser();
		if (!$user || !$zp = $user->get('zp')) {
			return $this->error('office_zp_err_nouser');
		}

		$params = array(
			'CLIENT_ACCOUNT' => $zp
			,$this->config['activationType'] => $params['code']
		);
		if ($result = $this->request('create_purse', $params, array('ID_INTERFACE','ZP_ACCOUNT','CLIENT_ACCOUNT'))) {
			if ($result['Status'] != 1) {
				return $this->error($result['Message']);
			}
			else {
				$user->set('active', 1);
				$user->set('activatedon', time());
				$user->save();
				return $this->success($result['Message'], array('reload' => 1));
			}
		}
		else {
			return $this->error('office_zp_err_request');
		}
	}


	/**
	 * Returns user balance from remote service and saves it into zpUser
	 *
	 * @return array|string
	 */
	public function getBalance() {
		if (!$this->modx->user->isAuthenticated()) {
			return $this->error($this->modx->lexicon('office_err_auth'));
		}

		$user = $this->getUser();
		if (!$user || !$user->get('active') || !$zp = $user->get('zp')) {
			return $this->error('office_zp_err_nouser');
		}

		if ($result = $this->request('get_balance', array('CLIENT_ACCOUNT' => $zp))) {
			if ($result['Status'] != 1) {
				return $this->error($result['Message']);
			}
			else {
				$user->set('balance', $result['Balance']);
				$user->save();
				return $this->success($result['Message'], array('balance' => $this->moneyFormat($user->get('balance'))));
			}
		}
		else {
			return $this->error('office_zp_err_request');
		}
	}


	public function createPay($id_pay, $zp, $sum, $comment = '') {
		if (!$this->modx->user->isAuthenticated('mgr')) {
			return $this->error($this->modx->lexicon('office_err_auth'));
		}

		$hash_order = array('ID_PAY','ID_INTERFACE','ZP_ACCOUNT','SRC_ACCOUNT','DST_ACCOUNT','SUMMA_OUT','COMMENT','TYPE_TRANSFER');
		$params = array(
			'ID_PAY' => $id_pay
			,'DST_ACCOUNT' => $zp
			,'SRC_ACCOUNT' => $this->config['ZP_ACCOUNT']
			,'SUMMA_OUT' => number_format($sum, 2, '.', '')
			,'COMMENT' => empty($comment) ? 'Payment operation #'.$id_pay : $comment
			,'TYPE_TRANSFER' => 'PAY_ZP'
		);

		if ($result = $this->request('create_pay', $params, $hash_order)) {
			if ($result['Status'] != 1) {
				return $this->error($result['Message']);
			}
			else {
				$result['Comment'] = $params['COMMENT'];
				return $this->confirmPay($result);
			}
		}
		else {
			return $this->error('office_zp_err_request');
		}
	}


	public function confirmPay($params = array()) {
		if (!$this->modx->user->isAuthenticated('mgr')) {
			return $this->error($this->modx->lexicon('office_err_auth'));
		}

		$hash_order = array('ID_PAY','ID_OPERATION','ID_INTERFACE','ZP_ACCOUNT','SRC_ACCOUNT','DST_ACCOUNT','SUMMA_IN','SUMMA_OUT','COMMENT','TYPE_TRANSFER');
		$params = array(
			'ID_PAY' => @$params['IdPay']
			,'ID_OPERATION' => @$params['IdOperation']
			,'DST_ACCOUNT' => @$params['DstAccount']
			,'SRC_ACCOUNT' => @$params['SrcAccount']
			,'SUMMA_IN' => @$params['SummaIn']
			,'SUMMA_OUT' => @$params['SummaOut']
			,'TYPE_TRANSFER' => 'PAY_ZP'
			,'COMMENT' => @$params['Comment']
			//,'CONFIRM_CODE' => '0000'
		);

		if ($result = $this->request('create_pay', $params, $hash_order)) {
			if ($result['Status'] != 1) {
				return $this->error($result['Message']);
			}
			else {
				echo '<pre>';print_r($result);die;
				return $this->success($result);
			}
		}
		else {
			return $this->error('office_zp_err_request');
		}
	}


	public function getHistory($limit = 10, $offset = 0) {
		if (!$this->modx->user->isAuthenticated()) {
			return $this->error($this->modx->lexicon('office_err_auth'));
		}

		$user = $this->getUser();
		if (!$user->get('active') || !$zp = $user->get('zp')) {
			return $this->error('office_zp_err_nouser');
		}

		$hash_order = array('ID_INTERFACE','ZP_ACCOUNT','CLIENT_ACCOUNT','START_DATE','END_DATE');
		$params = array(
			'CLIENT_ACCOUNT' => $zp
			,'START_DATE' => '2013-06-10 00:00:00'
			,'END_DATE' => date('Y-m-d H:i:s')
			,'MAX_COUNT_PAGE' => $limit
			,'NUMBER_PAGE' => $offset > 0 ? floor($offset / $limit) : 0
		);

		if ($result = $this->request('get_history', $params, $hash_order)) {
			if ($result['Status'] != 1) {
				return $this->error($result['Message']);
			}
			else {
				return $this->success($result['Message'], print_r($result['Operations'],1));
			}
		}
		else {
			return $this->error('office_zp_err_request');
		}
	}
}

return 'officeZPaymentController';