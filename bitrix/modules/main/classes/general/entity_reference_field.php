<?php


class CReferenceEntityField extends CEntityField
{
	protected
		$refEntity,
		$localField,
		$remoteField;


	public function __construct($name, CBaseEntity $entity = NULL, $refEntity, $localFieldName, $remoteFieldName)
	{
		parent::__construct($name, $refEntity->getName(), $entity);

		$this->refEntity = $refEntity;

		$this->localField = $this->entity->GetField($localFieldName);
		$this->remoteField = $this->refEntity->GetField($remoteFieldName);
	}


	public function GetSQLDefinition()
	{
		throw new Exception('There is no SQL for Reference Entity Field.');
	}

	public function GetRefEntity()
	{
		return $this->refEntity;
	}

	public function GetLocalField()
	{
		return $this->localField;
	}

	public function GetRemoteField()
	{
		return $this->remoteField;
	}

}


