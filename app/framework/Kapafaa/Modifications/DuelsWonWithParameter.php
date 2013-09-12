<?php

namespace Framework\Kapafaa\Modifications;


/**
 * @method string getParameter()
 */
class DuelsWonWithParameter extends Modification
{
	/** @var string */
	private $parameter;
	
	
	/**
	 * @param string $operator
	 * @param string $parameter
	 */
	public function __construct($operator, $parameter)
	{
		parent::__construct($operator);
		$this->parameter = $parameter;
	}
	
	
	public function __toString()
	{
		return $this->operator . ' var.duelsWonWithParameter.' . $this->parameter;
	}
}
