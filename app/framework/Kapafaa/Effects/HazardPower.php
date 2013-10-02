<?php

namespace Framework\Kapafaa\Effects;

use Framework\Kapafaa\Targets\PlayerTarget,
	Framework\Kapafaa\Modifications\Modification,
	Framework\Kapafaa\Multipliers\Multiplier;


/**
 * @kapafaa eff.gameplay(%target%.hazardPower %modification%%multiply%)
 * @description Hazard (hodnota)
 */
class HazardPower extends Effect
{
	/** @var \Framework\Kapafaa\Targets\PlayerTarget */
	public $target;
	
	/** @var \Framework\Kapafaa\Modifications\Modification */
	public $modification;
	
	/** @var \Framework\Kapafaa\Multipliers\Multiplier */
	public $multiply;
	
	
	/**
	 * @param \Framework\Kapafaa\Targets\PlayerTarget $target
	 * @param \Framework\Kapafaa\Modifications\Modification $modification
	 * @param \Framework\Kapafaa\Multipliers\Multiplier $multiply
	 */
	public function __construct(PlayerTarget $target, Modification $modification, Multiplier $multiply = NULL)
	{
		$this->target = $target;
		$this->multiply = $multiply;
		$this->modification = $modification;
	}
}
