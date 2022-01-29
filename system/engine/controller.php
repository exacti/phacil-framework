<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use TypeError;
use Mustache_Exception_UnknownTemplateException;
use RuntimeException;
use SmartyException;

/** 
 * Extend this class to create interation with your module controller to Phacil engine controller.
 * 
 * Use as:
 * <code>
 * <?php 
 * namespace YourPrefix\Path\Controller;
 * class YouClass extends \Phacil\Framework\Controller {
 *  public function index() {
 *      #Your code
 *  }
 * } 
 * </code>
 * 
 * You can use the __construct function on call the \Phacil\Framework\Register object inside parent.
 * 
 * <code>
 *  public funcion __construct(\Phacil\Framework\Registry $registry){ parent::__construct($registry); YOUR_CODE; }
 * </code>
 * 
 * @abstract
 * @package Phacil\Framework 
 * @since 0.1.0
 */
abstract class Controller {
    /**
     * 
     * @var Registry
     */
    protected $registry;

    /**
     * 
     * @var int
     */
    protected $id;

    /**
     * 
     * @var mixed
     */
    protected $layout;

    /**
     * The template path
     * 
     * @var string
     */
    protected $template;

    /**
     * The childrens parts of the controller
     * 
     * @var array
     */
    protected $children = array();

    /**
     * The variables of controller
     * 
     * @var array
     */
    protected $data = array();

    /**
     * The twig aditional funcions created by your!
     * 
     * @var array
     */
    protected $twig = array();

    /**
     * The errors array
     * 
     * @var array
     */
    protected $error = array();
    
    /**
     * The rendered output 
     * 
     * @var string
     */
    protected $output;

    /**
     * Allowed template types
     * 
     * @var string[]
     */
    public $templateTypes = ["tpl", "twig", "mustache", "smarty", "phtml"];

    /**
     * The original route of childrens
     * @var string
     */
    public $routeOrig;

    /**
     * The output content type
     * 
     * @var string
     */
    public $contentType = 'text/html; charset=utf-8';

    /**
     * Implements constructor.
     * 
     * If you use this, don't forget the parent::__construct($registry);
     * 
     * @param \Phacil\Framework\Registry $registry 
     * @return void 
     */
    public function __construct(\Phacil\Framework\Registry $registry = null) {
        if (!$registry) {

            /**
             * @global \Phacil\Framework\startEngineExacTI $engine
             */
            global $engine;

            $registry =& $engine->registry;
        }
        $this->registry =& $registry;
    }

    /**
     * 
     * @param string $key 
     * @return Registry 
     * @final
     */
    final public function __get($key) {
        return $this->registry->get($key);
    }

    /**
     * 
     * @param string $key 
     * @param object $value 
     * @return void 
     * @final
     */
    final public function __set($key, $value) {
        $this->registry->set($key, $value);
    }

    /**
     * @param string $route 
     * @param array $args 
     * @return \Phacil\Framework\Interfaces\Action
     * @final
     */
    final protected function forward($route, array $args = array()) {
        return new Action($route, $args);
    }

    /**
     * Send redirect HTTP header to specified URL
     * 
     * Use the \Phacil\Framework\Response::redirect() registered object.
     * 
     * @param string $url 
     * @param int $status 
     * @return never 
     * @final
     */
    final protected function redirect($url, int $status = 302) {
        $this->registry->response->redirect($url, $status);
    }

    /**
     * Get and load the childrens controller classes
     * 
     * @final
     * @param string $child 
     * @param array $args 
     * @return object 
     */
    final protected function getChild($child, array $args = array()) {
        $action = new Action($child, $args);
        $file = $action->getFile();
        $class = $action->getClass();
		$classAlt = $action->getClassAlt();
        $method = $action->getMethod();

        if (file_exists($file)) {
            require_once($file);

            foreach($classAlt as $classController){
				try {
                    if(class_exists($classController)){
                        $this->registry->routeOrig = $child;
					    $controller = new $classController($this->registry);
					
					    break;
                    }
				} catch (\Throwable $th) {
					//throw $th;
				}
			}

            $controller->$method($args);

            $this->registry->routeOrig = null;

            return $controller->output;
        } else {
            throw new Exception("Could not load controller " . $child . '!', 1);
            
            //exit();
        }
    }

    /**
     * @return string 
     * @throws TypeError 
     * @throws Mustache_Exception_UnknownTemplateException 
     * @throws RuntimeException 
     * @throws SmartyException 
     * @throws Exception 
     * @final
     */
    final protected function render() {

        foreach ($this->children as $child) {
            $this->data[basename($child)] = $this->getChild($child);
        }

        $pegRout = explode("/", ($this->registry->routeOrig)?: $this->request->get['route']);
        $pegRoutWithoutLast = $pegRout;
        array_pop($pegRoutWithoutLast);
        $pegRoutWithoutPenultimate = $pegRoutWithoutLast;
        array_pop($pegRoutWithoutPenultimate);

        if($this->template === NULL) {

            $thema = ($this->config->get("config_template") != NULL) ? $this->config->get("config_template") : "default";

            $structure = [];
            foreach($this->templateTypes as $extensionTemplate) {

                $structure[] =  $thema.'/'.$pegRout[0].'/'.$pegRout[1].((isset($pegRout[2])) ? '_'.$pegRout[2] : '').'.'.$extensionTemplate;
                $structure[] = 'default/'.$pegRout[0].'/'.$pegRout[1].((isset($pegRout[2])) ? '_'.$pegRout[2] : '').'.'.$extensionTemplate;
                $structure[] = $pegRout[0].'/'.$pegRout[1].((isset($pegRout[2])) ? '_'.$pegRout[2] : '').'.'.$extensionTemplate;
                $structure[] = implode("/", $pegRoutWithoutLast).'/View/'.end($pegRout).'.'.$extensionTemplate;
                $structure[] = implode("/", $pegRoutWithoutPenultimate).'/View/'.((isset($pegRout[count($pegRout)-2])) ? $pegRout[count($pegRout)-2]."_".end($pegRout) : end($pegRout)).'.'.$extensionTemplate;


                foreach($structure as $themefile){
                    if(file_exists(DIR_APP_MODULAR .$themefile)){
                        $this->template = $themefile;
                        $templatePath = DIR_APP_MODULAR;
                        break;
                    }
                    if(file_exists(DIR_TEMPLATE .$themefile)){
                        $this->template = $themefile;
                        $templatePath = DIR_TEMPLATE;
                        break;
                    }
                }
                
            }
        } else {
            //$teste = DIR_APP_MODULAR.implode("/", $pegRoutWithoutLast)."/View/" .$this->template;
            if(file_exists(DIR_APP_MODULAR.implode("/", $pegRoutWithoutLast)."/View/" .$this->template)){
                $templatePath = DIR_APP_MODULAR.implode("/", $pegRoutWithoutLast)."/View/";
            } elseif(file_exists(DIR_APP_MODULAR.implode("/", $pegRoutWithoutPenultimate)."/View/" .$this->template)){
                $templatePath = DIR_APP_MODULAR.implode("/", $pegRoutWithoutPenultimate)."/View/";
            }
            if(file_exists(DIR_TEMPLATE .$this->template)){
                $templatePath = DIR_TEMPLATE;
            }
        }

        if (file_exists($templatePath . $this->template)) {

            $templateFileInfo = pathinfo($templatePath .$this->template);
            $templateType = $templateFileInfo['extension'];

            switch($templateType) {
                case 'tpl':
                    extract($this->data);

                    ob_start();
                    require($templatePath . $this->template);

                    $this->output = ob_get_contents();

                    ob_end_clean();
                    break;

                case 'phtml':
                    extract($this->data);

                    ob_start();
                    require($templatePath . $this->template);

                    $this->output = ob_get_contents();

                    ob_end_clean();
                    break;

                case 'twig':
                    require_once(DIR_SYSTEM."templateEngines/Twig/autoload.php");

                    /**
                     * @var array
                     */
                    $config = array(
                        'autoescape' => false,
                        'cache'		 => DIR_CACHE."twig/",
                        'debug'      => (defined('DEBUG')) ? DEBUG : false
                    );
                    $TwigLoaderFilesystem = constant('\TwigLoaderFilesystem');
                    $Twig_Environment = constant('\TwigEnvironment');
                    $Twig_SimpleFilter = constant('\TwigSimpleFilter');
                    $Twig_Extension_Debug = constant('\TwigExtensionDebug');

                    /**
                     * @var \TwigLoaderFilesystem
                     */
                    $loader = new $TwigLoaderFilesystem ($templatePath);

                    /**
                     * @var \TwigEnvironment
                     */
                    $twig = new $Twig_Environment($loader, $config);

                    if($config['debug']) {
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

                    foreach ($this->twig as $key => $item) {
                        $twig->addFilter(new $Twig_SimpleFilter($key, $item));
                    }

                    $template = $twig->load($this->template);

                    $this->output = $template->render($this->data);
                    break;


                case 'mustache':
                    require_once(DIR_SYSTEM."templateEngines/Mustache/autoload.php");

                    \Mustache_Autoloader::register();

                    /**
                     * @var \Mustache_Engine
                     */
                    $mustache = new \Mustache_Engine(array(
                        //'template_class_prefix' => '__MyTemplates_',
                        'cache' => DIR_CACHE.'mustache',
                        'cache_file_mode' => 0666, // Please, configure your umask instead of doing this :)
                        //'cache_lambda_templates' => true,
                        'loader' => new \Mustache_Loader_FilesystemLoader($templatePath),
                        //'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views/partials'),
                        'helpers' => array('translate' => function($text) {
                            if (class_exists('Translate')) {
                                $trans = new Translate();
                                return ($trans->translation($text));
                            } else {
                                return $text;
                            }// do something translatey here...
                        }),
                        /*'escape' => function($value) {
                            return $value;
                        },*/
                        //'charset' => 'ISO-8859-1',
                        //'logger' => new Mustache_Logger_StreamLogger('php://stderr'),
                        //'strict_callables' => true,
                        //'pragmas' => [Mustache_Engine::PRAGMA_FILTERS],
                    ));

                    $tpl = $mustache->loadTemplate($this->template);
                    $this->output = $tpl->render($this->data);
                    break;

                case 'smarty':
                    require_once(DIR_SYSTEM."templateEngines/smarty/autoload.php");

                    /**
                     * @var \Smarty
                     */
                    $smarty = new \Smarty();

                    $smarty->setTemplateDir($templatePath);
                    $smarty->setCompileDir(DIR_CACHE."Smarty/compile/");
                    //$smarty->setConfigDir('/web/www.example.com/guestbook/configs/');
                    $smarty->setCacheDir(DIR_CACHE."Smarty/cache/");

                    $smarty->registerPlugin("block","translate", "translate");

                    $smarty->assign($this->data);

                    $smarty->caching = \Smarty::CACHING_LIFETIME_CURRENT;

                    //** un-comment the following line to show the debug console
                    $smarty->debugging = (defined('DEBUG')) ? DEBUG : false;

                    $this->output = $smarty->display($this->template);
                    break;

                default:
                    extract($this->data);

                    ob_start();
                    require($templatePath . $this->template);

                    $this->output = ob_get_contents();

                    ob_end_clean();
                    break;

            }

            $this->registry->response->addHeader('Content-Type', $this->contentType);

            return $this->output;

        } else {
            trigger_error('Error: Could not load template ' . $templatePath . $this->template . '!');
            exit();
        }
    }

    /**
     * @param bool $commonChildren 
     * @return \Phacil\Framework\Response 
     * @throws Exception 
     * @final
     */
    final protected function out ($commonChildren = true) {
        if($commonChildren === true){
            $this->children = array_merge(array(
                'common/footer',
                'common/header'), $this->children
            );
        }

        return $this->response->setOutput($this->render());
    }
}

