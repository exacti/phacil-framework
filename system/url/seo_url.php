<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 */

 //namespace Phacil\Framework;

use Phacil\Framework\Action;
use Phacil\Framework\Controller as ControllerController;
use Phacil\Framework\Registry;
use Phacil\Framework\Config;

class SystemUrlSeoUrl extends ControllerController {

    /**
     * 
     * @var string
     */
    private $notfound = 'error/not_found';

    /**
     * 
     * @var string[]
     */
    private $regType = array(
        "%d" => '(\\d{1,})',
        "%w" => '(\\w{1,})',
        "%a" => '([[:ascii:]]{1,})',
        "%" => '(.*)');

    /**
     * @param Registry $registry 
     * @return void 
     */
    public function __construct(\Phacil\Framework\Registry $registry)
    {
        parent::__construct($registry);

        $this->notfound = Config::NOT_FOUND() ?: $this->notfound;
        
    }

    /** @return Action|void  */
    public function index() {
        // Add rewrite to url class
        if ($this->config->get('config_seo_url')) {
            $this->url->addRewrite($this);
        }

        $match = array();

        // Decode URL
        if (isset($this->request->get['_route_'])) {
            $parts_count = explode('/', $this->request->get['_route_']);
            $parts = array($this->request->get['_route_']);

            foreach ($parts as $part) {
                if(Config::USE_DB_CONFIG())
                    $query = $this->db->query("SELECT * FROM url_alias WHERE keyword = '" . $this->db->escape($part) . "'");

                if (isset($query) && $query != false && $query->num_rows === 1) {

                    //$url = explode('=', $query->row['query']);

                    if($query->row['get'] != "") {
                        $a = explode(',', $query->row['get']);

                        foreach($a as $value) {
                            $b = explode('=', $value);
                            $_GET[$b[0]] = $b[1];
                        }
                    }

                    $this->request->get['route'] = $query->row['query'];

                } elseif (Config::ROUTES() && is_array(Config::ROUTES())) {
                    $rotas = Config::ROUTES();

                    if(isset($rotas[$part])){
                        $this->request->get['route'] = $rotas[$part];
                    } else {

                        foreach($rotas as $key => $page) {

                            if(isRegularExpression($key)) {

                                $preg = preg_quote($key, "/");

                                $preg = str_replace(array_keys($this->regType), array_values($this->regType), $preg);

                                if((@preg_match("/". $preg . "/", $parts[0], $match))) {

                                    $countTree = explode("/", $match[0]);

                                    if(count($countTree) == count($parts_count)){

                                        unset($match[0]);

                                        $match = $this->request->clean($match);

                                        $pagina = $page;

                                        break;
                                    }

                                }
                            }
                        }

                        if(isset($pagina)) {
                            $this->request->get['route'] = $pagina;
                        } else {
                            $this->request->get['route'] = $this->notfound;
                        }
                    }

                } else {
                    $this->request->get['route'] = $this->notfound;
                }
            }


            if (isset($this->request->get['route'])) {
                return $this->forward($this->request->get['route'], $match);
            }
        }
    }

    /**
     * @param string $link 
     * @return string 
     */
    public function rewrite($link) {
        if ($this->config->get('config_seo_url')) {
            $url_data = parse_url(str_replace('&amp;', '&', $link));

            $url = '';

            $data = array();

            parse_str($url_data['query'], $data);

            $joinRegex = implode("|", array_keys($this->regType));

            foreach ($data as $key => $value) {

                if (isset($data['route'])) {
                    if(Config::USE_DB_CONFIG())
                        $query = $this->db->query("SELECT * FROM url_alias WHERE `query` = '" . $this->db->escape($value) . "'");
                    if (isset($query) && $query->num_rows && $query->num_rows != NULL) {
                        $url .= '/' . $query->row['keyword'];
                    } elseif (Config::ROUTES() && is_array(Config::ROUTES())) {
                        //$arV = array_search($value, ROUTES);
                        foreach(Config::ROUTES() as $query => $page) {
                            if($page == $value){
                                if(isRegularExpression($query)){
                                    unset($data['route']);
                                    $qnt = substr_count($query, '%');
                                    if(count($data) == $qnt) {
                                        $str = $query;

                                        foreach($data as $replace){
                                            $str = preg_replace('/('.$joinRegex.')/', $replace, $str, 1);
                                        }

                                        $url .= '/' .($str);

                                        $data = false;

                                        break;
                                    }
                                } else {
                                    $url .= '/' . (array_search($value, Config::ROUTES()));
                                }

                            }

                        }

                    }
                    unset($data[$key]);
                }

            }

            if ($url) {
                unset($data['route']);

                $query = '';

                if ($data) {
                    foreach ($data as $key => $value) {
                        $query .= '&' . $key . '=' . $value;
                    }

                    if ($query) {
                        $query = '?' . trim($query, '&');
                    }
                }

                return $url_data['scheme'] . '://' . $url_data['host'] . (isset($url_data['port']) ? ':' . $url_data['port'] : '') . str_replace('/index.php', '', $url_data['path']) . $url . $query;
            } else {
                return $link;
            }
        } else {
            return $link;
        }
    }
}

/**
 * @param string $string 
 * @return bool 
 */
function isRegularExpression($string) {
    return (strpos($string, '%') !== false);
}

