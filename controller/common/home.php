<?php  
class ControllerCommonHome extends Controller {
	
	public function index() {
		$this->document->setTitle("Hello World! %s");
		
        $this->data['variable'] = "Hello World!";

        $this->out();
	}
}