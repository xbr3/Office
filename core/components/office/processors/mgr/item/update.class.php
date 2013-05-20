<?php
/**
 * Update an Item
 * 
 * @package office
 * @subpackage processors
 */
class OfficeItemUpdateProcessor extends modObjectUpdateProcessor {
	public $objectType = 'OfficeItem';
	public $classKey = 'OfficeItem';
	public $languageTopics = array('office');
	public $permission = 'update_document';
}

return 'OfficeItemUpdateProcessor';