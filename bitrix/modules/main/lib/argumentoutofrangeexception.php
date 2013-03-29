<?php
namespace Bitrix\Main;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Exception is thrown when the value of an argument is outside the allowable range of values.
 */
class ArgumentOutOfRangeException
	extends ArgumentException
{
	protected $lowerLimit;
	protected $upperLimit;

	/**
	 * Creates new exception object.
	 *
	 * @param string $parameter Argument that generates exception
	 * @param null $lowerLimit Either lower limit of the allowable range of values or an array of allowable values
	 * @param null $upperLimit Upper limit of the allowable values
	 * @param \Exception $previous
	 */
	public function __construct($parameter, $lowerLimit = null, $upperLimit = null, \Exception $previous = null)
	{
		if (is_array($lowerLimit))
		{
			$messageCode = "argument_out_of_range_array_exception_message";
			$messageParams = array(
				"#PARAMETER#" => $parameter,
				"#ALLOWABLE_VALUES#" => implode(", ", $lowerLimit),
			);
		}
		elseif (($lowerLimit !== null) && ($upperLimit !== null))
		{
			$messageCode = "argument_out_of_range_between_exception_message";
			$messageParams = array(
				"#PARAMETER#" => $parameter,
				"#LOWER_LIMIT#" => $lowerLimit,
				"#UPPER_LIMIT#" => $upperLimit,
			);
		}
		elseif (($lowerLimit === null) && ($upperLimit !== null))
		{
			$messageCode = "argument_out_of_range_upper_exception_message";
			$messageParams = array(
				"#PARAMETER#" => $parameter,
				"#UPPER_LIMIT#" => $upperLimit,
			);
		}
		elseif (($lowerLimit !== null) && ($upperLimit === null))
		{
			$messageCode = "argument_out_of_range_lower_exception_message";
			$messageParams = array(
				"#PARAMETER#" => $parameter,
				"#LOWER_LIMIT#" => $lowerLimit,
			);
		}
		else
		{
			$messageCode = "argument_out_of_range_exception_message";
			$messageParams = array(
				"#PARAMETER#" => $parameter,
			);
		}

		$message = Loc::getMessage($messageCode, $messageParams);
		$this->lowerLimit = $lowerLimit;
		$this->upperLimit = $upperLimit;
		parent::__construct($message, $parameter, $previous);
	}

	public function getLowerLimitType()
	{
		return $this->lowerLimit;
	}

	public function getUpperType()
	{
		return $this->upperLimit;
	}
}
