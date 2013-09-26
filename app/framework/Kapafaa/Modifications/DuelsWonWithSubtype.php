<?php

namespace Framework\Kapafaa\Modifications;

use Framework\Kapafaa\Operators\Operator,
	Framework\Kapafaa\Subtypes\Subtype;


/**
 * @kapafaa %operator% var.duelsWonWithSubtype.%subtype%
 * @description Vyhrané duely se subtypem
 */
class DuelsWonWithSubtype extends Modification
{
	/** @var \Framework\Kapafaa\Operators\Operator */
	public $operator;
	
	/** @var \Framework\Kapafaa\Subtypes\Subtype */
	public $subtype;
	
	
	/**
	 * @param \Framework\Kapafaa\Operators\Operator $operator
	 * @param \Framework\Kapafaa\Subtypes\Subtype $subtype
	 */
	public function __construct(Operator $operator, Subtype $subtype)
	{
		$this->operator = $operator;
		$this->subtype = $subtype;
	}
}
