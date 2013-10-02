<?php

namespace Framework\Kapafaa\Effects;

use Framework\Kapafaa\Targets\PlayerTarget,
	Framework\Kapafaa\Modifications\Modification,
	Framework\Kapafaa\Multipliers\Multiplier;


/**
 * @kapafaa eff.gameplay(%target%.%parameter% %modification%%multiply%)
 * @description Parametr
 */
class Parameter extends Effect
{
	/** @var \Framework\Kapafaa\Targets\PlayerTarget */
	public $target;
	
	/** @var \Framework\Kapafaa\Parameters\Parameter */
	public $parameter;
	
	/** @var \Framework\Kapafaa\Modifications\Modification */
	public $modification;
	
	/** @var \Framework\Kapafaa\Multipliers\Multiplier */
	public $multiply;
	
	
	/**
	 * @param \Framework\Kapafaa\Targets\PlayerTarget $target
	 * @param \Framework\Kapafaa\Parameters\Parameter $parameter
	 * @param \Framework\Kapafaa\Modifications\Modification $modification
	 * @param \Framework\Kapafaa\Multipliers\Multiplier $multiply
	 */
	public function __construct(PlayerTarget $target, \Framework\Kapafaa\Parameters\Parameter $parameter, Modification $modification, Multiplier $multiply = NULL)
	{
		$this->target = $target;
		$this->parameter = $parameter;
		$this->multiply = $multiply;
		$this->modification = $modification;
	}
}
