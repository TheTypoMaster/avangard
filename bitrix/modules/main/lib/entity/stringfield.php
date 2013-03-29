<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2012 Bitrix
 */

namespace Bitrix\Main\Entity;

/**
 * Entity field class for string data type
 * @package bitrix
 * @subpackage main
 */
class StringField extends ScalarField
{
	protected $format;

	function __construct($name, $dataType, Base $entity, $parameters = array())
	{
		parent::__construct($name, $dataType, $entity, $parameters);

		if (!empty($parameters['format']))
		{
			$this->format = $parameters['format'];
		}
	}

	public function validateValue($value)
	{
		if (parent::validateValue($value))
		{
			if ($this->format !== null)
			{
				// RETURN 'FORMAT'  (lang FIELD_INVALID_FORMAT)
				// or even return array('FORMAT')
				return (bool) preg_match($this->format, $value);
			}

			return true;
		}

		return false;
	}

	public function getFormat()
	{
		return $this->format;
	}
}