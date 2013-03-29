<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/user_counter.php");

class CUserCounter extends CAllUserCounter
{
	public static function Set($user_id, $code, $value, $site_id = SITE_ID, $tag = '')
	{
		global $DB;

		$value = intval($value);
		$user_id = intval($user_id);
		if ($user_id <= 0 || strlen($code) <= 0)
			return false;

		$rs = $DB->Query("
			SELECT CNT FROM b_user_counter
			WHERE USER_ID = ".$user_id."
			AND SITE_ID = '".$DB->ForSQL($site_id)."'
			AND CODE = '".$DB->ForSQL($code)."'
		");

		if ($rs->Fetch())
		{
			$ssql = "";
			if ($tag != "")
				$ssql = ", TAG = '".$DB->ForSQL($tag)."'";

			$DB->Query("
				UPDATE b_user_counter SET
				CNT = ".$value." ".$ssql."
				WHERE USER_ID = ".$user_id."
				AND SITE_ID = '".$DB->ForSQL($site_id)."'
				AND CODE = '".$DB->ForSQL($code)."'
			");
		}
		else
		{
			$DB->Query("
				INSERT INTO b_user_counter
				(CNT, USER_ID, SITE_ID, CODE, TAG)
				VALUES
				(".$value.", ".$user_id.", '".$DB->ForSQL($site_id)."', '".$DB->ForSQL($code)."', '".$DB->ForSQL($tag)."')
			", true);
		}

		if (is_array(self::$counters) && !empty(self::$counters))
		{
			if ($site_id == '**')
			{
				foreach(self::$counters as $key => $tmp)
					self::$counters[$key][$user_id][$code] = $value;
			}
			else
			{
				self::$counters[$site_id][$user_id][$code] = $value;
			}
		}

		return true;
	}

	public static function Increment($user_id, $code, $site_id = SITE_ID)
	{
		global $DB;

		$user_id = intval($user_id);
		if ($user_id <= 0 || strlen($code) <= 0)
			return false;

		$strSQL = "
			INSERT INTO b_user_counter (USER_ID, CNT, SITE_ID, CODE)
			VALUES (".$user_id.", 1, '".$DB->ForSQL($site_id)."', '".$DB->ForSQL($code)."')
			ON DUPLICATE KEY UPDATE CNT = CNT + 1";
		$DB->Query($strSQL, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

		if (is_array(self::$counters) && !empty(self::$counters))
		{
			if ($site_id == '**')
			{
				foreach(self::$counters as $key => $tmp)
				{
					if (isset(self::$counters[$key][$user_id][$code]))
						self::$counters[$key][$user_id][$code]++;
					else
						self::$counters[$key][$user_id][$code] = 1;
				}
			}
			else
			{
				if (isset(self::$counters[$site_id][$user_id][$code]))
					self::$counters[$site_id][$user_id][$code]++;
				else
					self::$counters[$site_id][$user_id][$code] = 1;
			}
		}

		return true;
	}

	public static function Decrement($user_id, $code, $site_id = SITE_ID)
	{
		global $DB;

		$user_id = intval($user_id);
		if ($user_id <= 0 || strlen($code) <= 0)
			return false;

		$strSQL = "
			INSERT INTO b_user_counter (USER_ID, CNT, SITE_ID, CODE)
			VALUES (".$user_id.", -1, '".$DB->ForSQL($site_id)."', '".$DB->ForSQL($code)."')
			ON DUPLICATE KEY UPDATE CNT = CNT - 1";
		$DB->Query($strSQL, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

		if (is_array(self::$counters) && !empty(self::$counters))
		{
			if ($site_id == '**')
			{
				foreach(self::$counters as $key => $tmp)
				{
					if (isset(self::$counters[$key][$user_id][$code]))
						self::$counters[$key][$user_id][$code]--;
					else
						self::$counters[$key][$user_id][$code] = -1;
				}
			}
			else
			{
				if (isset(self::$counters[$site_id][$user_id][$code]))
					self::$counters[$site_id][$user_id][$code]--;
				else
					self::$counters[$site_id][$user_id][$code] = -1;
			}
		}
		return true;
	}

	public static function IncrementWithSelect($sub_select)
	{
		global $DB;

		if (strlen($sub_select) > 0)
		{
			self::$counters = Array();
			$strSQL = "
				INSERT INTO b_user_counter (USER_ID, CNT, SITE_ID, CODE) (".$sub_select.")
				ON DUPLICATE KEY UPDATE CNT = CNT + 1
			";
			$DB->Query($strSQL, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		}
	}

	public static function Clear($user_id, $code, $site_id = SITE_ID)
	{
		global $DB;

		$user_id = intval($user_id);
		if ($user_id <= 0 || strlen($code) <= 0)
			return false;

		$strSQL = "
			INSERT INTO b_user_counter (USER_ID, SITE_ID, CODE, CNT, LAST_DATE)
			VALUES (".$user_id.", '".$DB->ForSQL($site_id)."', '".$DB->ForSQL($code)."', 0, ".$DB->CurrentTimeFunction().")
			ON DUPLICATE KEY UPDATE CNT = 0, LAST_DATE = ".$DB->CurrentTimeFunction()."
		";
		$res = $DB->Query($strSQL, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

		if (is_array(self::$counters) && !empty(self::$counters))
		{
			if ($site_id == '**')
			{
				foreach(self::$counters as $key => $tmp)
					self::$counters[$key][$user_id][$code] = 0;
			}
			else
			{
				self::$counters[$site_id][$user_id][$code] = 0;
			}
		}

		return true;
	}

	protected static function dbIF($condition, $yes, $no)
	{
		return "if(".$condition.", ".$yes.", ".$no.")";
	}

	// legacy function
	public static function ClearByUser($user_id, $site_id = SITE_ID, $code = "**")
	{
		return self::Clear($user_id, $code, $site_id);
	}
}
?>