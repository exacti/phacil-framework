<?php
class ControllerErrorNotfound extends Controller {
	
	public function index() {
		$this->document->setTitle('404 %s');
		
		header("HTTP/1.1 404 Not Found"); 
		header("Status: 404 Not Found");
		
		$this->children = array(
			'common/footer',
			'common/header'
		);
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/error/not_found.tpl')) {
			$this->template = $this->config->get('config_template') . '/error/not_found.tpl';
		} else {
			$this->template = 'default/error/not_found.tpl';
		}
										
		$this->response->setOutput($this->render());
	}
}
