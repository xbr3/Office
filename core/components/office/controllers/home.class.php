<?php
/**
 * The home manager controller for Office.
 *
 * @package office
 */
class OfficeHomeManagerController extends OfficeMainController {
	/* @var Office $Office */
	public $Office;


	public function process(array $scriptProperties = array()) {}
	

	public function getPageTitle() { return $this->modx->lexicon('office'); }
	

	public function loadCustomCssJs() {
		$this->modx->regClientStartupScript($this->Office->config['jsUrl'].'mgr/widgets/items.grid.js');
		$this->modx->regClientStartupScript($this->Office->config['jsUrl'].'mgr/widgets/home.panel.js');
	 	$this->modx->regClientStartupScript($this->Office->config['jsUrl'].'mgr/sections/home.js');
	}
	

	public function getTemplateFile() {
		return $this->Office->config['templatesPath'].'home.tpl';
	}
}