<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\templateEngines\Twig\Extension;

use Phacil\Framework\templateEngines\Twig\Extension\Node;
use Phacil\Framework\templateEngines\Twig\Api\Extension\TranslateInterface;

class TokenParser extends \Twig\TokenParser\AbstractTokenParser
{

	public function parse(\Twig\Token $token)
	{
		$lineno = $token->getLine();

		$stream = $this->parser->getStream();

		// recovers all inline parameters close to your tag name
		$params = array_merge(array(), $this->getInlineParams($token));

		$continue = true;
		while ($continue) {
			// create subtree until the decidetransFork() callback returns true
			$body = $this->parser->subparse(array($this, 'decidetransFork'));

			// I like to put a switch here, in case you need to add middle tags, such
			// as: {% trans %}, {% nexttrans %}, {% endtrans %}.
			$tag = $stream->next()->getValue();

			switch ($tag) {
				case TranslateInterface::TWIG_TAG_CLOSE:
					$continue = false;
					break;
				default:
					throw new \Twig_Error_Syntax(sprintf('Unexpected end of template. Twig was looking for the following tags ' . TranslateInterface::TWIG_TAG_CLOSE . ' to close the ' . TranslateInterface::TWIG_TAG_INI . ' block started at line %d)', $lineno), -1);
			}

			// you want $body at the beginning of your arguments
			array_unshift($params, $body);

			// if your endtrans can also contains params, you can uncomment this line:
			// $params = array_merge($params, $this->getInlineParams($token));
			// and comment this one:
			$stream->expect(\Twig\Token::BLOCK_END_TYPE);
		}

		return new Node(new \Twig\Node\Node($params), $lineno, $this->getTag());
	}

	/**
	 * Recovers all tag parameters until we find a BLOCK_END_TYPE ( %} )
	 *
	 * @param \Twig_Token $token
	 * @return array
	 */
	protected function getInlineParams(\Twig\Token $token)
	{
		$stream = $this->parser->getStream();
		$params = array();
		while (!$stream->test(\Twig\Token::BLOCK_END_TYPE)) {
			$params[] = $this->parser->getExpressionParser()->parseExpression();
		}
		$stream->expect(\Twig\Token::BLOCK_END_TYPE);
		return $params;
	}

	/**
	 * Callback called at each tag name when subparsing, must return
	 * true when the expected end tag is reached.
	 *
	 * @param \Twig\Token $token
	 * @return bool
	 */
	public function decidetransFork(\Twig\Token $token)
	{
		return $token->test(array(TranslateInterface::TWIG_TAG_CLOSE));
	}

	/**
	 * Your tag name: if the parsed tag match the one you put here, your parse()
	 * method will be called.
	 *
	 * @return string
	 */
	public function getTag()
	{
		return TranslateInterface::TWIG_TAG_INI;
	}
}