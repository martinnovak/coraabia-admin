<?php

namespace Model;

use Nette;


class Coraabia extends Model
{
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getDecks()
	{
		return $this->connection->selectionFactory->table('deck')
				->select('deck.*, user.user_id, user.username');
	}
}
