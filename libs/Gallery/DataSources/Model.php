<?php

namespace Gallery\DataSources;

/**
 * @property-read IDataSource $dataSource
 */
class Model extends \Nette\Object
{
    /** @var array */
    public $callback = array();

    /** @var \Gallery\DataSources\IDataSource */
    protected $dataSource;

	
    /**
     * @param mixed $model
     * @throws \InvalidArgumentException
     */
    public function __construct($model)
    {
        if ($model instanceof \Nette\Database\Table\Selection) {
            $dataSource = new NetteDatabase($model);
        } elseif ($model instanceof IDataSource) {
            $dataSource = $model;
        } else {
            throw new \InvalidArgumentException('Model must be implemented \Grido\DataSources\IDataSource.');
        }
        $this->dataSource = $dataSource;
    }

	
    /**
     * @return \Gallery\DataSources\IDataSource
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

	
    public function __call($method, $args)
    {
        return isset($this->callback[$method])
            ? callback($this->callback[$method])->invokeArgs(array($this->dataSource, $args))
            : call_user_func_array(array($this->dataSource, $method), $args);
    }
}
