<?php

namespace Framework\Kapafaa\Modifications;


/**
 * @kapafaa %operator% var.sacrificedCards
 */
class SacrificedCards extends Modification
{
	/** @var string */
	public $operator;
	
	
	/**
	 * @param string $operator
	 */
	public function __construct($operator)
	{
		$this->operator = $operator;
	}
}
