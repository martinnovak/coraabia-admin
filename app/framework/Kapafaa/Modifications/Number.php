<?php

namespace Framework\Kapafaa\Modifications;

use Framework\Kapafaa\Operators\Operator;


/**
 * @kapafaa %operator% %number%
 */
class Number extends Modification
{
	/** @var \Framework\Kapafaa\Operators\Operator */
	public $operator;
	
	/** @var int */
	public $number;
	
	
	/**
	 * @param \Framework\Kapafaa\Operators\Operator $operator
	 * @param int $number
	 */
	public function __construct(Operator $operator, $number)
	{
		$this->operator = $operator;
		$this->number = $number;
	}
}
