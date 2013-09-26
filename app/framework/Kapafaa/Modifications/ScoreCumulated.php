<?php

namespace Framework\Kapafaa\Modifications;

use Framework\Kapafaa\Operators\Operator;


/**
 * @kapafaa %operator% var.scoreCumulated
 * @description Skóre
 */
class ScoreCumulated extends Modification
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
