<?php
/**
 * Get a list of Orders
 *
 * @package minishop2
 * @subpackage processors
 */
class msOrderLogGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msOrderLog';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'DESC';
	public $languageTopics = array('default','minishop2:manager');


	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->leftJoin('msOrderStatus','msOrderStatus', '`msOrderLog`.`entry` = `msOrderStatus`.`id`');
		$c->leftJoin('msOrder','msOrder', '`msOrder`.`id` = `msOrderLog`.`order_id`');
		$c->where(array(
			'order_id' => $this->getProperty('order_id')
			,'msOrder.user_id' => $this->modx->user->id
			,'action' => 'status'
		));

		$c->select($this->modx->getSelectColumns('msOrderLog', 'msOrderLog', '', array('timestamp','action','entry')));
		$c->select('`msOrderStatus`.`name` as `entry`, `msOrderStatus`.`color`');
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
		if (!empty($data['color'])) {
			$data['entry'] = '<span style="color:#'.$data['color'].';">'.$data['entry'].'</span>';
		}
		$data['action'] = $this->modx->lexicon('ms2_'.$data['action']);

		return $data;
	}

}

return 'msOrderLogGetListProcessor';