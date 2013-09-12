<?php

namespace Framework\Kapafaa\Triggers;


class DuelDrawn extends GameTrigger
{
	const DUEL_DRAWN = 'duel_drawn';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::DUEL_DRAWN, $target);
	}
}
