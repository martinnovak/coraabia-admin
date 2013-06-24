<?php

namespace Model;

use Nette;


class Game extends Model
{
	/**
	 * @return \DibiFluent
	 */
	public function getCards()
	{
		return $this->connection
				->select('c.*, t.value AS translated_name')
				->from('card c')
				->leftJoin('translation t')
				->on('t.key = %s || CAST(c.card_id AS TEXT)', 'card.')
				->where('t.lang = %s', $this->locales->lang);
	}
	
	
	
	/**
	 * @return \DibiFluent
	 */
	public function getUserdata()
	{
		return $this->connection
				->select('*')
				->from('userdata');
	}
	
	
	
	/**
	 * @return \DibiFluent
	 */
	public function getTranslations()
	{
		return $this->connection
				->select('*')
				->from('translation');
	}
	
	
	
	/**
	 * @return \DibiFluent
	 */
	public function getPermissions()
	{
		return $this->connection
				->select('role_id, resource, server')
				->from('permission');
	}
	
	
	
	public function getGamerooms()
	{
		$result = $this->connection
				->select('g.*, t.value AS translated_name')
				->from('gameroom g')
				->leftJoin('translation t')
				->on('t.key = %s || CAST(g.gameroom_id AS TEXT)', 'gameroom.')
				->where('t.lang = %s', $this->locales->lang);
		return $this->locales->server == 'dev' ? $result : $result->and('g.ready = %b', TRUE);
	}
}
