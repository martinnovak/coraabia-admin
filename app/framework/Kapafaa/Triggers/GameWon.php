<?php

namespace Framework\Kapafaa\Triggers;


class GameWon extends GameTrigger
{
	const GAME_WON = 'game_won';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::GAME_WON, $target);
	}
}
