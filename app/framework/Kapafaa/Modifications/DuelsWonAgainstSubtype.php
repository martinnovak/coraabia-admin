<?php

namespace Framework\Kapafaa\Modifications;


/**
 * @kapafaa %operator% var.duelsWonAgainstSubtype.%subtype%
 */
class DuelsWonAgainstSubtype extends Modification
{
	/** @var string */
	public $operator;
	
	/** @var string */
	public $subtype;
	
	
	/**
	 * @param string $operator
	 * @param string $subtype
	 */
	public function __construct($operator, $subtype)
	{
		$this->operator = $operator;
		$this->subtype = $subtype;
	}
}
