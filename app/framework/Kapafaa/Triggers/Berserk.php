<?php

namespace Framework\Kapafaa\Triggers;


class Berserk extends GameTrigger
{
	const BERSERK = 'berserk';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::BERSERK, $target);
	}
}
