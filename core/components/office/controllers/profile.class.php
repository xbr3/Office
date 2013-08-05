<?php

class officeProfileController extends officeDefaultController {

	public function setDefault($config = array()) {
		if (defined('MODX_ACTION_MODE') && MODX_ACTION_MODE && !empty($_SESSION['Office']['Profile'])) {
			$this->config = $_SESSION['Office']['Profile'];
			$this->config['json_response'] = true;
		}
		else {
			$this->config = array_merge(array(
				'tplProfile' => 'tpl.Office.profile.form'
				,'tplActivate' => 'tpl.Office.profile.activate'

				,'profileFields' => 'email:50,fullname:50,phone:12,mobilephone:12,dob:10,gender,address,country,city,state,zip,fax,photo,comment,website'
				,'requiredFields' => 'email,fullname'

				,'pageId' => 0
			), $config);

			$page_id = $this->modx->getOption('office_profile_page_id');
			if (empty($page_id)) {
				/* @var modSystemSetting $setting */
				if ($setting = $this->modx->getObject('modSystemSetting', 'office_profile_page_id')) {
					$setting->set('value', $this->modx->resource->id);
					$setting->save();
				}
			}
			$_SESSION['Office']['Profile'] = $this->config;
		}
	}


	public function getLanguageTopics() {
		return array('office:profile');
	}


	public function defaultAction() {
		if (!$this->modx->user->isAuthenticated()) {
			//$this->modx->sendUnauthorizedPage();
			return '';
		}
		else {
			$config = $this->office->makePlaceholders($this->office->config);
			if ($css = trim($this->modx->getOption('office_profile_frontend_css', null, '[[+cssUrl]]profile/default.css'))) {
				$this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
			}
			if ($js = trim($this->modx->getOption('office_profile_frontend_js', null, '[[+jsUrl]]profile/default.js'))) {
				$this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));
			}

			$user = $this->modx->user->toArray();
			$profile = $this->modx->user->getOne('Profile')->toArray();
			$user['gravatar'] = 'http://www.gravatar.com/avatar/'.md5(strtolower($profile['email']));

			return $this->modx->getChunk($this->config['tplProfile'], array_merge($profile, $user));
		}
	}


	/*
	 * Updates user profile
	 *
	 * $param array $fields Array with new values
	 * */
	public function Update($data = array()) {
		if (!$this->modx->user->isAuthenticated()) {
			return $this->error($this->modx->lexicon('office_err_auth'));
		}

		$fields = array();
		$profileFields = explode(',', $this->config['profileFields']);
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
		}

		$fields['requiredFields'] = array_map('trim', explode(',', $this->config['requiredFields']));
		$current_email = $this->modx->user->get('username');
		$new_email =  @$fields['email'];
		$fields['username'] = $current_email;
		$changeEmail = !($current_email == $new_email);

		/* @var modProcessorResponse $response */
		$response = $this->office->runProcessor('profile/update', $fields);
		if ($response->isError()) {
			$errors = array();
			$tmp = $response->getAllErrors(':');
			foreach ($tmp as $v) {
				$tmp2 = explode(':',$v);
				$errors[$tmp2[0]] = $tmp2[1];
			}
			return $this->error($this->modx->lexicon('office_profile_err_update'), $errors);
		}

		if ($changeEmail) {
			$page_id = !empty($data['pageId']) ? $data['pageId'] : $this->modx->getOption('office_profile_page_id');
			$change = $this->changeEmail($new_email, $page_id);
			return ($change !== true)
				? $this->success($this->modx->lexicon('office_profile_msg_save_noemail', array('errors', implode(',', $change))))
				: $this->success($this->modx->lexicon('office_profile_msg_save_email'));
		}
		else {
			$saved = array();
			$tmp = $this->modx->user->getOne('Profile')->toArray();
			$tmp = array_merge($response->response['object'], $tmp);
			foreach ($fields as $k => $v) {
				if (isset($tmp[$k]) && isset($data[$k])) {
					$saved[$k] = $tmp[$k];
				}
			}
			//if ($_SERVER['REMOTE_ADDR'] == '194.190.81.82') {}
			return $this->success($this->modx->lexicon('office_profile_msg_save'), $saved);
		}
	}


	/*
	 * Sanitizes a string
	 *
	 * @param string $string The string to sanitize
	 * @param integer $length The length of sanitized string
	 * @return string The sanitized string.
	 * */
	public function Sanitize($string = '', $length = 0) {
		$expr = '/[^-_a-zа-яё0-9@\s\.\,\:\/\\\]+/iu';
		$sanitized = trim(preg_replace($expr, '', $string));

		return !empty($length)
			? substr($sanitized, 0, $length)
			: $sanitized;
	}


	public function changeEmail($email, $id) {
		$activationHash = md5(uniqid(md5($this->modx->user->get('email') . '/' . $this->modx->user->get('id')), true));

		$this->modx->getService('registry', 'registry.modRegistry');
		$this->modx->registry->getRegister('user', 'registry.modDbRegister');
		$this->modx->registry->user->connect();
		$this->modx->registry->user->subscribe('/email/change/');
		$this->modx->registry->user->send('/email/change/',
			array(md5($this->modx->user->get('username')) => array(
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

		$send = $this->modx->user->sendEmail(
			$this->modx->getChunk(
				$this->config['tplActivate']
				,array_merge(
					$this->modx->user->getOne('Profile')->toArray()
					,$this->modx->user->toArray()
					,array('link' => $link)
				)
			)
			,array(
				'subject' => $this->modx->lexicon('office_profile_email_subject')
			)
		);

		return ($send !== true)
			? $this->modx->mail->mailer->errorInfo
			: true;
	}


	public function confirmEmail($data) {
		$this->modx->getService('registry', 'registry.modRegistry');
		$this->modx->registry->getRegister('user', 'registry.modDbRegister');
		$this->modx->registry->user->connect();
		$this->modx->registry->user->subscribe('/email/change/' . md5($this->modx->user->get('username')));
		$msgs = $this->modx->registry->user->read(array('poll_limit' => 1));
		if (!empty($msgs[0])) {
			$msgs = reset($msgs);
			if (@$data['hash'] === @$msgs['hash'] && !empty($msgs['email'])) {
				$this->modx->user->set('username', $msgs['email']);
				$this->modx->user->getOne('Profile')->set('email', $msgs['email']);
				$this->modx->user->save();
			}
		}

		$this->modx->sendRedirect($this->modx->makeUrl($this->modx->resource->id, '', '', 'full'));
	}

}

return 'officeProfileController';