<?php
class PluginTranslate extends Dwoo\Block\Plugin
{
    // parameters go here if you need any settings
    public function init()
    {
    }

    // this can be ommitted, it's called once when the block ends, don't implement if you don't need it
    public function end()
    {
    }

    // this is called when the block is required to output it's data, it should read $this->buffer, process it and return it
    public function process(){
		
		if (class_exists('Translate')) {
    
			$trans = new Translate();
			return $trans->translation($this->buffer);
			
		} else {
			return $this->buffer;
		}
		
        
    }
}