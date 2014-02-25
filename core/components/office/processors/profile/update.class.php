<?php
require MODX_CORE_PATH.'model/modx/processors/security/user/update.class.php';

class officeProfileUserUpdateProcessor extends modUserUpdateProcessor {
	public $classKey = 'modUser';
	public $languageTopics = array('core:default','core:user');
	public $permission = '';
	public $beforeSaveEvent = 'OnBeforeUserFormSave';
	public $afterSaveEvent = 'OnUserFormSave';
	protected $new_email;
	protected $current_email;


	/**
	 * {@inheritDoc}
	 * @return boolean|string
	 */
	public function initialize() {
		$this->setProperty('id', $this->modx->user->id);
		return parent::initialize();
	}


	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function beforeSet() {
		$fields = $this->getProperty('requiredFields', '');
		if (!empty($fields) && is_array($fields)) {
			foreach ($fields as $field) {
				if (preg_match('/(.*?)\[(.*?)\]/', $field, $matches)) {
					$tmp = $this->getProperty($matches[1],null);
					$tmp = is_array($tmp) && isset($tmp[$matches[2]])
						? $tmp[$matches[2]]
						: null;
				}
				else {
					$tmp = $this->getProperty($field,null);
				}

				if ($field == 'email' && !preg_match('/^[^@а-яА-Я]+@[^@а-яА-Я]+(?<!\.)\.[^\.а-яА-Я]{2,}$/m', $tmp)) {
					$this->addFieldError('email', $this->modx->lexicon('user_err_not_specified_email'));
				}
				/*
				elseif ($field == 'email' && $this->modx->getCount('modUser', array('username' => $tmp, 'id:!=' => $this->object->id))) {
					$this->addFieldError('email', $this->modx->lexicon('user_err_already_exists_email'));
				}
				*/
				elseif ($field == 'email' && $this->modx->getCount('modUserProfile', array('email' => $tmp, 'internalKey:!=' => $this->object->id))) {
					$this->addFieldError('email', $this->modx->lexicon('user_err_already_exists_email'));
				}
				elseif (empty($tmp)) {
					$this->addFieldError($field, $this->modx->lexicon('field_required'));
				}
			}
		}
		if (!$this->getProperty('username')) {
			$this->setProperty('username', $this->object->get('username'));
		}
		$this->current_email = $this->object->Profile->get('email');
		$this->new_email = $this->getProperty('email');

		return parent::beforeSet();
	}


	/**
	 * Upload user photo
	 *
	 * @return bool
	 */
	public function beforeSave() {
		// Params
		$default_params = array('w' => 200, 'h' => 200, 'bg' => 'ffffff', 'q' => 95, 'zc' => 0, 'f' => 'jpg');
		$params = $this->modx->fromJSON($this->getProperty('avatarParams'));
		if (!is_array($params)) {
			$params = array();
		}
		$params = array_merge($default_params, $params);

		$path = trim($this->getProperty('avatarPath', 'images/users/'), '/') . '/';
		$file = strtolower(md5($this->object->id . time()) . '.' . $params['f']);

		$url = MODX_ASSETS_URL . $path . $file;
		$dst = MODX_ASSETS_PATH . $path . $file;

		$tmp = explode('/', $this->object->Profile->get('photo'));
		$cur = !empty($tmp)
			? MODX_ASSETS_PATH . $path . end($tmp)
			: '';

		// Check image dir
		$tmp = explode('/', str_replace(MODX_CORE_PATH, '', MODX_ASSETS_PATH . $path));
		$dir = rtrim(MODX_CORE_PATH, '/');
		foreach ($tmp as $v) {
			if (empty($v)) {continue;}
			$dir .= '/' . $v;
			if (!file_exists($dir) || !is_dir($dir)) {
				@mkdir($dir);
			}
		}
		if (!file_exists(MODX_ASSETS_PATH . $path) || !is_dir(MODX_ASSETS_PATH . $path)) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] Could not create images dir "'.MODX_ASSETS_PATH . $path.'"');
			return false;
		}

		// Remove image
		if ($this->object->Profile->get('photo') && isset($_POST['photo']) && empty($_POST['photo'])) {
			if (!empty($cur) && file_exists($cur)) {@unlink($cur);}
			$this->object->Profile->set('photo', '');
		}
		// Upload a new one
		elseif (!empty($_FILES['newphoto']) && preg_match('/image/', $_FILES['newphoto']['type']) && $_FILES['newphoto']['error'] == 0) {
			move_uploaded_file($_FILES['newphoto']['tmp_name'], $dst);

			$phpThumb = $this->modx->getService('modphpthumb','modPhpThumb', MODX_CORE_PATH . 'model/phpthumb/', array());
			$phpThumb->setSourceFilename($dst);
			foreach ($params as $k => $v) {
				$phpThumb->setParameter($k, $v);
			}
			if ($phpThumb->GenerateThumbnail()) {
				if ($phpThumb->renderToFile($dst)) {
					if (!empty($cur) && file_exists($cur)) {@unlink($cur);}
					$this->object->Profile->set('photo', $url);
				}
				else {
					$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] Could not save rendered image to "'.$dst.'"');
				}
			}
			else {
				$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] ' . print_r($phpThumb->debugmessages, true));
			}
		}

		return true;
	}


	public function afterSave() {
		if ($this->new_email != $this->current_email) {
			$this->object->Profile->set('email', $this->current_email);
			$this->object->Profile->save();
		}
		/*
		elseif ($this->object->username != $this->current_email) {
			$this->object->set('username', $this->current_email);
			$this->object->save();
		}
		*/

		return parent::afterSave();
	}

}

return 'officeProfileUserUpdateProcessor';