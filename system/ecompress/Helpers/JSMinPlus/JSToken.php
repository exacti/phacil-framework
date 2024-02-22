<?php
/**
 * Copyright © 2024 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\ECompress\Helpers\JSMinPlus;

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

class JSToken
{
	public $type;
	public $value;
	public $start;
	public $end;
	public $lineno;
	public $assignOp;
}
