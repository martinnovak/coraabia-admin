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
}
