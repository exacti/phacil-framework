<?php  
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

use Phacil\Framework\Controller;

class ControllerCommonHome extends Controller {
	
	public function index() {
		var_dump('oi');
		$this->document->setTitle("Hello World! %s");

		$this->load->model('common/teste');

		var_dump(get_class_methods($this->model_common_teste));

		var_dump($this->model_common_teste->oi());

		$this->load->helper('common/Data');

		$help = new TestOfHelp;

		var_dump($help->helpme());
		
        $this->data['variable'] = "Hello World!";

        $this->out();
	}
}