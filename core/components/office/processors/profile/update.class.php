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
				elseif ($field == 'email' && $this->modx->getCount('modUser', array('username' => $tmp, 'id:!=' => $this->object->id))) {
					$this->addFieldError('email', $this->modx->lexicon('user_err_already_exists_email'));
				}
				elseif ($field == 'email' && $this->modx->getCount('modUserProfile', array('email' => $tmp, 'internalKey:!=' => $this->object->id))) {
					$this->addFieldError('email', $this->modx->lexicon('user_err_already_exists_email'));
				}
				elseif (empty($tmp)) {
					$this->addFieldError($field, $this->modx->lexicon('field_required'));
				}
			}
		}
		$this->setProperty('username', $this->object->get('username'));
		$this->current_email = $this->object->Profile->get('email');
		$this->new_email = $this->getProperty('email');

		return parent::beforeSet();
	}


	public function afterSave() {
		if ($this->new_email != $this->current_email) {
			$this->object->Profile->set('email', $this->current_email);
			$this->object->Profile->save();
		}
		elseif ($this->object->username != $this->current_email) {
			$this->object->set('username', $this->current_email);
			$this->object->save();
		}

		return parent::afterSave();
	}

}

return 'officeProfileUserUpdateProcessor';