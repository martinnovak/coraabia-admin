<?php

namespace Model\Datasource;


class GameXmlSource extends XmlSource
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
