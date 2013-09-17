<?php

namespace Framework\Kapafaa\Effects;


/**
 * @kapafaa eff.world.addCardByAbstractionID %card%
 * @description Přidej kartu
 */
class AddCardByAbstractionID extends Effect
{
	/** @var int */
	public $card;
	
	
	/**
	 * @param int $card
	 */
	public function __construct($card)
	{
		$this->card = $card;
	}
}
