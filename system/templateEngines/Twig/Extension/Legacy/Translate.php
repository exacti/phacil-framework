<?php
/**
 * Copyright (c) 2024. ExacTI Technology Solutions
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\templateEngines\Twig\Extension\Legacy;

use Phacil\Framework\templateEngines\Twig\Api\Extension\TranslateInterface;
use Phacil\Framework\templateEngines\Twig\Extension\Legacy\TokenParser;

class Translate extends \Twig_Extension implements TranslateInterface
{

	/**
	 * {@inheritdoc}
	 */
	public function getTokenParsers()
	{
		return array(
			new TokenParser(),
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return self::TWIG_TAG_INI;
	}

	/**
	 * {@inheritdoc}
	 */
	public function traduzir()
	{
		$params = func_get_args();
		$body = array_shift($params);

		/** @var \Phacil\Framework\Translate */
		$trans = \Phacil\Framework\Registry::getInstance("Phacil\Framework\Translate");
		echo ($trans->translation($body));
	}

	/**
	 * {@inheritdoc}
	 */
	static public function translate() {
		$params = func_get_args();
		$body = array_shift($params);

		/** @var \Phacil\Framework\Translate */
		$trans = \Phacil\Framework\Registry::getInstance("Phacil\Framework\Translate");
		echo ($trans->translation($body));
	}
}
