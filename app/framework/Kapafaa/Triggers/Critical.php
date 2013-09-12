<?php

namespace Framework\Kapafaa\Triggers;


class Critical extends GameTrigger
{
	const CRITICAL = 'critical';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::CRITICAL, $target);
	}
}
