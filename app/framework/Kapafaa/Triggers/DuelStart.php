<?php

namespace Framework\Kapafaa\Triggers;


class DuelStart extends GameTrigger
{
	const DUEL_START = 'duel_start';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::DUEL_START, $target);
	}
}
