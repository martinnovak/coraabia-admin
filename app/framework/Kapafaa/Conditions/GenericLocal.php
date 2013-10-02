<?php

namespace Framework\Kapafaa\Conditions;

use Framework\Kapafaa\TruthValues\TruthValue,
	Framework\Kapafaa\Modifications\Modification;


/**
 * @kapafaa %negation%con.gen.local(#%variable%# %modification%)
 * @description Lokální proměnná
 */
class GenericLocal extends Condition
{
	/** @var :negation */
	public $negation;
	
	/** @var string */
	public $variable;
	
	/** @var \Framework\Kapafaa\Modifications\Modification */
	public $modification;
	
	
	/**
	 * @param \Framework\Kapafaa\TruthValues\TruthValue $negation
	 * @param string
	 * @param \Framework\Kapafaa\Modifications\Modification $modification
	 */
	public function __construct(TruthValue $negation, $variable, Modification $modification) {
		$this->negation = $negation;
		$this->variable = $variable;
		$this->modification = $modification;
	}
}
