<?php
/**
 * Get a list of Orders
 *
 * @package minishop2
 * @subpackage processors
 */
class msOrderGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msOrder';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'DESC';
	public $languageTopics = array('default','minishop2:manager');


	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->where(array('user_id' => $this->modx->user->id));

		$all = array_keys($this->modx->getFieldMeta('msOrder'));;
		$enabled = array_map('trim', explode(',', $this->modx->getOption('office_ms2_order_grid_fields', null, 'id,num,status,cost,weight,delivery,payment,createdon,updatedon', true)));
		$tmp = array_intersect($enabled, $all); unset($tmp['comment']);
		if (!in_array('id', $tmp)) {$tmp[] = 'id';}
		$c->select($this->modx->getSelectColumns('msOrder', 'msOrder', '', $tmp));

		if (in_array('status', $enabled)) {
			$c->leftJoin('msOrderStatus','msOrderStatus', '`msOrder`.`status` = `msOrderStatus`.`id`');
			$c->select('`msOrderStatus`.`name` as `status`, `msOrderStatus`.`color`');
		}
		if (in_array('delivery', $enabled)) {
			$c->leftJoin('msDelivery','msDelivery', '`msOrder`.`delivery` = `msDelivery`.`id`');
			$c->select('`msDelivery`.`name` as `delivery`');
		}
		if (in_array('payment', $enabled)) {
			$c->leftJoin('msPayment','msPayment', '`msOrder`.`payment` = `msPayment`.`id`');
			$c->select('`msPayment`.`name` as `payment`');
		}
		if (in_array('customer', $enabled)) {
			$c->leftJoin('modUserProfile','modUserProfile', '`msOrder`.`user_id` = `modUserProfile`.`internalKey`');
			$c->select('`modUserProfile`.`fullname` as `customer`');
		}
		if (in_array('receiver', $enabled)) {
			$c->leftJoin('msOrderAddress','msOrderAddress', '`msOrder`.`address` = `msOrderAddress`.`id`');
			$c->select('`msOrderAddress`.`receiver`');
		}

		if ($query = $this->getProperty('query')) {
			$c->where(array(
				'num:LIKE' => '%'.$query.'%'
			));
		}
		if ($status = $this->getProperty('status')) {
			$c->where(array('status' => $status));
		}

		return $c;
	}

	public function getData() {
		$data = array();
		$limit = intval($this->getProperty('limit'));
		$start = intval($this->getProperty('start'));

		/* query for chunks */
		$c = $this->modx->newQuery($this->classKey);
		$c = $this->prepareQueryBeforeCount($c);
		$data['total'] = $this->modx->getCount($this->classKey,$c);
		$c = $this->prepareQueryAfterCount($c);

		$sortClassKey = $this->getSortClassKey();
		$sortKey = $this->modx->getSelectColumns($sortClassKey,$this->getProperty('sortAlias',$sortClassKey),'',array($this->getProperty('sort')));
		if (empty($sortKey)) $sortKey = $this->getProperty('sort');
		$c->sortby($sortKey,$this->getProperty('dir'));
		if ($limit > 0) {
			$c->limit($limit,$start);
		}

		if ($c->prepare() && $c->stmt->execute()) {
			$data['results'] = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		return $data;
	}

	public function iterate(array $data) {
		$list = array();
		$list = $this->beforeIteration($list);
		$this->currentIndex = 0;
		/** @var xPDOObject|modAccessibleObject $object */
		foreach ($data['results'] as $array) {
			$list[] = $this->prepareArray($array);
			$this->currentIndex++;
		}
		$list = $this->afterIteration($list);
		return $list;
	}

	public function prepareArray(array $data) {
		$data['status'] = '<span style="color:#'.$data['color'].';">'.$data['status'].'</span>';
		unset($data['color']);
		if (isset($data['cost'])) {$data['cost'] = round($data['cost'],2);}
		if (isset($data['cart_cost'])) {$data['cost'] = round($data['cost'],2);}
		if (isset($data['delivery_cost'])) {$data['cost'] = round($data['delivery_cost'],2);}
		if (isset($data['weight'])) {$data['cost'] = round($data['weight'],3);}

		$data = array_map('strip_tags', $data);
		return $data;
	}


}

return 'msOrderGetListProcessor';