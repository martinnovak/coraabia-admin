<?php

namespace Coraabia\Mapi;

use Nette;



class MapiDataSource extends Nette\Object implements \Grido\DataSources\IDataSource
{
	/** @var \Coraabia\Mapi\MapiRequest */
	private $request;
	
	/** @var array */
	private $data = array();
	
	
	
	/**
	 * @param \Coraabia\Mapi\MapiRequest $request 
	 */
	public function __construct(MapiRequest $request)
	{
		$this->request = $request;
	}
	
	
	
	/**
     * @return array
     */
    public function getData()
    {
        return $this->data = $this->request->load();
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
     * @return void
     */
    public function filter(array $condition)
	{
		//@todo
	}

	
	
    /**
     * @param int $offset
     * @param int $limit
     * @return void
     */
    public function limit($offset, $limit)
	{
		$this->request->setParam('start', $offset);
		$this->request->setParam('count', $limit);
	}

	
	
    /**
     * @param array $sorting
     * @return void
     */
    public function sort(array $sorting)
	{
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

            $this->data = array();
            foreach($data as $i) {
                foreach($i as $item) {
                    $this->data[] = $item;
                }
            }
        }
	}

	
	
    /**
     * @param mixed $column
     * @param array $conditions
     * @return array
     */
    public function suggest($column, array $conditions)
	{
		throw new Nette\NotImplementedException;
		return $this->data;
	}
}
