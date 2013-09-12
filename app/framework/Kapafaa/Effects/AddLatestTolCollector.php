<?php

namespace Framework\Kapafaa\Effects;


class AddLatestTolCollector extends WorldEffect
{
	const ADD_LATEST_TOL_COLLECTOR = 'addLatestTolCollector';
	
	
	public function __construct()
	{
		parent::__construct(self::ADD_LATEST_TOL_COLLECTOR);
	}
}
