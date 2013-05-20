<?php
/**
 * Create an Item
 * 
 * @package office
 * @subpackage processors
 */
class OfficeItemCreateProcessor extends modObjectCreateProcessor {
	public $objectType = 'OfficeItem';
	public $classKey = 'OfficeItem';
	public $languageTopics = array('office');
	public $permission = 'new_document';
	
	public function beforeSet() {
		$alreadyExists = $this->modx->getObject('OfficeItem',array(
			'name' => $this->getProperty('name'),
		));
		if ($alreadyExists) {
			$this->modx->error->addField('name',$this->modx->lexicon('office_item_err_ae'));
		}
		return !$this->hasErrors();
	}
	
}

return 'OfficeItemCreateProcessor';