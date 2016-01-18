<?php

namespace Craod\Core\Orm\Expression;

/**
 * Class Expression
 *
 * @package Craod\Core
 */
class Expression {

	/**
	 * Use this function in a findBy or countBy function call in the reverse order you would find a MEMBER OF, for instance:
	 *
	 * ['filters' => ['parents' => Expression::contains($parentToGetChildrenFrom)]]
	 *
	 * @param $value
	 * @return ContainedExpression
	 */
	public static function contains($value) {
		$containedExpression = new ContainedExpression();
		$containedExpression->value = $value;
		return $containedExpression;
	}
}