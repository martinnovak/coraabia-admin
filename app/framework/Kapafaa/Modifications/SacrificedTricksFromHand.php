<?php

namespace Framework\Kapafaa\Modifications;


/**
 * @kapafaa %operator% var.sacrificedTricksFromHand
 */
class SacrificedTricksFromHand extends Modification
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
