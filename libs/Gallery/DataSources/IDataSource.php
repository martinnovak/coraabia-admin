<?php

namespace Gallery\DataSources;


interface IDataSource
{
    /**
     * @return int
     */
    function getCount();

    /**
     * @return array
     */
    function getData();

    /**
     * @param array $condition
     * @return void
     */
    function filter(array $condition);

    /**
     * @param int $offset
     * @param int $limit
     * @return void
     */
    function limit($offset, $limit);

    /**
     * @param array $sorting
     * @return void
     */
    function sort(array $sorting);
}
