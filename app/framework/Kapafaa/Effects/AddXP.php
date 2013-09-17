<?php

namespace Framework\Kapafaa\Effects;


/**
 * @kapafaa eff.world.addXP %xp%
 * @description Přidej XP
 */
class AddXP extends Effect
{
	/** @var int */
	public $xp;
	
	
	/**
	 * @param int $xp
	 */
	public function __construct($xp)
	{
		$this->xp = $xp;
	}
}
