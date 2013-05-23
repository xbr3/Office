<?php

class extraKeyGetProcessor extends modObjectGetProcessor {
	public $objectType = 'extraKey';
	public $classKey = 'extraKey';
	public $languageTopics = array('extras:default');

	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function initialize() {
		$primaryKey = $this->getProperty($this->primaryKeyField,false);
		if (empty($primaryKey)) return $this->modx->lexicon($this->objectType.'_err_ns');
		$this->object = $this->modx->getObject($this->classKey, array($this->primaryKeyField => $primaryKey, 'user_id' => $this->modx->user->id));
		if (empty($this->object)) return $this->modx->lexicon($this->objectType.'_err_nfs',array($this->primaryKeyField => $primaryKey));

		if ($this->checkViewPermission && $this->object instanceof modAccessibleObject && !$this->object->checkPolicy('view')) {
			return $this->modx->lexicon('access_denied');
		}

		return true;
	}

}

return 'extraKeyGetProcessor';