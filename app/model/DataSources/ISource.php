<?php

namespace Model\DataSources;


interface ISource
{
	
	public function getSource();
	
	public function beginTransaction();
	
	public function commit();
	
	public function rollBack();
}