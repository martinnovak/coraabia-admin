<?php

namespace Framework\Kapafaa\Effects;


/**
 * @kapafaa eff.world.addCardByAbstractionID %card%
 */
class AddCardByAbstractionID extends Effect
{
	/** @var int */
	private $card;
	
	
	/**
	 * @param int $card
	 */
	public function __construct($card)
	{
		$this->card = $card;
	}
}
