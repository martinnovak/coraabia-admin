<?php

namespace Framework\Kapafaa;

use Nette,
	Framework\Kapafaa\Configs\Config,
	Framework\Kapafaa\Triggers\Trigger,
	Framework\Kapafaa\Conditions\Condition,
	Framework\Kapafaa\Effects\Effect;


/**
 * Cora.g
 * revision 6360
 * retrieved 11.9.2013
 * 
 * @method array getTrigers()
 * @method array getConditions()
 * @method array getEffects()
 * @method array getConfigs()
 */
class Script extends Nette\Object
{
	/** @var array */
	private $triggers = array();
	
	/** @var array */
	private $conditions = array();
	
	/** @var array */
	private $effects = array();
	
	/** @var array */
	private $configs = array();
	
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		$script = array_merge(
				array('('),
				$this->triggers,
				$this->conditions,
				$this->effects,
				$this->configs,
				array(')')
		);
		return implode("\n", $script);
	}

	
	/**
	 * @param \Framework\Kapafaa\Configs\Config $config
	 * @return \Framework\Kapafaa\Script
	 */
	public function addConfig(Config $config)
	{
		$this->configs[] = $config;
		return $this;
	}
	
	
	/**
	 * @param \Framework\Kapafaa\Triggers\Trigger $trigger
	 * @return \Framework\Kapafaa\Script
	 */
	public function addTrigger(Trigger $trigger)
	{
		$this->triggers[] = $trigger;
		return $this;
	}
	
	
	/**
	 * @param \Framework\Kapafaa\Conditions\Condition $condition
	 * @return \Framework\Kapafaa\Script
	 */
	public function addCondition(Condition $condition)
	{
		$this->conditions[] = $condition;
		return $this;
	}
	
	
	/**
	 * @param \Framework\Kapafaa\Effects\Effect $effect
	 * @return \Framework\Kapafaa\Script
	 */
	public function addEffect(Effect $effect)
	{
		$this->effects[] = $effect;
		return $this;
	}
	
	
	/**
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		if (strpos($name, 'addConfig') === 0) {
			$class = '\Framework\Kapafaa\Configs\\' . ucfirst(substr($name, 9));
			$rc = new \Nette\Reflection\ClassType($class);
			return $this->addConfig($rc->newInstanceArgs($args));
		} else if (strpos($name, 'addTrigger') === 0) {
			$class = '\Framework\Kapafaa\Triggers\\' . ucfirst(substr($name, 10));
			$rc = new \Nette\Reflection\ClassType($class);
			return $this->addTrigger($rc->newInstanceArgs($args));
		} else if (strpos($name, 'addCondition') === 0) {
			$class = '\Framework\Kapafaa\Conditions\\' . ucfirst(substr($name, 12));
			$rc = new \Nette\Reflection\ClassType($class);
			return $this->addCondition($rc->newInstanceArgs($args));
		} else if (strpos($name, 'addEffect') === 0) {
			$class = '\Framework\Kapafaa\Effects\\' . ucfirst(substr($name, 9));
			$rc = new \Nette\Reflection\ClassType($class);
			return $this->addEffect($rc->newInstanceArgs($args));
		}
		
		return parent::__call($name, $args);
	}
}
