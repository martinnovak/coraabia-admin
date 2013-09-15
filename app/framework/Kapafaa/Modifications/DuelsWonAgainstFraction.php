<?php

namespace Framework\Kapafaa\Modifications;


/**
 * @kapafaa %operator% var.duelsWonAgainstFraction.%fraction%
 */
class DuelsWonAgainstFraction extends Modification
{
	/** @var string */
	public $operator;
	
	/** @var string */
	public $fraction;
	
	
	/**
	 * @param string $operator
	 * @param string $fraction
	 */
	public function __construct($operator, $fraction)
	{
		$this->operator = $operator;
		$this->fraction = $fraction;
	}
}
