<?php

namespace Framework\Kapafaa\Effects;


class AddRandomCard extends WorldEffect
{
	const ADD_RANDOM_CARD = 'addRandomCard';
	
	
	public function __construct()
	{
		parent::__construct(self::ADD_RANDOM_CARD);
	}
}
