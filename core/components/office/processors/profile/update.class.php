<?php
require MODX_CORE_PATH.'model/modx/processors/security/user/update.class.php';

class officeProfileUserUpdateProcessor extends modUserUpdateProcessor {
	public $classKey = 'modUser';
	public $languageTopics = array('core:default','core:user');
	public $permission = '';
	public $beforeSaveEvent = 'OnBeforeUserFormSave';
	public $afterSaveEvent = 'OnUserFormSave';


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
				$tmp = trim($this->getProperty($field,null));
				if ($field == 'email' && !preg_match('/.+@.+..+/i', $tmp)) {
					$this->addFieldError('email', $this->modx->lexicon('user_err_not_specified_email'));
				}
				else if ($field == 'email' && $this->modx->getCount('modUser', array('username' => $tmp, 'id:!=' => $this->object->id))) {
					$this->addFieldError('email', $this->modx->lexicon('user_err_already_exists_email'));
				}
				else if (empty($tmp)) {
					$this->addFieldError($field, $this->modx->lexicon('field_required'));
				}
				else {
					$this->setProperty($field, $tmp);
				}
			}
		}

		return parent::beforeSet();
	}

	public function beforeSave() {
		$this->setProperty('email', $this->getProperty('username'));

		return parent::beforeSave();
	}

}

return 'officeProfileUserUpdateProcessor';