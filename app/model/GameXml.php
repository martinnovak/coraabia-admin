<?php

namespace Model;


class GameXml extends XmlModel
{
	
	/**
	 * @return array
	 */
	public function getCards()
	{
		$result = array();
		foreach ($this->element->getByName('editions')->getByName('edition') as $edition) {
			$result = array_merge($result, $edition->getByName('card'));
		}
		return $result;
	}
}
