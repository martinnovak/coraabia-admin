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
		return $this->connection->selectionFactory->table('news')
				->select('*, CASE WHEN valid_from IS NOT NULL THEN valid_from ELSE created END AS order_by');
	}
	
	
	
	public function updateNews($newsId, array $values)
	{
		if (isset($values['valid_from']) && $values['valid_from'] == '') { //intentionaly ==
			$values['valid_from'] = NULL;
		}
		if (isset($values['valid_to']) && $values['valid_to'] == '') { //intentionaly ==
			$values['valid_to'] = NULL;
		}
		
		if ($newsId !== NULL) { //update
			$this->connection->selectionFactory->table('news')
					->where('news_id = ?', $newsId)
					->fetch()
					->update($values);
		} else { //insert
			foreach ($this->locales->langs as $lang) {
				if (!isset($values['title_' . $lang])) {
					$values['title_' . $lang] = '';
				}
				if (!isset($values['text_' . $lang])) {
					$values['text_' . $lang] = '';
				}
			}
			return $this->connection->selectionFactory->table('news')->insert($values);
		}
	}
}
