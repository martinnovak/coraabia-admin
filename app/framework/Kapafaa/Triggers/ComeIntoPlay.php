<?php

namespace Framework\Kapafaa\Triggers;


class ComeIntoPlay extends GameTrigger
{
	const COME_INTO_PLAY = 'comeIntoPlay';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::COME_INTO_PLAY, $target);
	}
}
