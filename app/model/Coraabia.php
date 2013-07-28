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
	
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getAudits()
	{
		return $this->connection->selectionFactory->table('audit_event');
	}
	
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getNews()
	{
		return $this->connection->selectionFactory->table('news');
	}
	
	
	
	/**
	 * @param int $id
	 * @param boolean $valid 
	 */
	public function validateNews($id, $valid)
	{
		$this->connection->selectionFactory->table('news')
				->where('news_id = ?', $id)
				->fetch()
				->update(array('valid' => (bool)$valid));
	}
}
