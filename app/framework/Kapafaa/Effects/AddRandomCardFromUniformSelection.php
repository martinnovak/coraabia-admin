<?php

namespace Framework\Kapafaa\Effects;

use Framework\Kapafaa\Editions\Edition;


/**
 * @kapafaa eff.world.addRandomCardFromUniformSelection #%edition%#
 * @description Přidej kartu z expanze
 */
class AddRandomCardFromUniformSelection extends Effect
{
	/** @var \Framework\Kapafaa\Editions\Edition */
	public $edition;
	
	
	/**
	 * @param \Framework\Kapafaa\Editions\Edition $edition
	 */
	public function __construct(Edition $edition)
	{
		$this->edition = $edition;
	}
}
