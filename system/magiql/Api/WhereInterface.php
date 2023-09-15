<?php 
/**
 * Copyright © 2023 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework\MagiQL\Api;

interface WhereInterface {
	const OPERATOR_GREATER_THAN_OR_EQUAL = '>=';
	const OPERATOR_GREATER_THAN = '>';
	const OPERATOR_LESS_THAN_OR_EQUAL = '<=';
	const OPERATOR_LESS_THAN = '<';
	const OPERATOR_LIKE = 'LIKE';
	const OPERATOR_NOT_LIKE = 'NOT LIKE';
	const OPERATOR_EQUAL = '=';
	const OPERATOR_NOT_EQUAL = '<>';
	const CONJUNCTION_AND = 'AND';
	const CONJUNCTION_AND_NOT = 'AND NOT';
	const CONJUNCTION_OR = 'OR';
	const CONJUNCTION_OR_NOT = 'OR NOT';
	const CONJUNCTION_EXISTS = 'EXISTS';
	const CONJUNCTION_NOT_EXISTS = 'NOT EXISTS';
}