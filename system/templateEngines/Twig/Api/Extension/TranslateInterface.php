<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\templateEngines\Twig\Api\Extension;

/**
 * Twig translate extension interface
 * 
 * @package Phacil\Framework\templateEngines\Twig\Api\Extension
 */
interface TranslateInterface {

	const TWIG_TAG_INI = 't';

	const TWIG_TAG_CLOSE = 'endt';

	/**
	 * 
	 * @return \Twig\TokenParser\TokenParserInterface[] 
	 */
	public function getTokenParsers();

	/**
	 * 
	 * @return string 
	 */
	public function getName();

	/**
	 * 
	 * @return void 
	 * @throws \ReflectionException 
	 * @throws \Exception 
	 * @throws \Phacil\Framework\Exception 
	 * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException 
	 */
	public function traduzir();

	/**
	 * 
	 * @return void 
	 * @throws \ReflectionException 
	 * @throws \Exception 
	 * @throws \Phacil\Framework\Exception 
	 * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException 
	 */
	static public function translate();
}