<?php
/**
 * Copyright © 2022 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 * @author Bruno O. Notario <bruno@exacti.com.br>
 * 
 * Only for old versions of PHP that not support variable-length argument lists (...)
 */


namespace Phacil\Framework\Compatibility\Polyfill {

	class Mbstring {
		static function load() {
			return true;
		}
	}
}

namespace {
	if (!function_exists('mb_convert_variables')) {
		function mb_convert_variables($toEncoding, $fromEncoding, &$a = null, &$b = null, &$c = null, &$d = null, &$e = null, &$f = null)
		{
			return \Symfony\Polyfill\Mbstring\Mbstring::mb_convert_variables($toEncoding, $fromEncoding, $v0, $a, $b, $c, $d, $e, $f);
		}
	}
}
