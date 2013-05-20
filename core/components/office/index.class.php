<?php
/**
 * The main manager controller for Office.
 *
 * @package office
 */

require_once dirname(__FILE__) . '/model/office/office.class.php';

abstract class OfficeMainController extends modExtraManagerController {
	/** @var Office $Office */
	public $Office;

	public function initialize() {
		$this->Office = new Office($this->modx);
		
		$this->modx->regClientCSS($this->Office->config['cssUrl'].'mgr/main.css');
		$this->modx->regClientStartupScript($this->Office->config['jsUrl'].'mgr/office.js');
		$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
		Ext.onReady(function() {
			Office.config = '.$this->modx->toJSON($this->Office->config).';
			Office.config.connector_url = "'.$this->Office->config['connectorUrl'].'";
		});
		</script>');
		
		parent::initialize();
	}

	public function getLanguageTopics() {
		return array('office:default');
	}

	public function checkPermissions() { return true;}
}


class IndexManagerController extends OfficeMainController {
	public static function getDefaultController() { return 'home'; }
}