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

class CEvent extends CAllEvent
{
	function Send($event, $lid, $arFields, $Duplicate = "Y", $message_id="")
	{
		global $DB;
		$flds = "";
		$arLocalFields = Array();
		if ($Duplicate!="N") $Duplicate = "Y";

		$keys = array_keys($arFields);
		for($i=0; $i<count($keys); $i++)
			$flds .= "&".CEvent::fieldencode($keys[$i])."=".CEvent::fieldencode($arFields[$keys[$i]]);

		if($flds!="")
			$flds=substr($flds, 1);

		if(is_array($lid))
			$lid = implode(",", $lid);

		$arLocalFields["EVENT_NAME"] = $event;
		$arLocalFields["LID"] = $lid;
		$arLocalFields["C_FIELDS"] = $flds;
		$arLocalFields["DUPLICATE"] = $Duplicate;

		$arInsert = $DB->PrepareInsert("b_event", $arLocalFields);

		$ID = $DB->NextID("sq_b_event");

		$message_sql = (intval($message_id)<=0) ? "null" : intval($message_id);
		$strSql =
			"INSERT INTO b_event(ID, MESSAGE_ID, ".$arInsert[0].") ".
			"VALUES(".$ID.", ".$message_sql.", ".$arInsert[1].")";

		$arBinds=Array();
		$arBinds["C_FIELDS"] = $arLocalFields["C_FIELDS"];

		if(CACHED_b_event!==false)
			@unlink($_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/managed_cache/".$DB->type."/b_event");

		$DB->QueryBind($strSql, $arBinds);

		return $ID;
	}

	function CheckEvents()
	{
		if ((defined("DisableEventsCheck") && DisableEventsCheck===true) || (defined("BX_CRONTAB_SUPPORT") && BX_CRONTAB_SUPPORT===true && BX_CRONTAB!==true)) 
			return;
		global $DB;

		if(CACHED_b_event!==false && file_exists($_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/managed_cache/".$DB->type."/b_event"))
			return "";

		$DB->StartTransaction();

		$strSql = "
			SELECT /*+RULE*/ E.ID, E.C_FIELDS, E.EVENT_NAME, E.MESSAGE_ID, E.LID,
				TO_CHAR(E.DATE_INSERT, 'DD.MM.YYYY HH24:MI:SS') as DATE_INSERT, DUPLICATE
			FROM b_event E
			WHERE E.SUCCESS_EXEC='N'
			ORDER BY E.DATE_INSERT
			FOR UPDATE NOWAIT
			";

		$fill_to_mail = COption::GetOptionString("main", "fill_to_mail", "N");

		if(!($db_result_event = $DB->Query($strSql, true)))
		{
			$DB->Commit();
			return;
		}

		$eol = CEvent::GetMailEOL();

		$cnt=0;
		while($db_result_event_array = $db_result_event->Fetch())
		{
			$ar = CEvent::ExtractMailFields($db_result_event_array["C_FIELDS"]);
			$strSites = $db_result_event_array["LID"];
			$arSites = explode(",", $strSites);
			$strSites = "";
			foreach($arSites as $strSite)
			{
				if($strSites!="")
					$strSites .= ",";
				$strSites .= "'".$DB->ForSql($strSite, 2)."'";
			}

			$strSql = "SELECT CHARSET FROM b_lang WHERE LID IN (".$strSites.") ORDER BY DEF DESC, SORT";
			$dbCharset = $DB->Query($strSql, false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
			$arCharset = $dbCharset->Fetch();
			$charset = $arCharset["CHARSET"];

			$strWhere = "";
			$MESSAGE_ID = intval($db_result_event_array["MESSAGE_ID"]);
			if ($MESSAGE_ID>0)
			{
				$strSql = "SELECT 'x' FROM b_event_message M WHERE M.ID=$MESSAGE_ID";
				$z = $DB->Query($strSql);
				if ($z->Fetch()) $strWhere = "WHERE M.ID=$MESSAGE_ID and M.ACTIVE='Y'";
			}

			$strSql =
					"SELECT M.ID, M.SUBJECT, M.MESSAGE, M.EMAIL_FROM, M.EMAIL_TO, M.BODY_TYPE, M.BCC ".
					"FROM b_event_message M ".
					($strWhere==""?
						"WHERE M.ACTIVE='Y' ".
						"	AND M.EVENT_NAME='".$DB->ForSql($db_result_event_array["EVENT_NAME"])."' ".
						"	AND EXISTS(".
						"		SELECT 'x' ".
						"		FROM b_event_message_site MS ".
						"		WHERE M.ID=MS.EVENT_MESSAGE_ID ".
						"			AND MS.SITE_ID IN (".$strSites.") ".
						"		) "
					:
						$strWhere
					);

			$db_mail_result = $DB->Query($strSql);

			$bSuccess=false;
			$bFail=false;
			$bWas=false;
			while($db_mail_result_array = $db_mail_result->Fetch())
			{
				$strSqlMLid =
					"SELECT MS.SITE_ID ".
					"FROM b_event_message_site MS ".
					"WHERE MS.EVENT_MESSAGE_ID = ".$db_mail_result_array["ID"]."  ".
					"	AND MS.SITE_ID IN (".$strSites.")";

				$dbr_mlid = $DB->Query($strSqlMLid);
				if($ar_mlid = $dbr_mlid->Fetch())
					$arFields = $ar + CEvent::GetSiteFieldsArray($ar_mlid["SITE_ID"]);
				else
					$arFields = $ar + CEvent::GetSiteFieldsArray(false);

				$email_from = CEvent::ReplaceTemplate($db_mail_result_array["EMAIL_FROM"], $arFields);
				$email_to = CEvent::ReplaceTemplate($db_mail_result_array["EMAIL_TO"], $arFields);
				$message = CEvent::ReplaceTemplate($db_mail_result_array["MESSAGE"], $arFields);
				$subject = CEvent::ReplaceTemplate($db_mail_result_array["SUBJECT"], $arFields);
				$bcc = CEvent::ReplaceTemplate($db_mail_result_array["BCC"], $arFields);

				$email_from = Trim($email_from, "\r\n");
				$email_to = Trim($email_to, "\r\n");
				$subject = Trim($subject, "\r\n");
				$bcc = Trim($bcc, "\r\n");

				if(COption::GetOptionString("main", "convert_mail_header", "Y")=="Y")
				{
					$email_from = CEvent::EncodeMimeString($email_from, $charset);
					$email_to = CEvent::EncodeMimeString($email_to, $charset);
					$subject = CEvent::EncodeMimeString($subject, $charset);
				}

				//если есть желающие получать всю почту, добавим их...
				if ($db_result_event_array["DUPLICATE"]=="Y")
				{
					$all_bcc = COption::GetOptionString("main", "all_bcc", "");
					$bcc .= (strlen($all_bcc)>0?(strlen($bcc)>0?",":"").$all_bcc:"");
				}

				if(COption::GetOptionString("main", "send_mid", "N")=="Y")
					$message .= ($db_mail_result_array["BODY_TYPE"]=="html"?"<br><br>":"\n\n")."MID #".$db_result_event_array["ID"].".".$db_mail_result_array["ID"]." (".$db_result_event_array["DATE_INSERT"].")\n";

				$message = str_replace("\r\n", "\n", $message);//удалить эту строку при возникновении проблем с новыми строками в письмах

				if (COption::GetOptionString("main", "CONVERT_UNIX_NEWLINE_2_WINDOWS", "N")=="Y")
					$message = str_replace("\n", "\r\n", $message);

				$header = "";
				if(COption::GetOptionString("main", "fill_to_mail", "N")=="Y")
					$header = "To: $email_to".$eol;

				$header=
					"From: $email_from".$eol.
					$header.
					"Reply-To: $email_from".$eol.
					"X-Priority: 3 (Normal)".$eol.
					"X-MID: ".$db_result_event_array["ID"].".".$db_mail_result_array["ID"]." (".$db_result_event_array["DATE_INSERT"].")".$eol.
					"X-EVENT_NAME: ".$db_result_event_array["EVENT_NAME"].$eol.
					(strpos($bcc, "@")!==false?"BCC:$bcc".$eol:"").
					($db_mail_result_array["BODY_TYPE"]=="html"
					?
						"Content-Type: text/html; charset=".$charset.$eol
					:
						"Content-Type: text/plain; charset=".$charset.$eol
					).
					"Content-Transfer-Encoding: 8bit";
/*
echo "header = ".$header."\n";
echo "email_to = ".$email_to."\n";
echo "subject = ".$subject."\n";
echo "message = ".$message."\n";
*/

				if(defined("ONLY_EMAIL") && $email_to!=ONLY_EMAIL)
					$bSuccess=true;
				elseif(@mail($email_to, $subject, $message, $header))
					$bSuccess=true;
				else
					$bFail=true;

				$bWas=true;
			}

			/*
			'0' - нет шаблонов (не нужно было ничего отправлять)
			'Y' - все отправлены
			'F' - все не смогли быть отправлены
			'P' - частично отправлены
			*/
			$DB->Query("UPDATE b_event SET DATE_EXEC = SYSDATE, SUCCESS_EXEC = '".($bWas?($bSuccess && $bFail?'P':($bFail?'F':'Y')):'0')."' WHERE ID = ".$db_result_event_array["ID"]);
			$cnt++;
			if($cnt>5)break;
		}
		$DB->Commit();
		if($cnt===0 && CACHED_b_event!==false)
			@fclose(@fopen($_SERVER["DOCUMENT_ROOT"]."/".BX_ROOT."/managed_cache/".$DB->type."/b_event","w"));
	}

	function CleanUpAgent()
	{
		global $DB;
		$DB->Query("DELETE FROM b_event WHERE DATE_EXEC <= SYSDATE-14");
		return "CEvent::CleanUpAgent();";
	}
}

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
		$bIsLang = false;
		if (is_array($arFilter))
		{
			$filter_keys = array_keys($arFilter);
			for ($i=0; $i<count($filter_keys); $i++)
			{
				$val = $arFilter[$filter_keys[$i]];
				if (strlen($val)<=0 || $val."!"=="NOT_REF!") continue;
				switch(strtoupper($filter_keys[$i]))
				{
				case "ID":
					$arSqlSearch[] = GetFilterQuery("M.ID",$val,"N");
					break;
				case "TYPE":
					$arSqlSearch[] = GetFilterQuery("M.EVENT_NAME, T.NAME",$val);
					break;
				case "EVENT_NAME":
				case "TYPE_ID":
					$arSqlSearch[] = GetFilterQuery("M.EVENT_NAME",$val,"N");
					break;
				case "TIMESTAMP_1":
					$arSqlSearch[] = "M.TIMESTAMP_X>=TO_DATE('".FmtDate($val, "D.M.Y")." 00:00:00','dd.mm.yyyy hh24:mi:ss')";
					break;
				case "TIMESTAMP_2":
					$arSqlSearch[] = "M.TIMESTAMP_X<=TO_DATE('".FmtDate($val, "D.M.Y")." 23:59:59','dd.mm.yyyy hh24:mi:ss')";
					break;
				case "LID":
				case "LANG":
				case "SITE_ID":
					if (is_array($val)) $val = implode(" | ",$val);
					$arSqlSearch[] = GetFilterQuery("MS.SITE_ID",$val,"N");
					$bIsLang = true;
					break;
				case "ACTIVE":
					$arSqlSearch[] = ($val=="Y") ? "M.ACTIVE = 'Y'" : "M.ACTIVE = 'N'";
					break;
				case "FROM":
					$arSqlSearch[] = GetFilterQuery("M.EMAIL_FROM", $val);
					break;
				case "TO":
					$arSqlSearch[] = GetFilterQuery("M.EMAIL_TO", $val);
					break;
				case "BCC":
					$arSqlSearch[] = GetFilterQuery("M.BCC", $val);
					break;
				case "SUBJECT":
					$arSqlSearch[] = GetFilterQuery("M.SUBJECT", $val);
					break;
				case "BODY_TYPE":
					$arSqlSearch[] = ($val=="text") ? "M.BODY_TYPE = 'text'" : "M.BODY_TYPE = 'html'";
					break;
				case "BODY":
					$arSqlSearch[] = GetFilterQuery("M.MESSAGE", $val);
					break;
				}
			}
		}

		if ($by == "id")							$strSqlOrder = " ORDER BY M.ID ";
		elseif ($by == "active")					$strSqlOrder = " ORDER BY M.ACTIVE ";
		elseif ($by == "to")						$strSqlOrder = " ORDER BY M.TO ";
		elseif ($by == "bcc")						$strSqlOrder = " ORDER BY M.BCC ";
		elseif ($by == "body_type")					$strSqlOrder = " ORDER BY M.BODY_TYPE ";
		elseif ($by == "event_name")				$strSqlOrder = " ORDER BY M.EVENT_NAME ";
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
		$strSql =
			"SELECT M.ID, M.EVENT_NAME, M.LID, M.LID as SITE_ID, M.ACTIVE, M.EMAIL_FROM, M.EMAIL_TO, M.SUBJECT, M.MESSAGE, M.BODY_TYPE, M.BCC, ".
			"	".$DB->DateToCharFunction("M.TIMESTAMP_X")." TIMESTAMP_X, ".
			"	decode(nvl(T.ID,0), ".
			"		0, M.EVENT_NAME,  ".
			"		'[ '||T.EVENT_NAME||' ] '||nvl(T.NAME,'') ".
			"	)	EVENT_TYPE ".
			"FROM b_event_message M ".
				($bIsLang?" LEFT JOIN b_event_message_site MS ON (M.ID = MS.EVENT_MESSAGE_ID)":"")." ".
			"       LEFT JOIN b_event_type T ON (T.EVENT_NAME = M.EVENT_NAME and T.LID = '".LANG_ADMIN."') ".
			"WHERE ".
			$strSqlSearch.
			$strSqlOrder;

		//echo $strSql;
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		$res->is_filtered = (IsFiltered($strSqlSearch));
		return $res;
	}
}

class CEventType extends CAllEventType
{
	function Add($arFields)
	{
		global $DB;

		if(!is_set($arFields, "LID") && is_set($arFields, "SITE_ID"))
			$arFields["LID"] = $arFields["SITE_ID"];

		$arInsert = $DB->PrepareInsert("b_event_type", $arFields);

		$ID = $DB->NextID("sq_b_event_type");
		$strSql =
			"INSERT INTO b_event_type(ID, ".$arInsert[0].") ".
			"VALUES(".$ID.", ".$arInsert[1].")";

		$DB->Query($strSql);
		return $ID;
	}
}
?>
