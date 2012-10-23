<?
/*
##############################################
# Bitrix: SiteManager                        #
# Copyright (c) 2002 Bitrix                  #
# http://www.bitrix.ru                       #
# mailto:admin@bitrix.ru                     #
##############################################
*/
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/agent.php");

class CAgent extends CAllAgent
{
	function CheckAgents()
	{
		global $DB, $DOCUMENT_ROOT;

		$agents_use_crontab	= COption::GetOptionString("main", "agents_use_crontab", "N");
		$str_crontab = "";
		if($agents_use_crontab=="Y" || (defined("BX_CRONTAB_SUPPORT") && BX_CRONTAB_SUPPORT===true))
		{
			if(defined("BX_CRONTAB") && BX_CRONTAB==true)
				$str_crontab = " AND IS_PERIOD='N' ";
			else
				$str_crontab = " AND IS_PERIOD='Y' ";
		}

		if(CACHED_b_agent!==false)
		{
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/managed_cache/".$DB->type."/b_agent"))
			{
				include($_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/managed_cache/".$DB->type."/b_agent");
				if(time()<$saved_time)
					return "";
			}
		}

		$DB->StartTransaction();
		$strSql=
			"SELECT ID, NAME, AGENT_INTERVAL, IS_PERIOD, MODULE_ID ".
			"FROM b_agent ".
			"WHERE ACTIVE='Y'  ".
			"	AND NEXT_EXEC<=SYSDATE ".
			$str_crontab.
			"FOR UPDATE NOWAIT ".
			"ORDER BY SORT desc";

		if(!($db_result_agents = $DB->Query($strSql, true)))
		{
			$DB->Commit();
			return;
		}

		$i=0;
		while($arAgent = $db_result_agents->Fetch())
		{
			if($i==0)
			{
				@set_time_limit(0);
				ignore_user_abort(true);
				$i=1;
			}

			if(strlen($arAgent["MODULE_ID"])>0 && $arAgent["MODULE_ID"]!="main")
			{
				if(!CModule::IncludeModule($arAgent["MODULE_ID"]))
					continue;
			}

			//эти переменные могут измениться в вызываемой функции
			$pPERIOD = $arAgent["AGENT_INTERVAL"];

			$eval_result="";
			eval("\$eval_result=".$arAgent["NAME"]);

			if(strlen($eval_result)<=0)
				$strSql="DELETE FROM b_agent WHERE ID=".$arAgent["ID"];
			else
			{
				if($arAgent["IS_PERIOD"]=="Y")
					$strSql="UPDATE b_agent SET NAME='".$eval_result."', LAST_EXEC=SYSDATE, NEXT_EXEC=NEXT_EXEC+".$pPERIOD."/86400 WHERE ID=".$arAgent["ID"];
				else
					$strSql="UPDATE b_agent SET NAME='".$eval_result."', LAST_EXEC=SYSDATE, NEXT_EXEC=SYSDATE+".$pPERIOD."/86400 WHERE ID=".$arAgent["ID"];
			}
			$DB->Query($strSql);
		}
		$DB->Commit();
		if($i===0 && CACHED_b_agent!==false)
		{
			$fp = @fopen($_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/managed_cache/".$DB->type."/b_agent", "w");
			if($fp)
			{
				$rs = $DB->Query("SELECT round((MIN(NEXT_EXEC) - SYSDATE)*86400) DATE_DIFF FROM b_agent WHERE ACTIVE='Y'");
				$ar = $rs->Fetch();
				if(!$ar || $ar["DATE_DIFF"]<0)
					$date_diff = 0;
				elseif($ar["DATE_DIFF"]>CACHED_b_agent)
					$date_diff = CACHED_b_agent;
				else
					$date_diff = $ar["DATE_DIFF"];
				fputs($fp, "<?php \$saved_time=".intval(time()+$date_diff).";?>");
				fclose($fp);
			}
		}
	}
}
?>
