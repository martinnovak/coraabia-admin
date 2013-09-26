<?php

namespace Framework\Kapafaa\Modifications;

use Framework\Kapafaa\Operators\Operator;


/**
 * @kapafaa %operator% var.duelsWon
 * @description Vyhrané duely
 */
class DuelsWon extends Modification
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
