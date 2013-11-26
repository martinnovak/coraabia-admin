<?php

namespace Model;


class Coraabia extends Model
{
	/**
	 * @return array
	 */
	public function getPlayers()
	{
		return $this->getDatasource()->getPlayers();
	}
	
	
	public function getPlayerById($userId)
	{
		foreach ($this->getPlayers() as $player) {
			if ($player->user_id == (int)$userId) {
				return $player;
			}
		}
	}
	
	
	public function getPlayerByName($username)
	{
		foreach ($this->getPlayers() as $player) {
			if ($player->username == $username) {
				return $player;
			}
		}
	}
	
	
	/**
	 * @return array
	 */
	public function getDecks()
	{
		return $this->getDatasource()->getDecks();
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
	 * @return array
	 */
	public function getNews()
	{
		return $this->getDatasource()->getNews();
	}
	
	
	/**
	 * @param int $newsId
	 * @return object
	 */
	public function getNewsById($newsId)
	{
		foreach ($this->getDatasource()->getNews() as $news) {
			if ($news->news_id == (int)$newsId) {
				return $news;
			}
		}
	}
	
	
	/**
	 * @param int $newsId
	 */
	public function deleteNews($newsId)
	{
		return $this->getDatasource()->deleteNews((int)$newsId);
	}
	
	
	/**
	 * @param int|NULL $newsId
	 * @param array $values
	 * @return \Nette\Database\Table\ActiveRow|NULL
	 */
	public function updateNews($newsId, array $values)
	{
		if ($newsId !== NULL) { //update
			return $this->getDatasource()->updateNews((int)$newsId, $values);
		} else { //insert
			return $this->getDatasource()->createNews($values);
		}
	}
	
	
	/**
	 * @param int $userId
	 * @param array $values
	 */
	public function updatePlayer($userId, array $values)
	{
		if ($userId !== NULL) { //update
			return $this->getDatasource()->updatePlayer((int)$userId, $values);
		} else { //insert
			throw new \Nette\NotSupportedException;
			//return $this->getDatasource()->createPlayer($values);
		}
	}
}
