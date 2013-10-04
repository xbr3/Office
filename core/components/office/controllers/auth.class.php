<?php

class officeAuthController extends officeDefaultController {

	public function setDefault($config = array()) {
		if (defined('MODX_ACTION_MODE') && MODX_ACTION_MODE && !empty($_SESSION['Office']['Auth'][$this->modx->context->key])) {
			$this->config = $_SESSION['Office']['Auth'][$this->modx->context->key];
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
				,'loginContext' => ''
				,'addContexts' => ''

				,'HybridAuth' => true
				,'providerTpl' => 'tpl.HybridAuth.provider'
				,'activeProviderTpl' => 'tpl.HybridAuth.provider.active'
			), $config);
		}

		$this->config['page_id'] = $this->modx->getOption('office_auth_page_id');
		if ($this->modx->resource->id && $this->modx->resource->id != $this->config['page_id']) {
			/* @var modContextSetting $setting */
			$key = array('key' => 'office_auth_page_id', 'context_key' => $this->modx->context->key);
			if (!$setting = $this->modx->getObject('modContextSetting', $key)) {
				$setting = $this->modx->newObject('modContextSetting');
				$setting->fromArray($key, '', true, true);
				$setting->set('value', $this->modx->resource->id);
				$setting->save();
			}

			/* @var modSystemSetting $setting */
			if (!$setting = $this->modx->getObject('modSystemSetting', 'office_auth_page_id')) {
				$setting = $this->modx->newObject('modSystemSetting');
				$setting->set('key', 'office_auth_page_id');
				$setting->set('value', $this->modx->resource->id);
				$setting->save();
			}

			$this->config['page_id'] = $this->modx->resource->id;
		}

		if (empty($this->config['loginContext'])) {$this->config['loginContext'] = $this->modx->context->key;}
		//if (empty($this->config['loginResourceId'])) {$this->config['loginResourceId'] = $this->config['page_id'];}
		//if (empty($this->config['logoutResourceId'])) {$this->config['logoutResourceId'] = $this->config['page_id'];}
		$_SESSION['Office']['Auth'][$this->modx->context->key] = $this->config;
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

		$pls = array();
		if ($this->config['HybridAuth'] && file_exists(MODX_CORE_PATH . 'components/hybridauth/')) {
			if ($this->modx->loadClass('hybridauth', MODX_CORE_PATH . 'components/hybridauth/model/hybridauth/', false, true)) {
				$HybridAuth = new HybridAuth($this->modx, $this->config);
				$HybridAuth->initialize($this->modx->context->key);
				$pls['providers'] = $HybridAuth->getProvidersLinks();
			}
		}

		if (!$this->modx->user->isAuthenticated($this->modx->context->key)) {
			return $this->modx->getChunk($this->config['tplLogin'], $pls);
		}
		else {
			$user = $this->modx->user->toArray();
			$profile = $this->modx->user->Profile->toArray();
			$pls = array_merge($pls, $profile, $user);
			$pls['gravatar'] = 'http://gravatar.com/avatar/'.md5(strtolower($profile['email']));

			return $this->modx->getChunk($this->config['tplLogout'], $pls);
		}
	}


	public function sendLink($data = array()) {
		$email = strtolower(trim(@$data['email']));
		if ($this->modx->user->isAuthenticated($this->modx->context->key)) {
			return $this->success(
				$this->modx->lexicon('office_auth_err_already_logged')
				,array('refresh' => $this->modx->makeUrl($this->config['loginResourceId'], '', '', 'full'))
			);
		}
		if (empty($email)) {
			return $this->error($this->modx->lexicon('office_auth_err_email_ns'));
		}
		else if (!preg_match('/^[^@а-яА-Я]+@[^@а-яА-Я]+(?<!\.)\.[^\.а-яА-Я]{2,}$/m', $email)) {
			return $this->error($this->modx->lexicon('office_auth_err_email'));
		}

		if ($this->modx->getCount('modUser', array('username' => $email)) || $this->modx->getCount('modUserProfile', array('email' => $email))) {
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
		$id = !empty($this->config['loginResourceId'])
			? $this->config['loginResourceId']
			: (!empty($_REQUEST['pageId'])
				? $_REQUEST['pageId']
				: $this->modx->getOption('site_start')
			);
		$this->modx->log(1, $id);
		$link = $this->modx->makeUrl($id, '', array(
				'action' => 'auth/login'
				,'email' => $email
				,'hash' => $activationHash.':'.$newPassword
			), 'full');

		$content = $this->modx->getChunk(
			$this->config['tplActivate']
			,array_merge(
				$user->getOne('Profile')->toArray()
				,$user->toArray()
				,array('link' => $link)
			)
		);
		$maxIterations= (integer) $this->modx->getOption('parser_max_iterations', null, 10);
		$this->modx->getParser()->processElementTags('', $content, false, false, '[[', ']]', array(), $maxIterations);
		$this->modx->getParser()->processElementTags('', $content, true, true, '[[', ']]', array(), $maxIterations);
		$send = $user->sendEmail(
			$content
			,array(
				'subject' => $this->modx->lexicon('office_auth_email_subject')
			)
		);

		if ($send !== true) {
			$errors = $this->modx->mail->mailer->errorInfo;
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

				$login_data = array(
					'username' => $data['email']
					,'password' => $password
					,'rememberme' => $this->config['rememberme']
					,'login_context' => $this->config['loginContext']
				);
				if (!empty($this->config['addContexts'])) {$login_data['add_contexts'] = $this->config['addContexts'];}

				$response = $this->modx->runProcessor('security/login', $login_data);
				if ($response->isError()) {
					$errors = implode(', ',$response->getAllErrors());
					$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] unable to login user '.$data['email'].'. Message: '.$errors);
					return $this->modx->lexicon('office_auth_err_login', array('errors' => $errors));
				}
				$this->sendRedirect('login');
			}
		}

		$this->sendRedirect('login');
	}


	public function Logout() {
		if ($this->config['HybridAuth'] && file_exists(MODX_CORE_PATH . 'components/hybridauth/')) {
			if ($this->modx->loadClass('hybridauth', MODX_CORE_PATH . 'components/hybridauth/model/hybridauth/', false, true)) {
				$HybridAuth = new HybridAuth($this->modx, $this->config);
				@$HybridAuth->Hybrid_Auth->logoutAllProviders();
			}
		}

		$logout_data = array();
		if (!empty($this->config['loginContext'])) {$logout_data['login_context'] = $this->config['loginContext'];}
		if (!empty($this->config['addContexts'])) {$logout_data['add_contexts'] = $this->config['addContexts'];}

		$response = $this->modx->runProcessor('security/logout', $logout_data);
		if ($response->isError()) {
			$errors = implode(', ',$response->getAllErrors());
			$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] logout error. Username: '.$this->modx->user->get('username').', uid: '.$this->modx->user->get('id').'. Message: '.$errors);
		}

		$this->sendRedirect('logout');
	}



	/**
	 * Reloads site page on various events.
	 *
	 * @param string $action The action to do
	 * @return void
	 */
	function sendRedirect($action = '') {
		$error_pages = array($this->modx->getOption('error_page'), $this->modx->getOption('unauthorized_page'));

		if ($action == 'login' && $this->config['loginResourceId']) {
			if (in_array($this->config['loginResourceId'], $error_pages)) {
				$this->config['loginResourceId'] = $this->config['page_id'];
			}
			$url = $this->modx->makeUrl($this->config['loginResourceId'], '', '', 'full');
		}
		else if ($action == 'logout' && $this->config['logoutResourceId']) {
			if (in_array($this->config['logoutResourceId'], $error_pages)) {
				$this->config['logoutResourceId'] = $this->config['page_id'];
			}
			$url = $this->modx->makeUrl($this->config['logoutResourceId'], '', '', 'full');
		}
		else {
			$request = preg_replace('#^'.$this->modx->getOption('base_url').'#', '', $_SERVER['REQUEST_URI']);
			$url = $this->modx->getOption('site_url') . ltrim($request, '/');

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
	}

}

return 'officeAuthController';