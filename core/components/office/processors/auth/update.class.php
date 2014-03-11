<?php
require MODX_CORE_PATH.'model/modx/processors/security/user/update.class.php';

class officeAuthUserUpdateProcessor extends modUserUpdateProcessor {
	public $classKey = 'modUser';
	public $languageTopics = array('core:default','core:user');
	public $permission = '';
	public $beforeSaveEvent = 'OnBeforeUserFormSave';
	public $afterSaveEvent = 'OnUserFormSave';

	public function beforeSet() {
		if ($this->object->get('sudo')) {
			return 'You can`t update this user.';
		}

		return parent::beforeSet();
	}

}

return 'officeAuthUserUpdateProcessor';