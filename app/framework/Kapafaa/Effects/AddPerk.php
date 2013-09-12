<?php

namespace Framework\Kapafaa\Effects;


/**
 * @method string getPerk()
 */
class AddPerk extends WorldEffect
{
	const ADD_PERK = 'addPerk';
	
	/** @var string */
	private $perk;
	
	
	/**
	 * @param string $perk
	 */
	public function __construct($perk)
	{
		parent::__construct(self::ADD_PERK);
		$this->perk = $perk;
	}
	
	
	public function __toString() {
		return parent::__toString() . ' @' . $this->perk . '@';
	}
}
