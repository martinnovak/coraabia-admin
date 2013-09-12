<?php

namespace Framework\Kapafaa\Effects;


/**
 * @method int getCardId()
 */
class AddCardByAbstractionID extends WorldEffect
{
	const ADD_CARD_BY_ABSTRACTION_ID = 'addCardByAbstractionID';
	
	/** @var int */
	private $cardId;
	
	
	/**
	 * @param int $cardId
	 */
	public function __construct($cardId)
	{
		parent::__construct(self::ADD_CARD_BY_ABSTRACTION_ID);
		$this->cardId = $cardId;
	}
	
	
	public function __toString() {
		return parent::__toString() . ' ' . $this->cardId;
	}
}
