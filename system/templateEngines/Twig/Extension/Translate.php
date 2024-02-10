<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\templateEngines\Twig\Extension;

use Phacil\Framework\templateEngines\Twig\Api\Extension\TranslateInterface;
use Phacil\Framework\templateEngines\Twig\Extension\TokenParser;

class Translate extends \Twig\Extension\AbstractExtension implements TranslateInterface
{
	/**
	 * 
	 * @var \Phacil\Framework\templateEngines\Twig\Extension\Legacy\TokenParser
	 */
	private $tokenParser;

	public function __construct(TokenParser $tokenParser)
	{
		$this->tokenParser = $tokenParser;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTokenParsers()
	{
		return array(
			$this->tokenParser,
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
		$trans = \Phacil\Framework\Registry::getInstance(\Phacil\Framework\Translate::class);
		echo ($trans->translation($body));
	}

	/**
	 * {@inheritdoc}
	 */
	static public function translate()
	{
		$params = func_get_args();
		$body = array_shift($params);

		/** @var \Phacil\Framework\Translate */
		$trans = \Phacil\Framework\Registry::getInstance(\Phacil\Framework\Translate::class);
		echo ($trans->translation($body));
	}
}