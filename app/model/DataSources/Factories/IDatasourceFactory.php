<?php

namespace Model\DataSources\Factories;


interface IDatasourceFactory
{
	/**
	 * @return \Model\DataSources\ISource
	 */
	public function access();
}
