<?php

namespace Framework\Kapafaa\Triggers;


class CardRemovedFromHandByOpp extends GameTrigger
{
	const CARD_REMOVED_FROM_HAND_BY_OPP = 'cardRemovedFromHandByOpp';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::CARD_REMOVED_FROM_HAND_BY_OPP, $target);
	}
}
