<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2012 Bitrix
 */

namespace Bitrix\Main\Entity;

/**
 * Base entity field class
 * @package bitrix
 * @subpackage main
 */
abstract class Field
{
	/** @var string */
	protected
		$name,
		$title,
		$dataType;

	/** @var Base */
	protected $entity;

	/**
	 * @param string      $name
	 * @param string      $dataType    scalar type or class name
	 * @param Base        $entity
	 * @param array       $parameters
	 * @throws \Exception
	 */
	public function __construct($name, $dataType, Base $entity, $parameters = array())
	{
		if (!strlen($name))
		{
			throw new \Exception('Field name required');
		}

		$this->name = $name;
		$this->dataType = $dataType;
		$this->entity = $entity;
		$this->title = (isset($parameters["title"])? $parameters["title"] : null);
	}

	abstract public function validateValue($value);

	public function getName()
	{
		return $this->name;
	}

	public function getTitle()
	{
		if($this->title !== null)
			return $this->title;
		return $this->getName();
	}

	public function getDataType()
	{
		return $this->dataType;
	}

	public function getEntity()
	{
		return $this->entity;
	}

	public function getLangCode()
	{
		return $this->getEntity()->getLangCode().'_'.$this->getName().'_FIELD';
	}

	public function getLangText()
	{
		if($this->title !== null)
			return $this->title;
		return HasMessage($this->getLangCode()) ? GetMessage($this->getLangCode()) : $this->getName();
	}
}
