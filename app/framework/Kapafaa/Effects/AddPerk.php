<?php

namespace Framework\Kapafaa\Effects;


/**
 * @kapafaa eff.world.addPerk #%perk%#
 */
class AddPerk extends Effect
{
	/** @var string */
	private $perk;
	
	
	/**
	 * @param string $perk
	 */
	public function __construct($perk)
	{
		$this->perk = $perk;
	}
}
