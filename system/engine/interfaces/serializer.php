<?php
/**
 * Copyright © 2022 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 */

namespace Phacil\Framework\Interfaces;

/**
 * Interface for serializing
 *
 * @api
 * @since 2.0.0
 */
interface Serializer
{
	/**
	 * Serialize data into string
	 *
	 * @param string|int|float|bool|array|null $data
	 * @return string|bool
	 * @throws \Phacil\Framework\Exception\InvalidArgumentException
	 * @since 2.0.0
	 */
	public function serialize($data);

	/**
	 * Unserialize the given string
	 *
	 * @param string $string
	 * @return string|int|float|bool|array|null
	 * @throws \Phacil\Framework\Exception\InvalidArgumentException
	 * @since 2.0.0
	 */
	public function unserialize($string);
}