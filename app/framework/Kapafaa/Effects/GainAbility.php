<?php

namespace Framework\Kapafaa\Effects;

use Framework\Kapafaa\Targets\PlayerTarget,
	Framework\Kapafaa\Multipliers\Multiplier;


/**
 * @kapafaa eff.gameplay(%target%.gainAbility (#%ability%#)%multiply%)
 * @description Získej schopnost
 */
class GainAbility extends Effect
{
	/** @var \Framework\Kapafaa\Targets\PlayerTarget */
	public $target;
	
	/** @var string */
	public $ability;
	
	/** @var \Framework\Kapafaa\Multipliers\Multiplier */
	public $multiply;
	
	
	/**
	 * @param \Framework\Kapafaa\Targets\PlayerTarget $target
	 * @param string $ability
	 * @param \Framework\Kapafaa\Multipliers\Multiplier $multiply
	 */
	public function __construct(PlayerTarget $target, $ability, Multiplier $multiply = NULL)
	{
		$this->target = $target;
		$this->ability = $ability;
		$this->multiply = $multiply;
	}
}
