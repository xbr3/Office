<?php

class extraKeyCreateProcessor extends modObjectCreateProcessor {
	/* @var extraKey $object */
	public $object;
	public $objectType = 'extraKey';
	public $classKey = 'extraKey';
	public $languageTopics = array('extras:default');
	public $permission = '';
	public $beforeSaveEvent = 'exOnBeforeKeySave';
	public $afterSaveEvent = 'exOnKeySave';

	public function beforeSet() {
		$properties = array(
			'key' => $this->getProperty('key')
			,'active' => true
			,'description' => $this->getProperty('description', null)
			,'user_id' => $this->modx->user->id
			,'createdon' => time()
			,'editedon' => 0
		);
		$this->properties = array();
		$this->setProperties($properties);

		foreach (array('user_id','key') as $v) {
			if (!$this->getProperty($v)) {
				$this->addFieldError($v, $this->modx->lexicon('extras_err_ns'));
			}
		}

		if ($this->modx->getCount($this->classKey, array('key' => $this->getProperty('key'), 'user_id' => $this->getProperty('user_id')))) {
			$this->addFieldError('key', $this->modx->lexicon('extras_err_ae'));
		}

		return !$this->hasErrors();
	}

}

return 'extraKeyCreateProcessor';