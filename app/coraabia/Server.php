<?php

namespace Coraabia;

use Nette;



final class Server
{
	const DEV = 'dev';
	const STAGE = 'stage';
	const BETA = 'beta';
	
	
	
	public function __construct()
	{
		throw new Nette\StaticClassException;
	}
}