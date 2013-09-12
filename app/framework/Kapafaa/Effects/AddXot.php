<?php

namespace Framework\Kapafaa\Effects;


/**
 * @method int getXot()
 */
class AddXot extends WorldEffect
{
	const ADD_XOT = 'addXot';
	
	/** @var int */
	private $xot;
	
	
	/**
	 * @param int $xot
	 */
	public function __construct($xot)
	{
		parent::__construct(self::ADD_XOT);
		$this->xot = $xot;
	}
	
	
	public function __toString() {
		return parent::__toString() . ' ' . $this->xot;
	}
}
