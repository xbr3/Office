<?php

class extraKeyUpdateProcessor extends modObjectUpdateProcessor {
	/* @var extraKey $object */
	public $object;
	public $objectType = 'extraKey';
	public $classKey = 'extraKey';
	public $languageTopics = array('extras:default');
	public $permission = '';

	public function initialize() {
		$primaryKey = $this->getProperty($this->primaryKeyField,false);
		if (empty($primaryKey)) return $this->modx->lexicon($this->objectType.'_err_ns');
		$this->object = $this->modx->getObject($this->classKey, array($this->primaryKeyField => $primaryKey, 'user_id' => $this->modx->user->id));
		if (empty($this->object)) return $this->modx->lexicon($this->objectType.'_err_nfs',array($this->primaryKeyField => $primaryKey));

		if ($this->checkSavePermission && $this->object instanceof modAccessibleObject && !$this->object->checkPolicy('save')) {
			return $this->modx->lexicon('access_denied');
		}
		return true;
	}

	public function beforeSet() {
		$properties = array(
			'active' => $this->getProperty('active', false)
			,'description' => $this->getProperty('description', null)
			,'user_id' => $this->modx->user->id
			,'editedon' => time()
		);
		$this->properties = array();

		$this->setProperties($properties);

		return !$this->hasErrors();
	}

}

return 'extraKeyUpdateProcessor';