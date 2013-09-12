<?php

namespace Framework\Kapafaa\Modifications;


/**
 * @method string getSubtype()
 */
class DuelsWonWithSubtype extends Modification
{
	/** @var string */
	private $subtype;
	
	
	/**
	 * @param string $operator
	 * @param string $subtype
	 */
	public function __construct($operator, $subtype)
	{
		parent::__construct($operator);
		$this->subtype = $subtype;
	}
	
	
	public function __toString()
	{
		return $this->operator . ' var.duelsWonWithSubtype.' . $this->subtype;
	}
}
