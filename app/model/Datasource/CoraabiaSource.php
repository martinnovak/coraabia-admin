<?php

namespace Model\Datasource;


class CoraabiaSource extends DatabaseSource
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
		return $this->connection->selectionFactory->table('news')
				->select('*, CASE WHEN valid_from IS NOT NULL THEN valid_from ELSE created END AS order_by');
	}
	
	
	/**
	 * @param int|NULL $newsId
	 * @param array $values
	 * @return \Nette\Database\Table\ActiveRow|NULL
	 */
	public function updateNews($newsId, array $values)
	{
		if ($newsId !== NULL) { //update
			$this->connection->selectionFactory->table('news')
					->where('news_id = ?', $newsId)
					->fetch()
					->update($values);
		} else { //insert
			return $this->connection->selectionFactory->table('news')
					->insert($values);
		}
	}
}
