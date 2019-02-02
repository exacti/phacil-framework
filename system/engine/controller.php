<?php

abstract class Controller {
    protected $registry;
    protected $id;
    protected $layout;
    protected $template;
    protected $children = array();
    protected $data = array();
    protected $twig = array();
    protected $error = array();
    protected $output;

    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function __get($key) {
        return $this->registry->get($key);
    }

    public function __set($key, $value) {
        $this->registry->set($key, $value);
    }

    protected function forward($route, $args = array()) {
        return new Action($route, $args);
    }

    protected function redirect($url, $status = 302) {
        header('Status: ' . $status);
        header('Location: ' . str_replace('&amp;', '&', $url));
        exit();
    }

    protected function getChild($child, $args = array()) {
        $action = new Action($child, $args);
        $file = $action->getFile();
        $class = $action->getClass();
        $method = $action->getMethod();

        if (file_exists($file)) {
            require_once($file);

            $controller = new $class($this->registry);

            $controller->$method($args);

            return $controller->output;
        } else {
            trigger_error('Error: Could not load controller ' . $child . '!');
            exit();
        }
    }

    protected function render() {

        foreach ($this->children as $child) {
            $this->data[basename($child)] = $this->getChild($child);
        }

        if($this->template === NULL) {
            $pegRout = explode("/", $this->request->get['route']);

            $this->templateTypes = array("tpl", "twig", "dwoo", "mustache", "smarty");

            foreach($this->templateTypes as $extensionTemplate) {
                if(isset($pegRout[2])){
                    if (file_exists(DIR_TEMPLATE . 'default/'.$pegRout[0].'/'.$pegRout[1].'_'.$pegRout[2].'.'.$extensionTemplate) != false) {
                        $this->template = 'default/'.$pegRout[0].'/'.$pegRout[1].'_'.$pegRout[2].'.'.$extensionTemplate;
                        break;
                    }
                } else {
                    if (file_exists(DIR_TEMPLATE . 'default/'.$pegRout[0].'/'.$pegRout[1].'.'.$extensionTemplate) != false) {
                        $this->template = 'default/'.$pegRout[0].'/'.$pegRout[1].'.'.$extensionTemplate;
                        break;
                    }
                }
            }

        }

        if (file_exists(DIR_TEMPLATE . $this->template)) {

            $templateType = substr(strrchr($this->template, '.'), 1);

            switch($templateType) {
                case 'tpl':
                    extract($this->data);

                    ob_start();
                    require(DIR_TEMPLATE . $this->template);

                    $this->output = ob_get_contents();

                    ob_end_clean();
                    break;

                case 'twig':
                    require_once(DIR_SYSTEM."templateEngines/Twig/autoload.php");

                    $config = array(
                        'autoescape' => false,
                        'cache'		 => DIR_CACHE."twig/",
                        'debug'      => (defined('DEBUG')) ? DEBUG : false
                    );
                    $loader = new Twig_Loader_Filesystem(DIR_TEMPLATE);
                    $twig = new Twig_Environment($loader, $config);

                    if($config['debug']) {
                        $twig->addExtension(new Twig_Extension_Debug());
                    }

                    $twig->addExtension(new transExtension());

                    $twig->addFilter(new Twig_SimpleFilter('translate', function ($str) {
                        // do something different from the built-in date filter
                        return traduzir($str);
                    }));

                    foreach ($this->twig as $key => $item) {
                        $twig->addFilter(new Twig_SimpleFilter($key, $item));
                    }

                    $template = $twig->load($this->template);

                    $this->output = $template->render($this->data);
                    break;

                case 'dwoo':
                    require_once(DIR_SYSTEM."templateEngines/Dwoo/autoload.php");

                    $dwoo = new Dwoo\Core();

                    // Configure directories
                    $dwoo->setCompileDir(DIR_CACHE.'dwoo/compiled/dir/'); // Folder to store compiled templates
                    $dwoo->setCacheDir(DIR_CACHE.'dwoo/cache/');// Folder to store cached templates
                    $dwoo->setCacheTime(3600); //Cache Time
                    $dwoo->setTemplateDir(DIR_TEMPLATE); // Folder containing template files

                    $dwoo->getLoader()->addDirectory(DIR_SYSTEM."templateEngines/Dwoo/extension/");

                    $this->output = $dwoo->get($this->template, $this->data);

                    break;

                case 'mustache':
                    require_once(DIR_SYSTEM."templateEngines/Mustache/autoload.php");

                    Mustache_Autoloader::register();

                    $mustache = new Mustache_Engine(array(
                        //'template_class_prefix' => '__MyTemplates_',
                        'cache' => DIR_CACHE.'mustache',
                        'cache_file_mode' => 0666, // Please, configure your umask instead of doing this :)
                        //'cache_lambda_templates' => true,
                        'loader' => new Mustache_Loader_FilesystemLoader(DIR_TEMPLATE),
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

                    $smarty = new Smarty();

                    $smarty->setTemplateDir(DIR_TEMPLATE);
                    $smarty->setCompileDir(DIR_CACHE."Smarty/compile/");
                    //$smarty->setConfigDir('/web/www.example.com/guestbook/configs/');
                    $smarty->setCacheDir(DIR_CACHE."Smarty/cache/");

                    $smarty->registerPlugin("block","translate", "translate");

                    $smarty->assign($this->data);

                    $smarty->caching = Smarty::CACHING_LIFETIME_CURRENT;

                    //** un-comment the following line to show the debug console
                    $smarty->debugging = (defined('DEBUG')) ? DEBUG : false;

                    $this->output = $smarty->display($this->template);
                    break;

                default:
                    extract($this->data);

                    ob_start();
                    require(DIR_TEMPLATE . $this->template);

                    $this->output = ob_get_contents();

                    ob_end_clean();
                    break;

            }

            return $this->output;

        } else {
            trigger_error('Error: Could not load template ' . DIR_TEMPLATE . $this->template . '!');
            exit();
        }
    }

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
?>