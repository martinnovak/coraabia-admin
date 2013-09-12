<?php

namespace Framework\Kapafaa\Effects;


abstract class WorldEffect extends Effect
{
	const PREFIX = 'eff.world';
	
	
	public function __toString()
	{
		return self::PREFIX . '.' . $this->type;
	}
}
