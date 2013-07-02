<?php

class msOrderGetProcessor extends modObjectGetProcessor {
	public $classKey = 'msOrder';
	public $languageTopics = array('minishop2:default');
	public $permission = '';


	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function initialize() {
		$primaryKey = $this->getProperty($this->primaryKeyField,false);
		if (empty($primaryKey)) return $this->modx->lexicon($this->objectType.'_err_ns');
		$this->object = $this->modx->getObject($this->classKey, array('id' => $primaryKey, 'user_id' => $this->modx->user->id));
		if (empty($this->object)) return $this->modx->lexicon($this->objectType.'_err_nfs',array($this->primaryKeyField => $primaryKey));

		if ($this->checkViewPermission && $this->object instanceof modAccessibleObject && !$this->object->checkPolicy('view')) {
			return $this->modx->lexicon('access_denied');
		}

		return true;
	}


	public function cleanup() {
		$order = $this->object->toArray();
		$address = $this->object->getOne('Address')->toArray('addr_');
		$profile = $this->object->getOne('UserProfile');

		$array = array_merge($order, $address, array('fullname' => $profile->get('fullname')));
		if ($tmp = $this->object->getOne('Status')) {
			$array['status'] = $tmp->get('name');
		}
		if ($tmp = $this->object->getOne('Delivery')) {
			$array['delivery'] = $tmp->get('name');
		}
		if ($tmp = $this->object->getOne('Payment')) {
			$array['payment'] = $tmp->get('name');
		}
		unset($array['comment']);

		$array['createdon'] = $this->formatDate($array['createdon']);
		$array['updatedon'] = $this->formatDate($array['updatedon']);

		return $this->success('', $array);
	}


	public function formatDate($date = '') {
		$df = $this->modx->getOption('office_ms2_date_format', null, '%d.%m.%Y %H:%M');
		return (!empty($date) && $date !== '0000-00-00 00:00:00') ? strftime($df, strtotime($date)) : '&nbsp;';
	}
}

return 'msOrderGetProcessor';