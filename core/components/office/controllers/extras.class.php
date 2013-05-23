<?php

class officeExtrasKeysController extends officeDefaultController {

	public function setDefault($config = array()) {
		if (defined('MODX_ACTION_MODE') && MODX_ACTION_MODE && !empty($_SESSION['Office']['Extras'])) {
			$this->config = $_SESSION['Office']['Extras'];
			$this->config['json_response'] = true;
		}
		else {
			$this->config = array_merge(array(
				'tplOuter' => 'tpl.Office.extras.outer'
				,'tplRow' => 'tpl.Office.extras.row'
			), $config);

			$_SESSION['Office']['Extras'] = $this->config;
		}
	}


	public function getLanguageTopics() {
		return array('office:extras');
	}

	public function initialize($ctx = 'web') {
		$this->modx->error->errors = array();
		$this->modx->error->message = '';
		return $this->loadPackage();
	}


	public function defaultAction() {
		if (!$this->modx->user->isAuthenticated()) {
			return $this->modx->sendUnauthorizedPage();
		}
		else {
			$this->modx->regClientCSS($this->office->config['cssUrl'] . 'main/lib/ext-all-notheme.css');
			$this->modx->regClientCSS($this->office->config['cssUrl'] . 'main/lib/xtheme-modx.css');
			$this->modx->regClientCSS($this->office->config['cssUrl'] . 'extras/default.css');

			$this->office->addClientJs(array(
				'/manager/assets/ext3/adapter/jquery/ext-jquery-adapter.js'
				,'/manager/assets/ext3/ext-all.js'
			), 'main/ext');

			$this->office->addClientJs(array(
				'/manager/assets/modext/core/modx.js'
			), 'main/modx');


			$this->office->addClientLexicon(array(
				'extras:default'
			), 'extras/lexicon');

			$this->office->addClientJs(array(
				'/manager/assets/modext/core/modx.localization.js'
				,'/manager/assets/modext/util/utilities.js'
				,'/manager/assets/modext/core/modx.component.js'
				,'/manager/assets/modext/widgets/core/modx.panel.js'
				,'/manager/assets/modext/widgets/core/modx.tabs.js'
				,'/manager/assets/modext/widgets/core/modx.window.js'
				,'/manager/assets/modext/widgets/core/modx.tree.js'
				,'/manager/assets/modext/widgets/core/modx.combo.js'
				,'/manager/assets/modext/widgets/core/modx.grid.js'
				,'/assets/components/extras/js/mgr/extras.js'
				,'/assets/components/extras/js/misc/extras.combo.js'
			), 'main/widgets');

			$this->office->addClientJs(array(
				$this->office->config['jsUrl'] . 'extras/keys.grid.js'
				,$this->office->config['jsUrl'] . 'extras/default.js'
			), 'extras/all');

			return $this->modx->getChunk($this->config['tplOuter']);
		}
	}


	public function loadPackage() {
		$corePath = $this->modx->getOption('extras.core_path', null, $this->modx->getOption('core_path').'components/extras/');
		$modelPath = $corePath.'model/';

		return $this->modx->addPackage('extras', $modelPath);
	}


	public function getKeys() {
		/* @var modProcessorResponse $response */
		$response = $this->office->runProcessor('extras/key/getlist');
		return $response->response;
	}
	public function getKey($data) {
		/* @var modProcessorResponse $response */
		$response = $this->office->runProcessor('extras/key/get', $data);
		if (!isset($response->response['data'])) {$response->response['data'] = array();}
		return $this->modx->toJSON($response->response);
	}
	public function createKey($data) {
		/* @var modProcessorResponse $response */
		$response = $this->office->runProcessor('extras/key/create', $data);
		if (!isset($response->response['data'])) {$response->response['data'] = array();}
		$response->response['message'] = '';
		return $this->modx->toJSON($response->response);
	}
	public function generateKey() {
		/* @var modProcessorResponse $response */
		$response = $this->office->runProcessor('extras/key/generate');
		if (!isset($response->response['data'])) {$response->response['data'] = array();}
		return $this->modx->toJSON($response->response);
	}
	public function updateKey($data) {
		/* @var modProcessorResponse $response */
		$response = $this->office->runProcessor('extras/key/update', $data);
		if (!isset($response->response['data'])) {$response->response['data'] = array();}
		return $this->modx->toJSON($response->response);
	}
	public function removeKey($data) {
		/* @var modProcessorResponse $response */
		$response = $this->office->runProcessor('extras/key/remove', $data);
		if (!isset($response->response['data'])) {$response->response['data'] = array();}
		return $this->response($response);
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

return 'officeExtrasKeysController';