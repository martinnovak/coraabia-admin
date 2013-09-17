<?php

namespace Framework\Kapafaa\Effects;


/**
 * @kapafaa eff.world.addXot %xot%
 * @description Přidej xot
 */
class AddXot extends Effect
{
	/** @var int */
	public $xot;
	
	
	/**
	 * @param int $xot
	 */
	public function __construct($xot)
	{
		$this->xot = $xot;
	}
}
