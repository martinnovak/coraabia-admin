<?php

namespace Framework\Grido\DataSources;

use Nette,
	Framework;


class SmartDataSource extends Nette\Object implements \Grido\DataSources\IDataSource
{
	/** @var array */
	private $data;
	
	/** @var int */
	private $offset;
	
	/** @var int */
	private $limit;
	
	/** @var array */
	private $sorting;
	
	/** @var array */
	private $condition;
	
	
	/**
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}
	
	
	/**
	 * @todo
     * @return array
     */
    public function getData()
    {
		//filter
		//sort
		//offset, limit
		return new Framework\Utils\SmartResult(array_slice($this->data, (int)$this->offset, $this->limit ?: count($this->data)));
    }

	
    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->getData());
    }
	
	
	/**
     * @param array $condition
     */
    public function filter(array $condition)
	{
		$this->condition = $condition;
	}

	
    /**
     * @param int $offset
     * @param int $limit
     */
    public function limit($offset, $limit)
	{
		$this->offset = $offset;
		$this->limit = $limit;
	}

	
    /**
     * @param array $sorting
     */
    public function sort(array $sorting)
	{
		$this->sorting = $sorting;
	}

	
    /**
	 * @todo
	 * @param mixed $column
	 * @param array $conditions
	 * @return array
	 */
	public function suggest($column, array $conditions)
	{
		return $this->getData();
	}
}
