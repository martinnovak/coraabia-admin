<?php

namespace Framework\Kapafaa\Modifications;


/**
 * @kapafaa %operator% #%variable%#
 */
class LocalVariable extends Modification
{
	/** @var string */
	public $operator;
	
	/** @var string */
	public $variable;
	
	
	/**
	 * @param string $operator
	 * @param string $variable
	 */
	public function __construct($operator, $variable)
	{
		$this->operator = $operator;
		$this->variable = $variable;
	}
}
