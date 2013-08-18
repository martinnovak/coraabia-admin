<?php

namespace Coraabia;

use Nette;


class CardTypeEnum
{
	const CHARACTER = 'CHARACTER';
	const TRICK_WIN = 'TRICK_WIN';
	const TRICK_NOW = 'TRICK_NOW';
	
	
	public function __construct()
	{
		throw new Nette\StaticClassException;
	}
}
