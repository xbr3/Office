<?php

class msProductGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msOrderProduct';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'ASC';
	public $languageTopics = array('minishop2:product');

	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->where(array(
			'order_id' => $this->getProperty('order_id')
			,'msOrder.user_id' => $this->modx->user->id
		));

		$c->innerJoin('msOrder','msOrder', '`msOrderProduct`.`order_id` = `msOrder`.`id`');
		$c->leftJoin('msProduct','msProduct', '`msOrderProduct`.`product_id` = `msProduct`.`id`');
		$c->leftJoin('msProductData','msProductData', '`msOrderProduct`.`product_id` = `msProductData`.`id`');
		$c->select(
			$this->modx->getSelectColumns('msOrderProduct','msOrderProduct')
			.', `msProduct`.`pagetitle`, `msProduct`.`context_key`'
			.', `msProductData`.`article`'
		);

		return $c;
	}


	public function prepareRow(xPDOObject $object) {
		$row = $object->toArray();
		$row['url'] = $this->modx->makeUrl($row['product_id'], $row['context_key'], '', 'full');
		unset($row['context_key'], $row['product_id'], $row['id'], $row['order_id']);

		if (!empty($row['options']) && is_array($row['options'])) {
			$tmp = array();
			foreach ($row['options'] as $k => $v) {
				$tmp[] = $this->modx->lexicon('ms2_'.$k) . ': ' .$v;
			}
			$row['options'] = implode('; ', $tmp);
		}

		return $row;
	}

}

return 'msProductGetListProcessor';