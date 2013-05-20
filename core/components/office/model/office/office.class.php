<?php
/**
 * The base class for Office.
 *
 * @package office
 */

class Office {
	/* @var modX $modx */
	public $modx;
	public $initialized = array();
	public $controllers = array();


	function __construct(modX &$modx,array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('office_core_path', $config, $this->modx->getOption('core_path').'components/office/');
		$assetsUrl = $this->modx->getOption('office_assets_url', $config, $this->modx->getOption('assets_url').'components/office/');
		$connectorUrl = $assetsUrl.'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl
			,'cssUrl' => $assetsUrl.'css/'
			,'jsUrl' => $assetsUrl.'js/'
			,'imagesUrl' => $assetsUrl.'images/'

			,'connectorUrl' => $connectorUrl
			,'controllersPath' => $corePath.'controllers/'
			,'controllers' => ''

			,'corePath' => $corePath
			,'modelPath' => $corePath.'model/'
			,'chunksPath' => $corePath.'elements/chunks/'
			,'templatesPath' => $corePath.'elements/templates/'
			,'chunkSuffix' => '.chunk.tpl'
			,'snippetsPath' => $corePath.'elements/snippets/'
			,'processorsPath' => $corePath.'processors/'
		), $config);

		//$this->modx->addPackage('office', $this->config['modelPath']);
		$this->modx->lexicon->load('office:default');
		$tmp = explode(',', $this->config['controllers']);
		$this->config['controllers'] = array();
		foreach ($tmp as $v) {
			$this->config['controllers'][] = strtolower(trim($v));
		}
	}


	/**
	 * Initializes Office into different contexts.
	 *
	 * @access public
	 * @param string $ctx The context to load. Defaults to web.
	 */
	public function initialize($ctx = 'web', $scriptProperties = array()) {
		$this->config = array_merge($this->config, $scriptProperties);
		$this->config['ctx'] = $ctx;
		if (!empty($this->initialized[$ctx])) {
			return true;
		}
		switch ($ctx) {
			case 'mgr': break;
			default:
				if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
					$config = $this->makePlaceholders($this->config);
					if ($css = $this->modx->getOption('off_frontend_css')) {
						$this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
					}
					if ($js = trim($this->modx->getOption('off_frontend_js'))) {
						$this->modx->regClientStartupScript(str_replace('					', '', '
						<script type="text/javascript">
						OfficeConfig = {
							cssUrl: "'.$this->config['cssUrl'].'web/"
							,jsUrl: "'.$this->config['jsUrl'].'web/"
							,imagesUrl: "'.$this->config['imagesUrl'].'web/"
							,actionUrl: "'.$this->config['actionUrl'].'"
							,ctx: "'.$this->modx->context->get('key').'"
							,close_all_message: "'.$this->modx->lexicon('off_message_close_all').'"
						};
						</script>
					'), true);
						if (!empty($js) && preg_match('/\.js$/i', $js)) {
							$this->modx->regClientScript(str_replace('							', '', '
							<script type="text/javascript">
							if(typeof jQuery == "undefined") {
								document.write("<script src=\"'.$this->config['jsUrl'].'web/lib/jquery.min.js\" type=\"text/javascript\"><\/script>");
							}
							</script>
							'), true);
							$this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));
						}
					}
				}
				$this->loadControllers();
				$this->initialized[$ctx] = true;
				break;
		}
		return true;
	}


	/* Method loads custom controllers
	 *
	 * @var string $dir Directory for load controllers
	 * @return void
	 * */
	public function loadControllers($controller = '') {
		require_once $this->config['controllersPath'] . 'controller.class.php';

		if (!empty($controller) && is_array($controller)) {
			$controllers = $controller;
		}
		else if (!empty($controller)) {
			$controllers = array($controller);
		}
		else {
			$controllers = $this->config['controllers'];
		}

		foreach ($controllers as $name) {
			$file = $this->config['controllersPath'] . $name . '/' . $name.'.class.php';
			if (file_exists($file)) {
				$class = include_once($file);
				if (!class_exists($class)) {
					$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] Wrong controller at '.$file);
				}
				/* @var officeDefaultController $controller */
				else if ($controller = new $class($this, $this->config)) {
					if ($controller instanceof officeControllerInterface && $controller->initialize()) {
						$this->controllers[strtolower($name)] = $controller;
					}
					else {
						$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] Could not load controller '.$file);
					}
				}
			}
			else {
				$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] Could not find controller '.$file);
			}
		}
	}


	public function loadAction($action, $params = array()) {
		list($controller, $action) = explode('/', strtolower(trim($action)));
		if (!empty($controller) && !isset($this->controllers[$controller])) {
			$this->loadControllers($controller);
		}

		if (isset($this->controllers[$controller]) && method_exists($this->controllers[$controller], $action)) {
			return $this->controllers[$controller]->$action($params);
		}
		return false;
	}


	/* Method for transform array to placeholders
	 *
	 * @var array $array With keys and values
	 * @return array $array Two nested arrays With placeholders and values
	 * */
	public function makePlaceholders(array $array = array(), $prefix = '') {
		$result = array(
			'pl' => array()
			,'vl' => array()
		);
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$result = array_merge_recursive($result, $this->makePlaceholders($v, $k.'.'));
			}
			else {
				$result['pl'][$prefix.$k] = '[[+'.$prefix.$k.']]';
				$result['vl'][$prefix.$k] = $v;
			}
		}
		return $result;
	}



}