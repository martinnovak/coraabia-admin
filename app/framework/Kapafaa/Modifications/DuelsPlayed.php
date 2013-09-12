<?php

namespace Framework\Kapafaa\Modifications;


class DuelsPlayed extends Modification
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
		return $this->operator . ' var.duelsPlayed';
	}
}
