<?php

namespace Framework\Kapafaa\Modifications;


/**
 * @kapafaa %operator% var.scoreCumulated
 */
class ScoreCumulated extends Modification
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
