<?php

namespace Framework\Kapafaa\Modifications;

use Framework\Kapafaa\Operators\Operator,
	Framework\Kapafaa\Parameters\Parameter;


/**
 * @kapafaa %operator% var.duelsWonWithParameter.%parameter%
 * @description Vyhrané duely s parametrem
 */
class DuelsWonWithParameter extends Modification
{
	/** @var \Framework\Kapafaa\Operators\Operator */
	public $operator;
	
	/** @var \Framework\Kapafaa\Parameters\Parameter */
	public $parameter;
	
	
	/**
	 * @param \Framework\Kapafaa\Operators\Operator $operator
	 * @param \Framework\Kapafaa\Parameters\Parameter $parameter
	 */
	public function __construct(Operator $operator, Parameter $parameter)
	{
		$this->operator = $operator;
		$this->parameter = $parameter;
	}
}
