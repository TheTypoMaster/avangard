<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/classes/general/subscription.php");

class CSubscription extends CSubscriptionGeneral
{
	//get by e-mail
	function GetByEmail($email)
	{
		global $DB;

		$strSql =
			"SELECT S.*, ".
			"	".$DB->DateToCharFunction("S.DATE_UPDATE", "FULL")." AS DATE_UPDATE, ".
			"	".$DB->DateToCharFunction("S.DATE_INSERT", "FULL")." AS DATE_INSERT, ".
			"	".$DB->DateToCharFunction("S.DATE_CONFIRM", "FULL")." AS DATE_CONFIRM ".
			"FROM b_subscription S ".
			"WHERE UPPER(S.EMAIL)=UPPER('".$DB->ForSQL($email)."') ";

		return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	//agent to delete unconfirmed subscription
	function CleanUp()
	{
		global $DB;
		$interval = intval(COption::GetOptionString("subscribe", "subscribe_confirm_period"));
		if($interval > 0)
		{
			$DB->Query(
				"DELETE FROM b_subscription_rubric ".
				"WHERE SUBSCRIPTION_ID IN (".
				"	SELECT ID FROM b_subscription ".
			"	WHERE CONFIRMED<>'Y' AND DATE_CONFIRM < DATEADD(DAY, -".$interval.", GETDATE())) "
				, false, "File: ".__FILE__."<br>Line: ".__LINE__
			);  
			$DB->Query(
				"DELETE FROM b_subscription ".
				"WHERE CONFIRMED<>'Y' AND DATE_CONFIRM < DATEADD(DAY, -".$interval.", GETDATE()) "
				, false, "File: ".__FILE__."<br>Line: ".__LINE__
			);
		}
		return "CSubscription::CleanUp();";
	}
}
?>