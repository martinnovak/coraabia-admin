<?php

namespace Framework\Kapafaa\Triggers;


class DuelLost extends GameTrigger
{
	const DUEL_LOST = 'duel_lost';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::DUEL_LOST, $target);
	}
}
