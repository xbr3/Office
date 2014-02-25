<?php

class officeProfileController extends officeDefaultController {

	public function setDefault($config = array()) {
		if (defined('MODX_ACTION_MODE') && MODX_ACTION_MODE && !empty($_SESSION['Office']['Profile'][$this->modx->context->key])) {
			$this->config = $_SESSION['Office']['Profile'][$this->modx->context->key];
			$this->config['json_response'] = true;
		}
		else {
			$this->config = array_merge(array(
				'tplProfile' => 'tpl.Office.profile.form',
				'tplActivate' => 'tpl.Office.profile.activate',

				'profileFields' => 'username:50,email:50,fullname:50,phone:12,mobilephone:12,dob:10,gender,address,country,city,state,zip,fax,photo,comment,website',
				'requiredFields' => 'username,email,fullname',

				'HybridAuth' => true,
				'providerTpl' => 'tpl.HybridAuth.provider',
				'activeProviderTpl' => 'tpl.HybridAuth.provider.active',

				'page_id' => $this->modx->getOption('office_profile_page_id'),

				'avatarPath' => 'images/users/',
				'avatarParams' => '{"w":200,"h":200,"zc":0,"bg":"ffffff","f":"jpg"}',
			), $config);
		}

		// Save main page_id if not exists. It will be used for another contexts that has no snippet call
		/* @var modSystemSetting $setting */
		if (!$setting = $this->modx->getObject('modSystemSetting', 'office_profile_page_id')) {
			$setting = $this->modx->newObject('modSystemSetting');
			$setting->set('key', 'office_profile_page_id');
			$setting->set('value', $this->modx->resource->id);
			$setting->set('namespace', 'office');
			$setting->set('area', 'office_profile');
			$setting->save();
		}

		$_SESSION['Office']['Profile'][$this->modx->context->key] = $this->config;
	}


	/**
	 * Returns array with language topics
	 *
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('office:profile','core:user');
	}


	/**
	 * Returns profile form
	 *
	 * @return string
	 */
	public function defaultAction() {
		if ($this->modx->resource->id && $this->modx->resource->id != $this->config['page_id']) {
			// Save page_id for current context
			/* @var modContextSetting $setting */
			$key = array('key' => 'office_profile_page_id', 'context_key' => $this->modx->context->key);
			if (!$setting = $this->modx->getObject('modContextSetting', $key)) {
				$setting = $this->modx->newObject('modContextSetting');
			}
			// It will be updated on every snippet call
			$setting->fromArray($key, '', true, true);
			$setting->set('value', $this->modx->resource->id);
			$setting->save();

			$this->config['page_id'] = $this->modx->resource->id;
		}

		if (!$this->modx->user->isAuthenticated($this->modx->context->key)) {return '';}

		$config = $this->office->makePlaceholders($this->office->config);
		if ($css = trim($this->modx->getOption('office_profile_frontend_css', null, '[[+cssUrl]]profile/default.css'))) {
			$this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
		}
		if ($js = trim($this->modx->getOption('office_profile_frontend_js', null, '[[+jsUrl]]profile/default.js'))) {
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

		if ($this->modx->resource->id != $this->config['page_id']) {
			/* @var modContextSetting $setting */
			$key = array('key' => 'office_profile_page_id', 'context_key' => $this->modx->context->key);
			if (!$setting = $this->modx->getObject('modContextSetting', $key)) {
				$setting = $this->modx->newObject('modContextSetting');
				$setting->fromArray($key, '', true, true);
			}
			$setting->set('value', $this->modx->resource->id);
			$setting->save();
			$this->config['page_id'] = $this->modx->resource->id;
		}

		$user = $this->modx->user->toArray();
		$profile = $this->modx->user->Profile->toArray();
		$pls = array_merge($pls, $profile, $user);
		if (!empty($_GET['off_req'])) {
			$required = explode('-', $_GET['off_req']);
			foreach ($required as $v) {
				$pls['error_'.$v] = $this->modx->lexicon('office_profile_err_field_'.$v);
			}
		}
		$pls['gravatar'] = 'http://gravatar.com/avatar/'.md5(strtolower($profile['email']));

		return $this->modx->getChunk($this->config['tplProfile'], $pls);
	}


	/**
	 * Updates profile of user
	 *
	 * @param array $data
	 *
	 * @return array|string
	 */
	public function Update($data = array()) {
		if (!$this->modx->user->isAuthenticated($this->modx->context->key)) {
			return $this->error($this->modx->lexicon('office_err_auth'));
		}

		$requiredFields = array_map('trim', explode(',', $this->config['requiredFields']));

		$fields = array(
			'requiredFields' => $requiredFields,
			'avatarPath' => $this->config['avatarPath'],
			'avatarParams' => $this->config['avatarParams'],
		);
		$profileFields = explode(',', $this->config['profileFields']);
		$profileFields = array_merge($profileFields, $requiredFields);
		foreach ($profileFields as $field) {
			if (strpos($field, ':') !== false) {
				list($key, $length) = explode(':', $field);
			}
			else {
				$key = $field;
				$length = 0;
			}

			if (isset($data[$key])) {
				if ($key == 'comment') {
					$fields[$key] = empty($length) ? $data[$key] : substr($data[$key], $length);
				}
				else {
					$fields[$key] = $this->Sanitize($data[$key], $length);
				}
			}
			elseif (preg_match('/(.*?)\[(.*?)\]/', $key, $matches)) {
				if (isset($data[$matches[1]][$matches[2]])) {
					$fields[$matches[1]][$matches[2]] = $this->Sanitize($data[$matches[1]][$matches[2]], $length);
				}
			}
		}

		$current_email = $this->modx->user->Profile->get('email');
		$new_email = !empty($fields['email']) ? trim($fields['email']) : '';
		$changeEmail = !($current_email == $new_email);

		/* @var modProcessorResponse $response */
		$response = $this->office->runProcessor('profile/update', $fields);
		if ($response->isError()) {
			$message = $response->hasMessage()
				? $response->getMessage()
				: $this->modx->lexicon('office_profile_err_update');
			$errors = array();
			if ($response->hasFieldErrors()) {
				if ($tmp = $response->getFieldErrors()) {
					foreach ($tmp as $error) {
						$errors[$error->field] = $error->message;
					}
				}
			}
			return $this->error($message, $errors);
		}

		if ($changeEmail) {
			$page_id = !empty($data['pageId'])
				? $data['pageId']
				: $this->modx->getOption('office_profile_page_id');

			$change = $this->changeEmail($new_email, $page_id);
			$message = ($change === true)
				? $this->modx->lexicon('office_profile_msg_save_email')
				: $this->modx->lexicon('office_profile_msg_save_noemail', array('errors' => $change));
		}
		else {
			$message = $this->modx->lexicon('office_profile_msg_save');
		}

		$saved = array();
		$user = $this->modx->getObject('modUser', $this->modx->user->id);
		$profile = $this->modx->getObject('modUserProfile', array('internalKey' => $this->modx->user->id));
		$tmp = array_merge($profile->toArray(), $user->toArray());
		$tmp['email'] = $new_email;
		foreach ($fields as $k => $v) {
			if (isset($tmp[$k]) && isset($data[$k])) {
				$saved[$k] = $tmp[$k];
			}
		}
		return $this->success($message, $saved);
	}


	/**
	 * Sanitizes a string
	 *
	 * @param string $string The string to sanitize
	 * @param integer $length The length of sanitized string
	 * @return string The sanitized string.
	 */
	public function Sanitize($string = '', $length = 0) {
		$expr = '/[^-_a-zа-яё0-9@\s\.\,\:\/\\\]+/iu';
		$sanitized = trim(preg_replace($expr, '', $string));

		return !empty($length)
			? substr($sanitized, 0, $length)
			: $sanitized;
	}


	/**
	 * Method for change email of user
	 *
	 * @param $email
	 * @param $id
	 *
	 * @return bool
	 */
	public function changeEmail($email, $id) {
		$activationHash = md5(uniqid(md5($this->modx->user->get('email') . '/' . $this->modx->user->get('id')), true));

		/** @var modDbRegister $register */
		$register = $this->modx->getService('registry', 'registry.modRegistry')->getRegister('user', 'registry.modDbRegister');
		$register->connect();
		$register->subscribe('/email/change/');
		$register->send('/email/change/',
			array(md5($this->modx->user->Profile->get('email')) => array(
				'hash' => $activationHash
				,'email' => $email
			)
		), array('ttl' => 86400));

		$link = $this->modx->makeUrl($id, '', array(
				'action' => 'profile/confirmEmail'
				,'email' => $email
				,'hash' => $activationHash
			)
			, 'full'
		);

		$chunk = $this->modx->getChunk($this->config['tplActivate'],
			array_merge(
				$this->modx->user->getOne('Profile')->toArray()
				,$this->modx->user->toArray()
				,array('link' => $link)
			)
		);

		/** @var modPHPMailer $mail */
		$mail = $this->modx->getService('mail', 'mail.modPHPMailer');
		$mail->set(modMail::MAIL_BODY, $chunk);
		$mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
		$mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
		$mail->set(modMail::MAIL_SENDER, $this->modx->getOption('emailsender'));
		$mail->set(modMail::MAIL_SUBJECT, $this->modx->lexicon('office_profile_email_subject'));
		$mail->address('to', $email);
		$mail->address('reply-to', $this->modx->getOption('emailsender'));
		$mail->setHTML(true);
		$response = !$mail->send()
			? $mail->mailer->errorInfo
			: true;
		$mail->reset();

		return $response;
	}


	/**
	 * Method for confirmation of user email
	 *
	 * @param $data
	 */
	public function confirmEmail($data) {
		/** @var modDbRegister $register */
		$register = $this->modx->getService('registry', 'registry.modRegistry')->getRegister('user', 'registry.modDbRegister');
		$register->connect();
		$register->subscribe('/email/change/' . md5($this->modx->user->Profile->get('email')));
		$msgs = $register->read(array('poll_limit' => 1));
		if (!empty($msgs[0])) {
			$msgs = reset($msgs);
			if (@$data['hash'] === @$msgs['hash'] && !empty($msgs['email'])) {
				//$this->modx->user->set('username', $msgs['email']);
				$this->modx->user->getOne('Profile')->set('email', $msgs['email']);
				$this->modx->user->save();
			}
		}

		$this->modx->sendRedirect($this->modx->makeUrl($this->modx->resource->id, '', '', 'full'));
	}

}

return 'officeProfileController';