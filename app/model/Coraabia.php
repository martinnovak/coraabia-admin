<?php

namespace Model;

use Nette;


class Coraabia extends Model
{
	/**
	 * @return \DibiFluent
	 */
	public function getDecks()
	{
		return $this->connection
				->select('d.*, u.*')
				->from('deck d')
				->leftJoin('userdata u')
				->on('u.user_id = d.user_id');
	}
	
	
	
	/**
	 * @return \DibiFluent
	 */
	public function getDeckInstances()
	{
		return $this->connection
				->select('i.*, di.*')
				->from('instance i')
				->leftJoin('deck_instance di')
				->on('di.instance_id = i.instance_id');
	}
}
