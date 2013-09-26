<?php

namespace Framework\Kapafaa\Modifications;

use Framework\Kapafaa\Operators\Operator;


/**
 * @kapafaa %operator% #%variable%#
 * @description Lokální proměnná
 */
class LocalVariable extends Modification
{
	/** @var \Framework\Kapafaa\Operators\Operator */
	public $operator;
	
	/** @var string */
	public $variable;
	
	
	/**
	 * @param \Framework\Kapafaa\Operators\Operator $operator
	 * @param string $variable
	 */
	public function __construct(Operator $operator, $variable)
	{
		$this->operator = $operator;
		$this->variable = $variable;
	}
}
