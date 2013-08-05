<?php

class officeMS2Controller extends officeDefaultController {

	public function setDefault($config = array()) {
		if (defined('MODX_ACTION_MODE') && MODX_ACTION_MODE && !empty($_SESSION['Office']['miniShop2'])) {
			$this->config = $_SESSION['Office']['miniShop2'];
			$this->config['json_response'] = true;
		}
		else {
			$this->config = array_merge(array(
				'tplOuter' => 'tpl.Office.ms2.outer'
			), $config);

			$_SESSION['Office']['miniShop2'] = $this->config;
		}
	}


	public function getLanguageTopics() {
		return array('office:minishop2');
	}

	public function initialize($ctx = 'web') {
		$this->modx->error->errors = array();
		$this->modx->error->message = '';
		return $this->loadPackage();
	}


	public function defaultAction() {
		if (!$this->modx->user->isAuthenticated()) {
			//$this->modx->sendUnauthorizedPage();
			return '';
		}
		else {
			$config = $this->office->makePlaceholders($this->office->config);
			if ($css = trim($this->modx->getOption('office_ms2_frontend_css'))) {
				$this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
			}
			if ($js = trim($this->modx->getOption('office_ms2_frontend_js', null, '[[+jsUrl]]minishop2/default.js'))) {
				$this->office->addClientExtJS();
				$this->office->addClientLexicon(array(
					'minishop2:default'
				), 'minishop2/lexicon');
				$this->office->addClientJs(array(
					$this->office->config['jsUrl'] . 'minishop2/minishop2.js'
					,$this->office->config['jsUrl'] . 'minishop2/misc.combo.js'
					,$this->office->config['jsUrl'] . 'minishop2/orders.grid.js'
					,str_replace($config['pl'], $config['vl'], $js)
				), 'minishop2/all');

				$this->setConfig();
			}

			return $this->modx->getChunk($this->config['tplOuter']);
		}
	}


	public function loadPackage() {
		$corePath = $this->modx->getOption('minishop2.core_path', null, $this->modx->getOption('core_path').'components/minishop2/');
		$modelPath = $corePath.'model/';

		return $this->modx->addPackage('minishop2', $modelPath);
	}


	public function setConfig() {
		$grid_fields = array_map('trim', explode(',', $this->modx->getOption('office_ms2_order_grid_fields', null, 'num,status,cost,weight,delivery,payment,createdon,updatedon', true)));
		$grid_fields = array_unique(array_merge($grid_fields, array('id','details')));

		$order_fields = array_map('trim', explode(',', $this->modx->getOption('office_ms2_order_form_fields')));
		$address_fields = array_map('trim', explode(',', $this->modx->getOption('office_ms2_order_address_fields')));
		$product_fields = array_map('trim', explode(',', $this->modx->getOption('office_ms2_order_product_fields', null, '')));
		$product_fields = array_values(array_unique(array_merge($product_fields, array('product_pagetitle','url'))));

		$this->modx->regClientScript(str_replace('				', '', '
			<script type="text/javascript">
				MODx.config.ms2_date_format = "'.str_replace('"','\"', $this->modx->getOption('office_ms2_date_format')).'";
				MODx.config.default_per_page = "'.$this->modx->getOption('default_per_page').'";
				MODx.config.order_grid_fields = '.$this->modx->toJSON($grid_fields).';
				MODx.config.order_form_fields = '.$this->modx->toJSON($order_fields).';
				MODx.config.order_address_fields = '.$this->modx->toJSON($address_fields).';
				MODx.config.order_product_fields = '.$this->modx->toJSON($product_fields).';
			</script>
		'), true);
	}


	public function getOrders($data) {
		/* @var modProcessorResponse $response */
		$response = $this->office->runProcessor('minishop2/orders/getlist', $data);
		return $response->response;
	}
	public function getOrder($data) {
		/* @var modProcessorResponse $response */
		$response = $this->office->runProcessor('minishop2/orders/get', $data);
		if (!isset($response->response['data'])) {$response->response['data'] = array();}
		return $this->modx->toJSON($response->response);
	}
	public function getOrderProducts($data) {
		/* @var modProcessorResponse $response */
		$response = $this->office->runProcessor('minishop2/orders/product/getlist', $data);
		return $response->response;
	}
	public function getLog($data) {
		/* @var modProcessorResponse $response */
		$response = $this->office->runProcessor('minishop2/orders/getlog', $data);
		return $response->response;
	}
	public function getStatus() {
		/* @var modProcessorResponse $response */
		$response = $this->office->runProcessor('minishop2/status/getlist');
		return $response->response;
	}

	public function response($response) {
		if (!isset($response->response['data'])) {
			$response->response['data'] = array();
		}
		if ($response->response['errors'] === null) {
			$response->response['errors'] = array();
		}
		if ($response->response['message'] === null) {
			$response->response['message'] = '';
		}

		return $this->modx->toJSON($response->response);
	}

}

return 'officeMS2Controller';