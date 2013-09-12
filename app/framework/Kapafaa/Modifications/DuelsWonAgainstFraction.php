<?php

namespace Framework\Kapafaa\Modifications;


/**
 * @method string getFraction()
 */
class DuelsWonAgainstFraction extends Modification
{
	/** @var string */
	private $fraction;
	
	
	/**
	 * @param string $operator
	 * @param string $fraction
	 */
	public function __construct($operator, $fraction)
	{
		parent::__construct($operator);
		$this->fraction = $fraction;
	}
	
	
	public function __toString()
	{
		return $this->operator . ' var.duelsWonAgainstFraction.' . $this->fraction;
	}
}
