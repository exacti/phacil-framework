<?php
/*
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

use \Phacil\Framework\Interfaces\Action as ActionInterface;
use \Phacil\Framework\Traits\Action as ActionTrait;
use \Phacil\Framework\Config;

/** 
 * Action class to route all framework system controllers
 * 
 * @since 1.0.1
 * @deprecated 2.0.0
 * @see \Phacil\Framework\Action
 * @package Phacil\Framework 
 */
final class ActionSystem implements ActionInterface
{

	use ActionTrait;

	/**
	 * @inheritdoc
	 */
	public function __construct($route, $args = array(), $local = self::APP)
	{
		$path = '';

		$parts = explode('/', str_replace('../', '', (string)$route));

		foreach ($parts as $part) {
			$path .= $part;

			if (is_dir(Config::DIR_SYSTEM() . '' . $path)) {
				$path .= '/';

				array_shift($parts);

				continue;
			}

			if (is_file(Config::DIR_SYSTEM() . '' . str_replace('../', '', $path) . '.php')) {
				$this->file = Config::DIR_SYSTEM() . '' . str_replace('../', '', $path) . '.php';

				$this->class = 'System' . preg_replace('/[^a-zA-Z0-9]/', '', $path);

				$this->classAlt = [
					'legacy' => $this->class,
					'direct' => preg_replace('/[^a-zA-Z0-9]/', '', $part)
				];

				array_shift($parts);

				break;
			}
		}

		if ($args) {
			$this->args = $args;
		}

		$method = array_shift($parts);

		if ($method) {
			$this->method = $method;
		} else {
			$this->method = 'index';
		}
	}
}