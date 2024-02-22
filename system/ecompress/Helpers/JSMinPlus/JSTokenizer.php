<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\ECompress\Helpers\JSMinPlus;

use Phacil\Framework\Exception;
use Phacil\Framework\ECompress\Helpers\JSMinPlus\JSToken;

/**
 * JSMinPlus version 1.4
 *
 * Minifies a javascript file using a javascript parser
 *
 * This implements a PHP port of Brendan Eich's Narcissus open source javascript engine (in javascript)
 * References: http://en.wikipedia.org/wiki/Narcissus_(JavaScript_engine)
 * Narcissus sourcecode: http://mxr.mozilla.org/mozilla/source/js/narcissus/
 * JSMinPlus weblog: http://crisp.tweakblogs.net/blog/cat/716
 *
 * Tino Zijdel <crisp@tweakers.net>
 *
 * Usage: $minified = JSMinPlus::minify($script [, $filename])
 *
 * Versionlog (see also changelog.txt):
 * 23-07-2011 - remove dynamic creation of OP_* and KEYWORD_* defines and declare them on top
 *              reduce memory footprint by minifying by block-scope
 *              some small byte-saving and performance improvements
 * 12-05-2009 - fixed hook:colon precedence, fixed empty body in loop and if-constructs
 * 18-04-2009 - fixed crashbug in PHP 5.2.9 and several other bugfixes
 * 12-04-2009 - some small bugfixes and performance improvements
 * 09-04-2009 - initial open sourced version 1.0
 *
 * Latest version of this script: http://files.tweakers.net/jsminplus/jsminplus.zip
 *
 */

/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1/GPL 2.0/LGPL 2.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is the Narcissus JavaScript engine.
 *
 * The Initial Developer of the Original Code is
 * Brendan Eich <brendan@mozilla.org>.
 * Portions created by the Initial Developer are Copyright (C) 2004
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s): Tino Zijdel <crisp@tweakers.net>
 * PHP port, modifications and minifier routine are (C) 2009-2011
 *
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 or later (the "GPL"), or
 * the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
 * in which case the provisions of the GPL or the LGPL are applicable instead
 * of those above. If you wish to allow use of your version of this file only
 * under the terms of either the GPL or the LGPL, and not to allow others to
 * use your version of this file under the terms of the MPL, indicate your
 * decision by deleting the provisions above and replace them with the notice
 * and other provisions required by the GPL or the LGPL. If you do not delete
 * the provisions above, a recipient may use your version of this file under
 * the terms of any one of the MPL, the GPL or the LGPL.
 *
 * ***** END LICENSE BLOCK ***** */

class JSTokenizer
{
	private $cursor = 0;
	private $source;

	public $tokens = array();
	public $tokenIndex = 0;
	public $lookahead = 0;
	public $scanNewlines = false;
	public $scanOperand = true;

	public $filename;
	public $lineno;

	private $keywords = array(
		'break',
		'case', 'catch', 'const', 'continue',
		'debugger', 'default', 'delete', 'do',
		'else', 'enum',
		'false', 'finally', 'for', 'function',
		'if', 'in', 'instanceof',
		'new', 'null',
		'return',
		'switch',
		'this', 'throw', 'true', 'try', 'typeof',
		'var', 'void',
		'while', 'with'
	);

	private $opTypeNames = array(
		';', ',', '?', ':', '||', '&&', '|', '^',
		'&', '===', '==', '=', '!==', '!=', '<<', '<=',
		'<', '>>>', '>>', '>=', '>', '++', '--', '+',
		'-', '*', '/', '%', '!', '~', '.', '[',
		']', '{', '}', '(', ')', '@*/'
	);

	private $assignOps = array('|', '^', '&', '<<', '>>', '>>>', '+', '-', '*', '/', '%');
	private $opRegExp;

	public function __construct()
	{
		$this->opRegExp = '#^(' . implode('|', array_map('preg_quote', $this->opTypeNames)) . ')#';
	}

	public function init($source, $filename = '', $lineno = 1)
	{
		$this->source = $source;
		$this->filename = $filename ? $filename : '[inline]';
		$this->lineno = $lineno;

		$this->cursor = 0;
		$this->tokens = array();
		$this->tokenIndex = 0;
		$this->lookahead = 0;
		$this->scanNewlines = false;
		$this->scanOperand = true;
	}

	public function getInput($chunksize)
	{
		if ($chunksize)
			return substr($this->source, $this->cursor, $chunksize);

		return substr($this->source, $this->cursor);
	}

	public function isDone()
	{
		return $this->peek() == TOKEN_END;
	}

	public function match($tt)
	{
		return $this->get() == $tt || $this->unget();
	}

	public function mustMatch($tt)
	{
	        if (!$this->match($tt))
			throw $this->newSyntaxError('Unexpected token; token ' . $tt . ' expected');

		return $this->currentToken();
	}

	public function peek()
	{
		if ($this->lookahead)
		{
			$next = $this->tokens[($this->tokenIndex + $this->lookahead) & 3];
			if ($this->scanNewlines && $next->lineno != $this->lineno)
				$tt = TOKEN_NEWLINE;
			else
				$tt = $next->type;
		}
		else
		{
			$tt = $this->get();
			$this->unget();
		}

		return $tt;
	}

	public function peekOnSameLine()
	{
		$this->scanNewlines = true;
		$tt = $this->peek();
		$this->scanNewlines = false;

		return $tt;
	}

	public function currentToken()
	{
		if (!empty($this->tokens))
			return $this->tokens[$this->tokenIndex];
	}

	public function get($chunksize = 1000)
	{
		while($this->lookahead)
		{
			$this->lookahead--;
			$this->tokenIndex = ($this->tokenIndex + 1) & 3;
			$token = $this->tokens[$this->tokenIndex];
			if ($token->type != TOKEN_NEWLINE || $this->scanNewlines)
				return $token->type;
		}

		$conditional_comment = false;

		// strip whitespace and comments
		while(true)
		{
			$input = $this->getInput($chunksize);

			// whitespace handling; gobble up \r as well (effectively we don't have support for MAC newlines!)
			$re = $this->scanNewlines ? '/^[ \r\t]+/' : '/^\s+/';
			if (preg_match($re, $input, $match))
			{
				$spaces = $match[0];
				$spacelen = strlen($spaces);
				$this->cursor += $spacelen;
				if (!$this->scanNewlines)
					$this->lineno += substr_count($spaces, "\n");

				if ($spacelen == $chunksize)
					continue; // complete chunk contained whitespace

				$input = $this->getInput($chunksize);
				if ($input == '' || $input[0] != '/')
					break;
			}

			// Comments
			if (!preg_match('/^\/(?:\*(@(?:cc_on|if|elif|else|end))?.*?\*\/|\/[^\n]*)/s', $input, $match))
			{
				if (!$chunksize)
					break;

				// retry with a full chunk fetch; this also prevents breakage of long regular expressions (which will never match a comment)
				$chunksize = null;
				continue;
			}

			// check if this is a conditional (JScript) comment
			if (!empty($match[1]))
			{
				$match[0] = '/*' . $match[1];
				$conditional_comment = true;
				break;
			}
			else
			{
				$this->cursor += strlen($match[0]);
				$this->lineno += substr_count($match[0], "\n");
			}
		}

		if ($input == '')
		{
			$tt = TOKEN_END;
			$match = array('');
		}
		elseif ($conditional_comment)
		{
			$tt = TOKEN_CONDCOMMENT_START;
		}
		else
		{
			switch ($input[0])
			{
				case '0':
					// hexadecimal
					if (($input[1] == 'x' || $input[1] == 'X') && preg_match('/^0x[0-9a-f]+/i', $input, $match))
					{
						$tt = TOKEN_NUMBER;
						break;
					}
				// FALL THROUGH

				case '1': case '2': case '3': case '4': case '5':
				case '6': case '7': case '8': case '9':
					// should always match
					preg_match('/^\d+(?:\.\d*)?(?:[eE][-+]?\d+)?/', $input, $match);
					$tt = TOKEN_NUMBER;
				break;

				case "'":
					if (preg_match('/^\'(?:[^\\\\\'\r\n]++|\\\\(?:.|\r?\n))*\'/', $input, $match))
					{
						$tt = TOKEN_STRING;
					}
					else
					{
						if ($chunksize)
							return $this->get(null); // retry with a full chunk fetch

						throw $this->newSyntaxError('Unterminated string literal');
					}
				break;

				case '"':
					if (preg_match('/^"(?:[^\\\\"\r\n]++|\\\\(?:.|\r?\n))*"/', $input, $match))
					{
						$tt = TOKEN_STRING;
					}
					else
					{
						if ($chunksize)
							return $this->get(null); // retry with a full chunk fetch

						throw $this->newSyntaxError('Unterminated string literal');
					}
				break;

				case '/':
					if ($this->scanOperand && preg_match('/^\/((?:\\\\.|\[(?:\\\\.|[^\]])*\]|[^\/])+)\/([gimy]*)/', $input, $match))
					{
						$tt = TOKEN_REGEXP;
						break;
					}
				// FALL THROUGH

				case '|':
				case '^':
				case '&':
				case '<':
				case '>':
				case '+':
				case '-':
				case '*':
				case '%':
				case '=':
				case '!':
					// should always match
					preg_match($this->opRegExp, $input, $match);
					$op = $match[0];
					if (in_array($op, $this->assignOps) && $input[strlen($op)] == '=')
					{
						$tt = OP_ASSIGN;
						$match[0] .= '=';
					}
					else
					{
						$tt = $op;
						if ($this->scanOperand)
						{
							if ($op == OP_PLUS)
								$tt = OP_UNARY_PLUS;
							elseif ($op == OP_MINUS)
								$tt = OP_UNARY_MINUS;
						}
						$op = null;
					}
				break;

				case '.':
					if (preg_match('/^\.\d+(?:[eE][-+]?\d+)?/', $input, $match))
					{
						$tt = TOKEN_NUMBER;
						break;
					}
				// FALL THROUGH

				case ';':
				case ',':
				case '?':
				case ':':
				case '~':
				case '[':
				case ']':
				case '{':
				case '}':
				case '(':
				case ')':
					// these are all single
					$match = array($input[0]);
					$tt = $input[0];
				break;

				case '@':
					// check end of conditional comment
					if (substr($input, 0, 3) == '@*/')
					{
						$match = array('@*/');
						$tt = TOKEN_CONDCOMMENT_END;
					}
					else
						throw $this->newSyntaxError('Illegal token');
				break;

				case "\n":
					if ($this->scanNewlines)
					{
						$match = array("\n");
						$tt = TOKEN_NEWLINE;
					}
					else
						throw $this->newSyntaxError('Illegal token');
				break;

				default:
					// FIXME: add support for unicode and unicode escape sequence \uHHHH
					if (preg_match('/^[$\w]+/', $input, $match))
					{
						$tt = in_array($match[0], $this->keywords) ? $match[0] : TOKEN_IDENTIFIER;
					}
					else
						throw $this->newSyntaxError('Illegal token');
			}
		}

		$this->tokenIndex = ($this->tokenIndex + 1) & 3;

		if (!isset($this->tokens[$this->tokenIndex]))
			$this->tokens[$this->tokenIndex] = new JSToken();

		$token = $this->tokens[$this->tokenIndex];
		$token->type = $tt;

		if ($tt == OP_ASSIGN)
			$token->assignOp = $op;

		$token->start = $this->cursor;

		$token->value = $match[0];
		$this->cursor += strlen($match[0]);

		$token->end = $this->cursor;
		$token->lineno = $this->lineno;

		return $tt;
	}

	public function unget()
	{
		if (++$this->lookahead == 4)
			throw $this->newSyntaxError('PANIC: too much lookahead!');

		$this->tokenIndex = ($this->tokenIndex - 1) & 3;
	}

	public function newSyntaxError($m)
	{
		return new Exception('Parse error: ' . $m . ' in file \'' . $this->filename . '\' on line ' . $this->lineno);
	}
}