<?php

namespace Coraabia\Mapi;

use Nette;



class MapiDataSource extends Nette\Object implements \Grido\DataSources\IDataSource
{
	/** @var \Coraabia\Mapi\MapiRequest */
	private $request;
	
	/** @var \Coraabia\Mapi\MapiResult */
	private $data;
	
	/** @var boolean */
	private $dirty;
	
	/** @var array|NULL */
	private $sorting = NULL;
	
	
	
	/**
	 * @param \Coraabia\Mapi\MapiRequest $request 
	 */
	public function __construct(MapiRequest $request)
	{
		$this->request = $request;
		$this->dirty = TRUE;
		$this->data = $this->getData();
	}
	
	
	
	/**
	 * @param array $condition
	 * @return array 
	 */
	protected function formatFilterCondition(array $condition)
    {
        $matches = \Nette\Utils\Strings::matchAll($condition[0], '/\[([\w_-]+)\]* [\w\!<>=]+ ([%\w]+)/');
        
        if ($matches) {
            foreach ($matches as $match) {
                return array(
                    $match[1],
                    $match[2]
                );
            }
        } else {
            return $condition;
        }
    }
	
	
	
	/**
     * @return array
     */
    public function getData()
    {
		if ($this->dirty) {
			$this->data = new MapiResult($this->request->load());
			$this->dirty = FALSE;
			if (is_array($this->sorting)) {
				$this->sort($this->sorting);
			}
		}
        return $this->data;
    }

	
	
    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->data);
    }
	
	
	
	/**
     * @param array $condition
     */
    public function filter(array $condition)
	{
		$value = str_replace('%', '', $condition[1]);
        $condition = $this->formatFilterCondition($condition);
		switch ($condition[1]) {
			case '%s': $value = (string)$value; break;
			case '%f': $value = (int)$value; break;
			case '%i': $value = (int)$value; break;
		}
		$this->request->setParam($condition[0], $value);
		$this->dirty = TRUE;
	}

	
	
    /**
     * @param int $offset
     * @param int $limit
     */
    public function limit($offset, $limit)
	{
		$this->request->setParam('start', $offset);
		$this->request->setParam('count', $limit);
		$this->dirty = TRUE;
	}

	
	
    /**
     * @param array $sorting
     */
    public function sort(array $sorting)
	{
		if (is_array($this->sorting)) {
			$sorting = $this->sorting;
			$this->sorting = NULL;
			
			foreach ($sorting as $column => $sort) {
				$data = array();
				foreach ($this->data as $item) {
					$data[(string) $item->$column][] = $item; //HOTFIX: (string)
				}

				if ($sort === 'ASC') {
					ksort($data);
				} else {
					krsort($data);
				}

				$newData = array();
				foreach($data as $i) {
					foreach($i as $item) {
						$newData[] = $item;
					}
				}
				$this->data = new MapiResult($newData);
			}
		} else {
			$this->sorting = $sorting;
		}
	}

	
	
    /**
	 * @param mixed $column
	 * @param array $conditions
	 * @return array
	 * @throws \Nette\NotImplementedException 
	 */
	public function suggest($column, array $conditions)
	{
		throw new Nette\NotImplementedException;
		return $this->data;
	}
}
