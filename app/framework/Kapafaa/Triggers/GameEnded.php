<?php

namespace Framework\Kapafaa\Triggers;

use Framework\Kapafaa\Targets\PlayerTarget;


/**
 * @kapafaa trigger.gameplay.%target%.game_ended
 * @description Hra skončila (???)
 */
class GameEnded extends Trigger
{
	/** @var \Framework\Kapafaa\Targets\PlayerTarget */
	public $target;
	
	
	/**
	 * @param \Framework\Kapafaa\Targets\PlayerTarget $target
	 */
	public function __construct(PlayerTarget $target)
	{
		$this->target = $target;
	}
}
