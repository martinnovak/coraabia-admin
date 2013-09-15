<?php

namespace Framework\Kapafaa\Effects;


/**
 * @kapafaa eff.world.addXP %xp%
 */
class AddXP extends Effect
{
	/** @var int */
	private $xp;
	
	
	/**
	 * @param int $xp
	 */
	public function __construct($xp)
	{
		$this->xp = $xp;
	}
}
