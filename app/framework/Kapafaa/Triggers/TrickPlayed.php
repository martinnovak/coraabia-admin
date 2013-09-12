<?php

namespace Framework\Kapafaa\Triggers;


class TrickPlayed extends GameTrigger
{
	const TRICK_PLAYED = 'trickPlayed';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::TRICK_PLAYED, $target);
	}
}
