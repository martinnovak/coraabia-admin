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
			throw new KapafaaException("Object does not have annotation @kapafaa.");
		}
		return preg_replace_callback('/%([a-z]+)%/i', function ($item) use ($rc, $self) {
			if (!$rc->hasProperty($item[1])) {
				throw new KapafaaException("Object does not have property '{$item[1]}'.");
			}
			return (string)$self->{$item[1]};
		}, str_replace('#', '@', $rc->getAnnotation('kapafaa')));
	}
	
	
	/**
	 * @return object
	 */
	final public function toJson()
	{
		$rc = $this->getReflection();
		if (!$rc->hasAnnotation('kapafaa')) {
			throw new KapafaaException("Object does not have annotation @kapafaa.");
		}
		$kapafaa = $rc->getAnnotation('kapafaa');
		if ($kapafaa === TRUE) {
			$kapafaa = "";
		}
		$description = $rc->getAnnotation('description');
		$def = array(
			'name' => $description ?: str_replace('#', '@', $kapafaa),
			'type' => ltrim($rc->getName(), '\\'),
			'kapafaa' => str_replace('#', '@', $kapafaa),
			'parent' => ltrim($rc->getParentClass(), '\\')
		);
		preg_match_all('/%([a-z]+)%/i', $def['kapafaa'], $matches);
		$params = array();
		for ($i = 0; $i < count($matches[1]); $i++) {
			$p = array();
			$p['name'] = $matches[1][$i];
			$p['type'] = ltrim($rc->getProperty($matches[1][$i])->getAnnotation('var'), '\\');
			switch ($p['type']) {
				case 'int':
				case 'float':
				case 'string':
					$value = $rc->getProperty($matches[1][$i])->getValue($this);
					break;
				default:
					$value = $rc->getProperty($matches[1][$i])->getValue($this)->toJson();
			}
			$p['value'] = $value;
			$params[] = $p;
		}
		$def['params'] = $params;
		return (object)$def;
	}
}
