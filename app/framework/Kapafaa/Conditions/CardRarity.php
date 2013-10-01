<?php

namespace Framework\Kapafaa\Conditions;

use Framework\Kapafaa\TruthValues\TruthValue,
	Framework\Kapafaa\Targets\PlayerTarget,
	Framework\Kapafaa\Rarities\Rarity;


/**
 * @kapafaa %negation%con.game.cardRarity.%target%.%rarity%
 * @description Rarita
 */
class CardRarity extends Condition
{
	/** @var :negation */
	public $negation;
	
	/** @var \Framework\Kapafaa\Targets\PlayerTarget */
	public $target;
	
	/** @var \Framework\Kapafaa\Rarities\Rarity */
	public $rarity;
	
	
	/**
	 * @param \Framework\Kapafaa\TruthValues\TruthValue $negation
	 * @param \Framework\Kapafaa\Targets\PlayerTarget $target
	 * @param \Framework\Kapafaa\Rarities\Rarity $rarity
	 */
	public function __construct(TruthValue $negation, PlayerTarget $target, Rarity $rarity) {
		$this->negation = $negation;
		$this->target = $target;
		$this->rarity = $rarity;
	}
}
