<?php

class officeAuthController extends officeDefaultController {


	/**
	 * Default config
	 *
	 * @param array $config
	 */
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
				,'page_id' => $this->modx->getOption('office_auth_page_id')

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

		// Save main page_id if not exists. It will be used for another contexts that has no snippet call
		/* @var modSystemSetting $setting */
		if (!$setting = $this->modx->getObject('modSystemSetting', 'office_auth_page_id')) {
			$setting = $this->modx->newObject('modSystemSetting');
			$setting->set('key', 'office_auth_page_id');
			$setting->set('value', $this->modx->resource->id);
			$setting->set('namespace', 'office');
			$setting->set('area', 'office_auth');
			$setting->save();
		}

		if (empty($this->config['loginContext'])) {$this->config['loginContext'] = $this->modx->context->key;}
		$_SESSION['Office']['Auth'][$this->modx->context->key] = $this->config;
	}


	/**
	 * Returns array with language topics
	 *
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('office:auth');
	}


	/**
	 * Returns login form
	 *
	 * @return string
	 */
	public function defaultAction() {
		if ($this->modx->resource->id && $this->modx->resource->id != $this->config['page_id']) {
			// Save page_id for current context
			/* @var modContextSetting $setting */
			$key = array('key' => 'office_auth_page_id', 'context_key' => $this->modx->context->key);
			if (!$setting = $this->modx->getObject('modContextSetting', $key)) {
				$setting = $this->modx->newObject('modContextSetting');
			}
			// It will be updated on every snippet call
			$setting->fromArray($key, '', true, true);
			$setting->set('value', $this->modx->resource->id);
			$setting->save();

			$this->config['page_id'] = $this->modx->resource->id;
		}

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
			// Login errors
			if (!empty($_SESSION['Office']['Auth']['error'])) {
				$pls['error'] = $_SESSION['Office']['Auth']['error'];
			}
			elseif (!empty($_SESSION['HA']['error'])) {
				$pls['error'] = $_SESSION['HA']['error'];
			}
			elseif (!empty($_SESSION['Office']['Auth']['error']) && !empty($_SESSION['HA']['error'])) {
				$pls['error'] = $_SESSION['Office']['Auth']['error'] . '<br/>' . $_SESSION['HA']['error'];
			}
			unset($_SESSION['Office']['Auth']['error'],$_SESSION['HA']['error']);

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


	/**
	 * Check email of user and start login process
	 *
	 * @param array $data
	 *
	 * @return array|string
	 */
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
		elseif (!preg_match('/^[^@а-яА-Я]+@[^@а-яА-Я]+(?<!\.)\.[^\.а-яА-Я]{2,}$/m', $email)) {
			return $this->error($this->modx->lexicon('office_auth_err_email'));
		}

		if ($this->modx->getCount('modUserProfile', array('email' => $email))) {
			return $this->sendMail($email);
		}
		else {
			return $this->createUser($email);
		}
	}


	/**
	 * Generate new password and send activation link for existing user
	 *
	 * @param $email
	 *
	 * @return array|string
	 */
	public function sendMail($email) {
		/** @var modUser $user */
		$q = $this->modx->newQuery('modUser');
		$q->innerJoin('modUserProfile', 'modUserProfile', 'modUser.id = modUserProfile.internalKey');
		//$q->where(array('modUser.username' => $email, 'OR:modUserProfile.email:=' => $email));
		$q->where(array('modUserProfile.email' => $email));
		$q->select($this->modx->getSelectColumns('modUser','modUser') . ',`modUserProfile`.`email`');
		if (!$user = $this->modx->getObject('modUser', $q)) {
			return $this->error($this->modx->lexicon('office_auth_err_email_nf'));
		}
		elseif ($user->sudo) {
			return $this->error($this->modx->lexicon('office_auth_err_sudo_user'));
		}

		$activationHash = md5(uniqid(md5($user->email . '/' . $user->id), true));

		/** @var modDbRegister $registry */
		$registry = $this->modx->getService('registry', 'registry.modRegistry')->getRegister('user', 'registry.modDbRegister');
		$registry->connect();

		// checking for already sent activation link
		$registry->subscribe('/pwd/reset/' . md5($user->username));
		$res = $registry->read(array('poll_limit' => 1, 'remove_read' => false));
		if (!empty($res)) {
			return $this->error($this->modx->lexicon('office_auth_err_already_sent'));
		}

		$registry->subscribe('/pwd/reset/');
		$registry->send('/pwd/reset/', array(md5($user->username) => $activationHash), array('ttl' => $this->config['linkTTL']));

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


	/**
	 * Create new user and send activation email
	 *
	 * @param $email
	 *
	 * @return array|string
	 */
	public function createUser($email) {
		$response = $this->office->runProcessor('auth/create', array(
			'username' => $email
			,'email' => $email
			,'active' => false
			,'blocked' => false
			,'groups' => $this->config['groups']
		));

		if ($response->isError()) {
			$errors = $this->formatProcessorErrors($response);
			$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] Unable to create user '.$email.'. Message: '.$errors);
			return $this->error($this->modx->lexicon('office_auth_err_create', array('errors' => $errors)));
		}
		else {
			return $this->sendMail($email);
		}
	}


	/**
	 * Login to site
	 *
	 * @param $data
	 *
	 * @return void|string
	 */
	public function Login($data) {
		/** @var modUser $user */
		$q = $this->modx->newQuery('modUser');
		$q->innerJoin('modUserProfile', 'modUserProfile', 'modUser.id = modUserProfile.internalKey');
		//$q->where(array('modUser.username' => @$data['email'], 'OR:modUserProfile.email:=' => @$data['email']));
		$q->where(array('modUserProfile.email' => @$data['email']));
		if ($user = $this->modx->getObject('modUser', $q)) {
			list($hash, $password) = explode(':', urldecode(@$data['hash']));
			$activate = $user->activatePassword($hash);
			if ($activate === true) {
				if (!$user->active) {
					$user->set('active', true);
					$user->save();
				}

				$login_data = array(
					'username' => $user->username
					,'password' => $password
					,'rememberme' => $this->config['rememberme']
					,'login_context' => $this->config['loginContext']
				);
				if (!empty($this->config['addContexts'])) {
					$login_data['add_contexts'] = $this->config['addContexts'];
				}

				$response = $this->modx->runProcessor('security/login', $login_data);
				if ($response->isError()) {
					$errors = $this->formatProcessorErrors($response);
					$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] unable to login user '.$data['email'].'. Message: '.$errors);
					return $this->modx->lexicon('office_auth_err_login', array('errors' => $errors));
				}
			}
		}
		$this->sendRedirect('login');
		return true;
	}


	/**
	 * Logount current user
	 */
	public function Logout($redirect = true) {
		if ($this->modx->user->isAuthenticated($this->modx->context->key)) {
			if (!empty($this->config['HybridAuth']) && file_exists(MODX_CORE_PATH . 'components/hybridauth/')) {
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
				$errors = $this->formatProcessorErrors($response);
				$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] logout error. Username: '.$this->modx->user->get('username').', uid: '.$this->modx->user->get('id').'. Message: '.$errors);
			}
		}

		if ($redirect) {
			$this->sendRedirect('logout');
		}
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
		elseif ($action == 'logout' && $this->config['logoutResourceId']) {
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


	/**
	 * More convenient error messages
	 *
	 * @param modProcessorResponse $response
	 * @param string $glue
	 *
	 * @return string
	 */
	public function formatProcessorErrors(modProcessorResponse $response, $glue = 'br') {
		$errormsgs = array();

		if ($response->hasMessage()) {
			$errormsgs[] = $response->getMessage();
		}
		if ($response->hasFieldErrors()) {
			if ($errors = $response->getFieldErrors()) {
				foreach ($errors as $error) {
					$errormsgs[] = $error->message;
				}
			}
		}

		return implode($glue, $errormsgs);
	}
}

return 'officeAuthController';