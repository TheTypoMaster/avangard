<?
/*
##############################################
# Bitrix: SiteManager                        #
# Copyright (c) 2002-2005 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
*/
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/event.php");

///////////////////////////////////////////////////////////////////
// Класс почтовых событий
///////////////////////////////////////////////////////////////////

class CEvent extends CAllEvent
{
	function err_mess()
	{
		return "<br>Class: CEvent<br>File: ".__FILE__;
	}

	function Send($event, $lid, $arFields, $Duplicate = "Y", $message_id="")
	{
		$err_mess = (CEvent::err_mess())."<br>Function: Send<br>Line: ";
		global $DB;
		$sqlFields = "";
		if (is_array($arFields) && count($arFields)>0)
		{
			$keys = array_keys($arFields);
			for($i=0; $i<count($keys); $i++)
			{
				$sqlFields .= "&".CEvent::fieldencode($keys[$i])."=".CEvent::fieldencode($arFields[$keys[$i]]);
			}
			if (strlen($sqlFields)>0) $sqlFields = substr($sqlFields, 1);
		}
		if (is_array($lid)) $lid = implode(",", $lid);

		$arFields = array(
			"EVENT_NAME"	=> "'".$DB->ForSQL($event)."'",
			"LID"			=> "'".$DB->ForSql($lid, 201)."'",
			"MESSAGE_ID"	=> (intval($message_id)<=0) ? "null" : intval($message_id),
			"C_FIELDS"		=> "'".$DB->ForSQL($sqlFields)."'",
			"DATE_INSERT"	=> "getdate()",
			"DUPLICATE"		=> ($Duplicate!="N") ? "'Y'" : "'N'",
			);

		if(CACHED_b_event!==false)
			@unlink($_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/managed_cache/".$DB->type."/b_event");

		return $DB->Insert("b_event", $arFields, $err_mess.__LINE__);
	}

	function CheckEvents()
	{
		if((defined("DisableEventsCheck") && DisableEventsCheck===true) || (defined("BX_CRONTAB_SUPPORT") && BX_CRONTAB_SUPPORT===true && BX_CRONTAB!==true)) 
			return;
		$err_mess = (CEvent::err_mess())."<br>Function: CheckEvents<br>Line: ";
		global $DB;

		if(CACHED_b_event!==false && file_exists($_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/managed_cache/".$DB->type."/b_event"))
			return "";

		$arCharset = array();
		$DB->StartTransaction();
		$DB->Query("SET LOCK_TIMEOUT 0", false, $err_mess.__LINE__);
		$strSql = "
			SELECT TOP 5
				ID,
				C_FIELDS,
				EVENT_NAME,
				MESSAGE_ID,
				LID,
				".$DB->DateToCharFunction("DATE_INSERT")." as DATE_INSERT_S,
				DUPLICATE
			FROM
				b_event
			WITH (TABLOCKX)
			WHERE
				SUCCESS_EXEC = 'N'
			ORDER BY
				DATE_INSERT
			";

		//echo "<pre>".$strSql."</pre>";
		$rsMails = $DB->Query($strSql, true);
		if(!$rsMails)
		{
			$DB->Commit();
			return;
		}

		$eol = CEvent::GetMailEOL();

		$cnt=0;
		while($arMail = $rsMails->Fetch())
		{
			$bWas = false;

			// сайты письма
			$site_id = "";
			$arSites = explode(",", $arMail["LID"]);
			$strSites = "";
			foreach ($arSites as $site_id)
			{
				$site_id = trim($site_id);
				if (strlen($site_id)>0)
				{
					if ($strSites!="") $strSites .= ",";
					$strSites .= "'".$DB->ForSql($site_id, 2)."'";
				}
			}

			// если сайт определен
			if (strlen($site_id)>0)
			{
				// кодировка письма
				if (!in_array($site_id, array_keys($arCharset)))
				{
					$strSql = "
						SELECT
							CHARSET
						FROM
							b_lang
						WHERE
							LID = '".$DB->ForSql($site_id, 2)."'
						";
					$rsSites = $DB->Query($strSql, false, $err_mess.__LINE__);
					$arSite = $rsSites->Fetch();
					$arCharset[$site_id] = $arSite["CHARSET"];
				}
				$charset = $arCharset[$site_id];

				// шаблоны письма
				$MESSAGE_ID = intval($arMail["MESSAGE_ID"]);
				if ($MESSAGE_ID>0)
				{
					$strSql = "
						SELECT
							M.ID,
							M.SUBJECT,
							M.MESSAGE,
							M.EMAIL_FROM,
							M.EMAIL_TO,
							M.BODY_TYPE,
							M.BCC
						FROM
							b_event_message M
						WHERE
							M.ID = $MESSAGE_ID
						and M.ACTIVE='Y'
						";
				}
				else
				{
					$strSql = "
						SELECT
							M.ID,
							M.SUBJECT,
							M.MESSAGE,
							M.EMAIL_FROM,
							M.EMAIL_TO,
							M.BODY_TYPE,
							M.BCC
						FROM
							b_event_message M
						WHERE
								M.ACTIVE = 'Y'
							and M.EVENT_NAME = '".$DB->ForSql($arMail["EVENT_NAME"])."'
							and exists(
									SELECT
										'x'
									FROM
										b_event_message_site MS
									WHERE
										M.ID = MS.EVENT_MESSAGE_ID
									and MS.SITE_ID in (".$strSites.")
									)
						";
				}

				// поля письма
				$arFields = CEvent::ExtractMailFields($arMail["C_FIELDS"]);

				$bSuccess = false;
				$bFail = false;

				$rsTemplates = $DB->Query($strSql, false, $err_mess.__LINE__);
				while ($arTemplate = $rsTemplates->Fetch())
				{
					// добавим из настроек сайта поля #SITE_NAME#, #SERVER_NAME#, #DEFAULT_EMAIL_FROM#
					$strSql = "
						SELECT
							MS.SITE_ID
						FROM
							b_event_message_site MS
						WHERE
								MS.EVENT_MESSAGE_ID = ".$arTemplate["ID"]."
							and MS.SITE_ID IN (".$strSites.")
						";
					$rsSites = $DB->Query($strSql, false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
					if($arSite = $rsSites->Fetch()) $arFields += CEvent::GetSiteFieldsArray($arSite["SITE_ID"]);
					else $arFields += CEvent::GetSiteFieldsArray(false);

					$email_from	= CEvent::ReplaceTemplate($arTemplate["EMAIL_FROM"], $arFields);
					$email_to	= CEvent::ReplaceTemplate($arTemplate["EMAIL_TO"], $arFields);
					$message	= CEvent::ReplaceTemplate($arTemplate["MESSAGE"], $arFields);
					$subject	= CEvent::ReplaceTemplate($arTemplate["SUBJECT"], $arFields);
					$bcc		= CEvent::ReplaceTemplate($arTemplate["BCC"], $arFields);

					$email_from	= trim($email_from, "\r\n");
					$email_to	= trim($email_to, "\r\n");
					$subject	= trim($subject, "\r\n");
					$bcc		= trim($bcc, "\r\n");

					if (COption::GetOptionString("main", "convert_mail_header", "Y")=="Y")
					{
						$email_from	= CEvent::EncodeMimeString($email_from, $charset);
						$email_to	= CEvent::EncodeMimeString($email_to, $charset);
						$subject	= CEvent::EncodeMimeString($subject, $charset);
					}

					//если есть желающие получать всю почту, добавим их...
					if ($arMail["DUPLICATE"]=="Y")
					{
						$all_bcc = COption::GetOptionString("main", "all_bcc", "");
						$bcc .= (strlen($all_bcc)>0 ? (strlen($bcc)>0 ? "," : "").$all_bcc : "");
					}

					if(COption::GetOptionString("main", "send_mid", "N")=="Y")
					{
						$message .= ($arTemplate["BODY_TYPE"]=="html"?"<br><br>":"\n\n")."MID #".$arMail["ID"].".".$arTemplate["ID"]." (".$arMail["DATE_INSERT"].")\n";
					}

					$message = str_replace("\r\n", "\n", $message);
					if (COption::GetOptionString("main", "CONVERT_UNIX_NEWLINE_2_WINDOWS", "N")=="Y")
						$message = str_replace("\n", "\r\n", $message);

					$header = "";
					if (COption::GetOptionString("main", "fill_to_mail", "N")=="Y")
						$header = "To: $email_to".$eol;

					$header =
						"From: $email_from".$eol.
						$header.
						"Reply-To: $email_from".$eol.
						"X-Priority: 3 (Normal)".$eol.
						"X-MID: ".$arMail["ID"].".".$arTemplate["ID"]." (".$arMail["DATE_INSERT_S"].")".$eol.
						"X-EVENT_NAME: ".$arMail["EVENT_NAME"].$eol;

					if (strpos($bcc, "@")!==false)
					{
						$header .= "BCC:$bcc".$eol;
					}

					if ($arTemplate["BODY_TYPE"]=="html")
					{
						$header .= "Content-Type: text/html; charset=".$charset.$eol;
					}
					else
					{
						$header .= "Content-Type: text/plain; charset=".$charset.$eol;
					}

					$header .= "Content-Transfer-Encoding: 8bit";

					/*
					echo "header = ".$header."\n";
					echo "email_to = ".$email_to."\n";
					echo "subject = ".$subject."\n";
					echo "message = ".$message."\n";
					*/

					if(defined("ONLY_EMAIL") && $email_to!=ONLY_EMAIL)
						$bSuccess = true;
					elseif(@mail($email_to, $subject, $message, $header))
						$bSuccess = true;
					else
						$bFail = true;

					$bWas = true;
				}
			}

			$flag = "0"; // нет шаблонов
			if ($bWas)
			{
				if ($bSuccess && $bFail)		$flag = "P";	// частично отправлены
				elseif ($bSuccess && !$bFail)	$flag = "Y";	// все отправлены
				elseif (!$bSuccess && $bFail)	$flag = "F";	// ни по одному из шаблонов не было успешной отправки письма
			}

			// обновим дату отправки и флаг состояния
			$strSql = "
				UPDATE b_event SET
					DATE_EXEC = getdate(),
					SUCCESS_EXEC = '$flag'
				WHERE
					ID = ".$arMail["ID"];
			$DB->Query($strSql, false, $err_mess.__LINE__);
			$cnt++;
			if($cnt>5)break;
		}
		$DB->Query("SET LOCK_TIMEOUT -1", false, $err_mess.__LINE__);
		$DB->Commit();
		if($cnt===0 && CACHED_b_event!==false)
			@fclose(@fopen($_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/managed_cache/".$DB->type."/b_event","w"));
	}

	function CleanUpAgent()
	{
		$err_mess = (CEvent::err_mess())."<br>Function: CleanUpAgent<br>Line: ";
		global $DB;
		$strSql = "DELETE FROM b_event WHERE DATE_EXEC <= dateadd(day, -7, getdate())";
		$DB->Query($strSql, true);
		return "CEvent::CleanUpAgent();";
	}
}

///////////////////////////////////////////////////////////////////
// Класс почтовых шаблонов
///////////////////////////////////////////////////////////////////

class CEventMessage extends CAllEventMessage
{
	function err_mess()
	{
		return "<br>Class: CEventMessage<br>File: ".__FILE__;
	}

	function GetList(&$by, &$order, $arFilter=Array())
	{
		$err_mess = (CEventMessage::err_mess())."<br>Function: GetList<br>Line: ";
		global $DB, $USER;
		$arSqlSearch = Array();
		$strSqlSearch = "";
		if (is_array($arFilter))
		{
			$filter_keys = array_keys($arFilter);
			for ($i=0; $i<count($filter_keys); $i++)
			{
				$key = $filter_keys[$i];
				$val = $arFilter[$filter_keys[$i]];
				if (strlen($val)<=0 || "$val"=="NOT_REF") continue;
				if (is_array($val) && count($val)<=0) continue;
				$match_value_set = (in_array($key."_EXACT_MATCH", $filter_keys)) ? true : false;
				$key = strtoupper($key);
				switch($key)
				{
					case "ID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("M.ID", $val, $match);
						break;
					case "TYPE":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("M.EVENT_NAME, T.NAME", $val, $match);
						break;
					case "EVENT_NAME":
					case "TYPE_ID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("M.EVENT_NAME", $val, $match);
						break;
					case "TIMESTAMP_1":
						$arSqlSearch[] = "M.TIMESTAMP_X >= ".$DB->CharToDateFunction($val, "SHORT");
						break;
					case "TIMESTAMP_2":
						$arSqlSearch[] = "M.TIMESTAMP_X < dateadd(day, 1, ".$DB->CharToDateFunction($val, "SHORT").")";
						break;
					case "LID":
					case "LANG":
					case "SITE_ID":
						if (is_array($val)) $val = implode(" | ",$val);
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("MS.SITE_ID", $val, $match);
						$join_site = "
							LEFT JOIN b_event_message_site MS ON (M.ID = MS.EVENT_MESSAGE_ID)
							";
						break;
					case "ACTIVE":
						$arSqlSearch[] = ($val=="Y") ? "M.ACTIVE = 'Y'" : "M.ACTIVE = 'N'";
						break;
					case "FROM":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("M.EMAIL_FROM", $val, $match);
						break;
					case "TO":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("M.EMAIL_TO", $val, $match);
						break;
					case "BCC":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("M.BCC", $val, $match);
						break;
					case "SUBJECT":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("M.SUBJECT", $val, $match);
						break;
					case "BODY_TYPE":
						$arSqlSearch[] = ($val=="text") ? "M.BODY_TYPE = 'text'" : "M.BODY_TYPE = 'html'";
						break;
					case "BODY":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("M.MESSAGE", $val, $match);
						break;
				}
			}
		}
		if ($by == "id")							$strSqlOrder = " ORDER BY M.ID ";
		elseif ($by == "active")					$strSqlOrder = " ORDER BY M.ACTIVE ";
		elseif ($by == "event_name")				$strSqlOrder = " ORDER BY M.EVENT_NAME ";
		elseif ($by == "to")						$strSqlOrder = " ORDER BY M.TO ";
		elseif ($by == "bcc")						$strSqlOrder = " ORDER BY M.BCC ";
		elseif ($by == "body_type")					$strSqlOrder = " ORDER BY M.BODY_TYPE ";
		elseif ($by == "lid" || $by == "site_id")	$strSqlOrder = " ORDER BY M.LID ";
		elseif ($by == "subject")					$strSqlOrder = " ORDER BY M.SUBJECT ";
		else
		{
			$strSqlOrder = " ORDER BY M.ID ";
			$by = "id";
		}
		if ($order!="asc")
		{
			$strSqlOrder .= " desc ";
			$order = "desc";
		}
		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		$strSql = "
			SELECT
				M.ID,
				M.EVENT_NAME,
				M.ACTIVE,
				M.LID,
				M.LID as SITE_ID,
				M.EMAIL_FROM,
				M.EMAIL_TO,
				M.SUBJECT,
				M.MESSAGE,
				M.BODY_TYPE,
				M.BCC,
				".$DB->DateToCharFunction("M.TIMESTAMP_X")." as TIMESTAMP_X,
				CASE
					when T.ID is null then M.EVENT_NAME
					else '[ ' + T.EVENT_NAME + ' ] ' + isnull(T.NAME,'')
				END as EVENT_TYPE
			FROM
				b_event_message M
			LEFT JOIN b_event_type T ON (T.EVENT_NAME = M.EVENT_NAME and T.LID = '".LANGUAGE_ID."')
			$join_site
			WHERE
				$strSqlSearch
				$strSqlOrder
			";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		$res->is_filtered = (IsFiltered($strSqlSearch));
		return $res;
	}
}

class CEventType extends CAllEventType
{
	function err_mess()
	{
		return "<br>Class: CEventType<br>File: ".__FILE__;
	}

	function Add($arFields)
	{
		$err_mess = (CEventType::err_mess())."<br>Function: Add<br>Line: ";
		global $DB;

		if (!is_set($arFields, "LID") && is_set($arFields, "SITE_ID"))
			$arFields["LID"] = $arFields["SITE_ID"];

		$arInsert = $DB->PrepareInsert("b_event_type", $arFields);

		$strSql = "
			INSERT INTO b_event_type (
				".$arInsert[0]."
			) VALUES (
				".$arInsert[1]."
			)
			";
		$DB->Query($strSql, false, $err_mess.__LINE__);
		return $DB->LastID();
	}
}
?>
