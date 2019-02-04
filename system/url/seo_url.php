<?php
class SystemUrlSeoUrl extends Controller {
    private $notfound = 'error/not_found';

	public function index() {
		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}
		
		// Decode URL
		if (isset($this->request->get['_route_'])) {
			//$parts = explode('/', $this->request->get['_route_']);
			$parts = array($this->request->get['_route_']);

			foreach ($parts as $part) {
			    if(defined('USE_DB_CONFIG') && USE_DB_CONFIG == true)
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
					//var_dump($query->row['query']);
					$this->request->get['route'] = $query->row['query'];

				} elseif (defined('ROUTES') && is_array(ROUTES)) {
				    $rotas = ROUTES;
				    if(isset($rotas[$part])){
                        $this->request->get['route'] = $rotas[$part];
                    } else {
                        $this->request->get['route'] = $this->notfound;
                    }

                } else {
					$this->request->get['route'] = $this->notfound;
				}
			}

			
			if (isset($this->request->get['route'])) {
				return $this->forward($this->request->get['route']);
			}
		}
	}
	
	public function rewrite($link) {
		if ($this->config->get('config_seo_url')) {
			$url_data = parse_url(str_replace('&amp;', '&', $link));
		
			$url = ''; 
			
			$data = array();
			
			parse_str($url_data['query'], $data);

			foreach ($data as $key => $value) {

				if (isset($data['route'])) {
                    if(defined('USE_DB_CONFIG') && USE_DB_CONFIG == true)
                        $query = $this->db->query("SELECT * FROM url_alias WHERE `query` = '" . $this->db->escape($value) . "'");
					if (isset($query) && $query->num_rows && $query->num_rows != NULL) {
						$url .= '/' . $query->row['keyword'];
					} elseif (defined('ROUTES') && is_array(ROUTES)) {
                        $url .= '/' .(array_search($value, ROUTES));
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
?>