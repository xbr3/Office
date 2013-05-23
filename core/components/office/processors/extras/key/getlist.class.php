<?php

class extraKeyGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'extraKey';
	public $classKey = 'extraKey';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'DESC';

	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->where(array('user_id' => $this->modx->user->id));
		return $c;
	}

	public function prepareQueryAfterCount(xPDOQuery $c) {
		$c->leftJoin('extraPackageAccess', 'extraPackageAccess', '`extraPackageAccess`.`key_id` = `extraKey`.`id`');
		$c->select($this->modx->getSelectColumns('extraKey','extraKey'));
		$c->select('COUNT(`extraPackageAccess`.`package_id`) as `packages`');
		$c->groupby('`extraKey`.`id`');
		return $c;
	}

	/* @var extraKey $object */
	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray('', true);
		return $array;
	}

}

return 'extraKeyGetListProcessor';