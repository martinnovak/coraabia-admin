<?php

namespace Framework\Kapafaa\Conditions;

use Framework\Kapafaa\TruthValues\TruthValue;


/**
 * @kapafaa %negation%con.card %fraction%
 * @description Frakce
 */
class Fraction extends Condition
{
	/** @var :negation */
	public $negation;
	
	/** @var \Framework\Kapafaa\Fractions\Fraction */
	public $fraction;
	
	
	/**
	 * @param \Framework\Kapafaa\TruthValues\TruthValue $negation
	 * @param \Framework\Kapafaa\Fractions\Fraction $fraction
	 */
	public function __construct(TruthValue $negation, \Framework\Kapafaa\Fractions\Fraction $fraction) {
		$this->negation = $negation;
		$this->fraction = $fraction;
	}
}
