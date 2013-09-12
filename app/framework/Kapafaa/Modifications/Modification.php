<?php

namespace Framework\Kapafaa\Modifications;

use Nette;


/**
 * @method string getOperator()
 */
abstract class Modification extends Nette\Object
{
	/** @var string */
	private $operator;
	
	
	/**
	 * @param string $operator
	 */
	public function __construct($operator)
	{
		$this->operator = $operator;
	}
}
