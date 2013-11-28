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
		$data = $this->data;
		//filter
		$this->applyFiltering($data);
		//sort
		$this->applySorting($data);
		//offset, limit
		$data = array_slice($data, (int)$this->offset, $this->limit ?: count($data));
		return new Framework\Utils\SmartResult($data);
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
		Nette\Diagnostics\Debugger::dump($this->condition);
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
	
	
	/**
	 * @todo
	 * @param array $data
	 */
	protected function applyFiltering(array &$data)
	{
		if (is_array($this->condition)) {
			foreach ($this->condition as $key => $value) {
				
			}
		}
	}
	
	
	/**
	 * @param array $data
	 */
	protected function applySorting(array &$data)
	{
		if (is_array($this->sorting)) {
			foreach ($this->sorting as $key => $order) {
				usort($data, function ($a, $b) use ($key, $order ){
					$compared = Framework\Utils\SmartObject::access($a)->$key < Framework\Utils\SmartObject::access($b)->$key;
					return $order == 'ASC' ? ($compared ? -1 : 1) : ($compared ? 1 : -1);
				});
			}
		}
	}
}
