<?php

namespace Framework\Kapafaa\Triggers;


class PointsChanged extends GameTrigger
{
	const POINTS_CHANGED = 'pointsChanged';
	
	
	/**
	 * @param string $target
	 */
	public function __construct($target) {
		parent::__construct(self::POINTS_CHANGED, $target);
	}
}
