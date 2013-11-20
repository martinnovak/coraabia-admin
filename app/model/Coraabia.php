<?php

namespace Model;


class Coraabia extends Model
{
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getPlayers()
	{
		return $this->getSource()->getSelectionFactory()->table('userdata');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getDecks()
	{
		return $this->getSource()->getSelectionFactory()->table('deck')
				->select('deck.*, user.user_id, user.username');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getDeckInstances()
	{
		return $this->getSource()->getSelectionFactory()->table('deck_instance')
				->select('deck_instance.*, instance.*');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getDeckConnections()
	{
		return $this->getSource()->getSelectionFactory()->table('deck_connection');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getNews()
	{
		return $this->getSource()->getSelectionFactory()->table('news')
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
			$this->getSource()->getSelectionFactory()->table('news')
					->where('news_id = ?', $newsId)
					->fetch()
					->update($values);
		} else { //insert
			return $this->getSource()->getSelectionFactory()->table('news')
					->insert($values);
		}
	}
	
	
	/**
	 * @param int $userId
	 * @param array $values
	 */
	public function updatePlayer($userId, array $values)
	{
		$this->getSource()->getSelectionFactory()->table('userdata')
				->where('user_id = ?', $userId)
				->fetch()
				->update($values);
	}
}
