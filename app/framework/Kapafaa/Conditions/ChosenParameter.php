<?php

namespace Framework\Kapafaa\Conditions;

use Framework\Kapafaa\TruthValues\TruthValue;


/**
 * @kapafaa %negation%con.game.parameter(%parameter%)
 * @description Deklarovaný parametr
 */
class ChosenParameter extends Condition
{
	/** @var :negation */
	public $negation;
	
	/** @var \Framework\Kapafaa\Parameters\Parameter */
	public $parameter;
	
	
	/**
	 * @param \Framework\Kapafaa\TruthValues\TruthValue $negation
	 * @param \Framework\Kapafaa\Parameters\Parameter $parameter
	 */
	public function __construct(TruthValue $negation, \Framework\Kapafaa\Parameters\Parameter $parameter) {
		$this->negation = $negation;
		$this->parameter = $parameter;
	}
}
