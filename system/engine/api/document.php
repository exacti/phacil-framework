<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\Api;

/**
 * @api
 * @since 2.0.0
 * @package Phacil\Framework\Api
 */
interface Document {
	/**
	 * @param string $title 
	 * @return void 
	 */
	public function setTitle($title);

	/** @return string  */
	public function getTitle();

	/**
	 * @param string $description 
	 * @return void 
	 */
	public function setDescription($description);

	/** @return string  */
	public function getDescription();

	/**
	 * @param string $keywords 
	 * @return void 
	 */
	public function setKeywords($keywords);

	/** @return string  */
	public function getKeywords();

	/**
	 * @param string $href 
	 * @param string $rel 
	 * @return void 
	 */
	public function addLink($href, $rel);

	/** @return array  */
	public function getLinks();

	/**
	 * @param string $href 
	 * @param string $rel 
	 * @param string $media 
	 * @param bool $minify 
	 * @return void 
	 */
	public function addStyle($href, $rel = 'stylesheet', $media = 'screen', $minify = true);

	/** @return array  */
	public function getStyles();

	/**
	 * @param string $script 
	 * @param int|string $sort 
	 * @param bool $minify 
	 * @return void 
	 */
	public function addScript($script, $sort = 0, $minify = true);

	/** @return array  */
	public function getScripts();
}