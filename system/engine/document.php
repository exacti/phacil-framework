<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

/** @package Phacil\Framework */
class Document {
	private $title;
	private $description;
	private $keywords;	
	private $links = array();		
	private $styles = array();
	private $scripts = array();
	private $fbmetas = array();
	
	/**
	 * @param string $title 
	 * @return void 
	 */
	public function setTitle($title) {
		if(PATTERSITETITLE != false) {
			$this->title =  sprintf($title, PATTERSITETITLE);
		} else {
			$this->title = $title;
		}
	}
	
	/** @return string  */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * @param string $description 
	 * @return void 
	 */
	public function setDescription($description) {
		$this->description = $description;
	}
	
	/** @return string  */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * @param string $keywords 
	 * @return void 
	 */
	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}
	
	/** @return string  */
	public function getKeywords() {
		return $this->keywords;
	}
	
	/**
	 * @param string $href 
	 * @param string $rel 
	 * @return void 
	 */
	public function addLink($href, $rel) {
		$this->links[md5($href)] = array(
			'href' => $href,
			'rel'  => $rel
		);			
	}
	
	/** @return array  */
	public function getLinks() {
		return $this->links;
	}

	/**
	 * @param string $var 
	 * @return string 
	 */
	private function checkCDN( $var) {

        if(defined('CDN')) {
            if($this->checkLocal($var)){
                $var = CDN.$var;
            }
        }

        return $var;

    }
	
	/**
	 * @param string $href 
	 * @param string $rel 
	 * @param string $media 
	 * @param bool $minify 
	 * @return void 
	 */
	public function addStyle($href, $rel = 'stylesheet', $media = 'screen', $minify = true) {

	    if ($minify) $href = $this->cacheMinify($href, 'css');

	    $href = $this->checkCDN($href);

		$this->styles[md5($href)] = array(
			'href'  => $href,
			'rel'   => $rel,
			'media' => $media
		);
	}
	
	/** @return array  */
	public function getStyles() {
		return $this->styles;
	}	
	
	/**
	 * @param string $script 
	 * @param int|string $sort 
	 * @param bool $minify 
	 * @return void 
	 */
	public function addScript($script, $sort = 0, $minify = true) {
	    if($minify) $script = $this->cacheMinify($script, 'js');
        $script = $this->checkCDN($script);
		$this->scripts[($sort)][md5($script)] = $script;			
	}
	
	/** @return array  */
	public function getScripts() {
		$a = $this->scripts;
		ksort($a);
		foreach($a as $value){
			foreach($value as $key => $value){
				$b[$key] = $value;
			}
		}
        return (isset($b)) ? $b : [];
	}
	
	/**
	 * @param string $property 
	 * @param string $content 
	 * @return void 
	 */
	public function addFBMeta($property, $content = ''){
		$this->fbmetas[md5($property)] = array(
			'property' => $property,
			'content'  => $content
		);
	}
	
	/** @return array  */
	public function getFBMetas(){
		return $this->fbmetas;
	}

	/**
	 * @param string $val 
	 * @return bool 
	 */
	private function checkLocal ($val) {
        $testaProtocolo = substr($val, 0, 7);

        return ($testaProtocolo != "http://" && $testaProtocolo != "https:/");
    }

	/**
	 * @param string $ref 
	 * @param string $type 
	 * @return string 
	 */
	private function cacheMinify($ref, $type) {

        $dir = "css-js-cache/";
	    $dirCache = DIR_PUBLIC. $dir;
        $newName = str_replace("/", "_", $ref);
        $file = DIR_PUBLIC.$ref;
        $cachedFile = $dirCache.$newName;
        $cacheFile = $dir.$newName;

        if(!$this->checkLocal($ref)) {
            return $ref;
        }

        if (!file_exists($dirCache)) {
            mkdir($dirCache, 0755, true);
        }

        if (file_exists($file) and defined('CACHE_MINIFY') and CACHE_MINIFY == true) {
            if($type == "js") {
                if(file_exists($cachedFile) and defined('CACHE_JS_CSS') and CACHE_JS_CSS == true) {
                    return $cacheFile;
                } else {

                    include_once DIR_SYSTEM."ecompress/JSMin.php";

                    $buffer = file_get_contents($file);

                    $buffer = preg_replace('/<!--(.*)-->/Uis', '', $buffer);

                    $buffer = JSMin::minify($buffer);

                    file_put_contents($cachedFile, $buffer);

                    return $cacheFile;

                }


            }elseif($type == "css") {
                if(file_exists($cachedFile) and defined('CACHE_JS_CSS') and CACHE_JS_CSS == true) {
                    return $cacheFile;
                } else {

                    include_once DIR_SYSTEM."ecompress/cssMin.php";

                    $buffer = file_get_contents($file);

                    $buffer = minimizeCSS($buffer);

                    file_put_contents($cachedFile, $buffer);

                    return $cacheFile;

                }

            } else {
                return $ref;
            }
        } else {
            return $ref;
        }


    }


}
