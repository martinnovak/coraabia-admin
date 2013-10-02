<?php

namespace Framework\Kapafaa\Effects;

use Framework\Kapafaa\Targets\PlayerTarget,
	Framework\Kapafaa\Modifications\Modification,
	Framework\Kapafaa\Multipliers\Multiplier;


/**
 * @kapafaa eff.gameplay(%target%.see.duel.%otherTarget% %modification%%multiply%)
 * @description Vidí (duel)
 */
class SeeDuel extends Effect
{
	/** @var \Framework\Kapafaa\Targets\PlayerTarget */
	public $target;
	
	/** @var \Framework\Kapafaa\Targets\PlayerTarget */
	public $otherTarget;
	
	/** @var \Framework\Kapafaa\Modifications\Modification */
	public $modification;
	
	/** @var \Framework\Kapafaa\Multipliers\Multiplier */
	public $multiply;
	
	
	/**
	 * @param \Framework\Kapafaa\Targets\PlayerTarget $target
	 * @param \Framework\Kapafaa\Targets\PlayerTarget $otherTarget
	 * @param \Framework\Kapafaa\Modifications\Modification $modification
	 * @param \Framework\Kapafaa\Multipliers\Multiplier $multiply
	 */
	public function __construct(PlayerTarget $target, PlayerTarget $otherTarget, Modification $modification, Multiplier $multiply = NULL)
	{
		$this->target = $target;
		$this->otherTarget = $otherTarget;
		$this->modification = $modification;
		$this->multiply = $multiply;
	}
}
