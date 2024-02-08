<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\templateEngines\Twig\Extension;

class Node extends \Twig\Node\Node
{

	public function __construct($params, $lineno = 0, $tag = null)
	{
		parent::__construct(array('params' => $params), array(), $lineno, $tag);
	}

	public function compile(\Twig\Compiler $compiler)
	{
		$count = count($this->getNode('params'));

		$compiler
			->addDebugInfo($this);

		for ($i = 0; ($i < $count); $i++) {
			// argument is not an expression (such as, a \Twig_Node_Textbody)
			// we should trick with output buffering to get a valid argument to pass
			// to the functionToCall() function.
			if (!($this->getNode('params')->getNode($i) instanceof \Twig\Node\Expression\AbstractExpression)) {
				$compiler
					->write('ob_start();')
					->raw(PHP_EOL);

				$compiler
					->subcompile($this->getNode('params')->getNode($i));

				$compiler
					->write('$_trans[] = ob_get_clean();')
					->raw(PHP_EOL);
			} else {
				$compiler
					->write('$_trans[] = ')
					->subcompile($this->getNode('params')->getNode($i))
					->raw(';')
					->raw(PHP_EOL);
			}
		}

		$compiler
			->write('call_user_func_array(')
			->string('\Phacil\Framework\templateEngines\Twig\Extension\Translate::translate')
			->raw(', $_trans);')
			->raw(PHP_EOL);

		$compiler
			->write('unset($_trans);')
			->raw(PHP_EOL);
	}
}