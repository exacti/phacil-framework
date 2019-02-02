<?php
class Document {
	private $title;
	private $description;
	private $keywords;	
	private $links = array();		
	private $styles = array();
	private $scripts = array();
	private $fbmetas = array();
	
	public function setTitle($title) {
		if(PATTERSITETITLE != false) {
			$this->title =  sprintf($title, PATTERSITETITLE);
		} else {
			$this->title = $title;
		}
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}
	
	public function getKeywords() {
		return $this->keywords;
	}
	
	public function addLink($href, $rel) {
		$this->links[md5($href)] = array(
			'href' => $href,
			'rel'  => $rel
		);			
	}
	
	public function getLinks() {
		return $this->links;
	}

	private function checkCDN($var) {

        if(defined('CDN')) {
            if($this->checkLocal($var)){
                $var = CDN.$var;
            }
        }

        return $var;

    }
	
	public function addStyle($href, $rel = 'stylesheet', $media = 'screen', $minify = true) {

	    if ($minify) $href = $this->cacheMinify($href, 'css');

	    $href = $this->checkCDN($href);

		$this->styles[md5($href)] = array(
			'href'  => $href,
			'rel'   => $rel,
			'media' => $media
		);
	}
	
	public function getStyles() {
		return $this->styles;
	}	
	
	public function addScript($script, $sort = '0', $minify = true) {
	    if($minify) $script = $this->cacheMinify($script, 'js');
        $script = $this->checkCDN($script);
		$this->scripts[($sort)][md5($script)] = $script;			
	}
	
	public function getScripts() {
		$a = $this->scripts;
		ksort($a);
		foreach($a as $value){
			foreach($value as $key => $value){
				$b[$key] = $value;
			}
		}
		return $b;
	}
	
	public function addFBMeta($property, $content = ''){
		$this->fbmetas[md5($property)] = array(
			'property' => $property,
			'content'  => $content
		);
	}
	
	public function getFBMetas(){
		return $this->fbmetas;
	}

	private function checkLocal ($val) {
        $testaProtocolo = substr($val, 0, 7);

        if($testaProtocolo != "http://" && $testaProtocolo != "https:/"){
            return true;
        } else {
            return false;
        }

    }

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
?>