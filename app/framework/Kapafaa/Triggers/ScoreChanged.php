<?php

namespace Framework\Kapafaa\Triggers;


class ScoreChanged extends GameTrigger
{
	const SCORE_CHANGED = 'scoreChanged';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::SCORE_CHANGED, $target);
	}
}
