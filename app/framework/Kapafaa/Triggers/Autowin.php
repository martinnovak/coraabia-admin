<?php

namespace Framework\Kapafaa\Triggers;


class Autowin extends GameTrigger
{
	const AUTOWIN = 'autowin';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::AUTOWIN, $target);
	}
}
