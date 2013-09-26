<?php

namespace Framework\Kapafaa\Modifications;

use Framework\Kapafaa\Operators\Operator,
	Framework\Kapafaa\Fractions\Fraction;


/**
 * @kapafaa %operator% var.sacrificedFractionFromDeck.%fraction%
 * @description Obětované karty frakce
 */
class SacrificedFractionFromDeck extends Modification
{
	/** @var \Framework\Kapafaa\Operators\Operator */
	public $operator;
	
	/** @var \Framework\Kapafaa\Fractions\Fraction */
	public $fraction;
	
	
	/**
	 * @param \Framework\Kapafaa\Operators\Operator $operator
	 * @param \Framework\Kapafaa\Fractions\Fraction $fraction
	 */
	public function __construct(Operator $operator, Fraction $fraction)
	{
		$this->operator = $operator;
		$this->fraction = $fraction;
	}
}
