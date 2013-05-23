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

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl
			,'assetsPath' => MODX_ASSETS_PATH .'components/office/'
			,'cssUrl' => $assetsUrl.'css/'
			,'jsUrl' => $assetsUrl.'js/'
			,'imagesUrl' => $assetsUrl.'images/'

			,'actionUrl' => $assetsUrl.'action.php'
			,'minifyUrl' => $assetsUrl.'min.php'
			,'controllersPath' => $corePath.'controllers/'
			,'cachePath' => $corePath.'cache/'
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
			$v = strtolower(trim($v));
			if (!empty($v)) {
				$this->config['controllers'][] = $v;
			}
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
					if ($css = $this->modx->getOption('office_frontend_css')) {
						$this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
					}
					if ($js = trim($this->modx->getOption('office_frontend_js'))) {
						$this->modx->regClientStartupScript(str_replace('					', '', '
						<script type="text/javascript">
						OfficeConfig = {
							cssUrl: "'.$this->config['cssUrl'].'"
							,jsUrl: "'.$this->config['jsUrl'].'"
							,actionUrl: "'.$this->config['actionUrl'].'"
							,close_all_message: "'.$this->modx->lexicon('office_message_close_all').'"
							,pageId: '.$this->modx->resource->id.'
						};
						</script>
					'), true);
						if (!empty($js) && preg_match('/\.js$/i', $js)) {
							$this->modx->regClientScript(str_replace('							', '', '
							<script type="text/javascript">
							if(typeof jQuery == "undefined") {
								document.write("<script src=\"'.$this->config['jsUrl'].'main/lib/jquery.min.js\" type=\"text/javascript\"><\/script>");
							}
							</script>
							'), true);
							$this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));
						}
					}
				}

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
	public function loadController($name) {
		require_once 'controller.class.php';

		$name = strtolower(trim($name));
		$file = $this->config['controllersPath'] . $name . '/' . $name.'.class.php';
		if (!file_exists($file)) {$file = $this->config['controllersPath'] . $name.'.class.php';}

		if (file_exists($file)) {
			$class = include_once($file);
			if (!class_exists($class)) {
				$this->modx->log(modX::LOG_LEVEL_ERROR, '[Office] Wrong controller at '.$file);
			}
			/* @var officeDefaultController $controller */
			else if ($controller = new $class($this, $this->config)) {
				if ($controller instanceof officeDefaultController && $controller->initialize()) {
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


	public function loadAction($action, $scriptProperties = array()) {
		if (!empty($action)) {
			@list($name, $action) = explode('/', strtolower(trim($action)));

			if (!isset($this->controllers[$name])) {
				$this->loadController($name);
			}

			if (isset($this->controllers[$name])) {
				/* @var officeDefaultController $controller */
				$controller = $this->controllers[$name];
				$controller->setDefault($scriptProperties);

				if (empty($action)) {$action = $controller->getDefaultAction();}
				if (method_exists($controller, $action)) {
					return $controller->$action($scriptProperties);
				}
			}
		}

		return false;
	}


	/*
	 * Shorthand for load and run an processor in this component
	 *
	 * {@inheritdoc}
	 * */
	function runProcessor($action = '', $scriptProperties = array()) {
		$this->modx->error->errors = $this->modx->error->message = null;

		return $this->modx->runProcessor($action, $scriptProperties, array(
				'processors_path' => $this->config['processorsPath']
			)
		);
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


	public function Minify($files = array()) {
		if (empty($files)) {return false;}

		$_GET['f'] = implode(',',$files);

		$min_libPath = MODX_MANAGER_PATH . 'min/lib';
		@set_include_path($min_libPath . PATH_SEPARATOR . get_include_path());

		if (!class_exists('Minify')) {
			require_once MODX_MANAGER_PATH . 'min/lib/Minify.php';
		}
		if (!class_exists('Minify_Controller_MinApp')) {
			require_once MODX_MANAGER_PATH . 'min/lib/Minify/Controller/MinApp.php';
		}

		$min_serveController = new Minify_Controller_MinApp();

		/* attempt to prevent suhosin issues */
		@ini_set('suhosin.get.max_value_length',4096);
		$min_serveOptions = array(
			'quiet' => true
			,'encodeMethod' => ''
		);
		if (!file_exists(MODX_CORE_PATH . 'cache/minify')) {
			mkdir(MODX_CORE_PATH . 'cache/minify');
		}
		Minify::setCache(MODX_CORE_PATH . 'cache/minify');

		if ($minify = Minify::serve($min_serveController, $min_serveOptions)) {
			if ($minify['success']) {
				return $minify['content'];
			}
		}

		return false;
	}


	public function addClientJs($files = array(), $file = 'main/all') {
		if ($js = $this->Minify($files)) {
			$file = 'js/'.$file.'.js';
			file_put_contents($this->config['assetsPath'] . $file, $js);
			if (file_exists($this->config['assetsPath'] . $file)) {
				$this->modx->regClientScript($this->config['assetsUrl'] . $file);
				return true;
			}
		}
		return false;
	}


	public function addClientLexicon($topics = array(), $file = 'main/lexicon') {
		$topics = array_merge(array('core:default'), $topics);

		foreach($topics as $topic) {
			$this->modx->lexicon->load($topic);
		}

		$entries = $this->modx->lexicon->fetch();
		$lang = '
			MODx.lang = {';
		$s = '';
		while (list($k,$v) = each ($entries)) {
			$s .= "'$k': ".'"'.strtr($v,array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/')).'",';
		}
		$s = trim($s,',');
		$lang .= $s.'
			};
			var _ = function(s,v) {
				return MODx.lang[s]
				if (v != null && typeof(v) == "object") {
					var t = ""+MODx.lang[s];
					for (var k in v) {
						t = t.replace("[[+"+k+"]]",v[k]);
					}
					return t;
				} else return MODx.lang[s];
			}';

		$lang = str_replace('			', '', $lang);
		$file = 'js/'.$file.'.js';
		file_put_contents($this->config['assetsPath'] . $file, $lang);
		if (file_exists($this->config['assetsPath'] . $file)) {
			$this->modx->regClientScript($this->config['assetsUrl'] . $file);
			return true;
		}

		return false;
	}


}