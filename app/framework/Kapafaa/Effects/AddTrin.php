<?php

namespace Framework\Kapafaa\Effects;


/**
 * @method int getTrin()
 */
class AddTrin extends WorldEffect
{
	const ADD_TRIN = 'addTrin';
	
	/** @var int */
	private $trin;
	
	
	/**
	 * @param int $trin
	 */
	public function __construct($trin)
	{
		parent::__construct(self::ADD_TRIN);
		$this->trin = $trin;
	}
	
	
	public function __toString() {
		return parent::__toString() . ' ' . $this->trin;
	}
}
