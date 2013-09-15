<?php

namespace Framework\Kapafaa\Modifications;


/**
 * @kapafaa %operator% var.tricksAddedOnHand
 */
class TricksAddedOnHand extends Modification
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
