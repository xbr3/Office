<?php
/**
 * Get a list of Items
 *
 * @package office
 * @subpackage processors
 */
class OfficeItemGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'OfficeItem';
	public $classKey = 'OfficeItem';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'DESC';
	public $renderers = '';
	
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		return $c;
	}

	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray();
		return $array;
	}
	
}

return 'OfficeItemGetListProcessor';