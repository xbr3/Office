<?php

class extraKeyUpdateProcessor extends modObjectUpdateProcessor {
	/* @var extraKey $object */
	public $object;
	public $objectType = 'extraKey';
	public $classKey = 'extraKey';
	public $languageTopics = array('extras:default');
	public $permission = '';
	public $beforeSaveEvent = 'exOnBeforeKeySave';
	public $afterSaveEvent = 'exOnKeySave';

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
			'description' => $this->getProperty('description', null)
			,'user_id' => $this->modx->user->id
			,'editedon' => time()
		);
		$this->properties = array();
		$properties = array_map('strip_tags', $properties);
		$this->setProperties($properties);

		return !$this->hasErrors();
	}

}

return 'extraKeyUpdateProcessor';