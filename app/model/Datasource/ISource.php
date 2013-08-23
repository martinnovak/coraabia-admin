<?php

namespace Model\Datasource;


interface ISource
{
	
	public function getConnection();
	
	
	public function getElement();
	
	
	public function getSelectionFactory();
}