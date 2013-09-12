<?php

namespace Framework\Kapafaa\Triggers;


class GameEnded extends GameTrigger
{
	const GAME_ENDED = 'game_ended';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::GAME_ENDED, $target);
	}
}
