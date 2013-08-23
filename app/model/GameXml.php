<?php

namespace Model;

class GameXml extends Model
{
	/**
	 * @return array
	 */
	public function getCards()
	{
		$result = array();
		foreach ($this->getDataSource()->getElement()->getByName('editions')->getByName('edition') as $edition) {
			$result = array_merge($result, $edition->getByName('card'));
		}
		return $result;
	}
}
