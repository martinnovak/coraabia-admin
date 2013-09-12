<?php

namespace Framework\Kapafaa\Triggers;


class GameLost extends GameTrigger
{
	const GAME_LOST = 'game_lost';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::GAME_LOST, $target);
	}
}
