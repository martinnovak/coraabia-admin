<?php

namespace Framework\Kapafaa\Conditions;

use Framework\Kapafaa\TruthValues\TruthValue,
	Framework\Kapafaa\Modifications\Modification;


/**
 * @kapafaa %negation%con.game.scoreDifference %modification%
 * @description Rozdíl skóre
 */
class ScoreDifference extends Condition
{
	/** @var :negation */
	public $negation;
	
	/** @var \Framework\Kapafaa\Modifications\Modification */
	public $modification;
	
	
	/**
	 * @param \Framework\Kapafaa\TruthValues\TruthValue $negation
	 * @param \Framework\Kapafaa\Modifications\Modification $modification
	 */
	public function __construct(TruthValue $negation, Modification $modification) {
		$this->negation = $negation;
		$this->modification = $modification;
	}
}
