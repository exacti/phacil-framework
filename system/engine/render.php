<?php 
/*
 * Copyright © 2022 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 */


 namespace Phacil\Framework;

 use Phacil\Framework\Translate;
 use Phacil\Framework\Registry;

 /**
  * 
  * @package Phacil\Framework
  */
 class Render {
	 /**
	  * 
	  * @var mixed
	  */
	 private $data;

	 /**
	  * 
	  * @var string
	  */
	 private $template;

	 /**
	  * 
	  * @var string
	  */
	 private $templatePath;

	 /**
	  * 
	  * @var string
	  */
	 private $templateType;

	 /**
	  * 
	  * @var mixed
	  */
	 private $output;

	 /**
	  * 
	  * @var mixed
	  */
	 private $extras;

	 /**
	  * 
	  * @var array
	  */
	 protected $templateTypes = ["tpl", "twig", "mustache", "smarty", "phtml"];

	/**
	 * 
	 * @var Registry
	 */
	 private $registry;

	 /**
	  * 
	  * @var \Phacil\Framework\Config
	  */
	 private $config;
	 
	 /**
	  * 
	  * @param mixed $templateType 
	  * @param mixed $templatePath 
	  * @param mixed $template 
	  * @param mixed $data 
	  * @param mixed $extras 
	  * @return void 
	  */
	function __construct(Registry $registry = null) {
		if (!$registry) {

			/**
			 * @global \Phacil\Framework\startEngineExacTI $engine
			 */
			global $engine;

			$registry = &$engine->registry;
		}
		$this->registry = &$registry;

		$this->config =& $this->registry->config;
	}

	/**
	 * 
	 * @param mixed $templateType 
	 * @param mixed $templatePath 
	 * @param mixed $template 
	 * @param mixed $data 
	 * @param mixed $extras 
	 * @return $this 
	 */
	public function setTemplate($templateType, $templatePath, $template, $data, $extras) {
		$this->data = $data;

		$this->template = $template;

		$this->templatePath = $templatePath;

		$this->templateType = $templateType;

		$this->extras = $extras;

		return $this;
	}

	/**
	 * 
	 * @return mixed 
	 */
	public function render(){
		$templateFunc = $this->templateType;

		if(method_exists($this,$templateFunc))
			$this->$templateFunc();

		return $this->output;
	}

	/**
	 * 
	 * @return array 
	 */
	public function getTemplateTypes(){
		return $this->templateTypes;
	}

	/**
	 * 
	 * @param array $templateTypes 
	 * @return array 
	 */
	public function setTemplateTypes(array $templateTypes){
		$this->templateTypes = $templateTypes;

		return $this->templateTypes;
	}

	/**
	 * 
	 * @param string $type 
	 * @return array 
	 */
	public function addTemplateType(string $type){
		$this->templateTypes[] = $type;

		return $this->templateTypes;
	}

	/**
	 * 
	 * @return void 
	 */
	protected function templateDefault(){
		extract($this->data);

		ob_start();
		require($this->templatePath . $this->template);

		$this->output = ob_get_contents();

		ob_end_clean();
	}

	protected function tpl(){
		$this->templateDefault();
	}

	protected function phtml () {
		$this->templateDefault();
	}

	/**
	 * Twig render
	 * @return void 
	 */
	protected function twig () {
		require_once(DIR_SYSTEM . "templateEngines/Twig/autoload.php");

		/**
		 * @var array
		 */
		$config = array(
			'autoescape' => false,
			'cache'		 => DIR_CACHE . "twig/",
			'debug'      => (defined('DEBUG')) ? DEBUG : false
		);
		$TwigLoaderFilesystem = constant('\TwigLoaderFilesystem');
		$Twig_Environment = constant('\TwigEnvironment');
		$Twig_SimpleFilter = constant('\TwigSimpleFilter');
		$Twig_Extension_Debug = constant('\TwigExtensionDebug');

		/**
		 * @var \TwigLoaderFilesystem
		 */
		$loader = new $TwigLoaderFilesystem($this->templatePath);

		/**
		 * @var \TwigEnvironment
		 */
		$twig = new $Twig_Environment($loader, $config);

		if ($config['debug']) {
			$twig->addExtension(new $Twig_Extension_Debug());
		}

		/**
		 * @var \transExtension
		 */
		$twig->addExtension(new \transExtension());

		$twig->addFilter(new $Twig_SimpleFilter('translate', function ($str) {
			// do something different from the built-in date filter
			return traduzir($str);
		}));

		$twig->addFilter(new $Twig_SimpleFilter('config', function ($str) {
			// do something different from the built-in date filter
			return $this->config->get($str);
		}));

		foreach ($this->extras as $key => $item) {
			$twig->addFilter(new $Twig_SimpleFilter($key, $item));
		}

		$template = $twig->load($this->template);

		$this->output = $template->render($this->data);
	}

	/**
	 * Mustache render
	 * @return void 
	 * @throws \TypeError 
	 * @throws \Mustache_Exception_UnknownTemplateException 
	 * @throws \RuntimeException 
	 */
	protected function mustache(){
		\Mustache_Autoloader::register();

		/**
		 * @var \Mustache_Engine
		 */
		$mustache = new \Mustache_Engine(array(
			'cache' => DIR_CACHE . 'mustache',
			'cache_file_mode' => 0666,
			'loader' => new \Mustache_Loader_FilesystemLoader($this->templatePath),
			'helpers' => array('translate' => function ($text) {
				if (class_exists('Translate')) {
					$trans = new Translate();
					return ($trans->translation($text));
				} else {
					return $text;
				} // do something translate here...
			})
		));

		$tpl = $mustache->loadTemplate($this->template);
		$this->output = $tpl->render($this->data);
	}

	/**
	 * Smarty 3 render
	 * @return void 
	 * @throws \SmartyException 
	 * @throws \Exception 
	 */
	protected function smarty() {
		/**
		 * @var \Smarty
		 */
		$smarty = new \Smarty();

		$smarty->setTemplateDir($this->templatePath);
		$smarty->setCompileDir(DIR_CACHE . "Smarty/compile/");
		
		$smarty->setCacheDir(DIR_CACHE . "Smarty/cache/");

		$smarty->registerPlugin("block", "translate", function ($text) {
			if (class_exists('Phacil\Framework\Translate')) {
				$trans = new Translate();
				return ($trans->translation($text));
			} else {
				return $text;
			} // do something translate here...
		});

		$smarty->assign($this->data);

		$smarty->caching = \Smarty::CACHING_LIFETIME_CURRENT;
		
		$smarty->debugging = (defined('DEBUG')) ? DEBUG : false;

		$this->output = $smarty->display($this->template);
	}
 }

