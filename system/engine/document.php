<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use Phacil\Framework\Config;
use Phacil\Framework\Api\Document as DocumentInterface;

/** @package Phacil\Framework */
class Document implements DocumentInterface {
	private $title;
	private $description;
	private $keywords;	
	private $links = array();		
	private $styles = array();
	private $scripts = array();
	private $fbmetas = array();
	
	/**
	 * {@inheritdoc}
	 */
	public function setTitle($title) {
		if(Config::PATTERSITETITLE()) {
			$this->title =  sprintf($title, Config::PATTERSITETITLE());
		} else {
			$this->title = $title;
		}
	}

	/** {@inheritdoc}  */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}

	/** {@inheritdoc} */
	public function getKeywords() {
		return $this->keywords;
	}

	/**
	 * {@inheritdoc}
	 */
	public function addLink($href, $rel) {
		$this->links[md5($href)] = array(
			'href' => $href,
			'rel'  => $rel
		);			
	}

	/** {@inheritdoc} */
	public function getLinks() {
		return $this->links;
	}

	/**
	 * @param string $var 
	 * @return string 
	 */
	private function checkCDN( $var) {

        if(Config::CDN()) {
            if($this->checkLocal($var)){
                $var = Config::CDN().$var;
            }
        }

        return $var;
    }

	/**
	 * {@inheritdoc}
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

	/** {@inheritdoc} */
	public function getStyles() {
		return $this->styles;
	}

	/**
	 * {@inheritdoc}
	 */
	public function addScript($script, $sort = 0, $minify = true) {
	    if($minify) $script = $this->cacheMinify($script, 'js');
        $script = $this->checkCDN($script);
		$this->scripts[($sort)][md5($script)] = $script;			
	}

	/** {@inheritdoc} */
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
	    $dirCache = Config::DIR_PUBLIC(). $dir;
        $newName = str_replace("/", "_", $ref);
        $file = Config::DIR_PUBLIC().$ref;
        $cachedFile = $dirCache.$newName;
        $cacheFile = $dir.$newName;

        if(!$this->checkLocal($ref)) {
            return $ref;
        }

        if (!file_exists($dirCache)) {
            mkdir($dirCache, 0755, true);
        }

        if (is_file($file) and Config::CACHE_MINIFY()) {
			switch ($type) {
				case 'js':
					if (file_exists($cachedFile) and Config::CACHE_JS_CSS()) {
						return $cacheFile;
					} else {
						$buffer = file_get_contents($file);

						$buffer = preg_replace('/<!--(.*)-->/Uis', '', $buffer);

						/** @var \Phacil\Framework\ECompress\JSMin */
						$buffer = \Phacil\Framework\Registry::getInstance()->create(\Phacil\Framework\ECompress\JSMin::class, [$buffer]);

						$buffer = $buffer->min();

						file_put_contents($cachedFile, $buffer);

						return $cacheFile;
					}
					break;
				case 'css':
					if (file_exists($cachedFile) && Config::CACHE_JS_CSS()) {
						return $cacheFile;
					} else {
						$buffer = file_get_contents($file);

						if($buffer){
							/** @var \Phacil\Framework\ECompress\cssMin */
							$cssMin = \Phacil\Framework\Registry::getInstance()->getInstance(\Phacil\Framework\ECompress\cssMin::class);

							file_put_contents($cachedFile, $cssMin->minify($buffer));

							return $cacheFile;
						}
						return $ref;
					}
					break;
				default:
					return $ref;
					break;
			}
        } else {
            return $ref;
        }
    }
}
