<?php

namespace Framework\Kapafaa\Conditions;

use Framework\Kapafaa\TruthValues\TruthValue;


/**
 * @kapafaa %negation%con.card %subtype%
 * @description Subtyp
 */
class Subtype extends Condition
{
	/** @var :negation */
	public $negation;
	
	/** @var \Framework\Kapafaa\Subtypes\Subtype */
	public $subtype;
	
	
	/**
	 * @param \Framework\Kapafaa\TruthValues\TruthValue $negation
	 * @param \Framework\Kapafaa\Subtypes\Subtype $subtype
	 */
	public function __construct(TruthValue $negation, \Framework\Kapafaa\Fractions\Fraction $subtype) {
		$this->negation = $negation;
		$this->subtype = $subtype;
	}
}
