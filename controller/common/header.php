<?php   
class ControllerCommonHeader extends Controller {
	protected function index() {
		$this->data['title'] = $this->document->getTitle();
		
		$this->document->addScript('https://code.jquery.com/jquery-3.3.1.min.js');
		$this->document->addScript('https://code.jquery.com/jquery-migrate-3.0.1.min.js');
		
		$this->document->addScript('https://use.fontawesome.com/releases/v5.4.1/js/all.js');
		$this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js');
		$this->document->addScript('https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js');
		
		
		
		$this->document->addStyle('https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');
        $this->document->addStyle('https://use.fontawesome.com/releases/v5.6.0/css/all.css');
        
		$this->document->addStyle('assets/style.css');
        
		$this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
		$this->data['links'] = $this->document->getLinks();	 
		$this->data['styles'] = $this->document->getStyles();
		$this->data['scripts'] = $this->document->getScripts();
		$this->data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		
		$this->data['icon'] = HTTP_IMAGE.'favicon.png';
		
		$this->template = 'default/common/header.twig';
		
    	$this->render();
	} 	
}
?>
