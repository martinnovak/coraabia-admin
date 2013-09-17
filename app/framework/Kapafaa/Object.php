<?php

namespace Framework\Kapafaa;

use Nette;


abstract class Object extends Nette\Object
{
	/**
	 * Dummy constructor so ReflectionClass::newInstanceArgs([]) doesn't scream.
	 */
	public function __construct()
	{
		
	}
	
	
	/**
	 * @return string
	 * @throws \Framework\Kapafaa\KapafaaException
	 */
	final public function __toString()
	{
		$self = $this;
		$rc = $this->getReflection();
		if (!$rc->hasAnnotation('kapafaa')) {
			throw new KapafaaException;
		}
		return preg_replace_callback('/%([a-z]+)%/i', function ($item) use ($rc, $self) {
			if (!$rc->hasProperty($item[1])) {
				throw new KapafaaException;
			}
			return (string)$self->{$item[1]};
		}, str_replace('#', '@', $rc->getAnnotation('kapafaa')));
	}
}
