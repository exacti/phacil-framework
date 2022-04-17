<?php
/**
 * Copyright © 2022 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 */


namespace Phacil\Framework;

/**
 * @since 2.0.0
 * @package Phacil\Framework
 */
class ReflectionClass extends \ReflectionClass{

	/**
	 * 
	 * @return \Phacil\Framework\PHPDocParser 
	 */
	public function getDocCommentParse() {
		$docParse = new \Phacil\Framework\PHPDocParser($this->getDocComment());

		$docParse->parse();
		
		return $docParse;
	}

}