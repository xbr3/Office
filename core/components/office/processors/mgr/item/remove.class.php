<?php
/**
 * Remove an Item.
 * 
 * @package office
 * @subpackage processors
 */
class OfficeItemRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $objectType = 'OfficeItem';
	public $classKey = 'OfficeItem';
	public $languageTopics = array('office');

}
return 'OfficeItemRemoveProcessor';