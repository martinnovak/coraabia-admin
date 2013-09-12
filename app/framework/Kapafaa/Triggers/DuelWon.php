<?php

namespace Framework\Kapafaa\Triggers;


class DuelWon extends GameTrigger
{
	const DUEL_WON = 'duel_won';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::DUEL_WON, $target);
	}
}
