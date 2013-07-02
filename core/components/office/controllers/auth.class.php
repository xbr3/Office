<?php

class officeAuthController extends officeDefaultController {

	public function setDefault($config = array()) {
		if (defined('MODX_ACTION_MODE') && MODX_ACTION_MODE && !empty($_SESSION['Office']['Auth'])) {
			$this->config = $_SESSION['Office']['Auth'];
			$this->config['json_response'] = true;
		}
		else {
			$this->config = array_merge(array(
				'tplLogin' => 'tpl.Office.auth.login'
				,'tplLogout' => 'tpl.Office.auth.logout'
				,'tplActivate' => 'tpl.Office.auth.activate'

				,'siteUrl' => $this->modx->getOption('site_url')
				,'linkTTL' => 600

				,'groups' => ''
				,'loginResourceId' => 0
				,'logoutResourceId' => 0
				,'rememberme' => true
				,'loginContext' => $this->modx->context->key
				,'addContexts' => ''
			), $config);

			$_SESSION['Office']['Auth'] = $this->config;
		}

		$page_id = $this->modx->getOption('office_auth_page_id');
		if (empty($page_id)) {
			/* @var modSystemSetting $setting */
			if ($setting = $this->modx->getObject('modSystemSetting', 'office_auth_page_id')) {
				$setting->set('value', $this->modx->resource->id);
				$setting->save();
			}
		}

		if (empty($this->config['loginResourceId'])) {$this->config['loginResourceId'] = $page_id;}
		if (empty($this->config['logoutResourceId'])) {$this->config['logoutResourceId'] = $page_id;}
	}


	public function getLanguageTopics() {
		return array('office:auth');
	}


	public function defaultAction() {
		$config = $this->office->makePlaceholders($this->office->config);
		if ($css = trim($this->modx->getOption('office_auth_frontend_css'))) {
			$this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
		}
		if ($js = trim($this->modx->getOption('office_auth_frontend_js', null, '[[+jsUrl]]auth/default.js'))) {
			$this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));
		}

		if (!$this->modx->user->isAuthenticated()) {
			return $this->modx->getChunk($this->config['tplLogin']);
		}
		else {
			$user = $this->modx->user->toArray();
			$profile = $this->modx->user->getOne('Profile')->toArray();
			$user['gravatar'] = 'http://www.gravatar.com/avatar/'.md5(strtolower($profile['email']));
			return $this->modx->getChunk($this->config['tplLogout'], array_merge($profile, $user));
		}
	}


	public function sendLink($data = array()) {
		$email = trim(@$data['email']);
		if ($this->modx->user->isAuthenticated()) {
			return $this->success(
				$this->modx->lexicon('office_auth_err_already_logged')
				,array('refresh' => $this->modx->makeUrl($this->config['loginResourceId'], '', '', 'full'))
			);
		}
		if (empty($email)) {
			return $this->error($this->modx->lexicon('office_auth_err_email_ns'));
		}
		else if (!preg_match('/.+@.+..+/i', $email)) {
			return $this->error($this->modx->lexicon('office_auth_err_email'));
		}

		if ($this->modx->getCount('modUser', array('username' => $email))) {
			return $this->sendMail($email);
		}
		else {
			return $this->createUser($email);
		}
	}


	public function sendMail($email) {
		/* @var modUser $user */
		if (!$user = $this->modx->getObject('modUser', array('username' => $email))) {
			return $this->error($this->modx->lexicon('office_auth_err_email_nf'));
		}

		$activationHash = md5(uniqid(md5($user->get('email') . '/' . $user->get('id')), true));

		$this->modx->getService('registry', 'registry.modRegistry');
		$this->modx->registry->getRegister('user', 'registry.modDbRegister');
		$this->modx->registry->user->connect();

		// checking for already sent activation link
		$this->modx->registry->user->subscribe('/pwd/reset/' . md5($user->get('username')));
		$res = $this->modx->registry->user->read(array('poll_limit' => 1, 'remove_read' => false));
		if (!empty($res)) {
			return $this->error($this->modx->lexicon('office_auth_err_already_sent'));
		}

		$this->modx->registry->user->subscribe('/pwd/reset/');
		$this->modx->registry->user->send('/pwd/reset/', array(md5($user->get('username')) => $activationHash), array('ttl' => $this->config['linkTTL']));

		$newPassword = $user->generatePassword();

		$user->set('cachepwd', $newPassword);
		$user->save();

		/* send activation email */
		$link = $this->modx->makeUrl($this->config['loginResourceId'], '', array(
				'action' => 'auth/login'
				,'email' => $email
				,'hash' => $activationHash.':'.$newPassword
			), 'full');

		$send = $user->sendEmail(
			$this->modx->getChunk(
				$this->config['tplActivate']
				,array_merge(
					$user->getOne('Profile')->toArray()
					,$user->toArray()
					,array('link' => $link)
				)
			)
			,array(
				'subject' => $this->modx->lexicon('office_auth_email_subject')
			)
		);

		if ($send !== true) {
			$errors = $this->modx->mail->mailer->errorInfo();
			$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] Unable to send email to '.$email.'. Message: '.$errors);
			return $this->error($this->modx->lexicon('office_auth_err_send', array('errors' => $errors)));
		}

		return $this->success($this->modx->lexicon('office_auth_email_send'));
	}


	public function createUser($email) {
		$response = $this->office->runProcessor('auth/create', array(
			'username' => $email
			,'email' => $email
			,'active' => false
			,'blocked' => false
			,'groups' => $this->config['groups']
		));

		if ($response->isError()) {
			$errors = implode(', ', $response->getAllErrors());
			$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] Unable to create user '.$email.'. Message: '.$errors);
			return $this->error($this->modx->lexicon('office_auth_err_create', array('errors' => $errors)));

		}
		else {
			return $this->sendMail($email);
		}
	}


	public function Login($data) {
		/* @var modUser $user */
		if ($user = $this->modx->getObject('modUser', array('username' => @$data['email']))) {
			list($hash, $password) = explode(':', @$data['hash']);
			$activate = $user->activatePassword($hash);
			if ($activate === true) {
				$user->set('active', 1);
				$user->save();

				$data = array(
					'username' => $data['email']
					,'password' => $password
					,'rememberme' => $this->config['rememberme']
					,'loginContext' => $this->config['loginContext']
					,'addContexts' => $this->config['addContexts']
				);
				$response = $this->modx->runProcessor('security/login', $data);
				if ($response->isError()) {
					$errors = implode(', ',$response->getAllErrors());
					$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] unable to login user '.$data['email'].'. Message: '.$errors);
					return $this->modx->lexicon('office_auth_err_login', array('errors' => $errors));
				}
				return $this->sendRedirect('login');
			}
		}

		return $this->sendRedirect();
	}


	public function Logout() {
		$response = $this->modx->runProcessor('security/logout');
		if ($response->isError()) {
			$errors = implode(', ',$response->getAllErrors());
			$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] logout error. Username: '.$this->modx->user->get('username').', uid: '.$this->modx->user->get('id').'. Message: '.$errors);
		}

		return $this->sendRedirect('logout');
	}


	/*
	 * Reloads site page on various events.
	 *
	 * @param string $action The action to do
	 * @return nothing
	 * */
	function sendRedirect($action = '') {
		if ($action == 'login' && $this->config['loginResourceId']) {
			$url = $this->modx->makeUrl($this->config['loginResourceId'],'','','full');
		}
		else if ($action == 'logout' && $this->config['logoutResourceId']) {
			$url = $this->modx->makeUrl($this->config['logoutResourceId'],'','','full');
		}
		else {
			$url = $this->config['siteUrl'] . substr($_SERVER['REQUEST_URI'], 1);
			$pos = strpos($url, '?');
			if ($pos !== false) {
				$arr = explode('&',substr($url, $pos+1));
				$url = substr($url, 0, $pos);
				if (count($arr) > 1) {
					foreach ($arr as $k => $v) {
						if (preg_match('/(action|provider|email|hash)+/i', $v, $matches)) {
							unset($arr[$k]);
						}
					}
					if (!empty($arr)) {
						$url = $url . '?' . implode('&', $arr);
					}
				}
			}
		}

		$this->modx->sendRedirect($url);
		return '';
	}

}

return 'officeAuthController';