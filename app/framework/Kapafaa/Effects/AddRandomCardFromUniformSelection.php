<?php

namespace Framework\Kapafaa\Effects;


/**
 * @kapafaa eff.world.addRandomCardFromUniformSelection #%edition%#
 * @description Přidej kartu z expanze
 */
class AddRandomCardFromUniformSelection extends Effect
{
	/** @var string */
	public $edition;
	
	
	/**
	 * @param string $edition
	 */
	public function __construct($edition)
	{
		$this->edition = $edition;
	}
}
