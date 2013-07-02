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
			$this->modx->sendUnauthorizedPage();
			return '';
		}
		else {
			$config = $this->office->makePlaceholders($this->office->config);
			if ($css = trim($this->modx->getOption('office_extras_frontend_css'))) {
				$this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
			}
			if ($js = trim($this->modx->getOption('office_extras_frontend_js', null, '[[+jsUrl]]extras/default.js'))) {
				$this->office->addClientExtJS();
				$this->office->addClientLexicon(array(
					'extras:default'
				), 'extras/lexicon');
				$this->office->addClientJs(array(
					'/assets/components/extras/js/mgr/extras.js'
					,'/assets/components/extras/js/misc/extras.combo.js'
					,$this->office->config['jsUrl'] . 'extras/keys.grid.js'
					,str_replace($config['pl'], $config['vl'], $js)
				), 'extras/all');
			}

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
	public function resetHost($data) {
		/* @var modProcessorResponse $response */
		$response = $this->office->runProcessor('extras/key/reset', $data);
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