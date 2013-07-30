<?php

namespace Framework\Hooks;

use Nette;



class HookManager extends Nette\Object
{
	/** @var array int => array( string => callable ) */
	protected $listeners;
	
	
	
	/**
	 * @param string $name
	 * @param \Framework\Hooks\BaseHook $hook
	 */
	public function fire($name, BaseHook $hook = NULL)
	{
		if (!isset($this->listeners[(string)$name])) {
			return;
		}
		
		if (NULL === $hook) {
			$hook = new BaseHook();
		}
		
		foreach ($this->listeners[(string)$name] as $priority => $listeners) {
			foreach ($listeners as $listener) {
				if ($listener instanceof Nette\Callback) {
					$listener->invoke($hook);
				} else if (is_callable($listener)) {
					call_user_func($listener, $hook);
				} else {
					is_callable($listener, TRUE, $textual);
					throw new Nette\InvalidStateException("Callback '$textual' nelze volat.");
				}
				if ($hook->isPropagationStopped()) {
					return;
				}
			}
		}
	}
	
	
	
	/**
	 * @param name $name
	 * @param callable $listener 
	 * @param int $priority
	 */
	public function listen($name, $listener, $priority = 0)
	{
		$this->listeners[(string)$name][$priority][] = callback($listener);
		krsort($this->listeners[(string)$name]);
	}
}