<?php

namespace Framework\Kapafaa\Triggers;


class DuelEnd extends GameTrigger
{
	const DUEL_END = 'duel_end';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::DUEL_END, $target);
	}
}
