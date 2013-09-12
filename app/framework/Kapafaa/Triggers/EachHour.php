<?php

namespace Framework\Kapafaa\Triggers;


class EachHour extends WorldTrigger
{
	const EACH_HOUR = 'eachHour';
	
	
	public function __construct() {
		parent::__construct(self::EACH_HOUR);
	}
}
