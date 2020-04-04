<?php

define('TWIGtagIni', 't');
define('TWIGtagClose', 'endt');



class transTokenParser extends \Twig\TokenParser\AbstractTokenParser
{

   public function parse(\Twig\Token $token)
   {
      $lineno = $token->getLine();

      $stream = $this->parser->getStream();

      // recovers all inline parameters close to your tag name
      $params = array_merge(array (), $this->getInlineParams($token));

      $continue = true;
      while ($continue)
      {
         // create subtree until the decidetransFork() callback returns true
         $body = $this->parser->subparse(array ($this, 'decidetransFork'));

         // I like to put a switch here, in case you need to add middle tags, such
         // as: {% trans %}, {% nexttrans %}, {% endtrans %}.
         $tag = $stream->next()->getValue();

         switch ($tag)
         {
            case TWIGtagClose:
               $continue = false;
               break;
            default:
               throw new \Twig_Error_Syntax(sprintf('Unexpected end of template. Twig was looking for the following tags '.TWIGtagClose.' to close the '.TWIGtagIni.' block started at line %d)', $lineno), -1);
         }

         // you want $body at the beginning of your arguments
         array_unshift($params, $body);

         // if your endtrans can also contains params, you can uncomment this line:
         // $params = array_merge($params, $this->getInlineParams($token));
         // and comment this one:
         $stream->expect(\Twig\Token::BLOCK_END_TYPE);
      }

      return new transNode(new \Twig\Node($params), $lineno, $this->getTag());
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
      $params = array ();
      while (!$stream->test(\Twig\Token::BLOCK_END_TYPE))
      {
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
      return $token->test(array (TWIGtagClose));
   }

   /**
    * Your tag name: if the parsed tag match the one you put here, your parse()
    * method will be called.
    *
    * @return string
    */
   public function getTag()
   {
      return TWIGtagIni;
   }

}


class transNode extends \Twig\Node\Node
{

   public function __construct($params, $lineno = 0, $tag = null)
   {
      parent::__construct(array ('params' => $params), array (), $lineno, $tag);
   }

   public function compile(\Twig\Compiler $compiler)
   {
      $count = count($this->getNode('params'));

      $compiler
         ->addDebugInfo($this);

      for ($i = 0; ($i < $count); $i++)
      {
         // argument is not an expression (such as, a \Twig_Node_Textbody)
         // we should trick with output buffering to get a valid argument to pass
         // to the functionToCall() function.
         if (!($this->getNode('params')->getNode($i) instanceof \Twig\Node\Expression\AbstractExpression))
         {
            $compiler
               ->write('ob_start();')
               ->raw(PHP_EOL);

            $compiler
               ->subcompile($this->getNode('params')->getNode($i));

            $compiler
               ->write('$_trans[] = ob_get_clean();')
               ->raw(PHP_EOL);
         }
         else
         {
            $compiler
               ->write('$_trans[] = ')
               ->subcompile($this->getNode('params')->getNode($i))
               ->raw(';')
               ->raw(PHP_EOL);
         }
      }

      $compiler
         ->write('call_user_func_array(')
         ->string('traduzir')
         ->raw(', $_trans);')
         ->raw(PHP_EOL);

      $compiler
         ->write('unset($_trans);')
         ->raw(PHP_EOL);
   }

}



class transExtension extends \Twig\Extension\AbstractExtension
{

   public function getTokenParsers()
   {
      return array (
              new transTokenParser(),
      );
   }

   public function getName()
   {
      return TWIGtagIni;
   }

}


function traduzir() {
	$params = func_get_args();
	$body = array_shift($params);
	
	if (class_exists('Translate')) {
		$trans = new Translate();
		echo ($trans->translation($body)); 
	} else {
		echo $body;
	}
	
}