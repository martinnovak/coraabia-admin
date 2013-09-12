<?php

namespace Framework\Kapafaa\Configs;


class ProgressVarName extends Config
{
	const PROGRESS_VAR_NAME = 'progress_var_name';
	
	
	/**
	 * @param string $value
	 */
	public function __construct($value) {
		parent::__construct(self::PROGRESS_VAR_NAME, $value);
	}
	
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		return self::PREFIX . '.' . $this->type . '(@' . $this->value . '@)';
	}
}
