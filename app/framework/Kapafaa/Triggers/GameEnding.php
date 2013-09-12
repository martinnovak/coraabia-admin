<?php

namespace Framework\Kapafaa\Triggers;


class GameEnding extends GameTrigger
{
	const GAME_ENDING = 'game_ending';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::GAME_ENDING, $target);
	}
}
