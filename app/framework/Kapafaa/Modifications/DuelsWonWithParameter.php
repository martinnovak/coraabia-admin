<?php

namespace Framework\Kapafaa\Modifications;


/**
 * @kapafaa %operator% var.duelsWonWithParameter.%parameter%
 */
class DuelsWonWithParameter extends Modification
{
	/** @var string */
	public $operator;
	
	/** @var string */
	public $parameter;
	
	
	/**
	 * @param string $operator
	 * @param string $parameter
	 */
	public function __construct($operator, $parameter)
	{
		$this->operator = $operator;
		$this->parameter = $parameter;
	}
}
