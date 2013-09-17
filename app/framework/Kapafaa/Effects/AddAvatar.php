<?php

namespace Framework\Kapafaa\Effects;


/**
 * @kapafaa eff.world.addAvatar %avatar%
 * @description Přidej avatara
 */
class AddAvatar extends Effect
{
	/** @var int */
	public $avatar;
	
	
	/**
	 * @param int $avatar
	 */
	public function __construct($avatar)
	{
		$this->avatar = $avatar;
	}
}
