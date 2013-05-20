<?php
/**
 * Get an Item
 * 
 * @package office
 * @subpackage processors
 */
class OfficeItemGetProcessor extends modObjectGetProcessor {
	public $objectType = 'OfficeItem';
	public $classKey = 'OfficeItem';
	public $languageTopics = array('office:default');
}

return 'OfficeItemGetProcessor';