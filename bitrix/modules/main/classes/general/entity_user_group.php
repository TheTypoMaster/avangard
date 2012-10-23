<?php

class CUserGroupEntity extends CBaseEntity
{
	protected function __construct() {}

	public function initialize()
	{
		$this->className = __CLASS__;
		$this->filePath = __FILE__;

		$this->fieldsMap = array(
			'USER_ID' => array(
				'data_type' => 'integer'
			),
			'USER' => array(
				'data_type' => 'User',
				'reference' => array('USER_ID', 'ID')
			),
			'GROUP_ID' => array(
				'data_type' => 'integer'
			),
			'GROUP' => array(
				'data_type' => 'Group',
				'reference' => array('GROUP_ID', 'ID')
			)
		);
	}


}