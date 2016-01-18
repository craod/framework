<?php

namespace Craod\Core\Orm\Expression;

/**
 * Class ContainedExpression - Contains an expression which represents the right hand side in a MEMBER OF relationship
 *
 * @package Craod\Core
 */
final class ContainedExpression {

	/**
	 * The right hand side of a MEMBER OF relationship
	 *
	 * @var mixed
	 */
	public $value;
}