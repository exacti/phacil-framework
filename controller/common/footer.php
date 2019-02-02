<?php  
class ControllerCommonFooter extends Controller {
	protected function index() {
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/common/footer.twig')) {
			$this->template = $this->config->get('config_template') . '/common/footer.twig';
		} else {
			$this->template = 'default/common/footer.twig';
		}
		
		$this->render();
		
	}
}
