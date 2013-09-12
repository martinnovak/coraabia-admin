<?php

namespace Framework\Kapafaa\Triggers;


class Strike extends GameTrigger
{
	const STRIKE = 'strike';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::STRIKE, $target);
	}
}
