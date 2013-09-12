<?php

namespace Framework\Kapafaa\Configs;


class MaxProgress extends Config
{
	const MAX_PROGRESS = 'max_progress';
	
	
	/**
	 * @param int|string $value
	 */
	public function __construct($value) {
		parent::__construct(self::MAX_PROGRESS, $value);
	}
	
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		return self::PREFIX . '.' . $this->type . '(' . $this->value . ')';
	}
}
