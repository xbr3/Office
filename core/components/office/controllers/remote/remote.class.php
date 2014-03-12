<?php

require_once dirname(dirname(__FILE__)) . '/auth.class.php';

class officeRemoteAuthController extends officeAuthController {


	/** {@inheritDoc} */
	public function setDefault($config = array()) {
		$this->config = array_merge(array(
			'tplLogin' => 'tpl.Office.remote.login',
			'tplLogout' => 'tpl.Office.remote.logout',
			'updateUser' => true,
			'createUser' => true,
			'page_id' => $this->modx->getOption('office_auth_page_id'),

			'groups' => '',
			'loginResourceId' => 0,
			'logoutResourceId' => 0,
			'rememberme' => true,
			'loginContext' => '',
			'addContexts' => '',

			'hosts' => '',
			'key' => '',
			'authId' => '',
			'remote' => '',
			'HybridAuth' => false,
		), $config);

		$_SESSION['Office']['Remote'][$this->modx->context->key] = $this->config;
	}


	/**
	 * Login user to client site
	 * Or return
	 *
	 * This method must be called on client
	 *
	 */
	public function defaultAction() {
		if ($this->modx->user->isAuthenticated($this->modx->context->key)) {
			$user = $this->modx->user->toArray();
			$profile = $this->modx->user->Profile->toArray();
			$pls = array_merge($profile, $user);
			$pls['gravatar'] = 'http://gravatar.com/avatar/'.md5(strtolower($profile['email']));

			$output = $this->modx->getChunk($this->config['tplLogout'], $pls);
		}
		else {
			if (empty($this->config['remote'])) {
				$output = $this->modx->lexicon('office_auth_err_remote_required');
			}
			elseif (empty($this->config['key'])) {
				$output = $this->modx->lexicon('office_auth_err_key_required');
			}
			else {
				$tmp = $this->explodeUrl($this->config['remote']);
				$tmp['params']['action'] = 'remote/auth';
				$remote = $tmp['uri'] . '?' . http_build_query($tmp['params']);

				$output = $this->modx->getChunk($this->config['tplLogin'], array(
					'remote' => urldecode($remote),
					'error' => !empty($_SESSION['Office']['Auth']['error'])
						? $_SESSION['Office']['Auth']['error']
						: '',
				));
				unset($_SESSION['Office']['Auth']['error']);
			}
		}

		return $output;
	}


	/**
	 * Get information from remote server by given hash and login user
	 *
	 * This method must be called on client
	 *
	 */
	public function Login() {
		if (!empty($_GET['hash'])) {
			$tmp = $this->explodeUrl($this->config['remote']);
			$tmp['params']['action'] = 'remote/info';
			$tmp['params']['hash'] = $_GET['hash'];
			$remote = $tmp['uri'] . '?' . http_build_query($tmp['params']);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, urldecode($remote));
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_REFERER, $this->modx->makeUrl($this->modx->getOption('site_start'),'','','full'));
			if (!@ini_get('safe_mode') && !@ini_get('open_basedir')) {
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			}

			$data = curl_exec($ch);
			if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200 && !empty($data)) {
				if ($Cypher = $this->getCypher()) {
					$data = $Cypher->decrypt($data);
					$data = $this->modx->fromJSON($data);
				}
				else {
					$data = array();
				}

				if (!empty($data)) {
					$data['remote_key'] = $data['id'];
					$data['remote_data'] = array('remote' => $this->config['remote']);
					/** @var modUser $user */
					if ($user = $this->modx->getObject('modUser', array('remote_key' => $data['id']))) {
						$data['id'] = $user->get('id');
						/** @var modProcessorResponse $response */
						$response = $this->office->runProcessor('auth/update', $data);
						if ($response->isError()) {
							$errors = $this->formatProcessorErrors($response);
							$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] Unable to update user "'.@$data['username'].'". Message: '.$errors);
							$_SESSION['Office']['Auth']['error'] = $this->modx->lexicon('office_auth_err_update', array('errors' => $errors));
						}
						else {
							$tmp = $response->getObject();
							/** @var modUser $user */
							$user = $this->modx->getObject('modUser', $tmp['id']);
						}
					}
					elseif ($this->config['createUser']) {
						unset($data['id']);
						$data['groups'] = $this->config['groups'];
						/** @var modProcessorResponse $response */
						$response = $this->office->runProcessor('auth/create', $data);
						if ($response->isError()) {
							$errors = $this->formatProcessorErrors($response);
							$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] Unable to create user "'.@$data['username'].'". Message: '.$errors);
							$_SESSION['Office']['Auth']['error'] = $this->modx->lexicon('office_auth_err_create', array('errors' => $errors));
						}
						else {
							$tmp = $response->getObject();
							/** @var modUser $user */
							$user = $this->modx->getObject('modUser', $tmp['id']);
						}
					}
					else {
						$_SESSION['Office']['Auth']['error'] = $this->modx->lexicon('office_auth_err_create', array('errors' => $this->modx->lexicon('office_auth_err_create_disabled')));
					}

					if (!empty($user) && $user instanceof modUser) {
						$_SESSION['Office']['Auth']['verified'] = true;
						$login_data = array(
							'username' => $user->get('username'),
							'password' => md5($user->get('username')),
							'rememberme' => !empty($rememberme)
						);
						if (!empty($loginContext)) {$login_data['login_context'] = $loginContext;}
						if (!empty($addContexts)) {$login_data['add_contexts'] = $addContexts;}

						$response = $this->modx->runProcessor('security/login', $login_data);
						if ($response->isError()) {
							$_SESSION['Office']['Auth']['error'] = $this->modx->lexicon('office_auth_err_login', array('errors' => $this->formatProcessorErrors($response)));
						}
					}
				}
			}
			else {
				$this->modx->log(modX::LOG_LEVEL_INFO, '[Office] Could not login user: remote server returned status "'.curl_getinfo($ch, CURLINFO_HTTP_CODE).'".');
			}
			curl_close($ch);
		}

		$this->sendRedirect('login');
	}


	/**
	 * Client - server logout
	 */
	public function Logout($redirect = true) {
		// Redirect to server
		if (!empty($this->config['remote']) && empty($_GET['provider']) && $this->modx->user->isAuthenticated($this->modx->context->key)) {
			$tmp = $this->explodeUrl($this->config['remote']);
			$tmp['params']['action'] = 'remote/logout';
			$tmp['params']['provider'] = 'client';

			$redirect = $tmp['uri'] . '?' . http_build_query($tmp['params']);
			$this->modx->sendRedirect($redirect);
		}
		elseif (!empty($_GET['provider'])) {
			// Server logout
			if ($_GET['provider'] == 'client') {
				parent::Logout(false);

				$redirect = !empty($_COOKIE['OFFICE_REMOTE_REDIRECT'])
					? $_COOKIE['OFFICE_REMOTE_REDIRECT']
					: $_SERVER['HTTP_REFERER'];
				$tmp = $this->explodeUrl($redirect);
				$tmp['params']['action'] = 'remote/logout';
				$tmp['params']['provider'] = 'server';
				// Back to client
				$redirect = $tmp['uri'] . '?' . http_build_query($tmp['params']);
				$this->modx->sendRedirect($redirect);
			}
			// Client logout
			elseif ($_GET['provider'] == 'server') {
				parent::Logout();
			}
		}

		if ($redirect) {
			parent::sendRedirect('logout');
		}
	}


	/**
	 * Redirect user to login form or to client site
	 * Uses config[hosts] and config[loginId]
	 *
	 * This method must be called on server
	 *
	 */
	public function Auth() {
		if ($this->checkHost()) {
			setcookie('OFFICE_REMOTE_REDIRECT', $_SERVER['HTTP_REFERER'], time() + 3600);
		}
		else {
			$_SERVER['HTTP_REFERER'] = '';
		}

		if ($this->modx->user->isAuthenticated($this->modx->context->key) && (!empty($_COOKIE['OFFICE_REMOTE_REDIRECT']) || !empty($_SERVER['HTTP_REFERER']))) {
			$redirect = !empty($_COOKIE['OFFICE_REMOTE_REDIRECT'])
				? $_COOKIE['OFFICE_REMOTE_REDIRECT']
				: $_SERVER['HTTP_REFERER'];

			$tmp = $this->explodeUrl($redirect);
			if (empty($error)) {
				/** @var modProcessorResponse $response */
				$response = $this->office->runProcessor('auth/get', array('id' => $this->modx->user->id));
				$data = $response->getObject();
				if (!empty($data['photo']) && strpos($data['photo'], '://') === false) {
					$data['photo'] = rtrim($this->modx->getOption('site_url'), '/') . '/' . ltrim($data['photo'], '/');
				}

				$tmp['params']['action'] = 'remote/login';
				$tmp['params']['hash'] = $hash = sha1(md5(rand() . time() . rand()));

				unset(
					$data['sudo'],
					$data['internalKey'],
					$data['logincount'],
					$data['sessionid'],
					$data['session_stale'],
					$data['remote_key'],
					$data['remote_data'],
					$data['primary_group'],
					$data['class_key'],
					$data['salt']
				);
				$this->modx->cacheManager->set('office/' . $hash, $data, 10);
			}
			setcookie('OFFICE_REMOTE_REDIRECT', '', time() + 3600);
			$redirect = $tmp['uri'];
			if (!empty($tmp['params'])) {
				$redirect .= '?' . http_build_query($tmp['params']);
			}
		}
		else {
			if (!$id = $this->config['authId']) {
				$id = $this->modx->resource->get('parent');
			}
			$redirect = $this->modx->makeUrl($id);
		}

		$this->modx->sendRedirect($redirect);
	}


	/**
	 * Get stored userinfo from cache, encrypt it and send to client
	 *
	 * This method must be called on server
	 *
	 */
	public function Info() {
		if (!$this->checkHost() || empty($_GET['hash'])) {
			header("HTTP/1.1 401 Unauthorized");
		}
		elseif ($data = $this->modx->cacheManager->get('office/' . $_GET['hash'])) {
			/** @var Cypher $Cypher */
			if ($Cypher = $this->getCypher()) {
				$data = $this->modx->toJSON($data);
				$data = $Cypher->encrypt($data);
				$this->modx->cacheManager->delete('office/' . $_GET['hash']);
			}
			else {
				header("HTTP/1.1 500 Internal server error");
				$data = '';
			}
			echo $data;
		}
		else {
			header("HTTP/1.1 410 Gone");
		}

		exit();
	}


	/**
	 * Check referer by given hosts
	 *
	 * @return bool
	 */
	public function checkHost() {
		$check = true;

		if (!empty($this->config['hosts'])) {
			$hosts = array_map('trim', explode(',', $this->config['hosts']));
			$hosts = array_map('preg_quote', $hosts);

			$pcre = '/\b('.implode('|',$hosts).')\b/i';

			if (empty($_SERVER['HTTP_REFERER']) || !preg_match($pcre, $_SERVER['HTTP_REFERER'])) {
				return false;
			}
		}

		return $check;
	}


	/**
	 * Get cypher service
	 *
	 * @return bool|Cypher
	 */
	public function getCypher() {
		if (!empty($this->config['key'])) {
			require_once 'cypher.class.php';
			/** @var Cypher $Cypher */
			$Cypher = new Cypher();
			$Cypher->setKey($this->config['key'], true);
		}
		else {
			$Cypher = false;
			$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] Could not protect data: you must specify the same nonempty keys at server and client. For example "&key=`8Hy76Jkw`".');
		}

		return $Cypher;
	}


	/**
	 * Explodes url to uri and params
	 *
	 * @param $url
	 *
	 * @return array
	 */
	public function explodeUrl($url) {
		if (strpos($url, '?') !== false) {
			list($uri, $tmp) = explode('?', $url);
			$tmp = explode('&', $tmp);
			$params = array();
			foreach ($tmp as $v) {
				if (strpos($v, '=') !== false) {
					$tmp3 = explode('=', $v);
					$params[$tmp3[0]] = $tmp3[1];
				}
			}
		}
		else {
			$uri = $url;
			$params = array();
		}

		return array(
			'uri' => $uri,
			'params' => $params,
		);
	}

}

return 'officeRemoteAuthController';