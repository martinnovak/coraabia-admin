<?php

namespace Framework\Kapafaa\Triggers;


class SacrificedCard extends GameTrigger
{
	const SACRIFICED_CARD = 'sacrificedCard';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::SACRIFICED_CARD, $target);
	}
}
