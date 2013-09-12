<?php

namespace Framework\Kapafaa\Triggers;


class GameDrawn extends GameTrigger
{
	const GAME_DRAWN = 'game_drawn';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::GAME_DRAWN, $target);
	}
}
