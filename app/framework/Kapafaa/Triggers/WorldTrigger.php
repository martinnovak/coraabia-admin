<?php

namespace Framework\Kapafaa\Triggers;


abstract class WorldTrigger extends Trigger
{
	const PREFIX = 'trigger.world';
	
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		return self::PREFIX . '.' . $this->type;
	}
}
