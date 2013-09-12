<?php

namespace Framework\Kapafaa\Effects;


/**
 * @method int getXp()
 */
class AddXP extends WorldEffect
{
	const ADD_XP = 'addXP';
	
	/** @var int */
	private $xp;
	
	
	/**
	 * @param int $xp
	 */
	public function __construct($xp)
	{
		parent::__construct(self::ADD_XP);
		$this->xp = $xp;
	}
	
	
	public function __toString() {
		return parent::__toString() . ' ' . $this->xp;
	}
}
