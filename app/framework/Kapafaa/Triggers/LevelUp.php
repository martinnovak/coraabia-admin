<?php

namespace Framework\Kapafaa\Triggers;


class LevelUp extends WorldTrigger
{
	const LEVEL_UP = 'levelUp';
	
	
	public function __construct() {
		parent::__construct(self::LEVEL_UP);
	}
}
