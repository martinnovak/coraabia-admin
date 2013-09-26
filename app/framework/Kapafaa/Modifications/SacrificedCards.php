<?php

namespace Framework\Kapafaa\Modifications;

use Framework\Kapafaa\Operators\Operator;


/**
 * @kapafaa %operator% var.sacrificedCards
 * @description Obětované karty
 */
class SacrificedCards extends Modification
{
	/** @var \Framework\Kapafaa\Operators\Operator */
	public $operator;
	
	
	/**
	 * @param \Framework\Kapafaa\Operators\Operator $operator
	 */
	public function __construct(Operator $operator)
	{
		$this->operator = $operator;
	}
}
