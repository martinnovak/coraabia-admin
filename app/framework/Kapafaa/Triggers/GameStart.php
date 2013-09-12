<?php

namespace Framework\Kapafaa\Triggers;


class GameStart extends GameTrigger
{
	const GAME_START = 'game_start';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::GAME_START, $target);
	}
}
