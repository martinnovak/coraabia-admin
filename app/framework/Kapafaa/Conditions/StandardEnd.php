<?php

namespace Framework\Kapafaa\Conditions;

use Framework\Kapafaa\TruthValues\TruthValue;


/**
 * @kapafaa %negation%con.game.standardEnd
 * @description Standardní konec
 */
class StandardEnd extends Condition
{
	/** @var :negation */
	public $negation;
	
	
	/**
	 * @param \Framework\Kapafaa\TruthValues\TruthValue $negation
	 */
	public function __construct(TruthValue $negation) {
		$this->negation = $negation;
	}
}
