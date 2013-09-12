<?php

namespace Framework\Kapafaa\Triggers;


class Restart extends GameTrigger
{
	const RESTART = 'restart';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::RESTART, $target);
	}
}
