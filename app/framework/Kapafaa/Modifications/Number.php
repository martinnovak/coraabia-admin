<?php

namespace Framework\Kapafaa\Modifications;


/**
 * @method int|float getNumber()
 */
class Number extends Modification
{
	/** @var int|float */
	private $number;
	
	
	/**
	 * @param string $operator
	 * @param int|float $number
	 */
	public function __construct($operator, $number)
	{
		parent::__construct($operator);
		$this->number = $number;
	}
	
	
	public function __toString()
	{
		return $this->operator . ' ' . $this->number;
	}
}
