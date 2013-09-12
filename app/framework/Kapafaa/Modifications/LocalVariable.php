<?php

namespace Framework\Kapafaa\Modifications;


/**
 * @method string getVariable()
 */
class LocalVariable extends Modification
{
	/** @var string */
	private $variable;
	
	
	/**
	 * @param string $operator
	 * @param string $variable
	 */
	public function __construct($operator, $variable)
	{
		parent::__construct($operator);
		$this->variable = $variable;
	}
	
	
	public function __toString()
	{
		return $this->operator . ' @' . $this->variable . '@';
	}
}
