<?php

namespace Framework\Kapafaa\Effects;


/**
 * @kapafaa eff.world.addXot %xot%
 */
class AddXot extends Effect
{
	/** @var int */
	private $xot;
	
	
	/**
	 * @param int $xot
	 */
	public function __construct($xot)
	{
		$this->xot = $xot;
	}
}
