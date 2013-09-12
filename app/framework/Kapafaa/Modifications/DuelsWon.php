<?php

namespace Framework\Kapafaa\Modifications;


class DuelsWon extends Modification
{
	/**
	 * @param string $operator
	 */
	public function __construct($operator)
	{
		parent::__construct($operator);
	}
	
	
	public function __toString()
	{
		return $this->operator . ' var.duelsWon';
	}
}
