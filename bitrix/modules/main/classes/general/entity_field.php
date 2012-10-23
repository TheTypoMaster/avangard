<?php


class CEntityField
{
	protected
		$name,
		$dataType,
		$entityName,
		$entity;

	protected
		$values; // for boolean


	public function __construct($name, $dataType, CBaseEntity $entity = NULL, $parameters = array())
	{
		$this->name = $name;
		$this->dataType = $dataType;

		// define entity
		if ($entity === NULL)
		{
			$probEntityName = substr($name, 0, strpos($name, '.'));

			if (!empty($probEntityName))
			{
				$this->entity = CBaseEntity::getInstance($probEntityName);
			}
			else
			{
				throw new Exception(sprintf(
					'Entity Object or Entity Name required for `%s` field.', $name
				));
			}
		}
		else
		{
			$this->entity = $entity;
		}

		if ($this->dataType == 'boolean')
		{
			if (empty($parameters['values']))
			{
				$this->values = array(false, true);
			}
			else
			{
				$this->values = $parameters['values'];
			}
		}

		$this->entityName = $this->entity->GetName();
	}

	public function GetSQLDefinition($tableAlias = null)
	{
		if (is_null($tableAlias))
		{
			$tableAlias = $this->entity->GetDBTableName();
		}

		//'`' . $tableAlias . '`.`' . $this->name . '`'
		return sprintf('%s%s%s.%s%s%s',
			CBaseEntity::$lEsc, $tableAlias, CBaseEntity::$rEsc,
			CBaseEntity::$lEsc, $this->name, CBaseEntity::$rEsc
		);
	}

	// for boolean attribute: convert classic bool values to field values
	public function normalizeValue($value)
	{
		if (
			(is_string($value) && ($value == '1' || $value == '0'))
			||
			(is_bool($value))
		)
		{
			$value = (int) $value;
		}
		elseif (is_string($value) && $value == 'true')
		{
			$value = 1;
		}
		elseif (is_string($value) && $value == 'false')
		{
			$value = 0;
		}

		if (is_integer($value) && ($value == 1 || $value == 0))
		{
			$value = $this->values[$value];
		}

		return $value;
	}

	public function GetName()
	{
		return $this->name;
	}

	public function GetDataType()
	{
		return $this->dataType;
	}

	public function GetEntityName()
	{
		return $this->entity->GetName();
	}

	public function GetEntity()
	{
		return $this->entity;
	}

	public function GetValues()
	{
		return $this->values;
	}
}


