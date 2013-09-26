<?php

namespace Framework\Kapafaa\Modifications;

use Framework\Kapafaa\Operators\Operator,
	Framework\Kapafaa\Fractions\Fraction;


/**
 * @kapafaa %operator% var.duelsWonAgainstFraction.%fraction%
 * @description Vyhrané duely proti frakci
 */
class DuelsWonAgainstFraction extends Modification
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
