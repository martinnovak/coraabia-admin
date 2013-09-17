<?php

namespace Framework\Kapafaa\Effects;


/**
 * @kapafaa eff.world.addTrin %trin%
 * @description Přidej triny
 */
class AddTrin extends Effect
{
	/** @var int */
	public $trin;
	
	
	/**
	 * @param int $trin
	 */
	public function __construct($trin)
	{
		$this->trin = $trin;
	}
}
