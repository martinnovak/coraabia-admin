<?php

namespace Framework\Kapafaa\Effects;


/**
 * @method string getEditionId()
 */
class AddRandomCardFromUniformSelection extends WorldEffect
{
	const ADD_RANDOM_CARD_FROM_UNIFORM_SELECTION = 'addRandomCardFromUniformSelection';
	
	/** @var string */
	private $editionId;
	
	
	/**
	 * @param string $editionId
	 */
	public function __construct($editionId)
	{
		parent::__construct(self::ADD_RANDOM_CARD_FROM_UNIFORM_SELECTION);
		$this->editionId = $editionId;
	}
	
	
	public function __toString() {
		return parent::__toString() . ' ' . $this->editionId;
	}
}
