<?php
/**
 * Copyright (c) 2019. ExacTI Technology Solutions
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

class ControllerErrorNotfound extends Controller {
	
	public function index() {
		$this->document->setTitle('404 %s');
		
		$this->response->addHeader("HTTP/1.1 404 Not Found");
        $this->response->addHeader("Status: 404 Not Found");
		
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
