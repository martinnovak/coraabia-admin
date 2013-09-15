<?php

namespace Framework\Kapafaa\Effects;


/**
 * @kapafaa eff.world.addTrin %trin%
 */
class AddTrin extends Effect
{
	/** @var int */
	private $trin;
	
	
	/**
	 * @param int $trin
	 */
	public function __construct($trin)
	{
		$this->trin = $trin;
	}
}
