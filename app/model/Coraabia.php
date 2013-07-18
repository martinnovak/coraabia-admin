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
	
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getDeckInstances()
	{
		return $this->connection->selectionFactory->table('deck_instance')->select('deck_instance.*, instance.*');
	}
	
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getDeckConnections()
	{
		return $this->connection->selectionFactory->table('deck_connection');
	}
}
