<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use Phacil\Framework\Config;
use Phacil\Framework\Registry;

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
 * @api
 */
abstract class Controller implements \Phacil\Framework\Interfaces\Controller {
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
             * @var \Phacil\Framework\Registry
             */
            $registry = \Phacil\Framework\Registry::getInstance();
        }
        $this->registry = &$registry;
    }

    /** @return void  */
    private function __getRegistryClass(){
        $this->registry = \Phacil\Framework\startEngineExacTI::getRegistry();
    }

    /**
     * 
     * {@inheritdoc}
     */
    static public function getInstance() {
        $class = get_called_class();
        return \Phacil\Framework\Registry::getAutoInstance((new $class()));
    }

    /**
     * @param string $key 
     * @return object 
     * @final
     */
    final public function __get($key) {
        if (!$this->registry) {
            $this->__getRegistryClass();
        }

        return $this->registry->get($key);
    }

    /**
     * @param string $key 
     * @return object 
     * @final
     * @todo Not yet...
     */
    /* final public function __call($key, array $arguments) {
        if (!$this->registry) {
            $this->__getRegistryClass();
        }

        return $this->registry->get($key);
    } */

    /**
     * 
     * @param string $key 
     * @param object $value 
     * @return void 
     * @final
     */
    final public function __set($key, $value) {
        if(!$this->registry) {
            $this->__getRegistryClass();
        }

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
    final protected function redirect($url, $status = 302) {
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
				} catch (Exception $th) {
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
     * Render template
     * 
     * @return string 
     * @throws TypeError 
     * @throws Exception 
     * @final
     */
    protected function render() {

        foreach ($this->children as $child) {
            $this->data[basename($child)] = $this->getChild($child);
        }

        $tpl = new \Phacil\Framework\Render($this->registry);

        $pegRout = explode("/", ($this->registry->routeOrig)?: Request::GET('route'));
        /* $pegRoutWithoutLast = $pegRout;
        array_pop($pegRoutWithoutLast);
        $pegRoutWithoutPenultimate = $pegRoutWithoutLast;
        array_pop($pegRoutWithoutPenultimate); */

        if($this->template === NULL) {
            $thema = ($this->config->get("config_template") != NULL) ? $this->config->get("config_template") : "default";

            $noCaseFunction = function ($str) {
                return \Phacil\Framework\Registry::case_insensitive_pattern($str);
            };

            //Just template not set manual
            $routePatterned = array_map($noCaseFunction, $pegRout);
            $routeWithoutLastPatterned = $routePatterned;
            $lastRoutePatterned = array_pop($routeWithoutLastPatterned);
            $routeWithoutFirstPatterned = $routePatterned;
            $firstRoutePatterned = array_shift($routeWithoutFirstPatterned);

            $structure = [];

            $structure[self::TEMPLATE_AREA_THEME][] =  $thema . '/' . implode("/", $routePatterned);
            $structure[self::TEMPLATE_AREA_THEME][] = 'default/' . implode("/", $routePatterned);
            //$structure[] = implode("/", $routePatterned);
            $structure[self::TEMPLATE_AREA_MODULAR][] = $firstRoutePatterned . '/View/' . implode("/", $routeWithoutFirstPatterned);

            if (count($routePatterned) > 2) {
                $structure[self::TEMPLATE_AREA_MODULAR][] = $firstRoutePatterned . '/View/' . implode("/", array_slice($routeWithoutFirstPatterned, 0, -1)) . "_" . $lastRoutePatterned;

                //Old compatibility
                $structure[self::TEMPLATE_AREA_THEME][] =  $thema . '/' . implode("/", $routeWithoutLastPatterned) . '_' . $lastRoutePatterned ;
                $structure[self::TEMPLATE_AREA_THEME][] = 'default/' . implode("/", $routeWithoutLastPatterned) . '_' . $lastRoutePatterned ;
                //$structure[self::TEMPLATE_AREA_THEME][] = implode("/", $routeWithoutLastPatterned) . '_' . $lastRoutePatterned ;
            }

            
            foreach($structure[self::TEMPLATE_AREA_MODULAR] as $themefile){
                $types = [
                    'modular' => Config::DIR_APP_MODULAR() .$themefile,
                    //'theme' => Config::DIR_TEMPLATE() .$themefile,
                ];
                $files['modular'] = null;
                $files['theme'] = null;
                foreach ($types as $type => $globs) {
                    foreach ($tpl->getTemplateTypes() as $extensionTemplate) {
                        $files[$type] = glob($globs. "." .$extensionTemplate, GLOB_BRACE);
                        if(count($files[$type]) > 0) break;
                    }
                }
                if(empty($files['modular']) && empty($files['theme'])) continue;
                if(!empty($files['modular'])) {
                    foreach ($files['modular'] as $modular){
                        $this->template = str_replace(Config::DIR_APP_MODULAR(), "", $modular);
                        $templatePath = Config::DIR_APP_MODULAR();
                        break;
                    }
                }
                if(!empty($files['theme'])) {
                    foreach ($files['theme'] as $modular){
                        $this->template = str_replace(Config::DIR_TEMPLATE(), "", $modular);
                        $templatePath = Config::DIR_TEMPLATE();
                        break;
                    }
                }
                if(!empty($this->template)) break;
            }
            
            foreach($structure[self::TEMPLATE_AREA_THEME] as $themefile){
                $types = [
                    //'modular' => Config::DIR_APP_MODULAR() .$themefile,
                    'theme' => Config::DIR_TEMPLATE() .$themefile,
                ];
                $files['modular'] = null;
                $files['theme'] = null;
                foreach ($types as $type => $globs) {
                    foreach ($tpl->getTemplateTypes() as $extensionTemplate) {
                        $files[$type] = glob($globs. "." .$extensionTemplate, GLOB_BRACE);
                        if(count($files[$type]) > 0) break;
                    }
                }
                if(empty($files['modular']) && empty($files['theme'])) continue;
                if(!empty($files['modular'])) {
                    foreach ($files['modular'] as $modular){
                        $this->template = str_replace(Config::DIR_APP_MODULAR(), "", $modular);
                        $templatePath = Config::DIR_APP_MODULAR();
                        break;
                    }
                }
                if(!empty($files['theme'])) {
                    foreach ($files['theme'] as $modular){
                        $this->template = str_replace(Config::DIR_TEMPLATE(), "", $modular);
                        $templatePath = Config::DIR_TEMPLATE();
                        break;
                    }
                }
                if(!empty($this->template)) break;
            }
            
        } else {
            if(file_exists(Config::DIR_APP_MODULAR(). $pegRout[0] ."/View/" .$this->template)){
                $templatePath = Config::DIR_APP_MODULAR(). $pegRout[0] ."/View/";
            } else {
                $filesSeted = glob(
                    Config::DIR_APP_MODULAR() .
                    \Phacil\Framework\Registry::case_insensitive_pattern($pegRout[0])
                    . "/View/" . $this->template,
                    GLOB_BRACE
                );

                if (count($filesSeted) > 0) {
                    $templatePath = str_replace($this->template, "", $filesSeted[0]);
                }
            }
            /* elseif(file_exists(Config::DIR_APP_MODULAR().implode("/", $pegRoutWithoutLast)."/View/" .$this->template)){
                $templatePath = Config::DIR_APP_MODULAR().implode("/", $pegRoutWithoutLast)."/View/";
            } elseif(file_exists(Config::DIR_APP_MODULAR().implode("/", $pegRoutWithoutPenultimate)."/View/" .$this->template)){
                $templatePath = Config::DIR_APP_MODULAR().implode("/", $pegRoutWithoutPenultimate)."/View/";
            } */
            if(file_exists(Config::DIR_TEMPLATE() .$this->template)){
                $templatePath = Config::DIR_TEMPLATE();
            }
        }

        if(empty($this->template)) {
            throw new Exception('Error: template not seted!');
        }

        if (file_exists($templatePath . $this->template)) {

            $templateFileInfo = pathinfo($templatePath .$this->template);
            $templateType = $templateFileInfo['extension'];

            $tpl->setTemplate($templateType, $templatePath, $this->template, $this->data, $this->twig);

            $this->registry->response->addHeader('Content-Type', $this->contentType);

            $this->output = $tpl->render();

            unset($tpl);

            return $this->output;

        } else {
            throw new Exception('Error: Could not load template ' . $templatePath . $this->template . '!');
        }
    }

    /**
     * @param bool $commonChildren (optional) Whether to include the common children
     * @return \Phacil\Framework\Response 
     * @throws Exception 
     * @since 1.1.0
     */
    protected function out ($commonChildren = true) {
        if($commonChildren === true){
            $this->children = array_merge(array(
                'common/footer',
                'common/header'), $this->children
            );
        }

        return $this->response->setOutput($this->render());
    }
}

