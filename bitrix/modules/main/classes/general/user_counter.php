<?
class CAllUserCounter
{
	protected static $counters = Array();

	public static function GetValue($user_id, $code, $site_id = SITE_ID)
	{
		global $DB;
		$user_id = intval($user_id);

		if ($user_id <= 0)
			return false;

		$arCodes = self::GetValues($user_id, $site_id);
		if (isset($arCodes[$code]))
			return intval($arCodes[$code]);
		else
			return 0;
	}

	public static function GetValues($user_id, $site_id = SITE_ID)
	{
		global $DB;

		$user_id = intval($user_id);
		if ($user_id <= 0)
			return array();

		if(!isset(self::$counters[$site_id][$user_id]))
		{
			$strSQL = "
				SELECT CODE, SITE_ID, CNT
				FROM b_user_counter
				WHERE USER_ID = ".$user_id;

			$dbRes = $DB->Query($strSQL, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			while ($arRes = $dbRes->Fetch())
			{
				if (in_array($arRes["SITE_ID"], array($site_id, '**')))
				{
					if (!isset(self::$counters[$site_id][$user_id][$arRes["CODE"]]))
						self::$counters[$site_id][$user_id][$arRes["CODE"]] = 0;
					self::$counters[$site_id][$user_id][$arRes["CODE"]] += $arRes["CNT"];
				}
			}
		}

		return self::$counters[$site_id][$user_id];
	}

	public static function GetLastDate($user_id, $code, $site_id = SITE_ID)
	{
		global $DB;

		$user_id = intval($user_id);
		if ($user_id <= 0 || strlen($code) <= 0)
			return 0;
		
		$strSQL = "
			SELECT ".$DB->DateToCharFunction("LAST_DATE", "FULL")." LAST_DATE
			FROM b_user_counter
			WHERE USER_ID = ".$user_id."
			AND (SITE_ID = '".$site_id."' OR SITE_ID = '**')
			AND CODE = '".$DB->ForSql($code)."'
		";

		$result = 0;
		$dbRes = $DB->Query($strSQL, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if ($arRes = $dbRes->Fetch())
			$result = MakeTimeStamp($arRes["LAST_DATE"]);

		return $result;
	}

	public static function ClearAll($user_id, $site_id = SITE_ID)
	{
		global $DB;

		$user_id = intval($user_id);
		if ($user_id <= 0)
			return false;

		$strSQL = "
			DELETE FROM b_user_counter 
			WHERE USER_ID = ".$user_id." 
			AND (SITE_ID = '".$site_id."' OR SITE_ID = '**')";
		$dbRes = $DB->Query($strSQL, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($site_id == '**')
		{
			if (isset(self::$counters))
			{
				foreach(self::$counters as $key => $tmp)
					unset(self::$counters[$key][$user_id]);
			}
		}			
		else
		{
			unset(self::$counters[$site_id][$user_id]);
		}

		return true;
	}

	public static function ClearByTag($tag, $code, $site_id = SITE_ID)
	{
		global $DB;

		if (strlen($tag) <= 0 || strlen($code) <= 0)
			return false;

		$strSQL = "
			DELETE FROM b_user_counter 
			WHERE TAG = '".$DB->ForSQL($tag)."' AND CODE = '".$DB->ForSQL($code)."'
			AND (SITE_ID = '".$site_id."' OR SITE_ID = '**')";
		$dbRes = $DB->Query($strSQL, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($site_id == '**')
		{
			self::$counters = Array();
		}			
		else
		{
			unset(self::$counters[$site_id]);
		}

		return true;
	}

	// legacy function
	public static function GetValueByUserID($user_id, $site_id = SITE_ID, $code = "**")
	{
		return self::GetValue($user_id, $code, $site_id);
	}
	public static function GetCodeValuesByUserID($user_id, $site_id = SITE_ID)
	{
		return self::GetValues($user_id, $site_id);
	}
	public static function GetLastDateByUserAndCode($user_id, $site_id = SITE_ID, $code = "**")
	{
		return self::GetLastDate($user_id, $code, $site_id);
	}
}
?>