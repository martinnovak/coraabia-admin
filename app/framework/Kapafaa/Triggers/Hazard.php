<?php

namespace Framework\Kapafaa\Triggers;


class Hazard extends GameTrigger
{
	const HAZARD = 'hazard';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::HAZARD, $target);
	}
}
