<?php

namespace Phacil\Framework;

use Phacil\Framework\Config;
/**
 * @since 2.0.0
 * @package Phacil\Framework
 */
final class Language
{
	public $directory;
	private $data = array();

	public function __construct($directory)
	{
		$this->directory = $directory;
	}

	public function get($key)
	{
		return (isset($this->data[$key]) ? $this->data[$key] : $key);
	}

	public function load($filename)
	{
		$file = Config::DIR_LANGUAGE() . $this->directory . '/' . $filename . '.php';

		if (file_exists($file)) {
			$_ = array();

			require($file);

			$this->data = array_merge($this->data, $_);

			return $this->data;
		} else {
			throw new \Phacil\Framework\Exception('Error: Could not load language ' . $filename . '!');
		}
	}
}
