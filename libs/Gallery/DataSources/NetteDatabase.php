<?php

namespace Gallery\DataSources;

/**
 * @property-read \Nette\Database\Table\Selection $selection
 * @property-read int $count
 * @property-read array $data
 */
class NetteDatabase extends \Nette\Object implements IDataSource
{
    /** @var \Nette\Database\Table\Selection */
    protected $selection;

	
    /**
     * @param \Nette\Database\Table\Selection $selection
     */
    public function __construct(\Nette\Database\Table\Selection $selection)
    {
        $this->selection = $selection;
    }

	
    /**
     * @return \Nette\Database\Table\Selection
     */
    public function getSelection()
    {
        return $this->selection;
    }

	
    protected function removePlaceholders(array $condition)
    {
        $condition[0] = trim(str_replace(array('%s', '%i', '%f'), '?', $condition[0]));
        return isset($condition[1])
            ? array(str_replace(array('[', ']'), array('', ''), $condition[0]) => $condition[1])
            : array(str_replace(array('[', ']'), array('', ''), $condition[0]));
    }

	
    /**
     * @return int
     */
    public function getCount()
    {
        return $this->selection->count('*');
    }

	
    /**
     * @return array
     */
    public function getData()
    {
        return $this->selection;
    }

	
    /**
     * @param array $condition
     */
    public function filter(array $condition)
    {
        $this->selection->where($this->removePlaceholders($condition));
    }

	
    /**
     * @param int $offset
     * @param int $limit
     */
    public function limit($offset, $limit)
    {
        $this->selection->limit($limit, $offset);
    }

	
    /**
     * @param array $sorting
     */
    public function sort(array $sorting)
    {
        foreach ($sorting as $column => $sort) {
            $this->selection->order("$column $sort");
        }
    }
}
