<?php

namespace Framework\Kapafaa\Effects;

use Framework\Kapafaa\Targets\PlayerTarget,
	Framework\Kapafaa\Multipliers\Multiplier;


/**
 * @kapafaa eff.gameplay(%target%.see.topDeckFraction.%otherTarget% %multiply%)
 * @description Prorok
 */
class TopDeckFraction extends Effect
{
	/** @var \Framework\Kapafaa\Targets\PlayerTarget */
	public $target;
	
	/** @var \Framework\Kapafaa\Targets\PlayerTarget */
	public $otherTarget;
	
	/** @var \Framework\Kapafaa\Multipliers\Multiplier */
	public $multiply;
	
	
	/**
	 * @param \Framework\Kapafaa\Targets\PlayerTarget $target
	 * @param \Framework\Kapafaa\Targets\PlayerTarget $otherTarget
	 * @param \Framework\Kapafaa\Multipliers\Multiplier $multiply
	 */
	public function __construct(PlayerTarget $target, PlayerTarget $otherTarget, Multiplier $multiply = NULL)
	{
		$this->target = $target;
		$this->otherTarget = $otherTarget;
		$this->multiply = $multiply;
	}
}
