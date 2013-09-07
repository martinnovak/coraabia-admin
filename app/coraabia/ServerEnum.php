<?php

namespace Coraabia;

use Nette;


class ServerEnum
{
	const DEV = 'dev';
	const STAGE = 'stage';
	const BETA = 'beta';

	
	/**
	 * @throws Nette\StaticClassException
	 */
	public function __construct()
	{
		throw new Nette\StaticClassException;
	}
}
