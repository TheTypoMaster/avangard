<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/vote/classes/general/vote.php");

class CVoteChannel extends CAllVoteChannel
{
	function err_mess()
	{
		$module_id = "vote";
		return "<br>Module: ".$module_id."<br>Class: CVoteChannel<br>File: ".__FILE__;
	}

	function GetList(&$by, &$order, $arFilter=Array(), &$is_filtered)
	{
		$err_mess = (CVoteChannel::err_mess())."<br>Function: GetList<br>Line: ";
		global $DB;
		$arSqlSearch = array();
		$arSqlSearch_1 = array();
		$strSqlSearch = "";
		$strSqlSearch_1 = "";
		if (is_array($arFilter))
		{
			$filter_keys = array_keys($arFilter);
			for ($i=0; $i<count($filter_keys); $i++)
			{
				$key = $filter_keys[$i];
				$val = $arFilter[$filter_keys[$i]];
				if (strlen($val)<=0 || $val=="NOT_REF") continue;
				if (is_array($val) && count($val)<=0) continue;
				$match_value_set = (in_array($key."_EXACT_MATCH", $filter_keys)) ? true : false;
				$key = strtoupper($key);
				switch($key)
				{
					case "ID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("C.ID",$val,$match);
						break;
					case "SITE":
						if (is_array($val)) $val = implode(" | ", $val);
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch_1[] = GetFilterQuery("CS.SITE_ID", $val, $match);
						$strSqlSearch_1 = GetFilterSqlSearch($arSqlSearch_1);
						$where = " and exists (SELECT 'x' FROM b_vote_channel_2_site CS WHERE $strSqlSearch_1 and C.ID = CS.CHANNEL_ID) ";
						break;
					case "TITLE":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("C.TITLE",$val,$match);
						break;
					case "SID":
					case "SYMBOLIC_NAME":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("C.SYMBOLIC_NAME",$val,$match);
						break;
					case "ACTIVE":
						$arSqlSearch[] = ($val=="Y") ? "C.ACTIVE='Y'" : "C.ACTIVE='N'";
						break;
					case "LID":
					case "FIRST_SITE_ID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("C.FIRST_SITE_ID",$val,$match);
						break;
				}
			}
		}

		if ($by == "s_id")					$strSqlOrder = "ORDER BY C.ID";
		elseif ($by == "s_timestamp")		$strSqlOrder = "ORDER BY C.TIMESTAMP_X";
		elseif ($by == "s_c_sort")			$strSqlOrder = "ORDER BY C.C_SORT";
		elseif ($by == "s_active")			$strSqlOrder = "ORDER BY C.ACTIVE";
		elseif ($by == "s_symbolic_name")	$strSqlOrder = "ORDER BY C.SYMBOLIC_NAME";
		elseif ($by == "s_title")			$strSqlOrder = "ORDER BY C.TITLE ";
		elseif ($by == "s_votes")			$strSqlOrder = "ORDER BY count(V.ID)";
		else 
		{
			$by = "s_id";
			$strSqlOrder = "ORDER BY C.ID";
		}
		if ($order!="asc")
		{
			$strSqlOrder .= " desc ";
			$order="desc";
		}

		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		$strSql = "
			SELECT 
				C.ID, C.ACTIVE, C.TITLE, C.C_SORT, 
				C.FIRST_SITE_ID,
				C.FIRST_SITE_ID											LID,
				C.SYMBOLIC_NAME,
				C.SYMBOLIC_NAME											SID,
				".$DB->DateToCharFunction("C.TIMESTAMP_X")."			TIMESTAMP_X,
				count(V.ID) VOTES
			FROM 
				b_vote_channel C
			LEFT JOIN b_vote V ON (V.CHANNEL_ID = C.ID)
			WHERE				
			$strSqlSearch
			$where
			GROUP BY
				C.ID, C.SYMBOLIC_NAME, C.ACTIVE, C.TITLE, C.C_SORT, C.FIRST_SITE_ID, C.TIMESTAMP_X
			$strSqlOrder
			";

		$is_filtered = IsFiltered($strSqlSearch);

		if (VOTE_CACHE_TIME===false)
		{
			$res = $DB->Query($strSql, false, $err_mess.__LINE__);
			return $res;
		}
		else
		{
			global $CACHE_MANAGER;
			$md5 = md5($strSql);
			if($CACHE_MANAGER->Read(VOTE_CACHE_TIME, "b_vote_channel_".$md5, "b_vote_channel"))
			{
				$arCache = $CACHE_MANAGER->Get("b_vote_channel_".$md5);
			}
			else
			{
				$res = $DB->Query($strSql, false, $err_mess.__LINE__);
				while($ar = $res->Fetch())
					$arCache[] = $ar;

				$CACHE_MANAGER->Set("b_vote_channel_".$md5, $arCache);
			}

			$r = new CDBResult();
			$r->InitFromArray($arCache);
			unset($arCache);
			return $r;
		}
	}

	function GetDropDownList()
	{
		global $DB;
		$err_mess = (CVoteChannel::err_mess())."<br>Function: GetDropDownList<br>Line: ";
		$strSql = "
			SELECT
				ID as REFERENCE_ID,
				'['+convert(varchar(8000),ID)+'] '+TITLE as REFERENCE
			FROM b_vote_channel
			ORDER BY C_SORT				
			";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}
}

class CVote extends CAllVote
{
	function err_mess()
	{
		$module_id = "vote";
		return "<br>Module: ".$module_id."<br>Class: CVote<br>File: ".__FILE__;
	}

	function GetDropDownList()
	{
		global $DB;
		$err_mess = (CVote::err_mess())."<br>Function: GetDropDownList<br>Line: ";
		$strSql = "
			SELECT
				ID as REFERENCE_ID,
				'['+convert(varchar(8000),ID)+'] '+TITLE as REFERENCE
			FROM b_vote
			ORDER BY C_SORT, ID
			";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}

	function CheckVotingIP($VOTE_ID, $REMOTE_ADDR, $KEEP_IP_SEC)
	{
		global $DB;
		$err_mess = (CVote::err_mess())."<br>Function: CheckVotingIP<br>Line: ";
		$VOTE_ID = intval($VOTE_ID);
		$KEEP_IP_SEC = intval($KEEP_IP_SEC);
		$check_date = date("d.m.Y H:i:s",time()-$KEEP_IP_SEC);
		$strSql = "
			SELECT 
				ID
			FROM 
				b_vote_event 
			WHERE 
				IP='".$DB->ForSql($REMOTE_ADDR,15)."' 
			and VOTE_ID='$VOTE_ID'
			and (DATE_VOTE>=".$DB->CharToDateFunction($check_date, "FULL")." or '$KEEP_IP_SEC'='0')
			";
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		$zr = $z->Fetch();
		$EVENT_ID = intval($zr["ID"]);
		if ($EVENT_ID>0) return false; else return true;
	}

	function GetNextStartDate($CHANNEL_ID)
	{
		global $DB;
		$err_mess = (CVote::err_mess())."<br>Function: GetNextStartDate<br>Line: ";
		$CHANNEL_ID = intval($CHANNEL_ID);
		$strSql = "
			SELECT
				".$DB->DateToCharFunction("max(dateadd(second, 1, DATE_END))")."	MIN_DATE_START
			FROM
				b_vote
			WHERE
				CHANNEL_ID = '$CHANNEL_ID'
			";
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		$zr = $z->Fetch();
		if (strlen($zr["MIN_DATE_START"])<=0) return GetTime(time(),"FULL"); 
		else return $zr["MIN_DATE_START"];
	}

	function WrongDateInterval($CURRENT_VOTE_ID, $DATE_START, $DATE_END, $CHANNEL_ID)
	{
		global $DB;
		$err_mess = (CVote::err_mess())."<br>Function: WrongDateInterval<br>Line: ";
		$CURRENT_VOTE_ID = intval($CURRENT_VOTE_ID);
		$CHANNEL_ID = intval($CHANNEL_ID);
		$st = $DB->CharToDateFunction($DATE_START, "FULL");
		//$en = (strlen($DATE_END)>0) ? $DB->CharToDateFunction($DATE_END, "FULL") : $DB->CharToDateFunction("31.12.2030 23:59:59", "FULL");
		$en = (strlen($DATE_END)>0) ? $DB->CharToDateFunction($DATE_END, "FULL") : $DB->CharToDateFunction(ConvertTimeStamp(mktime(23, 59, 59, 12, 31, 2030), "FULL"));

		//ConvertTimeStamp(mktime(23, 59, 59, 12, 31, 2030), "FULL")

		$strSql = "
			SELECT ID 
			FROM b_vote 
			WHERE
				ID<>'$CURRENT_VOTE_ID'
			and	CHANNEL_ID='".intval($CHANNEL_ID)."' 
			and ACTIVE='Y'
			and (
					($st between DATE_START and DATE_END) or
					($en between DATE_START and DATE_END) or
					(DATE_START between $st and $en) or
					(DATE_END between $st and $en)
				)
			";
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		$zr = $z->Fetch();
		$vid = intval($zr["ID"]);
		return $vid;
	}

	function Delete($ID)
	{
		global $DB, $CACHE_MANAGER;
		$err_mess = (CVote::err_mess())."<br>Function: Delete<br>Line: ";
		$ID = intval($ID);
		CVote::Reset($ID);

		if (VOTE_CACHE_TIME!==false) $CACHE_MANAGER->CleanDir("b_vote_channel");
		$res = $DB->Query("DELETE FROM b_vote WHERE ID='$ID'", false, $err_mess.__LINE__);
		return $res;
	}

	function Reset($ID)
	{
		global $DB;
		$err_mess = (CVote::err_mess())."<br>Function: Reset<br>Line: ";
		$ID = intval($ID);
		
		// обнуляем вопросы
		$z = $DB->Query("SELECT ID FROM b_vote_question WHERE VOTE_ID='$ID'", false, $err_mess.__LINE__);
		while ($zr = $z->Fetch()) CVoteQuestion::Reset($zr["ID"]);

		// удаляем все ответы по данному опросу
		$DB->Query("DELETE FROM b_vote_event WHERE VOTE_ID='$ID'", false, $err_mess.__LINE__);

		// обнуляем счетчик голосов у опроса
		$arFields = array("COUNTER"=>"0");
		unset($GLOBALS["VOTE_CACHE_VOTING"][$ID]);
		$DB->Update("b_vote",$arFields,"WHERE ID='$ID'",$err_mess.__LINE__);
	}

	function GetList(&$by, &$order, $arFilter=Array(), &$is_filtered)
	{
		$err_mess = (CVote::err_mess())."<br>Function: GetList<br>Line: ";
		global $DB;
		$arSqlSearch = Array();
		$strSqlSearch = "";
		if (is_array($arFilter))
		{
			$filter_keys = array_keys($arFilter);
			for ($i=0; $i<count($filter_keys); $i++)
			{
				$key = $filter_keys[$i];
				$val = $arFilter[$filter_keys[$i]];
				if (strlen($val)<=0 || $val=="NOT_REF") continue;
				if (is_array($val) && count($val)<=0) continue;
				$match_value_set = (in_array($key."_EXACT_MATCH", $filter_keys)) ? true : false;
				$key = strtoupper($key);
				switch($key)
				{
					case "ID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("V.ID",$val,$match);
						break;
					case "ACTIVE":
						$arSqlSearch[] = ($val=="Y") ? "V.ACTIVE='Y'" : "V.ACTIVE='N'";
						break;
					case "DATE_START_1":
						$arSqlSearch[] = "V.DATE_START >= ".$DB->CharToDateFunction($val, "SHORT");
						break;
					case "DATE_START_2":
						$arSqlSearch[] = "V.DATE_START < dateadd(day, 1, ".$DB->CharToDateFunction($val, "SHORT").")";
						break;
					case "DATE_END_1":
						$arSqlSearch[] = "V.DATE_END >= ".$DB->CharToDateFunction($val, "SHORT");
						break;
					case "DATE_END_2":
						$arSqlSearch[] = "V.DATE_END < dateadd(day, 1, ".$DB->CharToDateFunction($val, "SHORT").")";
						break;
					case "LAMP":
						if ($val=="red")
							$arSqlSearch[] = "(V.ACTIVE<>'Y' or getdate()<V.DATE_START or getdate()>V.DATE_END)";
						elseif ($val=="green")
							$arSqlSearch[] = "(V.ACTIVE='Y' and getdate()>=V.DATE_START and getdate()<=V.DATE_END)";
						break;
					case "CHANNEL":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("C.ID, C.TITLE, C.SYMBOLIC_NAME",$val,$match);
						break;
					case "CHANNEL_ID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("V.CHANNEL_ID",$val,$match);
						break;
					case "TITLE":
					case "DESCRIPTION":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("V.".$key,$val,$match);
						break;
					case "COUNTER_1":
						$arSqlSearch[] = "V.COUNTER>='".intval($val)."'";
						break;
					case "COUNTER_2":
						$arSqlSearch[] = "V.COUNTER<='".intval($val)."'";
						break;
				}
			}
		}

		if ($by == "s_id")					$strSqlOrder = "ORDER BY V.ID";
		elseif ($by == "s_title")			$strSqlOrder = "ORDER BY V.TITLE";
		elseif ($by == "s_date_start")		$strSqlOrder = "ORDER BY V.DATE_START";
		elseif ($by == "s_date_end")		$strSqlOrder = "ORDER BY V.DATE_END";
		elseif ($by == "s_lamp")			$strSqlOrder = "ORDER BY LAMP";
		elseif ($by == "s_counter")			$strSqlOrder = "ORDER BY V.COUNTER";
		elseif ($by == "s_active")			$strSqlOrder = "ORDER BY V.ACTIVE";
		elseif ($by == "s_c_sort")			$strSqlOrder = "ORDER BY V.C_SORT";
		elseif ($by == "s_channel")			$strSqlOrder = "ORDER BY V.CHANNEL_ID";
		else 
		{
			$by = "s_id";
			$strSqlOrder = "ORDER BY V.ID";
		}
		if ($order!="asc")
		{
			$strSqlOrder .= " desc ";
			$order="desc";
		}

		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		$strSql = "
			SELECT
				C.TITLE as CHANNEL_TITLE,
				V.ID, V.CHANNEL_ID, V.C_SORT, V.ACTIVE, V.COUNTER, V.TITLE, V.DESCRIPTION, V.DESCRIPTION_TYPE, V.IMAGE_ID, V.EVENT1, V.EVENT2, V.EVENT3, V.UNIQUE_TYPE, V.KEEP_IP_SEC, V.DELAY, V.DELAY_TYPE, V.TEMPLATE, V.RESULT_TEMPLATE, V.NOTIFY,
				".$DB->DateToCharFunction("V.DATE_START")."	DATE_START,
				".$DB->DateToCharFunction("V.DATE_END")."	DATE_END,
				datediff(second, V.DATE_START, V.DATE_END)	PERIOD,
				count(Q.ID) QUESTIONS,
				case 
					when V.ACTIVE='N' or getdate() not between V.DATE_START and V.DATE_END then 'red'
					else 'green'
				end											LAMP
			FROM
				b_vote V
			INNER JOIN b_vote_channel C ON (C.ID=V.CHANNEL_ID)
			LEFT JOIN b_vote_question Q ON (Q.VOTE_ID=V.ID)
			WHERE
			$strSqlSearch
			GROUP BY 
				C.TITLE,
				V.ID, V.CHANNEL_ID, V.C_SORT, V.ACTIVE, V.COUNTER, V.TITLE, V.DESCRIPTION, V.DESCRIPTION_TYPE, V.IMAGE_ID, V.EVENT1, V.EVENT2, V.EVENT3, V.UNIQUE_TYPE, V.KEEP_IP_SEC, V.DELAY, V.DELAY_TYPE, V.TEMPLATE, V.RESULT_TEMPLATE, V.NOTIFY,
				V.DATE_START, V.DATE_END
			$strSqlOrder
			";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		$is_filtered = IsFiltered($strSqlSearch);
		return $res;
	}

	function GetPublicList($arFilter=Array(), $strSqlOrder="ORDER BY C.C_SORT, C.ID, V.DATE_START desc")
	{
		$err_mess = (CVote::err_mess())."<br>Function: GetPublicList<br>Line: ";
		global $DB, $USER;
		$arSqlSearch = Array();
		$strSqlSearch = "";
		$arSqlSearch_1 = Array();
		$strSqlSearch_1 = "";
		if (is_array($arFilter))
		{
			$filter_keys = array_keys($arFilter);
			for ($i=0; $i<count($filter_keys); $i++)
			{
				$key = $filter_keys[$i];
				$val = $arFilter[$filter_keys[$i]];
				if (strlen($val)<=0 || $val=="NOT_REF") continue;
				if (is_array($val) && count($val)<=0) continue;
				$match_value_set = (in_array($key."_EXACT_MATCH", $filter_keys)) ? true : false;
				$key = strtoupper($key);
				switch($key)
				{
					case "SITE":
						if (is_array($val)) $val = implode(" | ", $val);
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch_1[] = GetFilterQuery("CS.SITE_ID", $val, $match);
						$strSqlSearch_1 = GetFilterSqlSearch($arSqlSearch_1);
						$where = " and exists (SELECT 'x' FROM b_vote_channel_2_site CS WHERE $strSqlSearch_1 and C.ID = CS.CHANNEL_ID) ";
						break;
					case "CHANNEL":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("C.SYMBOLIC_NAME", $val, $match);
						break;
					case "FIRST_SITE_ID":
					case "LID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("C.FIRST_SITE_ID",$val,$match);
						break;
				}
			}
		}
		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		$arGroups = $USER->GetUserGroupArray();
		if (!is_array($arGroups)) $arGroups[] = 2;
		$groups = implode(",",$arGroups);
		if (!($USER->IsAdmin()))
		{
			$strSql = "
				SELECT
					C.TITLE CHANNEL_TITLE, 
					V.ID, V.CHANNEL_ID, V.C_SORT, V.ACTIVE, V.COUNTER, V.TITLE, V.DESCRIPTION, V.DESCRIPTION_TYPE, V.IMAGE_ID, V.EVENT1, V.EVENT2, V.EVENT3, V.UNIQUE_TYPE, V.KEEP_IP_SEC, V.DELAY, V.DELAY_TYPE, V.TEMPLATE, V.RESULT_TEMPLATE, V.NOTIFY,
					".$DB->DateToCharFunction("V.DATE_START")."		DATE_START,
					".$DB->DateToCharFunction("V.DATE_END")."		DATE_END,
					max(G.PERMISSION)								MAX_PERMISSION,
					case 
						when V.ACTIVE='N' or getdate() not between V.DATE_START and V.DATE_END then 'red'
						else 'green'
					end												LAMP
				FROM
					b_vote V
				INNER JOIN b_vote_channel C ON (C.ID = V.CHANNEL_ID and C.ACTIVE = 'Y')
				INNER JOIN b_vote_channel_2_group G ON (G.CHANNEL_ID = C.ID and G.GROUP_ID in ($groups))
				WHERE
					$strSqlSearch
				and V.ACTIVE = 'Y'
				and V.DATE_START<=getdate()					
				$where
				GROUP BY
					C.ID, C.TITLE, 
					V.ID, V.CHANNEL_ID, V.C_SORT, V.ACTIVE, V.COUNTER, V.TITLE, V.DESCRIPTION, V.DESCRIPTION_TYPE, V.IMAGE_ID, V.EVENT1, V.EVENT2, V.EVENT3, V.UNIQUE_TYPE, V.KEEP_IP_SEC, V.DELAY, V.DELAY_TYPE, V.TEMPLATE, V.RESULT_TEMPLATE, V.NOTIFY,
					V.DATE_START, V.DATE_END, 
					C.C_SORT, C.ID
				HAVING 
					max(G.PERMISSION)>0
				$strSqlOrder
				";
		}
		else
		{
			$strSql = "
				SELECT
					C.TITLE CHANNEL_TITLE, 
					V.ID, V.CHANNEL_ID, V.C_SORT, V.ACTIVE, V.COUNTER, V.TITLE, V.DESCRIPTION, V.DESCRIPTION_TYPE, V.IMAGE_ID, V.EVENT1, V.EVENT2, V.EVENT3, V.UNIQUE_TYPE, V.KEEP_IP_SEC, V.DELAY, V.DELAY_TYPE, V.TEMPLATE, V.RESULT_TEMPLATE, V.NOTIFY,
					".$DB->DateToCharFunction("V.DATE_START")."		DATE_START,
					".$DB->DateToCharFunction("V.DATE_END")."		DATE_END,
					2												MAX_PERMISSION,
					case 
						when V.ACTIVE='N' or getdate() not between V.DATE_START and V.DATE_END then 'red'
						else 'green'
					end												LAMP
				FROM
					b_vote V
				INNER JOIN b_vote_channel C ON (C.ID = V.CHANNEL_ID and C.ACTIVE = 'Y')
				WHERE
					$strSqlSearch
				and V.ACTIVE = 'Y'
				and V.DATE_START<=getdate()
				$where
				GROUP BY
					C.ID, C.TITLE, 
					V.ID, V.CHANNEL_ID, V.C_SORT, V.ACTIVE, V.COUNTER, V.TITLE, V.DESCRIPTION, V.DESCRIPTION_TYPE, V.IMAGE_ID, V.EVENT1, V.EVENT2, V.EVENT3, V.UNIQUE_TYPE, V.KEEP_IP_SEC, V.DELAY, V.DELAY_TYPE, V.TEMPLATE, V.RESULT_TEMPLATE, V.NOTIFY, 
					V.DATE_START, V.DATE_END,
					C.C_SORT, C.ID
				$strSqlOrder
				";
		}
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}
}

class CVoteQuestion extends CAllVoteQuestion
{
	function err_mess()
	{
		$module_id = "vote";
		return "<br>Module: ".$module_id."<br>Class: CVoteQuestion<br>File: ".__FILE__;
	}

	function Delete($ID)
	{
		global $DB;
		$err_mess = (CVoteQuestion::err_mess())."<br>Function: Delete<br>Line: ";
		$ID = intval($ID);
		// обнуляем вопрос
		CVoteQuestion::Reset($ID);
		// удаляем вопрос
		$res = $DB->Query("DELETE FROM b_vote_question WHERE ID='$ID'", false, $err_mess.__LINE__);
		return $res;
	}

	function Reset($ID)
	{
		global $DB;
		$err_mess = (CVoteQuestion::err_mess())."<br>Function: Reset<br>Line: ";
		$ID = intval($ID);

		// удаляем вопросы при анкетировании
		$DB->Query("DELETE FROM b_vote_event_question WHERE QUESTION_ID='$ID'", false, $err_mess.__LINE__);

		// обнуляем счетчик у вопроса
		$arFields = array("COUNTER"=>"0");
		$DB->Update("b_vote_question",$arFields,"WHERE ID='$ID'",$err_mess.__LINE__);

		// обнуляем счетчики у ответов
		$arFields = array("COUNTER"=>"0");
		$DB->Update("b_vote_answer",$arFields,"WHERE QUESTION_ID='$ID'",$err_mess.__LINE__);
	}
}

class CVoteAnswer extends CAllVoteAnswer
{
	function err_mess()
	{
		$module_id = "vote";
		return "<br>Module: ".$module_id."<br>Class: CVoteAnswer<br>File: ".__FILE__;
	}
}

class CVoteUser extends CAllVoteUser
{
	function err_mess()
	{
		$module_id = "vote";
		return "<br>Module: ".$module_id."<br>Class: CVoteUser<br>File: ".__FILE__;
	}
}

class CVoteEvent extends CAllVoteEvent
{
	function err_mess()
	{
		$module_id = "vote";
		return "<br>Module: ".$module_id."<br>Class: CVoteEvent<br>File: ".__FILE__;
	}
}
?>