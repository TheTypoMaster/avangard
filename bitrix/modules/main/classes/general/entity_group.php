<?php

class CGroupEntity extends CBaseEntity
{
	protected function __construct() {}

	public function initialize()
	{
		$this->className = __CLASS__;
		$this->filePath = __FILE__;

		$this->dbTableName = 'b_sonet_group';

		$this->fieldsMap = array(
			'ID' => array(
				'data_type' => 'integer'
			),
			'NAME' => array(
				'data_type' => 'string'
			)
		);
	}


}