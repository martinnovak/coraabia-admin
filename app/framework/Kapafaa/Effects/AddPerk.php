<?php

namespace Framework\Kapafaa\Effects;


/**
 * @kapafaa eff.world.addPerk #%perk%#
 * @description Přidej konexi
 */
class AddPerk extends Effect
{
	/** @var string */
	public $perk;
	
	
	/**
	 * @param string $perk
	 */
	public function __construct($perk)
	{
		$this->perk = $perk;
	}
}
