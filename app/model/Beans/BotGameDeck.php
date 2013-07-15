<?php

namespace Model\Bean;

use Nette;



class BotGameDeck extends GameDeck
{	
	
	public static function from(Deck $deck)
	{
		$obj = new static;
		$obj->data = array(
			'deck_id' => 'DECK_'
		);
		return $obj;
	}
}
