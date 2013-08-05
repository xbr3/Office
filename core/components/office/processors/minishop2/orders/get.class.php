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
		$order_fields = array_map('trim', explode(',', $this->modx->getOption('office_ms2_order_form_fields', null, '', true)));
		$order_fields = array_intersect($order_fields, array_keys($this->modx->getFieldMeta('msOrder')));
		if (!in_array('cost', $order_fields)) {$order_fields[] = 'cost';}
		unset($order_fields['comment']);
		$order = array();
		foreach ($order_fields as $v) {
			$order[$v] = ($v == 'createdon' || $v == 'updatedon') ? $this->formatDate($this->object->get($v)) : $this->object->get($v);
		}

		$profile = $this->object->getOne('UserProfile');
		$array = array_merge($order, array('fullname' => $profile->get('fullname')));

		if (in_array('status', $order_fields) && $tmp = $this->object->getOne('Status')) {$array['status'] = $tmp->get('name');}
		if (in_array('delivery', $order_fields) && $tmp = $this->object->getOne('Delivery')) {$array['delivery'] = $tmp->get('name');}
		if (in_array('payment', $order_fields) && $tmp = $this->object->getOne('Payment')) {$array['payment'] = $tmp->get('name');}

		$address_fields = array_map('trim', explode(',', $this->modx->getOption('office_ms2_order_address_fields', null, '', true)));
		$address_fields = array_intersect($address_fields, array_keys($this->modx->getFieldMeta('msOrderAddress')));
		/* @var msOrderAddress $address */
		$address = $this->object->getOne('Address');
		foreach ($address_fields as $v) {
			$array['addr_'.$v] = $address->get($v);
		}

		return $this->success('', $array);
	}


	public function formatDate($date = '') {
		$df = $this->modx->getOption('office_ms2_date_format', null, '%d.%m.%Y %H:%M');
		return (!empty($date) && $date !== '0000-00-00 00:00:00') ? strftime($df, strtotime($date)) : '&nbsp;';
	}
}

return 'msOrderGetProcessor';