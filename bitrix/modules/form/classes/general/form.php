<?
/***************************************
			Веб-форма
***************************************/

class CAllForm extends CForm_old
{
	function err_mess()
	{
		$module_id = "form";
		@include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/install/version.php");
		return "<br>Module: ".$module_id." (".constant(strtoupper($module_id)."_VERSION").")<br>Class: CAllForm<br>File: ".__FILE__;
	}

	// true - если текущий пользователь имеет полный доступ к модулю
	// false - в противном случае
	function IsAdmin()
	{
		global $USER, $APPLICATION;
		if (!is_object($USER)) $USER = new CUser;
		if ($USER->IsAdmin()) return true;
		$FORM_RIGHT = $APPLICATION->GetGroupRight("form");
		if ($FORM_RIGHT>="W") return true;
	}

	// Функция возвращает массивы, содержащие данные по вопросам и полям формы, а также ответы и их значения.
	function GetResultAnswerArray($WEB_FORM_ID, &$arrColumns, &$arrAnswers, &$arrAnswersSID, $arFilter=Array())
	{
		$err_mess = (CAllForm::err_mess())."<br>Function: GetResultAnswerArray<br>Line: ";
		global $DB, $strError;
		$WEB_FORM_ID = intval($WEB_FORM_ID);
		$arSqlSearch = Array();
		$strSqlSearch = "";
		if (is_array($arFilter))
		{
			if (strlen($arFilter["FIELD_SID"])>0) $arFilter["FIELD_VARNAME"] = $arFilter["FIELD_SID"];
			elseif (strlen($arFilter["FIELD_VARNAME"])>0) $arFilter["FIELD_SID"] = $arFilter["FIELD_VARNAME"];

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
					case "FIELD_ID":
					case "RESULT_ID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("RA.".$key, $val, $match);
						break;
					case "FIELD_SID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("F.SID", $val, $match);
						break;
					case "IN_RESULTS_TABLE":
					case "IN_EXCEL_TABLE":
						$arSqlSearch[] = ($val=="Y") ? "F.".$key."='Y'" : "F.".$key."='N'";
						break;
				}
			}
		}
		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		$strSql = "
			SELECT
				RA.RESULT_ID, RA.FIELD_ID, F.SID, F.SID as VARNAME, F.TITLE, F.TITLE_TYPE, F.FILTER_TITLE, F.RESULTS_TABLE_TITLE,
				RA.ANSWER_ID, RA.ANSWER_TEXT, A.MESSAGE, RA.ANSWER_VALUE, A.VALUE, RA.USER_TEXT,
				".$DB->DateToCharFunction("RA.USER_DATE")."	USER_DATE,
				RA.USER_FILE_ID, RA.USER_FILE_NAME, RA.USER_FILE_IS_IMAGE, RA.USER_FILE_HASH, RA.USER_FILE_SUFFIX, RA.USER_FILE_SIZE,
				A.FIELD_TYPE, A.FIELD_WIDTH, A.FIELD_HEIGHT, A.FIELD_PARAM
			FROM
				b_form_result_answer RA
			INNER JOIN b_form_field F ON (F.ID = RA.FIELD_ID and F.ACTIVE='Y')
			LEFT JOIN b_form_answer A ON (A.ID = RA.ANSWER_ID)
			WHERE
			$strSqlSearch
			and RA.FORM_ID = $WEB_FORM_ID
			ORDER BY RA.RESULT_ID, F.C_SORT, A.C_SORT
			";
		//echo "<pre>".$strSql."</pre>";
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		while ($zr = $z->Fetch())
		{
			$arrAnswers[$zr["RESULT_ID"]][$zr["FIELD_ID"]][intval($zr["ANSWER_ID"])]=$zr;
			$arrAnswersSID[$zr["RESULT_ID"]][$zr["SID"]][]=$zr;
		}
		$q = CFormField::GetList($WEB_FORM_ID, "", $v1, $v2,
			array(
				"ID"				=> $arFilter["FIELD_ID"],
				"VARNAME"			=> $arFilter["FIELD_SID"],
				"SID"				=> $arFilter["FIELD_SID"],
				"IN_RESULTS_TABLE"	=> $arFilter["IN_RESULTS_TABLE"],
				"IN_EXCEL_TABLE"	=> $arFilter["IN_EXCEL_TABLE"],
				"ACTIVE"			=> "Y"),
			$is_filtered
			);
		while ($qr = $q->Fetch()) $arrColumns[$qr["ID"]] = $qr;

		/*
		echo "<pre>";
		echo "arrColumns:";
		print_r($arrColumns);
		echo "arrAnswers:";
		print_r($arrAnswers);
		echo "arrAnswersSID:";
		print_r($arrAnswersSID);
		echo "</pre>";
		*/
	}

	// получаем массив почтовых шаблонов связанных с формой
	function GetMailTemplateArray($FORM_ID)
	{
		$err_mess = (CAllForm::err_mess())."<br>Function: GetMailTemplateArray<br>Line: ";
		global $DB, $USER, $strError;
		$FORM_ID = intval($FORM_ID);
		if ($FORM_ID<=0) return false;
		$arrRes = array();
		$strSql = "
			SELECT
				FM.MAIL_TEMPLATE_ID
			FROM
				b_form_2_mail_template FM
			WHERE
				FM.FORM_ID = $FORM_ID
			";
		//echo "<pre>".$strSql."</pre>";
		$rs = $DB->Query($strSql, false, $err_mess.__LINE__);
		while ($ar = $rs->Fetch()) $arrRes[] = $ar["MAIL_TEMPLATE_ID"];
		return $arrRes;
	}

	// получаем массив сайтов связанных с формой
	function GetSiteArray($FORM_ID)
	{
		$err_mess = (CAllForm::err_mess())."<br>Function: GetSiteArray<br>Line: ";
		global $DB, $USER, $strError;
		$FORM_ID = intval($FORM_ID);
		if ($FORM_ID<=0) return false;
		$arrRes = array();
		$strSql = "
			SELECT
				FS.SITE_ID
			FROM
				b_form_2_site FS
			WHERE
				FS.FORM_ID = $FORM_ID
			";
		//echo "<pre>".$strSql."</pre>";
		$rs = $DB->Query($strSql, false, $err_mess.__LINE__);
		while ($ar = $rs->Fetch()) $arrRes[] = $ar["SITE_ID"];
		return $arrRes;
	}

	// функция вызывает заданный обработчик до смены статуса
	function ExecHandlerBeforeChangeStatus($RESULT_ID, $ACTION, $NEW_STATUS_ID=0)
	{
		global $arrPREV_RESULT_STATUS, $DB, $MESS, $APPLICATION, $USER, $HTTP_POST_VARS, $HTTP_GET_VARS, $strError;
		$err_mess = (CAllForm::err_mess())."<br>Function: ExecHandlerBeforeChangeStatus<br>Line: ";
		$RESULT_ID = intval($RESULT_ID);
		if ($RESULT_ID<=0) return;
		else
		{
			$strSql = "
				SELECT
					R.*,
					".$DB->DateToCharFunction("R.DATE_CREATE")."	DATE_CREATE,
					".$DB->DateToCharFunction("R.TIMESTAMP_X")."	TIMESTAMP_X,
					S.TITLE			STATUS_TITLE,
					S.DESCRIPTION	STATUS_DESCRIPTION,
					S.DEFAULT_VALUE	STATUS_DEFAULT_VALUE,
					S.CSS			STATUS_CSS,
					S.HANDLER_IN	STATUS_HANDLER_IN,
					S.HANDLER_OUT	STATUS_HANDLER_OUT
				FROM
					b_form_result R
				INNER JOIN b_form_status S ON (R.STATUS_ID=S.ID)
				WHERE
					R.ID = $RESULT_ID
				";
			//echo "<pre>".$strSql."</pre>";
			$rsResult = $DB->Query($strSql, false, $err_mess.__LINE__);
			if ($arResult = $rsResult->Fetch())
			{
				$arrPREV_RESULT_STATUS[$RESULT_ID] = $arResult["STATUS_ID"];
				$handler = trim($arResult["STATUS_HANDLER_OUT"]);
				if (strlen($handler)>0)
				{
					$fname = $handler;
					$fname = str_replace("\\", "/", $fname);
					$fname = str_replace("//", "/", $fname);
					$fname = TrimEx($fname,"/");
					$CURRENT_STATUS_ID = $arResult["STATUS_ID"];
					$fname = $_SERVER["DOCUMENT_ROOT"]."/".$fname;
					include($fname);
				}
			}
		}
	}

	// функция вызывает заданный обработчик после смены статуса
	function ExecHandlerAfterChangeStatus($RESULT_ID, $ACTION)
	{
		global $arrCURRENT_RESULT_STATUS, $arrPREV_RESULT_STATUS, $DB, $MESS, $APPLICATION, $USER, $HTTP_POST_VARS, $HTTP_GET_VARS, $strError;
		$err_mess = (CAllForm::err_mess())."<br>Function: ExecHandlerAfterChangeStatus<br>Line: ";
		$RESULT_ID = intval($RESULT_ID);
		if ($RESULT_ID<=0) return;
		else
		{
			$strSql = "
				SELECT
					R.*,
					".$DB->DateToCharFunction("R.DATE_CREATE")."	DATE_CREATE,
					".$DB->DateToCharFunction("R.TIMESTAMP_X")."	TIMESTAMP_X,
					S.TITLE			STATUS_TITLE,
					S.DESCRIPTION	STATUS_DESCRIPTION,
					S.DEFAULT_VALUE	STATUS_DEFAULT_VALUE,
					S.CSS			STATUS_CSS,
					S.HANDLER_IN	STATUS_HANDLER_IN,
					S.HANDLER_OUT	STATUS_HANDLER_OUT
				FROM
					b_form_result R
				INNER JOIN b_form_status S ON (R.STATUS_ID=S.ID)
				WHERE
					R.ID = $RESULT_ID
				";
			//echo "<pre>".$strSql."</pre>";
			$rsResult = $DB->Query($strSql, false, $err_mess.__LINE__);
			if ($arResult = $rsResult->Fetch())
			{
				$arrCURRENT_RESULT_STATUS[$RESULT_ID] = $arResult["STATUS_ID"];
				$handler = trim($arResult["STATUS_HANDLER_IN"]);
				if (strlen($handler)>0)
				{
					$fname = $handler;
					$fname = str_replace("\\", "/", $fname);
					$fname = str_replace("//", "/", $fname);
					$fname = TrimEx($fname,"/");
					$fname = $_SERVER["DOCUMENT_ROOT"]."/".$fname;
					$CURRENT_STATUS_ID = $arResult["STATUS_ID"];
					$PREV_STATUS_ID = $arrPREV_RESULT_STATUS[$RESULT_ID];
					include($fname);
				}
			}
		}
	}

	// права на веб-форму
	function GetPermissionList($get_default="Y")
	{
		global $MESS, $strError;
		$ref_id = array(1,10,15,20,25,30);
		$ref = array(
			"[1] ".GetMessage("FORM_DENIED"),
			"[10] ".GetMessage("FORM_FILL"),
			"[15] ".GetMessage("FORM_FILL_EDIT"),
			"[20] ".GetMessage("FORM_VIEW"),
			"[25] ".GetMessage("FORM_VIEW_PARAMS"),
			"[30] ".GetMessage("FORM_WRITE")
			);
		$ref_id_def = array();
		$ref_def = array();
		if ($get_default=="Y")
		{
			$default_perm = COption::GetOptionString("form", "FORM_DEFAULT_PERMISSION");
			$idx = array_search($default_perm, $ref_id);
			$ref_id_def[] = 0;
			$ref_def[] = GetMessage("FORM_DEFAULT")." - ".$ref[$idx];
		}
		$arr = array(
			"reference_id" => array_merge($ref_id_def,$ref_id),
			"reference" => array_merge($ref_def, $ref));
		return $arr;
	}

	function GetPermission($form_id, $arGroups=false, $get_from_database="")
	{
		global $DB, $USER, $strError;
		$err_mess = (CAllForm::err_mess())."<br>Function: GetPermission<br>Line: ";
		$default_right = COption::GetOptionString("form","FORM_DEFAULT_PERMISSION");
		if ($arGroups===false)
		{
			$arGroups = $USER->GetUserGroupArray();
			if (!is_array($arGroups)) $arGroups[] = 2;
		}
		if (CForm::IsAdmin() && $get_from_database!="Y") $right = 30;
		else
		{
			if (is_array($arGroups) && count($arGroups)>0)
			{
				$arr = array();
				$groups = implode(",",$arGroups);
				$form_id = intval($form_id);
				$strSql = "
					SELECT
						FG.PERMISSION,
						FG.GROUP_ID
					FROM
						b_form_2_group FG
					WHERE
						FG.FORM_ID = $form_id
					and FG.GROUP_ID in ($groups)
					";
				//echo "<pre>".$strSql."</pre>";
				$t = $DB->Query($strSql, false, $err_mess.__LINE__);
				while ($tr = $t->Fetch()) $arr[$tr["GROUP_ID"]] = $tr["PERMISSION"];
				if ($get_from_database!="Y")
				{
					$arr_keys = (is_array($arr)) ? array_keys($arr) : array();
					if (is_array($arGroups))
					{
						reset($arGroups);
						foreach ($arGroups as $gid) if (!in_array($gid, $arr_keys)) $arr[$gid] = $default_right;
					}
				}
				$arr_values = is_array($arr) ? array_values($arr) : array(0);
				$right = count($arr_values)>0 ? max($arr_values) : 0;
			}
		}
		$right = intval($right);
		if ($right<=0 && $get_from_database!="Y") $right = $default_right;
		//echo "right = ".$right;
		return $right;
	}

	function GetTemplateList($type="SHOW", $path="xxx", $WEB_FORM_ID=0)
	{
		$err_mess = (CAllForm::err_mess())."<br>Function: GetTemplateList<br>Line: ";
		global $DB, $strError;
		$WEB_FORM_ID = intval($WEB_FORM_ID);
		if ($type!="MAIL")
		{
			if ($path=="xxx")
			{
				if ($type=="SHOW") $path = COption::GetOptionString("form", "SHOW_TEMPLATE_PATH");
				elseif ($type=="SHOW_RESULT") $path = COption::GetOptionString("form", "SHOW_RESULT_TEMPLATE_PATH");
				elseif ($type=="PRINT_RESULT") $path = COption::GetOptionString("form", "PRINT_RESULT_TEMPLATE_PATH");
				elseif ($type=="EDIT_RESULT") $path = COption::GetOptionString("form", "EDIT_RESULT_TEMPLATE_PATH");
			}
			$arr = array();
			$handle=@opendir($_SERVER["DOCUMENT_ROOT"].$path);
			if($handle)
			{
				while (false!==($fname = readdir($handle)))
				{
					if (is_file($_SERVER["DOCUMENT_ROOT"].$path.$fname) && $fname!="." && $fname!="..")
					{
						$arReferenceId[] = $fname;
						$arReference[] = $fname;
					}
				}
				closedir($handle);
			}
		}
		elseif ($WEB_FORM_ID>0)
		{
			$arrSITE = array();
			$strSql = "
				SELECT
					F.MAIL_EVENT_TYPE,
					FS.SITE_ID
				FROM
					b_form F
				INNER JOIN b_form_2_site FS ON (FS.FORM_ID = F.ID)
				WHERE
					F.ID = $WEB_FORM_ID
				";
			$z = $DB->Query($strSql,false,$err_mess.__LINE__);
			while ($zr = $z->Fetch())
			{
				$MAIL_EVENT_TYPE = $zr["MAIL_EVENT_TYPE"];
				$arrSITE[] = $zr["SITE_ID"];
			}
			$arReferenceId = array();
			$arReference = array();
			$arFilter = Array(
				"ACTIVE"		=> "Y",
				"SITE_ID"		=> $arrSITE,
				"EVENT_NAME"	=> $MAIL_EVENT_TYPE
				);
			$e = CEventMessage::GetList($by="id", $order="asc", $arFilter);
			while ($er=$e->Fetch())
			{
				if (!in_array($er["ID"], $arReferenceId))
				{
					$arReferenceId[] = $er["ID"];
					$arReference[] = "(".$er["LID"].") ".TruncateText($er["SUBJECT"],50);
				}
			}
		}
		$arr = array("reference"=>$arReference,"reference_id"=>$arReferenceId);
		return $arr;
	}

	function GetMenuList($arFilter=Array(), $check_rights="Y")
	{
		$err_mess = (CAllForm::err_mess())."<br>Function: GetMenuList<br>Line: ";
		global $DB, $USER, $strError;
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
					case "FORM_ID":
					case "LID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("L.".$key,$val,$match);
						break;
					case "MENU":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("L.MENU", $val, $match);
						break;
				}
			}
		}
		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		if ($check_rights=="N" || CForm::IsAdmin())
		{
			$strSql = "
				SELECT
					F.ID,
					F.NAME,
					L.LID,
					L.MENU
				FROM
					b_form_menu L,
					b_form F
				WHERE
				$strSqlSearch
				and L.FORM_ID = F.ID
				ORDER BY F.C_SORT
				";
		}
		else
		{
			$arGroups = $USER->GetUserGroupArray();
			if (!is_array($arGroups)) $arGroups[] = 2;
			$groups = implode(",",$arGroups);
			$strSql = "
				SELECT
					F.ID,
					F.NAME,
					L.LID,
					L.MENU
				FROM
					b_form_menu L,
					b_form F,
					b_form_2_group G
				WHERE
				$strSqlSearch
				and L.FORM_ID = F.ID
				and G.FORM_ID = F.ID
				and G.GROUP_ID in ($groups)
				GROUP BY
					L.ID, L.LID, L.MENU, F.NAME, F.ID, F.C_SORT
				HAVING
					max(G.PERMISSION)>=15
				ORDER BY F.C_SORT
				";
		}
		//echo "<pre>".$strSql."</pre>";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}

	function GetNextSort()
	{
		global $DB, $strError;
		$err_mess = (CAllForm::err_mess())."<br>Function: GetNextSort<br>Line: ";
		$strSql = "SELECT max(C_SORT) as MAX_SORT FROM b_form";
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		$zr = $z->Fetch();
		return (intval($zr["MAX_SORT"])+100);
	}

	function ShowRequired($flag)
	{
		if ($flag=="Y") return "<font color='red'><font class='starrequired'>*</font></font>";
	}

	function GetTextFilter($FID, $size="45", $field_text="class=\"inputtext\"", $field_checkbox="class=\"inputcheckbox\"")
	{
		$var = "find_".$FID;
		$var_exec_match = "find_".$FID."_exact_match";
		global $$var, $$var_exec_match;
		$checked = ($$var_exec_match=="Y") ? "checked" : "";
		return '<input '.$field_text.' type="text" name="'.$var.'" size="'.$size.'" value="'.htmlspecialchars($$var).'"><input '.$field_checkbox.' type="checkbox" value="Y" name="'.$var.'_exact_match" title="'.GetMessage("FORM_EXACT_MATCH").'" '.$checked.'>'.ShowFilterLogicHelp();
	}

	function GetDateFilter($FID, $form_name="form1", $show_select="Y", $field_select="class=\"inputselect\"", $field_input="class=\"inputtext\"")
	{
		$var1 = "find_".$FID."_1";
		$var2 = "find_".$FID."_2";
		global $$var1, $$var2;
		return CalendarPeriod($var1, htmlspecialchars($$var1), $var2, htmlspecialchars($$var2), $form_name, $show_select, $field_select, $field_input);
	}

	function GetNumberFilter($FID, $size="10", $field="class=\"inputtext\"")
	{
		global $MESS;
		$var1 = "find_".$FID."_1";
		$var2 = "find_".$FID."_2";
		global $$var1, $$var2;
		return '<input '.$field.' type="text" name="'.$var1.'" size="'.$size.'" value="'.htmlspecialchars($$var1).'">&nbsp;'.GetMessage("FORM_TILL").'&nbsp;<input '.$field.' type="text" name="'.$var2.'" size="'.$size.'" value="'.htmlspecialchars($$var2).'">';
	}

	function GetExistFlagFilter($FID, $field="class=\"inputcheckbox\"")
	{
		global $MESS;
		$var = "find_".$FID;
		global $$var;
		return InputType("checkbox", $var, "Y", $$var, false, "", $field);
	}

	function GetDropDownFilter($ID, $PARAMETER_NAME, $FID, $field="class=\"inputselect\"")
	{
		$err_mess = (CAllForm::err_mess())."<br>Function: GetDropDownFilter<br>Line: ";
		global $DB, $MESS, $strError;
		if ($PARAMETER_NAME=="ANSWER_VALUE") $str=", VALUE as REFERENCE"; else $str=", MESSAGE as REFERENCE";
		$ID = intval($ID);
		$strSql = "
			SELECT
				ID as REFERENCE_ID
				$str
			FROM
				b_form_answer
			WHERE
				FIELD_ID = $ID
			ORDER BY
				C_SORT
			";
		$z = $DB->Query($strSql,false,$err_mess.__LINE__);
		$ref = array();
		$ref_id = array();
		while ($zr = $z->Fetch())
		{
			if (strlen(trim($zr["REFERENCE"]))>0)
			{
				$ref[] = TruncateText($zr["REFERENCE"],70);
				$ref_id[] = $zr["REFERENCE_ID"];
			}
		}
		$arr = array("reference_id"=>$ref_id, "reference"=>$ref);
		$var = "find_".$FID;
		global $$var;
		return SelectBoxFromArray($var, $arr, $$var, GetMessage("FORM_ALL"), $field);
	}

	function GetTextValue($FIELD_NAME, $arAnswer, $arrVALUES=false)
	{
		$fname = "form_text_".$FIELD_NAME;
		if (is_array($arrVALUES) && isset($arrVALUES[$fname])) $value = $arrVALUES[$fname];
		else $value = $arAnswer["VALUE"];
		return $value;
	}

	function GetPasswordValue($FIELD_NAME, $arAnswer, $arrVALUES=false)
	{
		$fname = "form_password_".$FIELD_NAME;
		if (is_array($arrVALUES) && isset($arrVALUES[$fname])) $value = $arrVALUES[$fname];
		else $value = $arAnswer["VALUE"];
		return $value;
	}

	function GetEmailValue($FIELD_NAME, $arAnswer, $arrVALUES=false)
	{
		$fname = "form_email_".$FIELD_NAME;
		if (is_array($arrVALUES) && isset($arrVALUES[$fname])) $value = $arrVALUES[$fname];
		else $value = $arAnswer["VALUE"];
		return $value;
	}

	function GetUrlValue($FIELD_NAME, $arAnswer, $arrVALUES=false)
	{
		$fname = "form_url_".$FIELD_NAME;
		if (is_array($arrVALUES) && isset($arrVALUES[$fname])) $value = $arrVALUES[$fname];
		else $value = $arAnswer["VALUE"];
		return $value;
	}

	function GetTextField($FIELD_NAME, $VALUE="", $SIZE="", $PARAM="")
	{
		if (strlen($PARAM)<=0) $PARAM = " class=\"inputtext\" ";
		return "<input type=\"text\" ".$PARAM." name=\"form_text_".$FIELD_NAME."\" value=\"".htmlspecialchars($VALUE)."\" size=\"".$SIZE."\">";
	}

	function GetEmailField($FIELD_NAME, $VALUE="", $SIZE="", $PARAM="")
	{
		if (strlen($PARAM)<=0) $PARAM = " class=\"inputtext\" ";
		return "<input type=\"text\" ".$PARAM." name=\"form_email_".$FIELD_NAME."\" value=\"".htmlspecialchars($VALUE)."\" size=\"".$SIZE."\">";
	}

	function GetUrlField($FIELD_NAME, $VALUE="", $SIZE="", $PARAM="")
	{
		if (strlen($PARAM)<=0) $PARAM = " class=\"inputtext\" ";
		return "<input type=\"text\" ".$PARAM." name=\"form_url_".$FIELD_NAME."\" value=\"".htmlspecialchars($VALUE)."\" size=\"".$SIZE."\">";
	}

	function GetPasswordField($FIELD_NAME, $VALUE="", $SIZE="", $PARAM="")
	{
		if (strlen($PARAM)<=0) $PARAM = " class=\"inputtext\" ";
		return "<input type=\"password\" ".$PARAM." name=\"form_password_".$FIELD_NAME."\" value=\"".htmlspecialchars($VALUE)."\" size=\"".$SIZE."\">";
	}

	function GetDropDownValue($FIELD_NAME, $arDropDown, $arrVALUES=false)
	{
		$fname = "form_dropdown_".$FIELD_NAME;
		if (is_array($arrVALUES) && isset($arrVALUES[$fname])) $value = intval($arrVALUES[$fname]);
		else
		{
			if (is_array($arDropDown[$FIELD_NAME]["param"]) && count($arDropDown[$FIELD_NAME]["param"])>0)
			{
				for ($i=0; $i<=count($arDropDown[$FIELD_NAME]["param"])-1; $i++)
				{
					if (strpos(strtolower($arDropDown[$FIELD_NAME]["param"][$i]), "selected")!==false || strpos(strtolower($arDropDown[$FIELD_NAME]["param"][$i]), "checked")!==false)
					{
						$value = $arDropDown[$FIELD_NAME]["reference_id"][$i];
						break;
					}
				}
			}
		}
		return $value;
	}

	function GetDropDownField($FIELD_NAME, $arDropDown, $VALUE, $PARAM="")
	{
		if (strlen($PARAM)<=0) $PARAM = " class=\"inputselect\" ";
		return SelectBoxFromArray("form_dropdown_".$FIELD_NAME, $arDropDown, $VALUE, "", $PARAM);
	}

	function GetMultiSelectValue($FIELD_NAME, $arMultiSelect, $arrVALUES=false)
	{
		$fname = "form_multiselect_".$FIELD_NAME;
		if (is_array($arrVALUES)) $value=$arrVALUES[$fname];
		else
		{
			if (is_array($arMultiSelect[$FIELD_NAME]["param"]) && count($arMultiSelect[$FIELD_NAME]["param"])>0)
			{
				for ($i=0;$i<=count($arMultiSelect[$FIELD_NAME]["param"])-1;$i++)
				{
					if (strpos(strtolower($arMultiSelect[$FIELD_NAME]["param"][$i]), "selected")!==false || strpos(strtolower($arMultiSelect[$FIELD_NAME]["param"][$i]), "checked")!==false)
						$value[] = $arMultiSelect[$FIELD_NAME]["reference_id"][$i];
				}
			}
		}
		return $value;
	}

	function GetMultiSelectField($FIELD_NAME, $arMultiSelect, $arSELECTED=array(), $HEIGHT="", $PARAM="")
	{
		if (strlen($PARAM)<=0) $PARAM = " class=\"inputselect\" ";
		return SelectBoxMFromArray("form_multiselect_".$FIELD_NAME."[]", $arMultiSelect, $arSELECTED, "", false, $HEIGHT, $PARAM);
	}

	function GetDateValue($FIELD_NAME, $arAnswer, $arrVALUES=false)
	{
		$fname = "form_date_".$FIELD_NAME;
		if (is_array($arrVALUES) && isset($arrVALUES[$fname])) $value = $arrVALUES[$fname];
		else
		{
			if (eregi("NOW_DATE",$arAnswer["FIELD_PARAM"])) $value = GetTime(time(),"SHORT");
			elseif (eregi("NOW_TIME",$arAnswer["FIELD_PARAM"])) $value = GetTime(time(),"FULL");
			else $value = $arAnswer["VALUE"];
		}
		return $value;
	}

	function GetDateField($FIELD_NAME, $FORM_NAME, $VALUE="", $FIELD_WIDTH="", $PARAM="")
	{
		if (strlen($PARAM)<=0) $PARAM = " class=\"inputtext\" ";
		return CalendarDate("form_date_".$FIELD_NAME, $VALUE, $FORM_NAME, $FIELD_WIDTH, $PARAM);
	}

	function GetCheckBoxValue($FIELD_NAME, $arAnswer, $arrVALUES=false)
	{
		$fname = "form_checkbox_".$FIELD_NAME;
		if (is_array($arrVALUES))
		{
			$arr = $arrVALUES[$fname];
			if (is_array($arr) && in_array($arAnswer["ID"],$arr)) $value = $arAnswer["ID"];
		}
		else
		{
			if ($value<=0)
			{
				if (strpos(strtolower($arAnswer["FIELD_PARAM"]), "selected")!==false || strpos(strtolower($arAnswer["FIELD_PARAM"]), "checked")!==false)
					$value = $arAnswer["ID"];
			}
		}
		return $value;
	}

	function GetCheckBoxField($FIELD_NAME, $FIELD_ID, $VALUE="", $PARAM="")
	{
		if (strlen($PARAM)<=0) $PARAM = " class=\"inputcheckbox\" ";
		return InputType("checkbox", "form_checkbox_".$FIELD_NAME."[]", $FIELD_ID, $VALUE, false, "", $PARAM);
	}

	function GetRadioValue($FIELD_NAME, $arAnswer, $arrVALUES=false)
	{
		$fname = "form_radio_".$FIELD_NAME;
		if (is_array($arrVALUES)) $value = intval($arrVALUES[$fname]);
		else
		{
			if (strpos(strtolower($arAnswer["FIELD_PARAM"]), "selected")!==false || strpos(strtolower($arAnswer["FIELD_PARAM"]), "checked")!==false)
				$value = $arAnswer["ID"];
		}
		return $value;
	}

	function GetRadioField($FIELD_NAME, $FIELD_ID, $VALUE="", $PARAM="")
	{
		if (strlen($PARAM)<=0) $PARAM = " class=\"inputradio\" ";
		return InputType("radio", "form_radio_".$FIELD_NAME, $FIELD_ID, $VALUE, false, "", $PARAM);
	}

	function GetTextAreaValue($FIELD_NAME, $arAnswer, $arrVALUES=false)
	{
		$fname = "form_textarea_".$FIELD_NAME;
		if (is_array($arrVALUES) && isset($arrVALUES[$fname])) $value = $arrVALUES[$fname];
		else $value = $arAnswer["VALUE"];
		return $value;
	}

	function GetTextAreaField($FIELD_NAME, $WIDTH="", $HEIGHT="", $PARAM="", $VALUE="")
	{
		if (strlen($PARAM)<=0) $PARAM = " class=\"inputtextarea\" ";
		return "<textarea name=\"form_textarea_".$FIELD_NAME."\" cols=\"".$WIDTH."\" rows=\"".$HEIGHT."\" ".$PARAM.">".htmlspecialchars($VALUE)."</textarea>";
	}

	function GetFileField($FIELD_NAME, $WIDTH="", $FILE_TYPE="IMAGE", $MAX_FILE_SIZE=0, $VALUE="", $PARAM_FILE="", $PARAM_CHECKBOX="")
	{
		global $USER;
		if (!is_object($USER)) $USER = new CUser;
		if (strlen($PARAM_FILE)<=0) $PARAM_FILE = " class=\"inputfile\" ";
		if (strlen($PARAM_CHECKBOX)<=0) $PARAM_CHECKBOX = " class=\"inputcheckbox\" ";
		$show_notes = (strtoupper($FILE_TYPE)=="IMAGE" || $USER->isAdmin()) ? true : false;
		return CFile::InputFile("form_".strtolower($FILE_TYPE)."_".$FIELD_NAME, $WIDTH, $VALUE, false, $MAX_FILE_SIZE, $FILE_TYPE, $PARAM_FILE, 0, "", $PARAM_CHECKBOX, $show_notes);
	}

	// возвращает массивы описывающие поля и вопросы формы
	function GetDataByID($WEB_FORM_ID, &$arForm, &$arQuestions, &$arAnswers, &$arDropDown, &$arMultiSelect, $additional="N")
	{
		global $strError;
		$WEB_FORM_ID = intval($WEB_FORM_ID);
		$arForm = array();
		$arQuestions = array();
		$arAnswers = array();
		$arDropDown = array();
		$arMultiSelect = array();
		$z = CForm::GetByID($WEB_FORM_ID);
		if ($arForm = $z->Fetch())
		{
			$u = CFormField::GetList($WEB_FORM_ID, $additional, ($by="s_c_sort"), ($order="asc"), array("ACTIVE"=>"Y"), $is_filtered);
			while ($ur=$u->Fetch())
			{
				$arQuestions[$ur["SID"]] = $ur;
				$w = CFormAnswer::GetList($ur["ID"], ($by="s_c_sort"), ($order="asc"), array("ACTIVE"=>"Y"), $is_filtered);
				while ($wr=$w->Fetch()) $arAnswers[$ur["SID"]][] = $wr;
			}
			// собираем по каждому вопросу все dropdown и multiselect в отдельные массивы
			if (is_array($arQuestions) && is_array($arAnswers))
			{
				while (list(,$arQ)=each($arQuestions))
				{
					$QUESTION_ID = $arQ["SID"];
					$arDropReference = array();
					$arDropReferenceID = array();
					$arDropParam = array();
					$arMultiReference = array();
					$arMultiReferenceID = array();
					$arMultiParam = array();
					if (is_array($arAnswers[$QUESTION_ID]))
					{
						while (list(,$arA)=each($arAnswers[$QUESTION_ID]))
						{
							switch ($arA["FIELD_TYPE"])
							{
								case "dropdown":
									$arDropReference[] = $arA["MESSAGE"];
									$arDropReferenceID[] = $arA["ID"];
									$arDropParam[] = $arA["FIELD_PARAM"];
									break;
								case "multiselect":
									$arMultiReference[] = $arA["MESSAGE"];
									$arMultiReferenceID[] = $arA["ID"];
									$arMultiParam[] = $arA["FIELD_PARAM"];
									break;
							}
						}
					}
					if (count($arDropReference)>0)
						$arDropDown[$QUESTION_ID] = array("reference"=>$arDropReference, "reference_id"=>$arDropReferenceID, "param" => $arDropParam);
					if (count($arMultiReference)>0)
						$arMultiSelect[$QUESTION_ID] = array("reference"=>$arMultiReference, "reference_id"=>$arMultiReferenceID, "param" => $arMultiParam);
				}
			}

			reset($arForm);
			reset($arQuestions);
			reset($arAnswers);
			reset($arDropDown);
			reset($arMultiSelect);

			/*
			echo "<pre>";
			print_r($arForm);
			print_r($arQuestions);
			print_r($arAnswers);
			print_r($arDropDown);
			print_r($arMultiSelect);
			echo "</pre>";
			*/

			return $arForm["ID"];
		}
		else return false;

	}

	// проверяет введенные значения на обязательность, правильность формата даты и типа файла
	function Check($WEB_FORM_ID, $arrVALUES=false, $RESULT_ID=false, $CHECK_RIGHTS="Y")
	{
		$err_mess = (CAllForm::err_mess())."<br>Function: Check<br>Line: ";
		global $DB, $USER, $_REQUEST, $HTTP_POST_VARS, $HTTP_GET_VARS, $HTTP_POST_FILES;
		if ($arrVALUES===false) $arrVALUES = $_REQUEST;

		$RESULT_ID = intval($RESULT_ID);

		$strError = "";
		$WEB_FORM_ID = intval($WEB_FORM_ID);
		if ($WEB_FORM_ID>0)
		{
			// получаем данные по форме
			$WEB_FORM_ID = CForm::GetDataByID($WEB_FORM_ID, $arForm, $arQuestions, $arAnswers, $arDropDown, $arMultiSelect, "ALL");
			$WEB_FORM_ID = intval($WEB_FORM_ID);
			if ($WEB_FORM_ID>0)
			{
				// проверяем права
				$F_RIGHT = ($CHECK_RIGHTS=="Y") ? CForm::GetPermission($WEB_FORM_ID) : 30;

				if ($F_RIGHT<10) $strError .= GetMessage("FORM_ACCESS_DENIED_FOR_FORM_WRITE")."<br>";
				else
				{
					$NOT_ANSWER = "NOT_ANSWER";
					// проходим по вопросам
					while (list($key,$arQuestion)=each($arQuestions))
					{
						$FIELD_ID = $arQuestion["ID"];
						if ($arQuestion["TITLE_TYPE"]=="html")
						{
							$FIELD_TITLE = strip_tags($arQuestion["TITLE"]);
						}
						else
						{
							$FIELD_TITLE = $arQuestion["TITLE"];
						}

						if ($arQuestion["ADDITIONAL"]!="Y")
						{
							// проверяем вопросы формы
							$FIELD_SID = $arQuestion["SID"];
							$FIELD_REQUIRED = $arQuestion["REQUIRED"];

							// массив полей: N - поле не отвечено; Y - поле отвечено;
							if ($FIELD_REQUIRED=="Y") $REQUIRED_FIELDS[$FIELD_SID] = "N";

							// проходим по ответам
							if (is_array($arAnswers[$FIELD_SID]))
							{
								reset($arAnswers[$FIELD_SID]);
								while (list($key,$arAnswer)=each($arAnswers[$FIELD_SID]))
								{
									$ANSWER_ID = 0;
									$FIELD_TYPE = $arAnswer["FIELD_TYPE"];
									$FIELD_PARAM = $arAnswer["FIELD_PARAM"];
									switch ($FIELD_TYPE) :

										case "radio":
										case "dropdown":

											$fname = "form_".$FIELD_TYPE."_".$FIELD_SID;
											$ANSWER_ID = intval($arrVALUES[$fname]);
											if ($ANSWER_ID>0 && $ANSWER_ID==$arAnswer["ID"])
											{
												if ($FIELD_REQUIRED=="Y" && !eregi($NOT_ANSWER, $FIELD_PARAM))
												{
													$REQUIRED_FIELDS[$FIELD_SID] = "Y";
												}
											}

										break;

										case "checkbox":
										case "multiselect":

											$fname = "form_".$FIELD_TYPE."_".$FIELD_SID;
											if (is_array($arrVALUES[$fname]) && count($arrVALUES[$fname])>0)
											{
												reset($arrVALUES[$fname]);
												foreach($arrVALUES[$fname] as $ANSWER_ID)
												{
													$ANSWER_ID = intval($ANSWER_ID);
													if ($ANSWER_ID>0 && $ANSWER_ID==$arAnswer["ID"])
													{
														if ($FIELD_REQUIRED=="Y" && !eregi($NOT_ANSWER, $FIELD_PARAM))
														{
															$REQUIRED_FIELDS[$FIELD_SID] = "Y";
															break;
														}
													}
												}
											}

										break;

										case "text":
										case "textarea":
										case "password":

											$fname = "form_".$FIELD_TYPE."_".$arAnswer["ID"];
											$ANSWER_ID = intval($arAnswer["ID"]);
											$USER_TEXT = $arrVALUES[$fname];
											if (strlen($USER_TEXT)>0)
											{
												if ($FIELD_REQUIRED=="Y")
												{
													$REQUIRED_FIELDS[$FIELD_SID] = "Y";
													break;
												}
											}
										break;

										case "url":

											$fname = "form_".$FIELD_TYPE."_".$arAnswer["ID"];
											$ANSWER_ID = intval($arAnswer["ID"]);
											$USER_TEXT = $arrVALUES[$fname];
											if (strlen($USER_TEXT)>0)
											{
												if (!eregi("^(http|https|ftp)://",$USER_TEXT))
												{
													$strError.=GetMessage('FORM_ERROR_BAD_URL').'<br>';
												}
												if ($FIELD_REQUIRED=="Y")
												{
													$REQUIRED_FIELDS[$FIELD_SID] = "Y";
													break;
												}
											}

										break;

										case "email":

											$fname = "form_".$FIELD_TYPE."_".$arAnswer["ID"];
											$ANSWER_ID = intval($arAnswer["ID"]);
											$USER_TEXT = $arrVALUES[$fname];
											if (strlen($USER_TEXT)>0)
											{
												if (!check_email($USER_TEXT))
												{
													$strError.=GetMessage('FORM_ERROR_BAD_EMAIL').'<br>';
												}
												if ($FIELD_REQUIRED=="Y")
												{
													$REQUIRED_FIELDS[$FIELD_SID] = "Y";
													break;
												}
											}

										break;

										case "date":

											$fname = "form_".$FIELD_TYPE."_".$arAnswer["ID"];
											$USER_DATE = $arrVALUES[$fname];
											if (strlen($USER_DATE)>0)
											{
												if (!CheckDateTime($USER_DATE))
												{
													$strError .= str_replace("#FIELD_NAME#", $FIELD_TITLE, GetMessage("FORM_INCORRECT_DATE_FORMAT"))."<br>";
												}
												if ($FIELD_REQUIRED=="Y")
												{
													$REQUIRED_FIELDS[$FIELD_SID] = "Y";
													break;
												}
											}
											break;

										case "image":

											$fname = "form_".$FIELD_TYPE."_".$arAnswer["ID"];
											$fname_del = $arrVALUES["form_".$FIELD_TYPE."_".$arAnswer["ID"]."_del"];
											$ANSWER_ID = intval($arAnswer["ID"]);
											$arIMAGE = is_set($fname, $arrVALUES) ? $arrVALUES[$fname] : $HTTP_POST_FILES[$fname];
											if (is_array($arIMAGE) && strlen($arIMAGE["tmp_name"])>0)
											{
												$arIMAGE["MODULE_ID"] = "form";
												if (strlen(CFile::CheckImageFile($arIMAGE))>0)
												{
													$strError .= str_replace("#FIELD_NAME#", $FIELD_TITLE, GetMessage("FORM_INCORRECT_FILE_TYPE"))."<br>";
												}
												if ($FIELD_REQUIRED=="Y")
												{
													$REQUIRED_FIELDS[$FIELD_SID] = "Y";
													break;
												}
											}
											elseif ($RESULT_ID>0 && $fname_del!="Y")
											{
												$REQUIRED_FIELDS[$FIELD_SID] = "Y";
												break;
											}

										break;

										case "file":

											$fname = "form_".$FIELD_TYPE."_".$arAnswer["ID"];
											$fname_del = $arrVALUES["form_".$FIELD_TYPE."_".$arAnswer["ID"]."_del"];
											$arIMAGE = is_set($fname, $arrVALUES) ? $arrVALUES[$fname] : $HTTP_POST_FILES[$fname];
											if (is_array($arIMAGE) && strlen($arIMAGE["tmp_name"])>0)
											{
												if ($FIELD_REQUIRED=="Y")
												{
													$REQUIRED_FIELDS[$FIELD_SID] = "Y";
													break;
												}
											}
											elseif ($RESULT_ID>0 && $fname_del!="Y")
											{
												$REQUIRED_FIELDS[$FIELD_SID] = "Y";
												break;
											}

										break;

									endswitch;
								}
							}
						}
						else // проверяем дополнительные поля
						{
							$FIELD_TYPE = $arQuestion["FIELD_TYPE"];
							switch ($FIELD_TYPE) :

								case "date":

									$fname = "form_date_ADDITIONAL_".$arQuestion["ID"];
									$USER_DATE = $arrVALUES[$fname];
									if (strlen($USER_DATE)>0)
									{
										if (!CheckDateTime($USER_DATE))
										{
											$strError .= str_replace("#FIELD_NAME#", $FIELD_TITLE, GetMessage("FORM_INCORRECT_DATE_FORMAT"))."<br>";
										}
									}
								break;

							endswitch;
						}
					}
					//echo "<pre>"; print_r($REQUIRED_FIELDS); echo "</pre>";
					if (is_array($REQUIRED_FIELDS) && count($REQUIRED_FIELDS)>0)
					{
						while (list($key,$value)=each($REQUIRED_FIELDS))
						{
							if ($value=="N")
							{
								if (strlen($arQuestions[$key]["RESULTS_TABLE_TITLE"])>0)
								{
									$title = $arQuestions[$key]["RESULTS_TABLE_TITLE"];
								}
								elseif (strlen($arQuestions[$key]["FILTER_TITLE"])>0)
								{
									$title = TrimEx($arQuestions[$key]["FILTER_TITLE"],":");
								}
								else
								{
									$title = ($arQuestions[$key]["TITLE_TYPE"]=="html") ? strip_tags($arQuestions[$key]["TITLE"]) : $arQuestions[$key]["TITLE"];
								}
								$EMPTY_REQUIRED_NAMES[] = $title;
							}
						}
					}
					if (is_array($EMPTY_REQUIRED_NAMES) && count($EMPTY_REQUIRED_NAMES)>0)
					{
						$strError .= GetMessage("FORM_EMPTY_REQUIRED_FIELDS")."<br>";
						foreach ($EMPTY_REQUIRED_NAMES as $name) $strError .= "&nbsp;&nbsp;&raquo;&nbsp;\"".$name."\"<br>";
					}
				}
			}
			else $strError .= GetMessage("FORM_INCORRECT_FORM_ID")."<br>";
		}
		return $strError;
	}

	// проверка формы
	function CheckFields($arFields, $FORM_ID, $CHECK_RIGHTS="Y")
	{
		$err_mess = (CAllForm::err_mess())."<br>Function: CheckFields<br>Line: ";
		global $DB, $strError, $APPLICATION, $USER;
		$str = "";
		$FORM_ID = intval($FORM_ID);
		$RIGHT_OK = "N";
		if ($CHECK_RIGHTS!="Y" || CForm::IsAdmin()) $RIGHT_OK = "Y";
		else
		{
			if (intval($FORM_ID)>0)
			{
				$F_RIGHT = CForm::GetPermission($FORM_ID);
				if ($F_RIGHT>=30) $RIGHT_OK = "Y";
			}
		}

		if ($RIGHT_OK=="Y")
		{

			if (strlen($arFields["SID"])>0) $arFields["VARNAME"] = $arFields["SID"];
			elseif (strlen($arFields["VARNAME"])>0) $arFields["SID"] = $arFields["VARNAME"];

			if ($FORM_ID<=0 || ($FORM_ID>0 && is_set($arFields, "NAME")))
			{
				if (strlen(trim($arFields["NAME"]))<=0) $str .= GetMessage("FORM_ERROR_FORGOT_NAME")."<br>";
			}

			if ($FORM_ID<=0 || ($FORM_ID>0 && is_set($arFields, "SID")))
			{
				if (strlen(trim($arFields["SID"]))<=0) $str .= GetMessage("FORM_ERROR_FORGOT_SID")."<br>";
				if (ereg("[^A-Za-z_0-9]",$arFields["SID"])) $str .= GetMessage("FORM_ERROR_INCORRECT_SID")."<br>";
				else
				{
					$strSql = "SELECT ID FROM b_form WHERE SID='".$DB->ForSql(trim($arFields["SID"]),50)."' and ID<>'$FORM_ID'";
					$z = $DB->Query($strSql, false, $err_mess.__LINE__);
					if ($zr = $z->Fetch())
					{
						$s = str_replace("#TYPE#", GetMessage("FORM_TYPE_FORM"), GetMessage("FORM_ERROR_WRONG_SID"));
						$s = str_replace("#ID#",$zr["ID"],$s);
						$str .= $s."<br>";
					}
					else
					{
						$strSql = "SELECT ID, ADDITIONAL FROM b_form_field WHERE SID='".$DB->ForSql(trim($arFields["SID"]),50)."'";
						$z = $DB->Query($strSql, false, $err_mess.__LINE__);
						if ($zr = $z->Fetch())
						{
							$s = ($zr["ADDITIONAL"]=="Y") ?
								str_replace("#TYPE#", GetMessage("FORM_TYPE_FIELD"), GetMessage("FORM_ERROR_WRONG_SID")) :
								str_replace("#TYPE#", GetMessage("FORM_TYPE_QUESTION"), GetMessage("FORM_ERROR_WRONG_SID"));

							$s = str_replace("#ID#",$zr["ID"],$s);
							$str .= $s."<br>";
						}
					}
				}
			}
			$str .= CFile::CheckImageFile($arFields["arIMAGE"]);
		}
		else $str .= GetMessage("FORM_ERROR_ACCESS_DENIED");

		$strError .= $str;
		if (strlen($str)>0) return false; else return true;
	}

	// добавление/обновление формы
	function Set($arFields, $FORM_ID=false, $CHECK_RIGHTS="Y")
	{
		$err_mess = (CAllForm::err_mess())."<br>Function: Set<br>Line: ";
		global $DB, $USER, $strError, $APPLICATION;
		if (CForm::CheckFields($arFields, $FORM_ID, $CHECK_RIGHTS))
		{
			$arFields_i = array();

			if (strlen(trim($arFields["SID"]))>0) $arFields["VARNAME"] = $arFields["SID"];
			elseif (strlen($arFields["VARNAME"])>0) $arFields["SID"] = $arFields["VARNAME"];

			$arFields_i["TIMESTAMP_X"] = $DB->GetNowFunction();

			if (is_set($arFields, "NAME"))
				$arFields_i["NAME"] = "'".$DB->ForSql($arFields["NAME"],255)."'";

			if (is_set($arFields, "SID"))
				$arFields_i["SID"] = "'".$DB->ForSql($arFields["SID"],255)."'";

			if (is_set($arFields, "DESCRIPTION"))
				$arFields_i["DESCRIPTION"] = "'".$DB->ForSql($arFields["DESCRIPTION"],2000)."'";

			if (is_set($arFields, "C_SORT"))
				$arFields_i["C_SORT"] = "'".intval($arFields["C_SORT"])."'";

			if (is_array($arrSITE))
			{
				reset($arrSITE);
				list($k, $arFields["FIRST_SITE_ID"]) = each($arrSITE);
			}

			if (is_set($arFields, "BUTTON"))
				$arFields_i["BUTTON"] = "'".$DB->ForSql($arFields["BUTTON"],255)."'";

			if (is_set($arFields, "DESCRIPTION_TYPE"))
				$arFields_i["DESCRIPTION_TYPE"] = ($arFields["DESCRIPTION_TYPE"]=="html") ? "'html'" : "'text'";

			if (is_set($arFields, "SHOW_TEMPLATE"))
				$arFields_i["SHOW_TEMPLATE"] = "'".$DB->ForSql($arFields["SHOW_TEMPLATE"],255)."'";

			if (is_set($arFields, "SHOW_RESULT_TEMPLATE"))
				$arFields_i["SHOW_RESULT_TEMPLATE"] = "'".$DB->ForSql($arFields["SHOW_RESULT_TEMPLATE"],255)."'";

			if (is_set($arFields, "PRINT_RESULT_TEMPLATE"))
				$arFields_i["PRINT_RESULT_TEMPLATE"] = "'".$DB->ForSql($arFields["PRINT_RESULT_TEMPLATE"],255)."'";

			if (is_set($arFields, "EDIT_RESULT_TEMPLATE"))
				$arFields_i["EDIT_RESULT_TEMPLATE"] = "'".$DB->ForSql($arFields["EDIT_RESULT_TEMPLATE"],255)."'";

			if (is_set($arFields, "FILTER_RESULT_TEMPLATE"))
				$arFields_i["FILTER_RESULT_TEMPLATE"] = "'".$DB->ForSql($arFields["FILTER_RESULT_TEMPLATE"],255)."'";

			if (is_set($arFields, "TABLE_RESULT_TEMPLATE"))
				$arFields_i["TABLE_RESULT_TEMPLATE"] = "'".$DB->ForSql($arFields["TABLE_RESULT_TEMPLATE"],255)."'";

			if (is_set($arFields, "STAT_EVENT1"))
				$arFields_i["STAT_EVENT1"] = "'".$DB->ForSql($arFields["STAT_EVENT1"],255)."'";

			if (is_set($arFields, "STAT_EVENT2"))
				$arFields_i["STAT_EVENT2"] = "'".$DB->ForSql($arFields["STAT_EVENT2"],255)."'";

			if (is_set($arFields, "STAT_EVENT3"))
				$arFields_i["STAT_EVENT3"] = "'".$DB->ForSql($arFields["STAT_EVENT3"],255)."'";

			if (CForm::IsOldVersion()!="Y")
			{
				unset($arFields_i["SHOW_TEMPLATE"]);
				unset($arFields_i["SHOW_RESULT_TEMPLATE"]);
				unset($arFields_i["PRINT_RESULT_TEMPLATE"]);
				unset($arFields_i["EDIT_RESULT_TEMPLATE"]);
			}

			$z = $DB->Query("SELECT IMAGE_ID, SID, SID as VARNAME FROM b_form WHERE ID='$FORM_ID'", false, $err_mess.__LINE__);
			$zr = $z->Fetch();
			$oldSID = $zr["SID"];
			if (strlen($arFields["arIMAGE"]["name"])>0 || strlen($arFields["arIMAGE"]["del"])>0)
			{
				$fid = CFile::SaveFile($arFields["arIMAGE"], "form");
				if (intval($fid)>0)	$arFields_i["IMAGE_ID"] = intval($fid);
				else $arFields_i["IMAGE_ID"] = "null";
			}

			$arFields_i["MAIL_EVENT_TYPE"] = "'".$DB->ForSql("FORM_FILLING_".$arFields["SID"],50)."'";

			if ($FORM_ID>0)
			{
				$DB->Update("b_form", $arFields_i, "WHERE ID='".$FORM_ID."'", $err_mess.__LINE__);
				CForm::SetMailTemplate($FORM_ID, "N", $oldSID);
			}
			else
			{
				$FORM_ID = $DB->Insert("b_form", $arFields_i, $err_mess.__LINE__);
				CForm::SetMailTemplate($FORM_ID, "N");
			}
			$FORM_ID = intval($FORM_ID);

			if ($FORM_ID>0)
			{
				// сайты
				if (is_set($arFields, "arSITE"))
				{
					$DB->Query("DELETE FROM b_form_2_site WHERE FORM_ID='".$FORM_ID."'", false, $err_mess.__LINE__);
					if (is_array($arFields["arSITE"]))
					{
						reset($arFields["arSITE"]);
						foreach($arFields["arSITE"] as $sid)
						{
							$strSql = "
								INSERT INTO b_form_2_site (FORM_ID, SITE_ID) VALUES (
									$FORM_ID,
									'".$DB->ForSql($sid,2)."'
								)
								";
							$DB->Query($strSql, false, $err_mess.__LINE__);
						}
					}
				}

				// меню
				if (is_set($arFields, "arMENU"))
				{
					$DB->Query("DELETE FROM b_form_menu WHERE FORM_ID='".$FORM_ID."'", false, $err_mess.__LINE__);
					if (is_array($arFields["arMENU"]))
					{
						reset($arFields["arMENU"]);
						while(list($lid,$menu)=each($arFields["arMENU"]))
						{
							$arFields_i = array(
								"FORM_ID"	=> $FORM_ID,
								"LID"		=> "'".$DB->ForSql($lid,2)."'",
								"MENU"		=> "'".$DB->ForSql($menu,50)."'"
								);
							$DB->Insert("b_form_menu", $arFields_i, $err_mess.__LINE__);
						}
					}
				}

				// почтовые шаблоны
				if (is_set($arFields, "arMAIL_TEMPLATE"))
				{
					$DB->Query("DELETE FROM b_form_2_mail_template WHERE FORM_ID='".$FORM_ID."'", false, $err_mess.__LINE__);
					if (is_array($arFields["arMAIL_TEMPLATE"]))
					{
						reset($arFields["arMAIL_TEMPLATE"]);
						foreach($arFields["arMAIL_TEMPLATE"] as $mid)
						{
							$strSql = "
								INSERT INTO b_form_2_mail_template (FORM_ID, MAIL_TEMPLATE_ID) VALUES (
									$FORM_ID,
									'".intval($mid)."'
								)
								";
							$DB->Query($strSql, false, $err_mess.__LINE__);
						}
					}
				}

				// группы
				if (is_set($arFields, "arGROUP"))
				{
					$DB->Query("DELETE FROM b_form_2_group WHERE FORM_ID='".$FORM_ID."'", false, $err_mess.__LINE__);
					if (is_array($arFields["arGROUP"]))
					{
						reset($arFields["arGROUP"]);
						while(list($group_id,$perm)=each($arFields["arGROUP"]))
						{
							if (intval($perm)>0)
							{
								$arFields_i = array(
									"FORM_ID"		=> $FORM_ID,
									"GROUP_ID"		=> "'".intval($group_id)."'",
									"PERMISSION"	=> "'".intval($perm)."'"
									);
								$DB->Insert("b_form_2_group", $arFields_i, $err_mess.__LINE__);
							}
						}
					}
				}
			}
			return $FORM_ID;
		}
		return false;
	}

	// копирует веб-форму
	function Copy($ID, $CHECK_RIGHTS="Y")
	{
		global $DB, $APPLICATION, $strError;
		$err_mess = (CAllForm::err_mess())."<br>Function: Copy<br>Line: ";
		$ID = intval($ID);
		if ($CHECK_RIGHTS!="Y" || CForm::IsAdmin())
		{
			$rsForm = CForm::GetByID($ID);
			$arForm = $rsForm->Fetch();

			// символьный код формы
			while(true)
			{
				$SID = $arForm["SID"]."_".RandString(5);
				if (strlen($SID)>=50) $SID = substr($SID, 6);
				$strSql = "SELECT 'x' FROM b_form WHERE SID='".$DB->ForSql($SID,50)."'";
				$z = $DB->Query($strSql, false, $err_mess.__LINE__);
				if (!($zr = $z->Fetch())) break;
			}

			$arFields = array(
				"NAME"						=> $arForm["NAME"],
				"SID"						=> $SID,
				"C_SORT"					=> $arForm["C_SORT"],
				"FIRST_SITE_ID"				=> $arForm["FIRST_SITE_ID"],
				"BUTTON"					=> $arForm["BUTTON"],
				"DESCRIPTION"				=> $arForm["DESCRIPTION"],
				"DESCRIPTION_TYPE"			=> $arForm["DESCRIPTION_TYPE"],
				"SHOW_TEMPLATE"				=> $arForm["SHOW_TEMPLATE"],
				"SHOW_RESULT_TEMPLATE"		=> $arForm["SHOW_RESULT_TEMPLATE"],
				"PRINT_RESULT_TEMPLATE"		=> $arForm["PRINT_RESULT_TEMPLATE"],
				"EDIT_RESULT_TEMPLATE"		=> $arForm["EDIT_RESULT_TEMPLATE"],
				"FILTER_RESULT_TEMPLATE"	=> $arForm["FILTER_RESULT_TEMPLATE"],
				"TABLE_RESULT_TEMPLATE"		=> $arForm["TABLE_RESULT_TEMPLATE"],
				"STAT_EVENT1"				=> $arForm["STAT_EVENT1"],
				"STAT_EVENT2"				=> $SID,
				"STAT_EVENT3"				=> $arForm["STAT_EVENT3"],
				"arSITE"					=> CForm::GetSiteArray($ID)
				);
			// пункты меню
			$z = CForm::GetMenuList(array("FORM_ID"=>$ID), "N");
			while ($zr = $z->Fetch()) $arFields["arMENU"][$zr["LID"]] = $zr["MENU"];

			// права групп
			$w = CGroup::GetList($v1="dropdown", $v2="asc", Array("ADMIN"=>"N"), $v3);
			$arGroups = array();
			while ($wr=$w->Fetch()) $arGroups[] = $wr["ID"];
			if (is_array($arGroups))
			{
				reset($arGroups);
				foreach($arGroups as $gid) $arFields["arGROUP"][$gid] = CForm::GetPermission($ID, array($gid), "Y");
			}

			// картинка
			if (intval($arForm["IMAGE_ID"])>0)
			{
				$arIMAGE = CFile::MakeFileArray(CFile::CopyFile($arForm["IMAGE_ID"]));
				$arIMAGE["MODULE_ID"] = "form";
				$arFields["arIMAGE"] = $arIMAGE;
			}

			$NEW_ID = CForm::Set($arFields, 0);

			if (intval($NEW_ID)>0)
			{
				// статусы
				$rsStatus = CFormStatus::GetList($ID, $by, $order, array(), $is_filtered);
				while ($arStatus = $rsStatus->Fetch()) CFormStatus::Copy($arStatus["ID"], "N", $NEW_ID);

				// вопросы/поля
				$rsField = CFormField::GetList($ID, "ALL", $by, $order, array(), $is_filtered);
				while ($arField = $rsField->Fetch()) CFormField::Copy($arField["ID"], "N", $NEW_ID);
			}
			return $NEW_ID;
		}
		else $strError .= GetMessage("FORM_ERROR_ACCESS_DENIED")."<br>";
		return false;
	}

	// удаляет веб-форму
	function Delete($ID, $CHECK_RIGHTS="Y")
	{
		global $DB, $strError;
		$err_mess = (CAllForm::err_mess())."<br>Function: Delete<br>Line: ";
		$ID = intval($ID);

		if ($CHECK_RIGHTS!="Y" || CForm::IsAdmin())
		{
			// удаляем результаты формы
			if (CForm::Reset($ID, "N"))
			{
				// удаляем статусы формы
				$rsStatuses = CFormStatus::GetList($ID, $by, $order, $arFilter, $is_filtered);
				while ($arStatus = $rsStatuses->Fetch()) CFormStatus::Delete($arStatus["ID"], "N");

				// удаляем поля и вопросы формы
				$rsFields = CFormField::GetList($ID, "ALL", $by, $order, array(), $is_filtered);
				while ($arField = $rsFields->Fetch()) CFormField::Delete($arField["ID"], "N");

				// удаляем изображения формы
				$strSql = "SELECT IMAGE_ID FROM b_form WHERE ID='$ID' and IMAGE_ID>0";
				$z = $DB->Query($strSql, false, $err_mess.__LINE__);
				while ($zr = $z->Fetch()) CFile::Delete($zr["IMAGE_ID"]);

				// удаляем тип почтового события и почтовые шаблоны приписанные данной форме
				$q = CForm::GetByID($ID);
				$qr = $q->Fetch();
				if (strlen(trim($qr["MAIL_EVENT_TYPE"]))>0)
				{
					// удалим почтовые шаблоны
					$em = new CEventMessage;
					$e = $em->GetList($by="id",$order="desc",array("EVENT_NAME"=>$qr["MAIL_EVENT_TYPE"], "EVENT_NAME_EXACT_MATCH" => "Y"));
					while ($er=$e->Fetch()) $em->Delete($er["ID"]);

					// удалим тип почтового события
					$et = new CEventType;
					$et->Delete($qr["MAIL_EVENT_TYPE"]);
				}

				// удаляем привязку к сайтам
				$DB->Query("DELETE FROM b_form_2_site WHERE FORM_ID='$ID'", false, $err_mess.__LINE__);

				// удаляем привязку к почтовым шаблонам
				$DB->Query("DELETE FROM b_form_2_mail_template WHERE FORM_ID='$ID'", false, $err_mess.__LINE__);

				// удаляем меню формы
				$DB->Query("DELETE FROM b_form_menu WHERE FORM_ID='$ID'", false, $err_mess.__LINE__);

				// удаляем права групп
				$DB->Query("DELETE FROM b_form_2_group WHERE FORM_ID='$ID'", false, $err_mess.__LINE__);

				// удаляем форму
				$DB->Query("DELETE FROM b_form WHERE ID='$ID'", false, $err_mess.__LINE__);

				return true;
			}
		}
		else $strError .= GetMessage("FORM_ERROR_ACCESS_DENIED")."<br>";
		return false;
	}

	// удаляем результаты формы
	function Reset($ID, $CHECK_RIGHTS="Y")
	{
		global $DB, $strError;
		$err_mess = (CAllForm::err_mess())."<br>Function: Reset<br>Line: ";
		$ID = intval($ID);

		$F_RIGHT = ($CHECK_RIGHTS!="Y") ? 30 : CForm::GetPermission($ID);
		if ($F_RIGHT>=30)
		{
			// обнуляем поля формы
			$rsFields = CFormField::GetList($ID, "ALL", $by, $order, array(), $is_filtered);
			while ($arField = $rsFields->Fetch()) CFormField::Reset($arField["ID"], "N");

			// удаляем результаты данной формы
			$DB->Query("DELETE FROM b_form_result WHERE FORM_ID='$ID'", false, $err_mess.__LINE__);

			return true;
		}
		else $strError .= GetMessage("FORM_ERROR_ACCESS_DENIED")."<br>";

		return false;
	}

	// создает тип почтового события и шаблон на языке формы
	function SetMailTemplate($WEB_FORM_ID, $ADD_NEW_TEMPLATE="Y", $old_SID="")
	{
		global $DB, $MESS, $strError;
		$err_mess = (CAllForm::err_mess())."<br>Function: SetMailTemplates<br>Line: ";
		$arrReturn = array();
		$WEB_FORM_ID = intval($WEB_FORM_ID);
		$q = CForm::GetByID($WEB_FORM_ID);
		if ($arrForm = $q->Fetch())
		{
			$MAIL_EVENT_TYPE = "FORM_FILLING_".$arrForm["SID"];
			if (strlen($old_SID)>0) $old_MAIL_EVENT_TYPE = "FORM_FILLING_".$old_SID;

			$et = new CEventType;
			$em = new CEventMessage;

			if (strlen($MAIL_EVENT_TYPE)>0)
				$et->Delete($MAIL_EVENT_TYPE);

			$z = CLanguage::GetList($v1, $v2);
			$OLD_MESS = $MESS;
			$MESS = array();
			while ($arLang = $z->Fetch())
			{
				IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/form/admin/form_mail.php", $arLang["LID"]);

				$str = "";
				$str .= "#RS_FORM_ID# - ".GetMessage("FORM_L_FORM_ID")."\n";
				$str .= "#RS_FORM_NAME# - ".GetMessage("FORM_L_NAME")."\n";
				$str .= "#RS_FORM_SID# - ".GetMessage("FORM_L_SID")."\n";
				$str .= "#RS_RESULT_ID# - ".GetMessage("FORM_L_RESULT_ID")."\n";
				$str .= "#RS_DATE_CREATE# - ".GetMessage("FORM_L_DATE_CREATE")."\n";
				$str .= "#RS_USER_ID# - ".GetMessage("FORM_L_USER_ID")."\n";
				$str .= "#RS_USER_EMAIL# - ".GetMessage("FORM_L_USER_EMAIL")."\n";
				$str .= "#RS_USER_NAME# - ".GetMessage("FORM_L_USER_NAME")."\n";
				$str .= "#RS_USER_AUTH# - ".GetMessage("FORM_L_USER_AUTH")."\n";
				$str .= "#RS_STAT_GUEST_ID# - ".GetMessage("FORM_L_STAT_GUEST_ID")."\n";
				$str .= "#RS_STAT_SESSION_ID# - ".GetMessage("FORM_L_STAT_SESSION_ID")."\n";

				$strFIELDS = "";
				$w = CFormField::GetList($WEB_FORM_ID,"ALL", $by, $order, array(), $is_filtered);
				while ($wr=$w->Fetch())
				{
					if (strlen($wr["RESULTS_TABLE_TITLE"])>0)
					{
						$FIELD_TITLE = $wr["RESULTS_TABLE_TITLE"];
					}
					elseif (strlen($wr["TITLE"])>0)
					{
						$FIELD_TITLE = $wr["TITLE_TYPE"]=="html" ? htmlspecialcharsback(strip_tags($wr["TITLE"])) : $wr["TITLE"];
					}
					else
					{
						$FIELD_TITLE = TrimEx($wr["FILTER_TITLE"],":");
					}

					$str .= "#".$wr["SID"]."# - ".$FIELD_TITLE."\n";
					$strFIELDS .= $FIELD_TITLE."\n*******************************\n#".$wr["SID"]."#\n\n";
				}
				$et->Add(
						Array(
						"LID"			=> $arLang["LID"],
						"EVENT_NAME"	=> $MAIL_EVENT_TYPE,
						"NAME"			=> GetMessage("FORM_FILLING")." \"".$arrForm["SID"]."\"",
						"DESCRIPTION"	=> $str
						)
					);
			}
			// задаем новый тип события для старых шаблонов
			if (strlen($old_MAIL_EVENT_TYPE)>0 && $old_MAIL_EVENT_TYPE!=$MAIL_EVENT_TYPE)
			{
				$e = $em->GetList($by="id",$order="desc",array("EVENT_NAME"=>$old_MAIL_EVENT_TYPE));
				while ($er=$e->Fetch())
				{
					$em->Update($er["ID"],array("EVENT_NAME"=>$MAIL_EVENT_TYPE));
				}
				if (strlen($old_MAIL_EVENT_TYPE)>0)
					$et->Delete($old_MAIL_EVENT_TYPE);
			}

			if ($ADD_NEW_TEMPLATE=="Y")
			{
				$z = CSite::GetList($v1, $v2);
				while ($arSite = $z->Fetch()) $arrSiteLang[$arSite["ID"]] = $arSite["LANGUAGE_ID"];

				$arrFormSite = CForm::GetSiteArray($WEB_FORM_ID);
				if (is_array($arrFormSite) && count($arrFormSite)>0)
				{
					foreach($arrFormSite as $sid)
					{
						IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/form/admin/form_mail.php", $arrSiteLang[$sid]);

						$SUBJECT = "#SERVER_NAME#: ".GetMessage("FORM_FILLING_S")." [#RS_FORM_ID#] #RS_FORM_NAME#";
						$MESSAGE = "#SERVER_NAME#

".GetMessage("FORM_FILLING").": [#RS_FORM_ID#] #RS_FORM_NAME#
-------------------------------------------------------

".GetMessage("FORM_DATE_CREATE")."#RS_DATE_CREATE#
".GetMessage("FORM_RESULT_ID")."#RS_RESULT_ID#
".GetMessage("FORM_USER")."[#RS_USER_ID#] #RS_USER_NAME# #RS_USER_AUTH#
".GetMessage("FORM_STAT_GUEST_ID")."#RS_STAT_GUEST_ID#
".GetMessage("FORM_STAT_SESSION_ID")."#RS_STAT_SESSION_ID#


$strFIELDS
".GetMessage("FORM_VIEW")."
http://#SERVER_NAME#/bitrix/admin/form_result_view.php?lang=".$arrSiteLang[$sid]."&WEB_FORM_ID=#RS_FORM_ID#&RESULT_ID=#RS_RESULT_ID#

-------------------------------------------------------
".GetMessage("FORM_GENERATED_AUTOMATICALLY")."
						";
						// добавляем новый шаблон
						$arFields = Array(
							"ACTIVE"		=> "Y",
							"EVENT_NAME"	=> $MAIL_EVENT_TYPE,
							"LID"			=> $sid,
							"EMAIL_FROM"	=> "#DEFAULT_EMAIL_FROM#",
							"EMAIL_TO"		=> "#DEFAULT_EMAIL_FROM#",
							"SUBJECT"		=> $SUBJECT,
							"MESSAGE"		=> $MESSAGE,
							"BODY_TYPE"		=> "text"
							);
						$TEMPLATE_ID = $em->Add($arFields);
						$arrReturn[] = $TEMPLATE_ID;
					}
				}
			}
			$MESS = $OLD_MESS;
		}
		return $arrReturn;
	}

	function GetBySID($SID)
	{ return CForm::GetByID($SID, "Y"); }
}

/***************************************
		Результат веб-формы
***************************************/

class CAllFormResult extends CFormResult_old
{
	function err_mess()
	{
		$module_id = "form";
		@include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/install/version.php");
		return "<br>Module: ".$module_id." (".constant(strtoupper($module_id)."_VERSION").")<br>Class: CAllFormResult<br>File: ".__FILE__;
	}

	function GetFileByAnswerID($RESULT_ID, $ANSWER_ID)
	{
		global $DB, $strError;
		$err_mess = (CAllFormResult::err_mess())."<br>Function: GetFileByAnswerID<br>Line: ";
		$RESULT_ID = intval($RESULT_ID);
		$ANSWER_ID = intval($ANSWER_ID);
		$strSql = "
			SELECT
				USER_FILE_ID,
				USER_FILE_NAME,
				USER_FILE_IS_IMAGE,
				USER_FILE_HASH,
				USER_FILE_SUFFIX,
				USER_FILE_SIZE
			FROM
				b_form_result_answer
			WHERE
				RESULT_ID='".$RESULT_ID."'
			and ANSWER_ID='".$ANSWER_ID."'
			";
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		if ($zr = $z->Fetch()) return $zr; else return false;
	}

	// возвращает данные по файлу ответа
	function GetFileByHash($RESULT_ID, $HASH)
	{
		global $DB, $APPLICATION, $strError;
		$err_mess = (CAllFormResult::err_mess())."<br>Function: GetAnswerFile<br>Line: ";
		$RESULT_ID = intval($RESULT_ID);
		if ($RESULT_ID<=0 || strlen(trim($HASH))<=0) return;
		$strSql = "
			SELECT
				F.FILE_NAME,
				F.SUBDIR,
				F.CONTENT_TYPE,
				RA.USER_FILE_NAME		ORIGINAL_NAME,
				RA.USER_FILE_IS_IMAGE
			FROM
				b_form_result_answer RA
			INNER JOIN b_file F ON (F.ID = RA.USER_FILE_ID)
			WHERE
				RA.RESULT_ID = $RESULT_ID
			and RA.USER_FILE_HASH = '".$DB->ForSql($HASH, 255)."'
		";
		//echo "<pre>".$strSql."</pre>";
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		if ($zr = $z->Fetch()) return $zr; else return false;
	}

	// создает новое событие в модуле статистики
	function SetEvent($RESULT_ID, $IN_EVENT1=false, $IN_EVENT2=false, $IN_EVENT3=false, $money="", $currency="", $goto="", $chargeback="N")
	{
		$err_mess = (CAllFormResult::err_mess())."<br>Function: SetEvent<br>Line: ";
		global $DB, $strError;

		if (CModule::IncludeModule("statistic"))
		{
			$RESULT_ID = intval($RESULT_ID);
			$strSql = "SELECT FORM_ID FROM b_form_result WHERE ID='".$RESULT_ID."'";
			$z = $DB->Query($strSql, false, $err_mess.__LINE__);
			if ($zr = $z->Fetch())
			{
				$WEB_FORM_ID = $zr["FORM_ID"];
				$strSql = "SELECT SID, STAT_EVENT1, STAT_EVENT2, STAT_EVENT3 FROM b_form WHERE ID = '".$WEB_FORM_ID."'";
				$z = $DB->Query($strSql, false, $err_mess.__LINE__);
				$zr = $z->Fetch();

				if ($IN_EVENT1===false)
				{
					$event1 = (strlen($zr["STAT_EVENT1"])<=0) ? "form" : $zr["STAT_EVENT1"];
				}
				else $event1 = $IN_EVENT1;

				if ($IN_EVENT2===false)
				{
					$event2 = (strlen($zr["STAT_EVENT2"])<=0) ? $zr["SID"] : $zr["STAT_EVENT2"];
				}
				else $event2 = $IN_EVENT2;

				if ($IN_EVENT3===false)
				{
					$event3 = (strlen($zr["STAT_EVENT3"])<=0) ? "http://".$_SERVER["HTTP_HOST"]."/bitrix/admin/form_result_list.php?lang=".LANGUAGE_ID."&WEB_FORM_ID=".$WEB_FORM_ID."&find_id=".$RESULT_ID."&find_id_exact_match=Y&set_filter=Y" : $zr["STAT_EVENT3"];
				}
				else $event3 = $IN_EVENT3;

				CStatEvent::AddCurrent($event1, $event2, $event3, $money, $currency, $goto, $chargeback);
				return true;
			}
			else $strError .= GetMessage("FORM_ERROR_RESULT_NOT_FOUND")."<br>";
		}
		return false;
	}

	// возвращает значение ответа для массива вопросов и полей формы
	function GetDataByID($RESULT_ID, $arrFIELD_SID, &$arrRES, &$arrANSWER)
	{
		global $DB, $strError;
		$err_mess = (CAllFormResult::err_mess())."<br>Function: GetDataByID<br>Line: ";
		$arrReturn = array();
		$RESULT_ID = intval($RESULT_ID);
		$z = CFormResult::GetByID($RESULT_ID);
		if ($arrRES = $z->Fetch())
		{
			if (is_array($arrFIELD_SID) && count($arrFIELD_SID)>0)
			{
				foreach($arrFIELD_SID as $field) $str .= ",'".$DB->ForSql($field,50)."'";
				$str = TrimEx($str,",");
				if (strlen($str)>0) $s = "and SID in ($str)";
			}
			$strSql = "SELECT ID, SID, SID as VARNAME FROM b_form_field WHERE FORM_ID='".$arrRES["FORM_ID"]."' ".$s;
			$q = $DB->Query($strSql, false, $err_mess.__LINE__);
			while ($qr = $q->Fetch())
			{
				$arrFIELDS[$qr["ID"]] = $qr["SID"];
			}
			if (is_array($arrFIELDS)) $arrKeys = array_keys($arrFIELDS);
			CForm::GetResultAnswerArray($arrRES["FORM_ID"], $arrColumns, $arrAnswers, $arrAnswersSID, array("RESULT_ID"=>$RESULT_ID));
			while (list($fid,$arrAns)=each($arrAnswers[$RESULT_ID]))
			{
				if (is_array($arrKeys))
				{
					if (in_array($fid,$arrKeys))
					{
						$sid = $arrFIELDS[$fid];
						$arrANSWER[$sid] = $arrAns;
						$arrA = array_values($arrAns);
						foreach($arrA as $arr) $arrReturn[$sid][] = $arr;
					}
				}
			}
		}
		else return false;

		if (is_array($arrANSWER)) reset($arrANSWER);
		if (is_array($arrReturn)) reset($arrReturn);
		if (is_array($arrRES)) reset($arrRES);

		/*
		echo "<pre>arrRES\n";
		print_r($arrRES);
		echo "arrANSWER\n";
		print_r($arrANSWER);
		echo "arrReturn\n";
		print_r($arrReturn);
		echo "</pre>";
		*/

		return $arrReturn;
	}

	// возвращает массив значений результата в специальном формате, используемом шаблоном для редактирования результата
	function GetDataByIDForHTML($RESULT_ID, $GET_ADDITIONAL="N")
	{
		$err_mess = (CAllFormResult::err_mess())."<br>Function: GetDataByIDForHTML<br>Line: ";
		global $DB, $strError;
		$z = CFormResult::GetByID($RESULT_ID);
		if ($zr=$z->Fetch())
		{
			$arrResult = $zr;
			$additional = ($GET_ADDITIONAL=="Y") ? "ALL" : "N";
			$WEB_FORM_ID = CForm::GetDataByID($arrResult["FORM_ID"], $arForm, $arQuestions, $arAnswers, $arDropDown, $arMultiSelect, $additional);
			CForm::GetResultAnswerArray($WEB_FORM_ID, $arrResultColumns, $arrResultAnswers, $arrResultAnswersSID, array("RESULT_ID" => $RESULT_ID));
			$arrResultAnswers = $arrResultAnswers[$RESULT_ID];

			// инициализируем массив значений-ответов
			$DB_VARS = array();
			while (list($key,$arQuestion)=each($arQuestions)):
				if ($arQuestion["ADDITIONAL"]!="Y")
				{
					$FIELD_SID = $arQuestion["SID"];
					if (is_array($arAnswers[$FIELD_SID]))
					{
						reset($arAnswers[$FIELD_SID]);
						while (list($key,$arAnswer)=each($arAnswers[$FIELD_SID])) :
							$arrResultAnswer = $arrResultAnswers[$arQuestion["ID"]][$arAnswer["ID"]];
							$FIELD_TYPE = $arAnswer["FIELD_TYPE"];
							switch ($FIELD_TYPE) :

								case "radio":
								case "dropdown":
									if (intval($arrResultAnswer["ANSWER_ID"])>0)
									{
										$fname = "form_".strtolower($FIELD_TYPE)."_".$FIELD_SID;
										$DB_VARS[$fname] = $arrResultAnswer["ANSWER_ID"];
									}
								break;

								case "checkbox":
								case "multiselect":
									if (intval($arrResultAnswer["ANSWER_ID"])>0)
									{
										$fname = "form_".strtolower($FIELD_TYPE)."_".$FIELD_SID;
										$DB_VARS[$fname][] = $arrResultAnswer["ANSWER_ID"];
									}
								break;

								case "text":
								case "password":
								case "textarea":
								case "date":
								case "email":
								case "url":
									if (strlen($arrResultAnswer["USER_TEXT"])>0)
									{
										$fname = "form_".strtolower($FIELD_TYPE)."_".$arAnswer["ID"];
										$DB_VARS[$fname] = $arrResultAnswer["USER_TEXT"];
									}
								break;

								case "image":
								case "file":
									if (intval($arrResultAnswer["USER_FILE_ID"])>0)
									{
										$fname = "form_".strtolower($FIELD_TYPE)."_".$arAnswer["ID"];
										$DB_VARS[$fname] = $arrResultAnswer["USER_FILE_ID"];
									}
								break;

							endswitch;
						endwhile;
					}
				}
				else
				{
					$FIELD_TYPE = $arQuestion["FIELD_TYPE"];
					$arrResultAnswer = $arrResultAnswers[$arQuestion["ID"]][0];
					switch ($FIELD_TYPE) :
						case "text":
							if (strlen($arrResultAnswer["USER_TEXT"])>0)
							{
								$fname = "form_textarea_ADDITIONAL_".$arQuestion["ID"];
								$DB_VARS[$fname] = $arrResultAnswer["USER_TEXT"];
							}
							break;
						case "integer":
							if (strlen($arrResultAnswer["USER_TEXT"])>0)
							{
								$fname = "form_text_ADDITIONAL_".$arQuestion["ID"];
								$DB_VARS[$fname] = $arrResultAnswer["USER_TEXT"];
							}
							break;
						case "date":
							$fname = "form_date_ADDITIONAL_".$arQuestion["ID"];
							$DB_VARS[$fname] = $arrResultAnswer["USER_TEXT"];
							break;
					endswitch;
				}
			endwhile;
			return $DB_VARS;
		}
	}

	// сохраняет в базе новый результат
	function Add($WEB_FORM_ID, $arrVALUES=false, $CHECK_RIGHTS="Y", $USER_ID=false)
	{
		$err_mess = (CAllFormResult::err_mess())."<br>Function: Add<br>Line: ";
		global $DB, $USER, $_REQUEST, $HTTP_POST_VARS, $HTTP_GET_VARS, $HTTP_POST_FILES, $strError;
		if ($arrVALUES===false) $arrVALUES = $_REQUEST;

		$WEB_FORM_ID = intval($WEB_FORM_ID);
		if ($WEB_FORM_ID>0)
		{
			// получаем данные по форме
			$WEB_FORM_ID = CForm::GetDataByID($WEB_FORM_ID, $arForm, $arQuestions, $arAnswers, $arDropDown, $arMultiSelect);
			$WEB_FORM_ID = intval($WEB_FORM_ID);
			// если поступивший ID формы корректный то
			if ($WEB_FORM_ID>0)
			{
				// проверяем права
				$F_RIGHT = CForm::GetPermission($WEB_FORM_ID);

				if (intval($F_RIGHT)>=10 || $CHECK_RIGHTS=="N")
				{
					if (intval($USER_ID)<=0)
					{
						$USER_AUTH = "N";
						$USER_ID = intval($_SESSION["SESS_LAST_USER_ID"]);
						if (intval($USER->GetID())>0)
						{
							$USER_AUTH = "Y";
							$USER_ID = intval($USER->GetID());
						}
					}
					else $USER_AUTH = "Y";

					// определим статус нового результата
					$fname = "status_".$arForm["SID"];
					$STATUS_ID = (intval($arrVALUES[$fname])<=0) ? CFormStatus::GetDefault($WEB_FORM_ID) : intval($arrVALUES[$fname]);

					if ($STATUS_ID<=0)
					{
						$strError .= GetMessage("FORM_STATUS_NOT_DEFINED")."<br>";
					} else { // если статус определён
						$arPerm = CFormStatus::GetPermissions($STATUS_ID);
						if (in_array("MOVE", $arPerm)) // есть право установить данный статус
						{
							// запоминаем сам результат
							$arFields = array(
								"TIMESTAMP_X"		=> $DB->GetNowFunction(),
								"DATE_CREATE"		=> $DB->GetNowFunction(),
								"STATUS_ID"			=> $STATUS_ID,
								"FORM_ID"			=> $WEB_FORM_ID,
								"USER_ID"			=> intval($USER_ID),
								"USER_AUTH"			=> "'".$USER_AUTH."'",
								"STAT_GUEST_ID"		=> intval($_SESSION["SESS_GUEST_ID"]),
								"STAT_SESSION_ID"	=> intval($_SESSION["SESS_SESSION_ID"]),
								);
							$RESULT_ID = $DB->Insert("b_form_result",$arFields, $err_mess.__LINE__);
						} else
							$strError .= GetMessage("FORM_ERROR_ACCESS_DENIED");
					}
					$RESULT_ID = intval($RESULT_ID);
					// если результат сохранился успешно то
					if ($RESULT_ID>0)
					{
						$arrANSWER_TEXT = array();
						$arrANSWER_VALUE = array();
						$arrUSER_TEXT = array();
						// проходим по вопросам
						while (list($key,$arQuestion)=each($arQuestions))
						{
							$FIELD_ID = $arQuestion["ID"];
							$FIELD_SID = $arQuestion["SID"];
							$radio = "N";
							$checkbox = "N";
							$multiselect = "N";
							$dropdown = "N";
							if (is_array($arAnswers[$FIELD_SID]))
							{
								// проходим по ответам
								reset($arAnswers[$FIELD_SID]);
								while (list($key,$arAnswer)=each($arAnswers[$FIELD_SID]))
								{
									$ANSWER_ID = 0;
									$FIELD_TYPE = $arAnswer["FIELD_TYPE"];
									$FIELD_PARAM = $arAnswer["FIELD_PARAM"];
									switch ($FIELD_TYPE) :

										case "radio":
										case "dropdown":

											if (($radio=="N" && $FIELD_TYPE=="radio") ||
												($dropdown=="N" && $FIELD_TYPE=="dropdown"))
											{
												$fname = "form_".$FIELD_TYPE."_".$FIELD_SID;
												$ANSWER_ID = intval($arrVALUES[$fname]);
												if ($ANSWER_ID>0)
												{
													$z = CFormAnswer::GetByID($ANSWER_ID);
													if ($zr = $z->Fetch())
													{
														$arFields = array(
															"RESULT_ID"			=> $RESULT_ID,
															"FORM_ID"			=> $WEB_FORM_ID,
															"FIELD_ID"			=> $FIELD_ID,
															"ANSWER_ID"			=> $ANSWER_ID,
															"ANSWER_TEXT"		=> trim($zr["MESSAGE"]),
															"ANSWER_VALUE"		=> $zr["VALUE"]
															);
														$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
														$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
														CFormResult::AddAnswer($arFields);
													}
													if ($FIELD_TYPE=="radio") $radio = "Y";
													if ($FIELD_TYPE=="dropdown") $dropdown = "Y";
												}
											}

										break;

										case "checkbox":
										case "multiselect":

											if (($checkbox=="N" && $FIELD_TYPE=="checkbox") ||
												($multiselect=="N" && $FIELD_TYPE=="multiselect"))
											{
												$fname = "form_".$FIELD_TYPE."_".$FIELD_SID;
												if (is_array($arrVALUES[$fname]) && count($arrVALUES[$fname])>0)
												{
													reset($arrVALUES[$fname]);
													foreach($arrVALUES[$fname] as $ANSWER_ID)
													{
														$ANSWER_ID = intval($ANSWER_ID);
														if ($ANSWER_ID>0)
														{
															$z = CFormAnswer::GetByID($ANSWER_ID);
															if ($zr = $z->Fetch())
															{
																$arFields = array(
																	"RESULT_ID"			=> $RESULT_ID,
																	"FORM_ID"			=> $WEB_FORM_ID,
																	"FIELD_ID"			=> $FIELD_ID,
																	"ANSWER_ID"			=> $ANSWER_ID,
																	"ANSWER_TEXT"		=> trim($zr["MESSAGE"]),
																	"ANSWER_VALUE"		=> $zr["VALUE"]
																	);
																$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
																$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
																CFormResult::AddAnswer($arFields);
															}
														}
													}
													if ($FIELD_TYPE=="checkbox") $checkbox = "Y";
													if ($FIELD_TYPE=="multiselect") $multiselect = "Y";
												}
											}

										break;

										case "text":
										case "textarea":
										case "password":
										case "email":
										case "url":

											$fname = "form_".$FIELD_TYPE."_".$arAnswer["ID"];
											$ANSWER_ID = intval($arAnswer["ID"]);
											$z = CFormAnswer::GetByID($ANSWER_ID);
											if ($zr = $z->Fetch())
											{
												$arFields = array(
													"RESULT_ID"			=> $RESULT_ID,
													"FORM_ID"			=> $WEB_FORM_ID,
													"FIELD_ID"			=> $FIELD_ID,
													"ANSWER_ID"			=> $ANSWER_ID,
													"ANSWER_TEXT"		=> trim($zr["MESSAGE"]),
													"ANSWER_VALUE"		=> $zr["VALUE"],
													"USER_TEXT"			=> $arrVALUES[$fname]
												);
												$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
												$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
												$arrUSER_TEXT[$FIELD_ID][] = ToUpper($arFields["USER_TEXT"]);
												CFormResult::AddAnswer($arFields);
											}

										break;

										case "date":

											$fname = "form_".$FIELD_TYPE."_".$arAnswer["ID"];
											$ANSWER_ID = intval($arAnswer["ID"]);
											$USER_DATE = $arrVALUES[$fname];
											if (CheckDateTime($USER_DATE))
											{
												$z = CFormAnswer::GetByID($ANSWER_ID);
												if ($zr = $z->Fetch())
												{
													$arFields = array(
														"RESULT_ID"			=> $RESULT_ID,
														"FORM_ID"			=> $WEB_FORM_ID,
														"FIELD_ID"			=> $FIELD_ID,
														"ANSWER_ID"			=> $ANSWER_ID,
														"ANSWER_TEXT"		=> trim($zr["MESSAGE"]),
														"ANSWER_VALUE"		=> $zr["VALUE"],
														"USER_DATE"			=> $USER_DATE,
														"USER_TEXT"			=> $USER_DATE
													);
													$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
													$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
													$arrUSER_TEXT[$FIELD_ID][] = ToUpper($arFields["USER_TEXT"]);
													CFormResult::AddAnswer($arFields);
												}
											}
											break;

										case "image":

											$fname = "form_".$FIELD_TYPE."_".$arAnswer["ID"];
											$ANSWER_ID = intval($arAnswer["ID"]);
											$arIMAGE = is_set($fname, $arrVALUES) ? $arrVALUES[$fname] : $HTTP_POST_FILES[$fname];
											$arIMAGE["MODULE_ID"] = "form";
											$fid = 0;
											if (strlen(CFile::CheckImageFile($arIMAGE))<=0)
											{
												if (strlen($arIMAGE["name"])>0)
												{
													$fid = CFile::SaveFile($arIMAGE, "form");
													$fid = intval($fid);
													if ($fid>0)
													{
														$z = CFormAnswer::GetByID($ANSWER_ID);
														if ($zr = $z->Fetch())
														{
															$arFields = array(
																"RESULT_ID"				=> $RESULT_ID,
																"FORM_ID"				=> $WEB_FORM_ID,
																"FIELD_ID"				=> $FIELD_ID,
																"ANSWER_ID"				=> $ANSWER_ID,
																"ANSWER_TEXT"			=> trim($zr["MESSAGE"]),
																"ANSWER_VALUE"			=> $zr["VALUE"],
																"USER_TEXT"				=> $arIMAGE["name"],
																"USER_FILE_ID"			=> $fid,
																"USER_FILE_IS_IMAGE"	=> "Y",
																"USER_FILE_NAME"		=> $arIMAGE["name"],
																"USER_FILE_SIZE"		=> $arIMAGE["size"],
															);
															$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
															$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
															$arrUSER_TEXT[$FIELD_ID][] = ToUpper($arFields["USER_TEXT"]);
															CFormResult::AddAnswer($arFields);
														}
													}
												}
											}

										break;

										case "file":

											$fname = "form_".$FIELD_TYPE."_".$arAnswer["ID"];
											$ANSWER_ID = intval($arAnswer["ID"]);
											$arFILE = is_set($fname, $arrVALUES) ? $arrVALUES[$fname] : $HTTP_POST_FILES[$fname];
											$arFILE["MODULE_ID"] = "form";

											if (strlen($arFILE["name"])>0)
											{
												$original_name = $arFILE["name"];
												$fid = 0;
												$max_size = COption::GetOptionString("form", "MAX_FILESIZE");
												$fes = COption::GetOptionString("form", "NOT_IMAGE_EXTENSION_SUFFIX");
												$arFILE["name"] .= $fes;
												$upload_dir = COption::GetOptionString("form", "NOT_IMAGE_UPLOAD_DIR");

												$fid = CFile::SaveFile($arFILE, $upload_dir, $max_size);
												$fid = intval($fid);
												if ($fid>0)
												{
													$md5 = md5(uniqid(mt_rand(), true).time());
													$z = CFormAnswer::GetByID($ANSWER_ID);
													if ($zr = $z->Fetch())
													{
														$arFields = array(
															"RESULT_ID"				=> $RESULT_ID,
															"FORM_ID"				=> $WEB_FORM_ID,
															"FIELD_ID"				=> $FIELD_ID,
															"ANSWER_ID"				=> $ANSWER_ID,
															"ANSWER_TEXT"			=> trim($zr["MESSAGE"]),
															"ANSWER_VALUE"			=> $zr["VALUE"],
															"USER_TEXT"				=> $original_name,
															"USER_FILE_ID"			=> $fid,
															"USER_FILE_NAME"		=> $original_name,
															"USER_FILE_IS_IMAGE"	=> "N",
															"USER_FILE_HASH"		=> $md5,
															"USER_FILE_SUFFIX"		=> $fes,
															"USER_FILE_SIZE"		=> $arFILE["size"],
														);
														$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
														$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
														$arrUSER_TEXT[$FIELD_ID][] = ToUpper($arFields["USER_TEXT"]);
														CFormResult::AddAnswer($arFields);
													}
												}
											}

										break;

									endswitch;
								}
								// обновляем поля для поиска
								$arrANSWER_TEXT_upd = $arrANSWER_TEXT[$FIELD_ID];
								$arrANSWER_VALUE_upd = $arrANSWER_VALUE[$FIELD_ID];
								$arrUSER_TEXT_upd = $arrUSER_TEXT[$FIELD_ID];
								TrimArr($arrANSWER_TEXT_upd);
								TrimArr($arrANSWER_VALUE_upd);
								TrimArr($arrUSER_TEXT_upd);
								if (is_array($arrANSWER_TEXT_upd)) $vl_ANSWER_TEXT = trim(implode(" ",$arrANSWER_TEXT_upd));
								if (is_array($arrANSWER_VALUE_upd)) $vl_ANSWER_VALUE = trim(implode(" ",$arrANSWER_VALUE_upd));
								if (is_array($arrUSER_TEXT_upd)) $vl_USER_TEXT = trim(implode(" ",$arrUSER_TEXT_upd));
								if (strlen($vl_ANSWER_TEXT)<=0) $vl_ANSWER_TEXT = false;
								if (strlen($vl_ANSWER_VALUE)<=0) $vl_ANSWER_VALUE = false;
								if (strlen($vl_USER_TEXT)<=0) $vl_USER_TEXT = false;
								$arFields = array(
									"ANSWER_TEXT_SEARCH"	=> $vl_ANSWER_TEXT,
									"ANSWER_VALUE_SEARCH"	=> $vl_ANSWER_VALUE,
									"USER_TEXT_SEARCH"		=> $vl_USER_TEXT
									);
								CFormResult::UpdateField($arFields, $RESULT_ID, $FIELD_ID);
							}
						}
						// вызываем обработчик на смену статуса после обновления
						CForm::ExecHandlerAfterChangeStatus($RESULT_ID, "ADD");
					}
				}
			}
		}
		return intval($RESULT_ID)>0 ? intval($RESULT_ID) : false;
	}

	// обновляет результат в базе
	function Update($RESULT_ID, $arrVALUES=false, $UPDATE_ADDITIONAL="N", $CHECK_RIGHTS="Y")
	{
		$err_mess = (CAllFormResult::err_mess())."<br>Function: Update<br>Line: ";
		global $DB, $USER, $_REQUEST, $HTTP_POST_VARS, $HTTP_GET_VARS, $HTTP_POST_FILES, $strError;
		if ($arrVALUES===false) $arrVALUES = $_REQUEST;

		InitBvar($UPDATE_ADDITIONAL);
		// проверяем есть ли в базе такой результат
		$RESULT_ID = intval($RESULT_ID);
		$z = CFormResult::GetByID($RESULT_ID);
		if ($zr=$z->Fetch())
		{
			$arrResult = $zr;
			$additional = ($UPDATE_ADDITIONAL=="Y") ? "ALL" : "N";
			// получаем данные по форме
			$WEB_FORM_ID = CForm::GetDataByID($arrResult["FORM_ID"], $arForm, $arQuestions, $arAnswers, $arDropDown, $arMultiSelect, $additional);
			if ($WEB_FORM_ID>0)
			{
				// проверим права на форму
				$F_RIGHT = ($CHECK_RIGHTS!="Y") ? 30 : intval(CForm::GetPermission($WEB_FORM_ID));
				if ($F_RIGHT>=20 || ($F_RIGHT>=15 && $arrResult["USER_ID"])==$USER->GetID())
				{
					// проверим права на результат (на его статус)
					$arrRESULT_PERMISSION = ($CHECK_RIGHTS!="Y") ? CFormStatus::GetMaxPermissions() : CFormResult::GetPermissions($RESULT_ID, $v);

					// если право есть то
					if (in_array("EDIT", $arrRESULT_PERMISSION))
					{
						// обновляем результат
						$arFields = array("TIMESTAMP_X"	=> $DB->GetNowFunction());
						$fname = "status_".$arForm["SID"];
						$STATUS_ID = intval($arrVALUES[$fname]);

						// если статус определен то
						if (intval($STATUS_ID)>0)
						{
							// проверим права на статус результата
							$arrNEW_STATUS_PERMISSION = ($CHECK_RIGHTS!="Y") ? CFormStatus::GetMaxPermissions() : CFormStatus::GetPermissions($STATUS_ID);

							// если право есть то
							if (in_array("MOVE",$arrNEW_STATUS_PERMISSION))
							{
								// присваиваем его
								$arFields["STATUS_ID"] = intval($arrVALUES[$fname]);
							}
						}
						// вызываем обработчик на смену статуса перед обновлением
						CForm::ExecHandlerBeforeChangeStatus($RESULT_ID, "UPDATE", $arFields["STATUS_ID"]);

						$rows = $DB->Update("b_form_result",$arFields,"WHERE ID='".$RESULT_ID."'",$err_mess.__LINE__);
						// если результат обновился успешно то
						if (intval($rows)>0)
						{
							$arrException = array();

							// собираем информацию по файлам
							$arrFILES = array();
							$strSql = "
								SELECT
									ANSWER_ID,
									USER_FILE_ID,
									USER_FILE_NAME,
									USER_FILE_IS_IMAGE,
									USER_FILE_HASH,
									USER_FILE_SUFFIX,
									USER_FILE_SIZE
								FROM
									b_form_result_answer
								WHERE
									RESULT_ID = $RESULT_ID
								and USER_FILE_ID>0
								";
							$q = $DB->Query($strSql,false,$err_mess.__LINE__);
							while ($qr = $q->Fetch()) $arrFILES[$qr["ANSWER_ID"]] = $qr;

							if (is_array($arrVALUES["ARR_CLS"])) $arrException = array_merge($arrException, $arrVALUES["ARR_CLS"]);

							// удаляем все вопросы и ответы на них для данного результата
							CFormResult::Reset($RESULT_ID, false, $UPDATE_ADDITIONAL, $arrException);

							// проходим по вопросам и полям
							while (list($key,$arQuestion)=each($arQuestions))
							{
								$FIELD_ID = $arQuestion["ID"];
								if (is_array($arrException) && count($arrException)>0)
								{
									if (in_array($FIELD_ID, $arrException)) continue;
								}
								$FIELD_SID = $arQuestion["SID"];
								if ($arQuestion["ADDITIONAL"]!="Y")
								{
									// обновляем вопросы формы
									$arrANSWER_TEXT = array();
									$arrANSWER_VALUE = array();
									$arrUSER_TEXT = array();
									$radio = "N";
									$checkbox = "N";
									$multiselect = "N";
									$dropdown = "N";
									// проходим по ответам
									if (is_array($arAnswers[$FIELD_SID]))
									{
										reset($arAnswers[$FIELD_SID]);
										while (list($key,$arAnswer)=each($arAnswers[$FIELD_SID]))
										{
											$ANSWER_ID = 0;
											$FIELD_TYPE = $arAnswer["FIELD_TYPE"];
											$FIELD_PARAM = $arAnswer["FIELD_PARAM"];
											switch ($FIELD_TYPE) :

												case "radio":
												case "dropdown":

													if (($radio=="N" && $FIELD_TYPE=="radio") ||
														($dropdown=="N" && $FIELD_TYPE=="dropdown"))
													{
														$fname = "form_".$FIELD_TYPE."_".$FIELD_SID;
														$ANSWER_ID = intval($arrVALUES[$fname]);
														if ($ANSWER_ID>0)
														{
															$z = CFormAnswer::GetByID($ANSWER_ID);
															if ($zr = $z->Fetch())
															{
																$arFields = array(
																	"RESULT_ID"			=> $RESULT_ID,
																	"FORM_ID"			=> $WEB_FORM_ID,
																	"FIELD_ID"			=> $FIELD_ID,
																	"ANSWER_ID"			=> $ANSWER_ID,
																	"ANSWER_TEXT"		=> trim($zr["MESSAGE"]),
																	"ANSWER_VALUE"		=> $zr["VALUE"]
																);
																$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
																$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
																CFormResult::AddAnswer($arFields);
															}
															if ($FIELD_TYPE=="radio") $radio = "Y";
															if ($FIELD_TYPE=="dropdown") $dropdown = "Y";
														}
													}

												break;

												case "checkbox":
												case "multiselect":

													if (($checkbox=="N" && $FIELD_TYPE=="checkbox") ||
														($multiselect=="N" && $FIELD_TYPE=="multiselect"))
													{
														$fname = "form_".$FIELD_TYPE."_".$FIELD_SID;
														if (is_array($arrVALUES[$fname]) && count($arrVALUES[$fname])>0)
														{
															reset($arrVALUES[$fname]);
															foreach($arrVALUES[$fname] as $ANSWER_ID)
															{
																$ANSWER_ID = intval($ANSWER_ID);
																if ($ANSWER_ID>0)
																{
																	$z = CFormAnswer::GetByID($ANSWER_ID);
																	if ($zr = $z->Fetch())
																	{
																		$arFields = array(
																		"RESULT_ID"			=> $RESULT_ID,
																		"FORM_ID"			=> $WEB_FORM_ID,
																		"FIELD_ID"			=> $FIELD_ID,
																		"ANSWER_ID"			=> $ANSWER_ID,
																		"ANSWER_TEXT"		=> trim($zr["MESSAGE"]),
																		"ANSWER_VALUE"		=> $zr["VALUE"]
																		);
																		$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
																		$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
																		CFormResult::AddAnswer($arFields);
																	}
																}
															}
															if ($FIELD_TYPE=="checkbox") $checkbox = "Y";
															if ($FIELD_TYPE=="multiselect") $multiselect = "Y";
														}
													}

												break;

												case "text":
												case "textarea":
												case "password":
												case "email":
												case "url":
													$fname = "form_".$FIELD_TYPE."_".$arAnswer["ID"];
													$ANSWER_ID = intval($arAnswer["ID"]);
													$z = CFormAnswer::GetByID($ANSWER_ID);
													if ($zr = $z->Fetch())
													{
														$arFields = array(
															"RESULT_ID"			=> $RESULT_ID,
															"FORM_ID"			=> $WEB_FORM_ID,
															"FIELD_ID"			=> $FIELD_ID,
															"ANSWER_ID"			=> $ANSWER_ID,
															"ANSWER_TEXT"		=> trim($zr["MESSAGE"]),
															"ANSWER_VALUE"		=> $zr["VALUE"],
															"USER_TEXT"			=> $arrVALUES[$fname]
														);
														$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
														$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
														$arrUSER_TEXT[$FIELD_ID][] = ToUpper($arFields["USER_TEXT"]);
														CFormResult::AddAnswer($arFields);
													}

												break;

												case "date":

													$fname = "form_".$FIELD_TYPE."_".$arAnswer["ID"];
													$ANSWER_ID = intval($arAnswer["ID"]);
													$USER_DATE = $arrVALUES[$fname];
													if (CheckDateTime($USER_DATE))
													{
														$z = CFormAnswer::GetByID($ANSWER_ID);
														if ($zr = $z->Fetch())
														{
															$arFields = array(
																"RESULT_ID"			=> $RESULT_ID,
																"FORM_ID"			=> $WEB_FORM_ID,
																"FIELD_ID"			=> $FIELD_ID,
																"ANSWER_ID"			=> $ANSWER_ID,
																"ANSWER_TEXT"		=> trim($zr["MESSAGE"]),
																"ANSWER_VALUE"		=> $zr["VALUE"],
																"USER_DATE"			=> $USER_DATE,
																"USER_TEXT"			=> $USER_DATE
															);
															$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
															$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
															$arrUSER_TEXT[$FIELD_ID][] = ToUpper($arFields["USER_TEXT"]);
															CFormResult::AddAnswer($arFields);
														}
													}
													break;

												case "image":

													$fname = "form_".$FIELD_TYPE."_".$arAnswer["ID"];
													$ANSWER_ID = intval($arAnswer["ID"]);
													$arIMAGE = is_set($fname, $arrVALUES) ? $arrVALUES[$fname] : $HTTP_POST_FILES[$fname];
													$arIMAGE["old_file"] = $arrFILES[$ANSWER_ID]["USER_FILE_ID"];
													$arIMAGE["del"] = $arrVALUES[$fname."_del"];
													$arIMAGE["MODULE_ID"] = "form";
													$fid = 0;
													if (strlen($arIMAGE["name"])>0 || strlen($arIMAGE["del"])>0)
													{
														$new_file="Y";
														if (strlen($arIMAGE["del"])>0 || strlen(CFile::CheckImageFile($arIMAGE))<=0)
														{
															$fid = CFile::SaveFile($arIMAGE, "form");
														}
													}
													else $fid = $arrFILES[$ANSWER_ID]["USER_FILE_ID"];

													$fid = intval($fid);
													if ($fid>0)
													{
														$z = CFormAnswer::GetByID($ANSWER_ID);
														if ($zr = $z->Fetch())
														{
															$arFields = array(
																"RESULT_ID"				=> $RESULT_ID,
																"FORM_ID"				=> $WEB_FORM_ID,
																"FIELD_ID"				=> $FIELD_ID,
																"ANSWER_ID"				=> $ANSWER_ID,
																"ANSWER_TEXT"			=> trim($zr["MESSAGE"]),
																"ANSWER_VALUE"			=> $zr["VALUE"],
																"USER_FILE_ID"			=> $fid,
																"USER_FILE_IS_IMAGE"	=> "Y"
																);
															if ($new_file=="Y")
															{
																$arFields["USER_FILE_NAME"] = $arIMAGE["name"];
																$arFields["USER_FILE_SIZE"] = $arIMAGE["size"];
															}
															else
															{
																$arFields["USER_FILE_NAME"] = $arrFILES[$ANSWER_ID]["USER_FILE_NAME"];
																$arFields["USER_FILE_SIZE"] = $arrFILES[$ANSWER_ID]["USER_FILE_SIZE"];
															}
															$arFields["USER_TEXT"] = $arFields["USER_FILE_NAME"];

															$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
															$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
															$arrUSER_TEXT[$FIELD_ID][] = ToUpper($arFields["USER_TEXT"]);
															CFormResult::AddAnswer($arFields);
														}
													}

												break;

												case "file":

													$fname = "form_".$FIELD_TYPE."_".$arAnswer["ID"];
													$ANSWER_ID = intval($arAnswer["ID"]);
													$arFILE = is_set($fname, $arrVALUES) ? $arrVALUES[$fname] : $HTTP_POST_FILES[$fname];
													$arFILE["old_file"] = $arrFILES[$ANSWER_ID]["USER_FILE_ID"];
													$arFILE["del"] = $arrVALUES[$fname."_del"];
													$arFILE["MODULE_ID"] = "form";
													$new_file="N";
													$fid = 0;
													if (strlen(trim($arFILE["name"]))>0 || strlen(trim($arFILE["del"]))>0)
													{
														$new_file="Y";
														$original_name = $arFILE["name"];
														$max_size = COption::GetOptionString("form", "MAX_FILESIZE");
														$suffix = COption::GetOptionString("form","NOT_IMAGE_EXTENSION_SUFFIX");
														$arFILE["name"] .= $suffix;
														$upload_dir = COption::GetOptionString("form", "NOT_IMAGE_UPLOAD_DIR");

														$fid = CFile::SaveFile($arFILE, $upload_dir, $max_size);
													}
													else $fid = $arrFILES[$ANSWER_ID]["USER_FILE_ID"];

													$fid = intval($fid);

													if ($fid>0)
													{
														$z = CFormAnswer::GetByID($ANSWER_ID);
														if ($zr = $z->Fetch())
														{
															$arFields = array(
																"RESULT_ID"				=> $RESULT_ID,
																"FORM_ID"				=> $WEB_FORM_ID,
																"FIELD_ID"				=> $FIELD_ID,
																"ANSWER_ID"				=> $ANSWER_ID,
																"ANSWER_TEXT"			=> trim($zr["MESSAGE"]),
																"ANSWER_VALUE"			=> $zr["VALUE"],
																"USER_FILE_ID"			=> $fid,
															);
															if ($new_file=="Y")
															{
																$arFields["USER_FILE_NAME"] = $original_name;
																$arFields["USER_FILE_IS_IMAGE"] = "N";
																$arFields["USER_FILE_HASH"] = md5(uniqid(mt_rand(), true).time());
																$arFields["USER_FILE_SUFFIX"] = $suffix;
																$arFields["USER_FILE_SIZE"] = $arFILE["size"];
															}
															else
															{
																$arFields["USER_FILE_NAME"] = $arrFILES[$ANSWER_ID]["USER_FILE_NAME"];
																$arFields["USER_FILE_IS_IMAGE"] = $arrFILES[$ANSWER_ID]["USER_FILE_IS_IMAGE"];
																$arFields["USER_FILE_HASH"] = $arrFILES[$ANSWER_ID]["USER_FILE_HASH"];
																$arFields["USER_FILE_SUFFIX"] = $arrFILES[$ANSWER_ID]["USER_FILE_SUFFIX"];
																$arFields["USER_FILE_SIZE"] = $arrFILES[$ANSWER_ID]["USER_FILE_SIZE"];
															}
															$arFields["USER_TEXT"] = $arFields["USER_FILE_NAME"];

															$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
															$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
															$arrUSER_TEXT[$FIELD_ID][] = ToUpper($arFields["USER_TEXT"]);
															CFormResult::AddAnswer($arFields);
														}
													}

												break;

											endswitch;
										}
									}
									// обновляем поля для поиска
									$arrANSWER_TEXT_upd = $arrANSWER_TEXT[$FIELD_ID];
									$arrANSWER_VALUE_upd = $arrANSWER_VALUE[$FIELD_ID];
									$arrUSER_TEXT_upd = $arrUSER_TEXT[$FIELD_ID];
									TrimArr($arrANSWER_TEXT_upd);
									TrimArr($arrANSWER_VALUE_upd);
									TrimArr($arrUSER_TEXT_upd);
									if (is_array($arrANSWER_TEXT_upd)) $vl_ANSWER_TEXT = trim(implode(" ",$arrANSWER_TEXT_upd));
									if (is_array($arrANSWER_VALUE_upd)) $vl_ANSWER_VALUE = trim(implode(" ",$arrANSWER_VALUE_upd));
									if (is_array($arrUSER_TEXT_upd)) $vl_USER_TEXT = trim(implode(" ",$arrUSER_TEXT_upd));
									if (strlen($vl_ANSWER_TEXT)<=0) $vl_ANSWER_TEXT = false;
									if (strlen($vl_ANSWER_VALUE)<=0) $vl_ANSWER_VALUE = false;
									if (strlen($vl_USER_TEXT)<=0) $vl_USER_TEXT = false;
									$arFields = array(
										"ANSWER_TEXT_SEARCH"	=> $vl_ANSWER_TEXT,
										"ANSWER_VALUE_SEARCH"	=> $vl_ANSWER_VALUE,
										"USER_TEXT_SEARCH"		=> $vl_USER_TEXT
										);
									CFormResult::UpdateField($arFields, $RESULT_ID, $FIELD_ID);
								}
								else // обновляем дополнительные поля
								{
									$FIELD_TYPE = $arQuestion["FIELD_TYPE"];
									switch ($FIELD_TYPE) :

										case "text":
											$fname = "form_textarea_ADDITIONAL_".$arQuestion["ID"];
											$arFields = array(
												"RESULT_ID"			=> $RESULT_ID,
												"FORM_ID"			=> $WEB_FORM_ID,
												"FIELD_ID"			=> $FIELD_ID,
												"USER_TEXT"			=> $arrVALUES[$fname],
												"USER_TEXT_SEARCH"	=> ToUpper($arrVALUES[$fname])
											);
											CFormResult::AddAnswer($arFields);
											break;

										case "integer":

											$fname = "form_text_ADDITIONAL_".$arQuestion["ID"];
											$arFields = array(
												"RESULT_ID"			=> $RESULT_ID,
												"FORM_ID"			=> $WEB_FORM_ID,
												"FIELD_ID"			=> $FIELD_ID,
												"USER_TEXT"			=> $arrVALUES[$fname],
												"USER_TEXT_SEARCH"	=> ToUpper($arrVALUES[$fname])
											);
											CFormResult::AddAnswer($arFields);

										break;

										case "date":

											$fname = "form_date_ADDITIONAL_".$arQuestion["ID"];
											$USER_DATE = $arrVALUES[$fname];
											if (CheckDateTime($USER_DATE))
											{
												$arFields = array(
													"RESULT_ID"			=> $RESULT_ID,
													"FORM_ID"			=> $WEB_FORM_ID,
													"FIELD_ID"			=> $FIELD_ID,
													"USER_DATE"			=> $USER_DATE,
													"USER_TEXT"			=> $USER_DATE,
													"USER_TEXT_SEARCH"	=> ToUpper($USER_DATE)
												);
												CFormResult::AddAnswer($arFields);
											}

										break;
									endswitch;
								}
							}
							// вызываем обработчик на смену статуса после обновления
							CForm::ExecHandlerAfterChangeStatus($RESULT_ID, "UPDATE");
							return true;
						}
					}
				}
			}
		}
		return false;
	}

	// сохраняет значение вопроса/поля для результата
	function SetField($RESULT_ID, $FIELD_SID, $VALUE=false)
	{
		global $DB, $strError;
		$err_mess = (CAllFormResult::err_mess())."<br>Function: SetField<br>Line: ";
		$RESULT_ID = intval($RESULT_ID);
		if (intval($RESULT_ID)>0)
		{
			// получим ID веб-формы
			$strSql = "
				SELECT
					FORM_ID
				FROM
					b_form_result
				WHERE
					ID = $RESULT_ID
				";
			$z = $DB->Query($strSql, false, $err_mess.__LINE__);
			$zr = $z->Fetch();
			$WEB_FORM_ID = $zr["FORM_ID"];
			if (intval($WEB_FORM_ID)>0)
			{
				// получим данные по вопросу/полю
				$strSql = "
					SELECT
						ID,
						FIELD_TYPE,
						ADDITIONAL
					FROM
						b_form_field
					WHERE
						FORM_ID = $WEB_FORM_ID
					and SID = '".$DB->ForSql($FIELD_SID,50)."'
					";
				$q = $DB->Query($strSql, false, $err_mess.__LINE__);
				if ($arField = $q->Fetch())
				{
					$FIELD_ID = $arField["ID"];
					$IS_FIELD = ($arField["ADDITIONAL"]=="Y") ? true : false;

					// если это поле веб-формы то
					if ($IS_FIELD)
					{
						// удаляем значения по данному полю у данного результата
						$strSql = "
							DELETE FROM
								b_form_result_answer
							WHERE
								RESULT_ID = $RESULT_ID
							and FIELD_ID = $FIELD_ID
							";
						//echo "<pre>".$strSql."</pre>";
						$DB->Query($strSql, false, $err_mess.__LINE__);

						if (strlen($VALUE)>0)
						{

							$FIELD_TYPE = $arField["FIELD_TYPE"];
							switch ($FIELD_TYPE) :

								case "text":
								case "integer":

									$arFields = array(
										"RESULT_ID"			=> $RESULT_ID,
										"FORM_ID"			=> $WEB_FORM_ID,
										"FIELD_ID"			=> $FIELD_ID,
										"USER_TEXT"			=> $VALUE,
										"USER_TEXT_SEARCH"	=> ToUpper($VALUE)
										);
									CFormResult::AddAnswer($arFields);
								break;

								case "date":

									if (CheckDateTime($VALUE))
									{
										$arFields = array(
											"RESULT_ID"			=> $RESULT_ID,
											"FORM_ID"			=> $WEB_FORM_ID,
											"FIELD_ID"			=> $FIELD_ID,
											"USER_DATE"			=> $VALUE,
											"USER_TEXT"			=> $VALUE,
											"USER_TEXT_SEARCH"	=> ToUpper($VALUE)
											);
										CFormResult::AddAnswer($arFields);
									}
								break;

							endswitch;
						}
					}
					else // иначе это вопрос веб-формы
					{
						// выберем все файлы
						$strSql = "
							SELECT
								USER_FILE_ID
							FROM
								b_form_result_answer
							WHERE
								RESULT_ID = $RESULT_ID
							and FIELD_ID = $FIELD_ID
							and USER_FILE_ID>0
							";
						$rsFiles = $DB->Query($strSql, false, $err_mess.__LINE__);
						while ($arFile = $rsFiles->Fetch()) CFile::Delete($arFile["USER_FILE_ID"]);

						// удалим все значения ответов по данному вопросу у данного результата
						$strSql = "
							DELETE FROM
								b_form_result_answer
							WHERE
								RESULT_ID = $RESULT_ID
							and FIELD_ID = $FIELD_ID
							";
						$DB->Query($strSql, false, $err_mess.__LINE__);

						if (is_array($VALUE) && count($VALUE)>0)
						{
							$arrANSWER_TEXT = array();
							$arrANSWER_VALUE = array();
							$arrUSER_TEXT = array();
							while(list($ANSWER_ID, $value) = each($VALUE))
							{
								$rsAnswer = CFormAnswer::GetByID($ANSWER_ID);
								if ($arAnswer = $rsAnswer->Fetch())
								{
									switch ($arAnswer["FIELD_TYPE"]) :

										case "radio":
										case "dropdown":
										case "checkbox":
										case "multiselect":

											$arFields = array(
												"RESULT_ID"				=> $RESULT_ID,
												"FORM_ID"				=> $WEB_FORM_ID,
												"FIELD_ID"				=> $FIELD_ID,
												"ANSWER_ID"				=> $ANSWER_ID,
												"ANSWER_TEXT"			=> trim($arAnswer["MESSAGE"]),
												"ANSWER_VALUE"			=> $arAnswer["VALUE"],
											);
											CFormResult::AddAnswer($arFields);
											$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
											$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);

										break;

										case "text":
										case "textarea":
										case "password":
										case "email":
										case "url":

											$arFields = array(
												"RESULT_ID"				=> $RESULT_ID,
												"FORM_ID"				=> $WEB_FORM_ID,
												"FIELD_ID"				=> $FIELD_ID,
												"ANSWER_ID"				=> $ANSWER_ID,
												"ANSWER_TEXT"			=> trim($arAnswer["MESSAGE"]),
												"ANSWER_VALUE"			=> $arAnswer["VALUE"],
												"USER_TEXT"				=> $value,
											);
											CFormResult::AddAnswer($arFields);
											$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
											$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
											$arrUSER_TEXT[$FIELD_ID][] = ToUpper($arFields["USER_TEXT"]);

										break;

										case "date":

											if (CheckDateTime($value))
											{
												$arFields = array(
													"RESULT_ID"				=> $RESULT_ID,
													"FORM_ID"				=> $WEB_FORM_ID,
													"FIELD_ID"				=> $FIELD_ID,
													"ANSWER_ID"				=> $ANSWER_ID,
													"ANSWER_TEXT"			=> trim($arAnswer["MESSAGE"]),
													"ANSWER_VALUE"			=> $arAnswer["VALUE"],
													"USER_TEXT"				=> $value,
													"USER_DATE"				=> $value
												);
												CFormResult::AddAnswer($arFields);
												$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
												$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
												$arrUSER_TEXT[$FIELD_ID][] = ToUpper($arFields["USER_TEXT"]);
											}

										break;

										case "image":

											$arIMAGE = $value;
											if (is_array($arIMAGE) && count($arIMAGE)>0)
											{
												$arIMAGE["MODULE_ID"] = "form";
												if (strlen(CFile::CheckImageFile($arIMAGE))<=0)
												{
													$fid = CFile::SaveFile($arIMAGE, "form");
													if (intval($fid)>0)
													{
														$arFields = array(
															"RESULT_ID"				=> $RESULT_ID,
															"FORM_ID"				=> $WEB_FORM_ID,
															"FIELD_ID"				=> $FIELD_ID,
															"ANSWER_ID"				=> $ANSWER_ID,
															"ANSWER_TEXT"			=> trim($arAnswer["MESSAGE"]),
															"ANSWER_VALUE"			=> $arAnswer["VALUE"],
															"USER_FILE_ID"			=> $fid,
															"USER_FILE_IS_IMAGE"	=> "Y",
															"USER_FILE_NAME"		=> $arIMAGE["name"],
															"USER_FILE_SIZE"		=> $arIMAGE["size"],
															"USER_TEXT"				=> $arIMAGE["name"]
															);
														CFormResult::AddAnswer($arFields);
														$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
														$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
														$arrUSER_TEXT[$FIELD_ID][] = ToUpper($arFields["USER_TEXT"]);
													}
												}
											}

										break;

										case "file":

											$arFILE = $value;
											if (is_array($arFILE) && count($arFILE)>0)
											{
												$arFILE["MODULE_ID"] = "form";
												$original_name = $arFILE["name"];
												$max_size = COption::GetOptionString("form", "MAX_FILESIZE");
												$suffix = COption::GetOptionString("form","NOT_IMAGE_EXTENSION_SUFFIX");
												$arFILE["name"] .= $suffix;
												$upload_dir = COption::GetOptionString("form", "NOT_IMAGE_UPLOAD_DIR");
												$fid = CFile::SaveFile($arFILE, $upload_dir, $max_size);
												if (intval($fid)>0)
												{
													$arFields = array(
														"RESULT_ID"				=> $RESULT_ID,
														"FORM_ID"				=> $WEB_FORM_ID,
														"FIELD_ID"				=> $FIELD_ID,
														"ANSWER_ID"				=> $ANSWER_ID,
														"ANSWER_TEXT"			=> trim($arAnswer["MESSAGE"]),
														"ANSWER_VALUE"			=> $arAnswer["VALUE"],
														"USER_FILE_ID"			=> $fid,
														"USER_FILE_IS_IMAGE"	=> "N",
														"USER_FILE_NAME"		=> $original_name,
														"USER_FILE_HASH"		=> md5(uniqid(mt_rand(), true).time()),
														"USER_FILE_SIZE"		=> $arFILE["size"],
														"USER_FILE_SUFFIX"		=> $suffix,
														"USER_TEXT"				=> $original_name,
														);
													CFormResult::AddAnswer($arFields);
													$arrANSWER_TEXT[$FIELD_ID][] = ToUpper($arFields["ANSWER_TEXT"]);
													$arrANSWER_VALUE[$FIELD_ID][] = ToUpper($arFields["ANSWER_VALUE"]);
													$arrUSER_TEXT[$FIELD_ID][] = ToUpper($arFields["USER_TEXT"]);
												}
											}

										break;

									endswitch;
								}
							}
							// обновляем поля для поиска
							$arrANSWER_TEXT_upd = $arrANSWER_TEXT[$FIELD_ID];
							$arrANSWER_VALUE_upd = $arrANSWER_VALUE[$FIELD_ID];
							$arrUSER_TEXT_upd = $arrUSER_TEXT[$FIELD_ID];
							TrimArr($arrANSWER_TEXT_upd);
							TrimArr($arrANSWER_VALUE_upd);
							TrimArr($arrUSER_TEXT_upd);
							if (is_array($arrANSWER_TEXT_upd)) $vl_ANSWER_TEXT = trim(implode(" ",$arrANSWER_TEXT_upd));
							if (is_array($arrANSWER_VALUE_upd)) $vl_ANSWER_VALUE = trim(implode(" ",$arrANSWER_VALUE_upd));
							if (is_array($arrUSER_TEXT_upd)) $vl_USER_TEXT = trim(implode(" ",$arrUSER_TEXT_upd));
							if (strlen($vl_ANSWER_TEXT)<=0) $vl_ANSWER_TEXT = false;
							if (strlen($vl_ANSWER_VALUE)<=0) $vl_ANSWER_VALUE = false;
							if (strlen($vl_USER_TEXT)<=0) $vl_USER_TEXT = false;
							$arFields = array(
								"ANSWER_TEXT_SEARCH"	=> $vl_ANSWER_TEXT,
								"ANSWER_VALUE_SEARCH"	=> $vl_ANSWER_VALUE,
								"USER_TEXT_SEARCH"		=> $vl_USER_TEXT
								);
							CFormResult::UpdateField($arFields, $RESULT_ID, $FIELD_ID);
						}
					}
					return true;
				}
			}
		}
		return false;
	}

	// удаляет результат
	function Delete($RESULT_ID, $CHECK_RIGHTS="Y")
	{
		global $DB, $USER, $strError;
		$err_mess = (CAllFormResult::err_mess())."<br>Function: Delete<br>Line: ";
		$RESULT_ID = intval($RESULT_ID);
		$strSql = "SELECT FORM_ID FROM b_form_result WHERE ID='".$RESULT_ID."'";
		$q = $DB->Query($strSql,false,$err_mess.__LINE__);
		if ($qr = $q->Fetch())
		{
			// проверим общие права
			$F_RIGHT = ($CHECK_RIGHTS!="Y") ? 20 : CForm::GetPermission($qr["FORM_ID"]);
			if ($F_RIGHT>=20) $RIGHT_OK = "Y";
			else
			{
				$strSql = "SELECT USER_ID FROM b_form_result WHERE ID='".$RESULT_ID."'";
				$z = $DB->Query($strSql,false,$err_mess.__LINE__);
				$zr = $z->Fetch();
				if ($F_RIGHT>=15 && intval($USER->GetID())==$zr["USER_ID"]) $RIGHT_OK = "Y";
			}
			if ($RIGHT_OK=="Y")
			{
				// проверим право на результат в зависимости от статуса результата
				$arrRESULT_PERMISSION = CFormResult::GetPermissions($RESULT_ID, $v);
				if (in_array("DELETE", $arrRESULT_PERMISSION)) // имеем право на удаление
				{
					CForm::ExecHandlerBeforeChangeStatus($RESULT_ID, "DELETE");
					if (CFormResult::Reset($RESULT_ID, true, "Y"))
					{
						// удаляем результат
						$DB->Query("DELETE FROM b_form_result WHERE ID='$RESULT_ID'", false, $err_mess.__LINE__);
						return true;
					}
				}
			}
			else $strError .= GetMessage("FORM_ERROR_ACCESS_DENIED")."<br>";
		}
		else $strError .= GetMessage("FORM_ERROR_RESULT_NOT_FOUND")."<br>";
		return false;
	}

	// обнуляем результат
	function Reset($RESULT_ID, $DELETE_FILES=true, $DELETE_ADDITIONAL="N", $arrException=array())
	{
		global $DB, $strError;
		$err_mess = (CAllFormResult::err_mess())."<br>Function: Reset<br>Line: ";
		$RESULT_ID = intval($RESULT_ID);

		if (is_array($arrException) && count($arrException)>0)
		{
			$strExc = implode(",",$arrException);
		}

		if ($DELETE_FILES)
		{
			$sqlExc = "";
			if (strlen($strExc)>0) $sqlExc = " and FIELD_ID not in ($strExc) ";
			// удаляем файлы результатов
			$strSql = "SELECT USER_FILE_ID, ANSWER_ID FROM b_form_result_answer WHERE RESULT_ID='$RESULT_ID' and USER_FILE_ID>0 $sqlExc";
			$z = $DB->Query($strSql, false, $err_mess.__LINE__);
			while ($zr = $z->Fetch()) CFile::Delete($zr["USER_FILE_ID"]);
		}

		// если надо поля то
		if ($DELETE_ADDITIONAL=="Y")
		{
			// удаляем все вместе с вопросами
			$sqlExc = "";
			if (strlen($strExc)>0) $sqlExc = " and FIELD_ID not in ($strExc) ";
			$DB->Query("DELETE FROM b_form_result_answer WHERE RESULT_ID='$RESULT_ID' $sqlExc", false, $err_mess.__LINE__);
		}
		else // иначе
		{
			$sqlExc = "";
			// удаляем только вопросы
			if (strlen($strExc)>0) $sqlExc = "and F.ID not in (".$strExc.")";
			$strSql = "
				SELECT
					F.ID
				FROM
					b_form_result R,
					b_form_field F
				WHERE
					R.ID = $RESULT_ID
				and F.FORM_ID = R.FORM_ID
				and F.ADDITIONAL = 'N'
				$sqlExc
				";
			$z = $DB->Query($strSql, false, $err_mess.__LINE__);
			while ($zr=$z->Fetch()) $arrD[] = $zr["ID"];
			if (is_array($arrD) && count($arrD)>0) $strD = implode(",",$arrD);
			if (strlen($strD)>0)
			{
				$DB->Query("DELETE FROM b_form_result_answer WHERE RESULT_ID='$RESULT_ID' and FIELD_ID in ($strD)", false, $err_mess.__LINE__);
			}
		}
		return true;
	}

	// функция меняет статус результата
	function SetStatus($RESULT_ID, $NEW_STATUS_ID, $CHECK_RIGHTS="Y")
	{
		$err_mess = (CAllFormResult::err_mess())."<br>Function: SetStatus<br>Line: ";
		global $DB, $USER, $strError;
		$NEW_STATUS_ID = intval($NEW_STATUS_ID);
		$RESULT_ID = intval($RESULT_ID);

		$strSql = "SELECT USER_ID, FORM_ID FROM b_form_result WHERE ID='".$RESULT_ID."'";
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		if ($zr = $z->Fetch())
		{
			$WEB_FORM_ID = intval($zr["FORM_ID"]);

			// проверка прав
			$RIGHT_OK = "N";
			if ($CHECK_RIGHTS!="Y") $RIGHT_OK="Y";
			else
			{
				// права на форму
				$F_RIGHT = CForm::GetPermission($WEB_FORM_ID);
				if ($F_RIGHT>=20 || ($F_RIGHT>=15 && $USER->GetID()==$zr["USER_ID"]))
				{
					// права на результат
					$arrRESULT_PERMISSION = CFormResult::GetPermissions($RESULT_ID, $v);

					// права на новый статус
					$arrNEW_STATUS_PERMISSION = CFormStatus::GetPermissions($NEW_STATUS_ID);

					// если имеем право редактировать данный результат и
					// имеем право перевести этот результат в новый статус
					if (in_array("EDIT", $arrRESULT_PERMISSION) && in_array("MOVE", $arrNEW_STATUS_PERMISSION))
					{
						$RIGHT_OK = "Y";
					}
				}
			}

			if ($RIGHT_OK=="Y")
			{
				// вызываем обработчик на смену статуса перед обновлением
				CForm::ExecHandlerBeforeChangeStatus($RESULT_ID, "SET_STATUS", $NEW_STATUS_ID);
				$arFields = Array(
					"TIMESTAMP_X"	=> $DB->GetNowFunction(),
					"STATUS_ID"		=> "'".intval($NEW_STATUS_ID)."'"
					);
				$DB->Update("b_form_result",$arFields,"WHERE ID='".$RESULT_ID."'",$err_mess.__LINE__);
				// вызываем обработчик на смену статуса после обновления
				CForm::ExecHandlerAfterChangeStatus($RESULT_ID, "SET_STATUS");
				return true;
			}
			else $strError .= GetMessage("FORM_ERROR_ACCESS_DENIED")."<br>";
		}
		else $strError .= GetMessage("FORM_ERROR_RESULT_NOT_FOUND")."<br>";
		return false;
	}

	// отправляет по почте результат
	function Mail($RESULT_ID, $TEMPLATE_ID = false)
	{
		global $DB, $MESS, $strError;
		$err_mess = (CAllFormResult::err_mess())."<br>Function: Mail<br>Line: ";
		$RESULT_ID = intval($RESULT_ID);
		if ($arrResult = CFormResult::GetDataByID($RESULT_ID, array(), $arrRES, $arrANSWER))
		{
			$z = CForm::GetByID($arrRES["FORM_ID"]);
			if ($arrFORM = $z->Fetch())
			{
				$TEMPLATE_ID = intval($TEMPLATE_ID);

				$rs = CSite::GetList(($by="sort"), ($order="asc"));
				while ($ar = $rs->Fetch()) $arrSites[$ar["ID"]] = $ar;

				$arrFormSites = CForm::GetSiteArray($arrRES["FORM_ID"]);
				$arrFormSites = (is_array($arrFormSites)) ? $arrFormSites : array();

				$arrFormTemplates = CForm::GetMailTemplateArray($arrRES["FORM_ID"]);
				$arrFormTemplates = (is_array($arrFormTemplates)) ? $arrFormTemplates : array();

				$arrTemplates = array();
				$rs = CEventMessage::GetList($by="id", $order="asc", array(
					"ACTIVE"		=> "Y",
					"SITE_ID"		=> $arrFormSites,
					"EVENT_NAME"	=> $arrFORM["MAIL_EVENT_TYPE"]
					));
				while ($ar = $rs->Fetch())
				{
					if ($TEMPLATE_ID>0)
					{
						if ($TEMPLATE_ID==$ar["ID"])
						{
							$arrTemplates[$ar["ID"]] = $ar;
							break;
						}
					}
					elseif (in_array($ar["ID"],$arrFormTemplates)) $arrTemplates[$ar["ID"]] = $ar;
				}

				foreach($arrTemplates as $arrTemplate)
				{
					$OLD_MESS = $MESS;
					$MESS = array();
					IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/form/form_mail.php", $arrSites[$arrTemplate["SITE_ID"]]["LANGUAGE_ID"]);

					$USER_AUTH = " ";
					if (intval($arrRES["USER_ID"])>0)
					{
						$w = CUser::GetByID($arrRES["USER_ID"]);
						$arrUSER = $w->Fetch();
						$USER_ID = $arrUSER["ID"];
						$USER_EMAIL = $arrUSER["EMAIL"];
						$USER_NAME = $arrUSER["NAME"]." ".$arrUSER["LAST_NAME"];
						if ($arrRES["USER_AUTH"]!="Y") $USER_AUTH="(".GetMessage("FORM_NOT_AUTHORIZED").")";
					}
					else
					{
						$USER_ID = GetMessage("FORM_NOT_REGISTERED");
						$USER_NAME = "";
						$USER_AUTH = "";
						$USER_EMAIL = "";
					}

					$arEventFields = array(
						"RS_FORM_ID"			=> $arrFORM["ID"],
						"RS_FORM_NAME"			=> $arrFORM["NAME"],
						"RS_FORM_VARNAME"		=> $arrFORM["SID"],
						"RS_FORM_SID"			=> $arrFORM["SID"],
						"RS_RESULT_ID"			=> $arrRES["ID"],
						"RS_DATE_CREATE"		=> $arrRES["DATE_CREATE"],
						"RS_USER_ID"			=> $USER_ID,
						"RS_USER_EMAIL"			=> $USER_EMAIL,
						"RS_USER_NAME"			=> $USER_NAME,
						"RS_USER_AUTH"			=> $USER_AUTH,
						"RS_STAT_GUEST_ID"		=> $arrRES["STAT_GUEST_ID"],
						"RS_STAT_SESSION_ID"	=> $arrRES["STAT_SESSION_ID"]
						);
					$w = CFormField::GetList($arrFORM["ID"], "ALL", $by, $order, array(), $is_filtered);
					while ($wr=$w->Fetch())
					{
						$answer = "";
						if (is_array($arrResult[$wr["SID"]]))
						{
							foreach($arrResult[$wr["SID"]] as $arrA)
							{
								$USER_TEXT_EXIST = (strlen(trim($arrA["USER_TEXT"]))>0);
								$ANSWER_TEXT_EXIST = (strlen(trim($arrA["ANSWER_TEXT"]))>0);
								$ANSWER_VALUE_EXIST = (strlen(trim($arrA["ANSWER_VALUE"]))>0);
								$USER_FILE_EXIST = (intval($arrA["USER_FILE_ID"])>0);

								if ($arrTemplate["BODY_TYPE"]=="html")
								{
									if (strlen(trim($answer))>0) $answer .= "<br>";

									if ($USER_TEXT_EXIST)
									{
										$answer .= $arrA["USER_TEXT"];
										if ($ANSWER_TEXT_EXIST || $ANSWER_VALUE_EXIST || $USER_FILE_EXIST)
										{
											$answer .= " ";
										}
									}

									if ($ANSWER_TEXT_EXIST)
									{
										$answer .= $arrA["ANSWER_TEXT"];
										if ($ANSWER_VALUE_EXIST || $USER_FILE_EXIST) $answer .= " ";
									}

									if ($ANSWER_VALUE_EXIST)
									{
										$answer .= $arrA["ANSWER_VALUE"];
										if ($USER_FILE_EXIST) $answer .= " ";
									}

									if ($USER_FILE_EXIST)
									{
										$f = CFile::GetByID($arrA["USER_FILE_ID"]);
										if ($fr = $f->Fetch())
										{
											$a = array("b", "Kb", "Mb", "Gb");
											$pos = 0;
											$size = $arrA["USER_FILE_SIZE"];
											while($size>=1024) {$size /= 1024; $pos++;}
											$file_size = round($size,2)." ".$a[$pos];

											if ($arrA["USER_FILE_IS_IMAGE"]=="Y")
											{
												$url = "http://".$_SERVER["HTTP_HOST"]."/".COption::GetOptionString("main", "upload_dir", "upload")."/".$fr["SUBDIR"]."/".$fr["FILE_NAME"];

												$answer .= "<a href=\"$url\">".$arrA["USER_FILE_NAME"]."</a> [".$fr["WIDTH"]." x ".$fr["HEIGHT"]."] (".$file_size.")";
											}
											else
											{
												$url = "http://".$_SERVER["HTTP_HOST"]. "/bitrix/tools/form_show_file.php?rid=".$RESULT_ID. "&hash=".$arrA["USER_FILE_HASH"]."&action=download&&lang=".LANGUAGE_ID;

												$answer .= "<a href=\"$url\">".$arrA["USER_FILE_NAME"]."</a> (".$file_size.")";
											}
										}
									}
								}
								else
								{
									if (strlen(trim($answer))>0) $answer .= "\n";

									if ($USER_TEXT_EXIST)
									{
										$answer .= trim($arrA["USER_TEXT"]);
										if ($ANSWER_TEXT_EXIST || $ANSWER_VALUE_EXIST || $USER_FILE_EXIST)
										{
											$answer .= " ";
										}
									}

									if ($ANSWER_TEXT_EXIST)
									{
										$answer .= $arrA["ANSWER_TEXT"];
										if ($ANSWER_VALUE_EXIST || $USER_FILE_EXIST) $answer .= " ";
									}

									if ($ANSWER_VALUE_EXIST)
									{
										$answer .= $arrA["ANSWER_VALUE"];
										if ($USER_FILE_EXIST) $answer .= " ";
									}

									if ($USER_FILE_EXIST)
									{
										$f = CFile::GetByID($arrA["USER_FILE_ID"]);
										if ($fr = $f->Fetch())
										{
											$a = array("b", "Kb", "Mb", "Gb");
											$pos = 0;
											$size = $arrA["USER_FILE_SIZE"];
											while($size>=1024) {$size /= 1024; $pos++;}
											$file_size = round($size,2)." ".$a[$pos];

											if ($arrA["USER_FILE_IS_IMAGE"]=="Y")
											{
												$url = "http://".$_SERVER["HTTP_HOST"]."/".COption::GetOptionString("main", "upload_dir", "upload")."/".$fr["SUBDIR"]."/".$fr["FILE_NAME"];

												$answer .= $arrA["USER_FILE_NAME"]." [".$fr["WIDTH"]." x ".$fr["HEIGHT"]."] (".$file_size.")\n".$url;
											}
											else
											{
												$url = "http://".$_SERVER["HTTP_HOST"]. "/bitrix/tools/form_show_file.php?rid=".$RESULT_ID. "&hash=".$arrA["USER_FILE_HASH"]."&action=download&&lang=".LANGUAGE_ID;

												$answer .= $arrA["USER_FILE_NAME"]." (".$file_size.")\n".$url;
											}
										}
									}
								}
							}
						}
						$arEventFields[$wr["SID"]] = (strlen($answer)<=0) ? " " : $answer;
					}
					//echo "<pre>"; print_r($arEventFields); echo "</pre>";
					CEvent::Send($arrTemplate["EVENT_NAME"], $arrTemplate["SITE_ID"], $arEventFields, "Y", $arrTemplate["ID"]);
					$MESS = $OLD_MESS;
				} //foreach($arrTemplates as $arrTemplate)
				return true;
			}
			else $strError .= GetMessage("FORM_ERROR_FORM_NOT_FOUND")."<br>";
		}
		else $strError .= GetMessage("FORM_ERROR_RESULT_NOT_FOUND")."<br>";
		return false;
	}

	function GetCount($WEB_FORM_ID)
	{
		global $DB, $USER, $strError;
		$err_mess = (CAllFormResult::err_mess())."<br>Function: GetCount<br>Line: ";
		$strSql = "SELECT count(ID) C FROM b_form_result WHERE FORM_ID=".intval($WEB_FORM_ID);
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		$zr = $z->Fetch();
		return intval($zr["C"]);
	}

	// функция подготавливает фильтрующий массив для списка результатов
	function PrepareFilter($WEB_FORM_ID, $arFilter)
	{
		$err_mess = (CAllFormResult::err_mess())."<br>Function: PrepareFilter<br>Line: ";
		global $DB, $strError;
		$arrFilterReturn = $arFilter;
		$arFilterKeys = array_keys($arFilter);
		if (in_array("FIELDS", $arFilterKeys))
		{
			$arFilterFields = $arFilter["FIELDS"];
			$rsForm = CForm::GetByID($WEB_FORM_ID);
			$arForm = $rsForm->Fetch();
			$WEB_FORM_NAME = $arForm["SID"];
			if (is_array($arFilterFields) && count($arFilterFields)>0)
			{
				reset($arFilterFields);
				while (list(,$arr) = each($arFilterFields))
				{
					if (strlen($arr["SID"])>0) $arr["CODE"] = $arr["SID"];
					else $arr["SID"] = $arr["CODE"];
					$FIELD_SID = $arr["SID"];
					$FILTER_TYPE = (strlen($arr["FILTER_TYPE"])>0) ? $arr["FILTER_TYPE"] : "text";
					if (strtoupper($FILTER_TYPE)=="ANSWER_ID") $FILTER_TYPE = "dropdown";
					$PARAMETER_NAME = (strlen($arr["PARAMETER_NAME"])>0) ? $arr["PARAMETER_NAME"] : "USER";
					$PART = $arr["PART"];
					$FILTER_KEY = $arForm["SID"]."_".$FIELD_SID."_".$PARAMETER_NAME."_".$FILTER_TYPE;
					if (strlen($PART)>0) $FILTER_KEY .= "_".intval($PART);
					$arrFilterReturn[$FILTER_KEY] = $arr["VALUE"];
					if ($FILTER_TYPE=="text")
					{
						$EXACT_MATCH = ($arr["EXACT_MATCH"]=="Y") ? "Y" : "N";
						$arrFilterReturn[$FILTER_KEY."_exact_match"] = $EXACT_MATCH;
					}
				}
			}
			unset($arrFilterReturn["FIELDS"]);
		}
		return $arrFilterReturn;
	}
}

/***************************************
			Вопрос/поле
***************************************/

class CAllFormField
{
	function err_mess()
	{
		$module_id = "form";
		@include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/install/version.php");
		return "<br>Module: ".$module_id." (".constant(strtoupper($module_id)."_VERSION").")<br>Class: CAllFormField<br>File: ".__FILE__;
	}

	// список вопросов/полей
	function GetList($WEB_FORM_ID, $get_fields, &$by, &$order, $arFilter=Array(), &$is_filtered)
	{
		$err_mess = (CAllFormField::err_mess())."<br>Function: GetList<br>Line: ";
		global $DB, $strError;
		$WEB_FORM_ID = intval($WEB_FORM_ID);
		if (strlen($get_fields)>0 && $get_fields!="ALL")
		{
			InitBVar($get_fields);
			$str = "and ADDITIONAL='$get_fields'";
		}
		$arSqlSearch = Array();
		$strSqlSearch = "";
		if (is_array($arFilter))
		{
			if (strlen($arFilter["SID"])>0) $arFilter["VARNAME"] = $arFilter["SID"];
			elseif (strlen($arFilter["VARNAME"])>0) $arFilter["SID"] = $arFilter["VARNAME"];

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
					case "SID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("F.".$key, $val, $match);
						break;
					case "TITLE":
					case "COMMENTS":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("F.".$key, $val, $match);
						break;
					case "ACTIVE":
					case "IN_RESULTS_TABLE":
					case "IN_EXCEL_TABLE":
					case "IN_FILTER":
					case "REQUIRED":
						$arSqlSearch[] = ($val=="Y") ? "F.".$key."='Y'" : "F.".$key."='N'";
						break;
				}
			}
		}
		if ($by == "s_id")						$strSqlOrder = "ORDER BY F.ID";
		elseif ($by == "s_active")				$strSqlOrder = "ORDER BY F.ACTIVE";
		elseif ($by == "s_varname" ||
				$by == "s_sid")					$strSqlOrder = "ORDER BY F.SID";
		elseif ($by == "s_c_sort" ||
				$by == "s_sort")				$strSqlOrder = "ORDER BY F.C_SORT";
		elseif ($by == "s_title")				$strSqlOrder = "ORDER BY F.TITLE";
		elseif ($by == "s_comments")			$strSqlOrder = "ORDER BY F.COMMENTS";
		elseif ($by == "s_required")			$strSqlOrder = "ORDER BY F.REQUIRED";
		elseif ($by == "s_in_results_table")	$strSqlOrder = "ORDER BY F.IN_RESULTS_TABLE";
		elseif ($by == "s_in_excel_table")		$strSqlOrder = "ORDER BY F.IN_EXCEL_TABLE";
		elseif ($by == "s_field_type")			$strSqlOrder = "ORDER BY F.FIELD_TYPE";
		else
		{
				$by = "s_sort";
				$strSqlOrder = "ORDER BY F.C_SORT";
		}
		if ($order!="desc")
		{
			$strSqlOrder .= " asc ";
			$order="asc";
		}
		else
		{
			$strSqlOrder .= " desc ";
			$order="desc";
		}
		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		$strSql = "
			SELECT
				F.*,
				F.SID as VARNAME,
				".$DB->DateToCharFunction("F.TIMESTAMP_X")."	TIMESTAMP_X
			FROM
				b_form_field F
			WHERE
			$strSqlSearch
			$str
			and FORM_ID='$WEB_FORM_ID'
			$strSqlOrder
			";
		//echo "<pre>".$strSql."</pre>";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		$is_filtered = (IsFiltered($strSqlSearch));
		return $res;
	}

	function GetByID($ID)
	{
		$err_mess = (CAllFormField::err_mess())."<br>Function: GetByID<br>Line: ";
		global $DB, $strError;
		$ID = intval($ID);
		$strSql = "
			SELECT
				F.*,
				F.SID as VARNAME,
				".$DB->DateToCharFunction("F.TIMESTAMP_X")."	TIMESTAMP_X
			FROM b_form_field F
			WHERE F.ID = $ID
			";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}

	function GetBySID($SID)
	{
		$err_mess = (CAllFormField::err_mess())."<br>Function: GetBySID<br>Line: ";
		global $DB, $strError;
		$strSql = "
			SELECT
				F.*,
				F.SID as VARNAME,
				".$DB->DateToCharFunction("F.TIMESTAMP_X")."	TIMESTAMP_X
			FROM b_form_field F
			WHERE F.SID = '".$DB->ForSql($SID,50)."'
			";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}

	function GetNextSort($WEB_FORM_ID)
	{
		global $DB, $strError;
		$err_mess = (CAllFormField::err_mess())."<br>Function: GetNextSort<br>Line: ";
		$WEB_FORM_ID = intval($WEB_FORM_ID);
		InitBVar($additional);
		$strSql = "SELECT max(C_SORT) as MAX_SORT FROM b_form_field WHERE FORM_ID='$WEB_FORM_ID'";
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		$zr = $z->Fetch();
		return (intval($zr["MAX_SORT"])+100);
	}

	// копирует вопрос/поле
	function Copy($ID, $CHECK_RIGHTS="Y", $NEW_FORM_ID=false)
	{
		global $DB, $APPLICATION, $strError;
		$err_mess = (CAllFormField::err_mess())."<br>Function: Copy<br>Line: ";
		$ID = intval($ID);
		$NEW_FORM_ID = intval($NEW_FORM_ID);
		$rsField = CFormField::GetByID($ID);
		if ($arField = $rsField->Fetch())
		{
			$RIGHT_OK = "N";
			if ($CHECK_RIGHTS!="Y" || CForm::IsAdmin()) $RIGHT_OK="Y";
			else
			{
				$F_RIGHT = CForm::GetPermission($arField["FORM_ID"]);
				// если имеем право на просмотр параметров формы
				if ($F_RIGHT>=25)
				{
					// если задана новая форма
					if ($NEW_FORM_ID>0)
					{
						$NEW_F_RIGHT = CForm::GetPermission($NEW_FORM_ID);
						// если имеем полный доступ на новую форму
						if ($NEW_F_RIGHT>=30) $RIGHT_OK = "Y";
					}
					elseif ($F_RIGHT>=30) // иначе если имеем полный доступ на исходную форму
					{
						$RIGHT_OK = "Y";
					}
				}
			}

			// если права проверили то
			if ($RIGHT_OK=="Y")
			{
				// символьный код формы
				while(true)
				{
					$SID = $arField["SID"]."_".RandString(5);
					$strSql = "SELECT 'x' FROM b_form WHERE SID='".$DB->ForSql($SID,50)."'";
					$z = $DB->Query($strSql, false, $err_mess.__LINE__);
					if (!($zr = $z->Fetch()))
					{
						$strSql = "SELECT 'x' FROM b_form_field WHERE SID='".$DB->ForSql($SID,50)."'";
						$t = $DB->Query($strSql, false, $err_mess.__LINE__);
						if (!($tr = $t->Fetch())) break;
					}
				}

				// копируем
				$arFields = array(
					"FORM_ID"				=> ($NEW_FORM_ID>0) ? $NEW_FORM_ID : $arField["FORM_ID"],
					"ACTIVE"				=> $arField["ACTIVE"],
					"TITLE"					=> $arField["TITLE"],
					"TITLE_TYPE"			=> $arField["TITLE_TYPE"],
					"SID"					=> $SID,
					"C_SORT"				=> $arField["C_SORT"],
					"ADDITIONAL"			=> $arField["ADDITIONAL"],
					"REQUIRED"				=> $arField["REQUIRED"],
					"IN_FILTER"				=> $arField["IN_FILTER"],
					"IN_RESULTS_TABLE"		=> $arField["IN_RESULTS_TABLE"],
					"IN_EXCEL_TABLE"		=> $arField["IN_EXCEL_TABLE"],
					"FIELD_TYPE"			=> $arField["FIELD_TYPE"],
					"COMMENTS"				=> $arField["COMMENTS"],
					"FILTER_TITLE"			=> $arField["FILTER_TITLE"],
					"RESULTS_TABLE_TITLE"	=> $arField["RESULTS_TABLE_TITLE"],
					);

				// картинка
				if (intval($arField["IMAGE_ID"])>0)
				{
					$arIMAGE = CFile::MakeFileArray(CFile::CopyFile($arField["IMAGE_ID"]));
					$arIMAGE["MODULE_ID"] = "form";
					$arFields["arIMAGE"] = $arIMAGE;
				}

				// фильтр
				$z = CFormField::GetFilterList($arField["FORM_ID"], Array("FIELD_ID" => $ID, "FIELD_ID_EXACT_MATCH" => "Y"));
				while ($zr = $z->Fetch())
				{
					if ($arField["ADDITIONAL"]!="Y") $arFields["arFILTER_".$zr["PARAMETER_NAME"]][] = $zr["FILTER_TYPE"];
					elseif ($zr["PARAMETER_NAME"]=="USER") $arFields["arFILTER_FIELD"][] = $zr["FILTER_TYPE"];
				}
				//echo "<pre>"; print_r($arFields); echo "</pre>";
				$NEW_ID = CFormField::Set($arFields);
				if (intval($NEW_ID)>0)
				{
					if ($arField["ADDITIONAL"]!="Y")
					{
						// ответы
						$rsAnswer = CFormAnswer::GetList($ID, $by, $order, array(), $is_filtered);
						while ($arAnswer = $rsAnswer->Fetch()) CFormAnswer::Copy($arAnswer["ID"], $NEW_ID);
					}
				}
				return $NEW_ID;
			}
			else $strError .= GetMessage("FORM_ERROR_ACCESS_DENIED")."<br>";
		}
		else $strError .= GetMessage("FORM_ERROR_FIELD_NOT_FOUND")."<br>";
		return false;
	}

	// удаляет вопрос/поле
	function Delete($ID, $CHECK_RIGHTS="Y")
	{
		global $DB, $strError;
		$err_mess = (CAllFormField::err_mess())."<br>Function: Delete<br>Line: ";
		$ID = intval($ID);

		$rsField = CFormField::GetByID($ID);
		if ($arField = $rsField->Fetch())
		{
			$WEB_FORM_ID = intval($arField["FORM_ID"]);

			$F_RIGHT = ($CHECK_RIGHTS!="Y") ? 30 : CForm::GetPermission($WEB_FORM_ID);
			if ($F_RIGHT>=30)
			{
				// очищаем результаты по данному полю
				CFormField::Reset($ID, $CHECK_RIGHTS);

				// удаляем изображения поля
				$strSql = "SELECT IMAGE_ID FROM b_form_field WHERE ID='$ID' and IMAGE_ID>0";
				$z = $DB->Query($strSql, false, $err_mess.__LINE__);
				while ($zr = $z->Fetch()) CFile::Delete($zr["IMAGE_ID"]);

				// удаляем варианты ответов на поле формы
				$DB->Query("DELETE FROM b_form_answer WHERE FIELD_ID='$ID'", false, $err_mess.__LINE__);

				// удаляем привязку к типам фильтра
				$DB->Query("DELETE FROM b_form_field_filter WHERE FIELD_ID='$ID'", false, $err_mess.__LINE__);

				// удаляем само поле
				$DB->Query("DELETE FROM b_form_field WHERE ID='$ID'", false, $err_mess.__LINE__);

				return true;
			}
			else $strError .= GetMessage("FORM_ERROR_ACCESS_DENIED")."<br>";
		}
		else $strError .= GetMessage("FORM_ERROR_FIELD_NOT_FOUND")."<br>";
		return false;
	}

	// обнуляем результаты по вопросу/полю
	function Reset($ID, $CHECK_RIGHTS="Y")
	{
		global $DB, $strError;
		$err_mess = (CAllFormField::err_mess())."<br>Function: Reset<br>Line: ";
		$ID = intval($ID);

		$rsField = CFormField::GetByID($ID);
		if ($arField = $rsField->Fetch())
		{
			$WEB_FORM_ID = intval($arField["FORM_ID"]);

			$F_RIGHT = ($CHECK_RIGHTS!="Y") ? 30 : CForm::GetPermission($WEB_FORM_ID);
			if ($F_RIGHT>=30)
			{
				// удаляем ответы по данному полю
				$DB->Query("DELETE FROM b_form_result_answer WHERE FIELD_ID='".$ID."'", false, $err_mess.__LINE__);

				return true;
			}
			else $strError .= GetMessage("FORM_ERROR_ACCESS_DENIED")."<br>";
		}
		else $strError .= GetMessage("FORM_ERROR_FIELD_NOT_FOUND")."<br>";
		return false;
	}

	function GetFilterTypeList(&$arrUSER, &$arrANSWER_TEXT, &$arrANSWER_VALUE, &$arrFIELD)
	{
		global $MESS;
		$arrUSER = array(
			"reference_id" => array(
				"text",
				"integer",
				"date",
				"exist",
				),
			"reference" => array(
				GetMessage("FORM_TEXT_FIELD"),
				GetMessage("FORM_NUMERIC_INTERVAL"),
				GetMessage("FORM_DATE_INTERVAL"),
				GetMessage("FORM_EXIST_FLAG"),
				)
			);
		$arrANSWER_TEXT = array(
			"reference_id" => array(
				"text",
				"integer",
				"dropdown",
				"exist",
				),
			"reference" => array(
				GetMessage("FORM_TEXT_FIELD"),
				GetMessage("FORM_NUMERIC_INTERVAL"),
				GetMessage("FORM_DROPDOWN_LIST"),
				GetMessage("FORM_EXIST_FLAG"),
				)
			);
		$arrANSWER_VALUE = array(
			"reference_id" => array(
				"text",
				"integer",
				"dropdown",
				"exist",
				),
			"reference" => array(
				GetMessage("FORM_TEXT_FIELD"),
				GetMessage("FORM_NUMERIC_INTERVAL"),
				GetMessage("FORM_DROPDOWN_LIST"),
				GetMessage("FORM_EXIST_FLAG"),
				)
			);
		$arrFIELD = array(
			"reference_id" => array(
				"text",
				"integer",
				"date",
				"exist",
				),
			"reference" => array(
				GetMessage("FORM_TEXT_FIELD"),
				GetMessage("FORM_NUMERIC_INTERVAL"),
				GetMessage("FORM_DATE_INTERVAL"),
				GetMessage("FORM_EXIST_FLAG"),
				)
			);
	}

	function GetTypeList()
	{
		global $MESS;
		$arr = array(
			"reference_id" => array(
				"text",
				"integer",
				"date"),
			"reference" => array(
				GetMessage("FORM_FIELD_TEXT"),
				GetMessage("FORM_FIELD_INTEGER"),
				GetMessage("FORM_FIELD_DATE")
				)
			);
		return $arr;
	}

	function GetFilterList($WEB_FORM_ID, $arFilter=Array())
	{
		$err_mess = (CAllFormField::err_mess())."<br>Function: GetFilterList<br>Line: ";
		global $DB, $strError;
		$WEB_FORM_ID = intval($WEB_FORM_ID);
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
					case "FIELD_ID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("F.ID",$val,$match);
						break;
					case "ACTIVE":
						$arSqlSearch[] = ($val=="Y") ? "F.ACTIVE='Y'" : "F.ACTIVE='N'";
						break;
					case "FILTER_TYPE":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("L.FILTER_TYPE", $val, $match);
						break;
					case "PARAMETER_NAME":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("L.PARAMETER_NAME", $val, $match);
						break;
				}
			}
		}
		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		$strSql = "
			SELECT
				F.*,
				F.SID as VARNAME,
				L.PARAMETER_NAME,
				L.FILTER_TYPE
			FROM
				b_form_field F,
				b_form_field_filter	L
			WHERE
			$strSqlSearch
			and F.FORM_ID = $WEB_FORM_ID
			and F.IN_FILTER = 'Y'
			and L.FIELD_ID = F.ID
			ORDER BY F.C_SORT, L.PARAMETER_NAME, L.FILTER_TYPE desc
			";
		//echo $strSql;
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}

	// проверка вопроса/поля
	function CheckFields($arFields, $FIELD_ID, $CHECK_RIGHTS="Y")
	{
		$err_mess = (CAllFormField::err_mess())."<br>Function: CheckFields<br>Line: ";
		global $DB, $strError, $APPLICATION, $USER;
		$str = "";
		$FORM_ID = intval($arFields["FORM_ID"]);
		if ($FORM_ID<=0) $str .= GetMessage("FORM_ERROR_FORM_ID_NOT_DEFINED")."<br>";
		else
		{
			$RIGHT_OK = "N";
			if ($CHECK_RIGHTS!="Y" || CForm::IsAdmin()) $RIGHT_OK = "Y";
			else
			{
				$F_RIGHT = CForm::GetPermission($FORM_ID);
				if ($F_RIGHT>=30) $RIGHT_OK = "Y";
			}

			if ($RIGHT_OK=="Y")
			{
				if (strlen(trim($arFields["SID"]))>0) $arFields["VARNAME"] = $arFields["SID"];
				elseif (strlen($arFields["VARNAME"])>0) $arFields["SID"] = $arFields["VARNAME"];

				if ($FIELD_ID<=0 || ($FIELD_ID>0 && is_set($arFields, "SID")))
				{
					if (strlen(trim($arFields["SID"]))<=0) $str .= GetMessage("FORM_ERROR_FORGOT_SID")."<br>";
					if (ereg("[^A-Za-z_0-9]",$arFields["SID"])) $str .= GetMessage("FORM_ERROR_INCORRECT_SID")."<br>";
					else
					{
						$strSql = "SELECT ID, ADDITIONAL FROM b_form_field WHERE SID='".$DB->ForSql(trim($arFields["SID"]),50)."' and ID<>'$FIELD_ID'";
						$z = $DB->Query($strSql, false, $err_mess.__LINE__);
						if ($zr = $z->Fetch())
						{
							$s = ($zr["ADDITIONAL"]=="Y") ?
								str_replace("#TYPE#", GetMessage("FORM_TYPE_FIELD"), GetMessage("FORM_ERROR_WRONG_SID")) :
								str_replace("#TYPE#", GetMessage("FORM_TYPE_QUESTION"), GetMessage("FORM_ERROR_WRONG_SID"));
							$s = str_replace("#ID#",$zr["ID"],$s);
							$str .= $s."<br>";
						}
						else
						{
							$strSql = "SELECT ID FROM b_form WHERE SID='".$DB->ForSql(trim($arFields["SID"]),50)."'";
							$z = $DB->Query($strSql, false, $err_mess.__LINE__);
							if ($zr = $z->Fetch())
							{
								$s = str_replace("#TYPE#", GetMessage("FORM_TYPE_FORM"), GetMessage("FORM_ERROR_WRONG_SID"));
								$s = str_replace("#ID#",$zr["ID"],$s);
								$str .= $s."<br>";
							}
						}
					}
				}

				$str .= CFile::CheckImageFile($arFields["arIMAGE"]);
			}
			else $str .= GetMessage("FORM_ERROR_ACCESS_DENIED");
		}

		$strError .= $str;
		if (strlen($str)>0) return false; else return true;
	}

	// добавление/обновление вопроса/поля
	function Set($arFields, $FIELD_ID=false, $CHECK_RIGHTS="Y")
	{
		$err_mess = (CAllFormField::err_mess())."<br>Function: Set<br>Line: ";
		global $DB, $USER, $strError, $APPLICATION;

		if (CFormField::CheckFields($arFields, $FIELD_ID, $CHECK_RIGHTS))
		{
			$arFields_i = array();

			if (strlen(trim($arFields["SID"]))>0) $arFields["VARNAME"] = $arFields["SID"];
			elseif (strlen($arFields["VARNAME"])>0) $arFields["SID"] = $arFields["VARNAME"];

			$arFields_i["TIMESTAMP_X"] = $DB->GetNowFunction();

			if (is_set($arFields, "ACTIVE"))
				$arFields_i["ACTIVE"] = ($arFields["ACTIVE"]=="Y") ? "'Y'" : "'N'";

			if (is_set($arFields, "TITLE"))
				$arFields_i["TITLE"] = "'".$DB->ForSql($arFields["TITLE"], 2000)."'";

			if (is_set($arFields, "TITLE_TYPE"))
				$arFields_i["TITLE_TYPE"] = ($arFields["TITLE_TYPE"]=="html") ? "'html'" : "'text'";

			if (is_set($arFields, "SID"))
				$arFields_i["SID"] = "'".$DB->ForSql($arFields["SID"],50)."'";

			if (is_set($arFields, "C_SORT"))
				$arFields_i["C_SORT"] = "'".intval($arFields["C_SORT"])."'";

			if (is_set($arFields, "ADDITIONAL"))
				$arFields_i["ADDITIONAL"] = ($arFields["ADDITIONAL"]=="Y") ? "'Y'" : "'N'";

			if (is_set($arFields, "REQUIRED"))
				$arFields_i["REQUIRED"] = ($arFields["REQUIRED"]=="Y") ? "'Y'" : "'N'";

			if (is_set($arFields, "IN_RESULTS_TABLE"))
				$arFields_i["IN_RESULTS_TABLE"] = ($arFields["IN_RESULTS_TABLE"]=="Y") ? "'Y'" : "'N'";

			if (is_set($arFields, "IN_EXCEL_TABLE"))
				$arFields_i["IN_EXCEL_TABLE"] = ($arFields["IN_EXCEL_TABLE"]=="Y") ? "'Y'" : "'N'";

			if (is_set($arFields, "FIELD_TYPE"))
				$arFields_i["FIELD_TYPE"] = "'".$DB->ForSql($arFields["FIELD_TYPE"],50)."'";

			if (is_set($arFields, "COMMENTS"))
				$arFields_i["COMMENTS"] = "'".$DB->ForSql($arFields["COMMENTS"],2000)."'";

			if (is_set($arFields, "FILTER_TITLE"))
				$arFields_i["FILTER_TITLE"] = "'".$DB->ForSql($arFields["FILTER_TITLE"],2000)."'";

			if (is_set($arFields, "RESULTS_TABLE_TITLE"))
				$arFields_i["RESULTS_TABLE_TITLE"] = "'".$DB->ForSql($arFields["RESULTS_TABLE_TITLE"],2000)."'";

			$z = $DB->Query("SELECT IMAGE_ID FROM b_form_field WHERE ID='$FIELD_ID'", false, $err_mess.__LINE__);
			$zr = $z->Fetch();
			if (strlen($arFields["arIMAGE"]["name"])>0 || strlen($arFields["arIMAGE"]["del"])>0)
			{
				$fid = CFile::SaveFile($arFields["arIMAGE"], "form");
				if (intval($fid)>0)	$arFields_i["IMAGE_ID"] = intval($fid);
				else $arFields_i["IMAGE_ID"] = "null";
			}

			if ($FIELD_ID>0)
			{
				$DB->Update("b_form_field", $arFields_i, "WHERE ID='".$FIELD_ID."'", $err_mess.__LINE__);
			}
			else
			{
				$arFields_i["FORM_ID"] = "'".intval($arFields["FORM_ID"])."'";
				$FIELD_ID = $DB->Insert("b_form_field", $arFields_i, $err_mess.__LINE__);
			}

			$FIELD_ID = intval($FIELD_ID);

			if ($FIELD_ID>0)
			{
				// ответы на вопрос
				if ($arFields["ADDITIONAL"]!="Y" && is_set($arFields, "arANSWER"))
				{
					$arANSWER = $arFields["arANSWER"];
					if (is_array($arANSWER) && count($arANSWER)>0)
					{
						$arrAnswers = array();
						$rs = CFormAnswer::GetList($FIELD_ID, $by, $order, array(), $is_filtered);
						while($ar=$rs->Fetch()) $arrAnswers[] = $ar["ID"];

						foreach($arANSWER as $arA)
						{
							$answer_id = in_array($arA["ID"], $arrAnswers) ? intval($arA["ID"]) : 0;
							if ($arA["DELETE"]=="Y" && $answer_id>0) CFormAnswer::Delete($answer_id, $FIELD_ID);
							else
							{
								if ($answer_id>0 || ($answer_id<=0 && strlen($arA["MESSAGE"])>0))
								{
									$arFields_a = array(
										"FIELD_ID"		=> $FIELD_ID,
										"MESSAGE"		=> $arA["MESSAGE"],
										"VALUE"			=> $arA["VALUE"],
										"C_SORT"		=> $arA["C_SORT"],
										"ACTIVE"		=> $arA["ACTIVE"],
										"FIELD_TYPE"	=> $arA["FIELD_TYPE"],
										"FIELD_WIDTH"	=> $arA["FIELD_WIDTH"],
										"FIELD_HEIGHT"	=> $arA["FIELD_HEIGHT"],
										"FIELD_PARAM"	=> $arA["FIELD_PARAM"],
										);
									//echo "<pre>"; print_r($arFields_a); echo "</pre>";
									CFormAnswer::Set($arFields_a, $answer_id, $FIELD_ID);
								}
							}
						}
					}
				}

				// тип почтового события
				CForm::SetMailTemplate(intval($arFields["FORM_ID"]),"N");

				// фильтр
				$in_filter="N";
				$DB->Query("UPDATE b_form_field SET IN_FILTER='N' WHERE ID='".$FIELD_ID."'", false, $err_mess.__LINE__);
				$arrFilterType = array(
					"arFILTER_USER"			=> "USER",
					"arFILTER_ANSWER_TEXT"	=> "ANSWER_TEXT",
					"arFILTER_ANSWER_VALUE"	=> "ANSWER_VALUE",
					"arFILTER_FIELD"		=> "USER",
				);

				while (list($key, $value) = each($arrFilterType))
				{
					if (is_set($arFields, $key))
					{
						$strSql = "DELETE FROM b_form_field_filter WHERE FIELD_ID='".$FIELD_ID."' and PARAMETER_NAME='".$value."'";
						$DB->Query($strSql, false, $err_mess.__LINE__);
						if (is_array($arFields[$key]))
						{
							reset($arFields[$key]);
							foreach($arFields[$key] as $type)
							{
								$arFields_i = array(
									"FIELD_ID"			=> "'".intval($FIELD_ID)."'",
									"FILTER_TYPE"		=> "'".$DB->ForSql($type,50)."'",
									"PARAMETER_NAME"	=> "'".$value."'",
								);
								$DB->Insert("b_form_field_filter",$arFields_i, $err_mess.__LINE__);
								$in_filter="Y";
							}
						}
					}
				}

				if ($in_filter=="Y")
					$DB->Query("UPDATE b_form_field SET IN_FILTER='Y' WHERE ID='".$FIELD_ID."'", false, $err_mess.__LINE__);

			}
			return $FIELD_ID;
		}
		return false;
	}
}


/***************************************
				Ответы
***************************************/

class CAllFormAnswer
{
	function err_mess()
	{
		$module_id = "form";
		@include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/install/version.php");
		return "<br>Module: ".$module_id." (".constant(strtoupper($module_id)."_VERSION").")<br>Class: CAllFormAnswer<br>File: ".__FILE__;
	}

	// копирует ответ
	function Copy($ID, $NEW_QUESTION_ID=false)
	{
		global $DB, $APPLICATION, $strError;
		$err_mess = (CAllFormAnswer::err_mess())."<br>Function: Copy<br>Line: ";
		$ID = intval($ID);
		$NEW_QUESTION_ID = intval($NEW_QUESTION_ID);
		$rsAnswer = CFormAnswer::GetByID($ID);
		if ($arAnswer = $rsAnswer->Fetch())
		{
			$arFields = array(
				"QUESTION_ID"	=> ($NEW_QUESTION_ID>0) ? $NEW_QUESTION_ID : $arAnswer["QUESTION_ID"],
				"MESSAGE"		=> $arAnswer["MESSAGE"],
				"VALUE"			=> $arAnswer["VALUE"],
				"C_SORT"		=> $arAnswer["C_SORT"],
				"ACTIVE"		=> $arAnswer["ACTIVE"],
				"FIELD_TYPE"	=> $arAnswer["FIELD_TYPE"],
				"FIELD_WIDTH"	=> $arAnswer["FIELD_WIDTH"],
				"FIELD_HEIGHT"	=> $arAnswer["FIELD_HEIGHT"],
				"FIELD_PARAM"	=> $arAnswer["FIELD_PARAM"],
				);
			$NEW_ID = CFormAnswer::Set($arFields);
			return $NEW_ID;
		}
		else $strError .= GetMessage("FORM_ERROR_ANSWER_NOT_FOUND")."<br>";
		return false;
	}

	// удаляем ответ
	function Delete($ID, $QUESTION_ID=false)
	{
		global $DB, $strError;
		$err_mess = (CAllFormAnswer::err_mess())."<br>Function: Delete<br>Line: ";
		$ID = intval($ID);
		$DB->Query("DELETE FROM b_form_answer WHERE ID='".$ID."'", false, $err_mess.__LINE__);
		if (intval($QUESTION_ID)>0) $str = " FIELD_ID = ".intval($QUESTION_ID)." and ";
		$DB->Query("DELETE FROM b_form_result_answer WHERE ".$str." ANSWER_ID='".$ID."'", false, $err_mess.__LINE__);
		return true;
	}

	function GetTypeList()
	{
		global $bSimple;
		$arrT = array(
				"text",
				"textarea",
				"radio",
				"checkbox",
				"dropdown",
				"multiselect",
				"date",
				"image",
				"file",
				"email",
				"url",
				"password"
				);
		if ($bSimple) $arrT[] = "hidden";
		$arr = array("reference_id" => $arrT, "reference" => $arrT);
		return $arr;
	}

	// возвращает список ответов
	function GetList($QUESTION_ID, &$by, &$order, $arFilter=Array(), &$is_filtered)
	{
		$err_mess = (CAllFormAnswer::err_mess())."<br>Function: GetList<br>Line: ";
		global $DB, $strError;
		$QUESTION_ID = intval($QUESTION_ID);
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
						$arSqlSearch[] = GetFilterQuery("A.ID",$val,$match);
						break;
					case "MESSAGE":
					case "VALUE":
					case "FIELD_TYPE":
					case "FIELD_WIDTH":
					case "FIELD_HEIGHT":
					case "FIELD_PARAM":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("A.".$key, $val, $match);
						break;
					case "ACTIVE":
						$arSqlSearch[] = ($val=="Y") ? "A.ACTIVE='Y'" : "A.ACTIVE='N'";
						break;
				}
			}
		}
		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		if ($by == "s_id") $strSqlOrder = "ORDER BY A.ID";
		elseif ($by == "s_c_sort" || $by == "s_sort") $strSqlOrder = "ORDER BY A.C_SORT";
		else
		{
			$by = "s_sort";
			$strSqlOrder = "ORDER BY A.C_SORT";
		}
		if ($order!="desc")
		{
			$strSqlOrder .= " asc ";
			$order="asc";
		}
		else
		{
			$strSqlOrder .= " desc ";
			$order="desc";
		}
		$strSql = "
			SELECT
				A.ID,
				A.FIELD_ID,
				A.FIELD_ID as QUESTION_ID,
				".$DB->DateToCharFunction("A.TIMESTAMP_X")."	TIMESTAMP_X,
				A.MESSAGE,
				A.VALUE,
				A.FIELD_TYPE,
				A.FIELD_WIDTH,
				A.FIELD_HEIGHT,
				A.FIELD_PARAM,
				A.C_SORT,
				A.ACTIVE
			FROM
				b_form_answer A
			WHERE
			$strSqlSearch
			and A.FIELD_ID = $QUESTION_ID
			$strSqlOrder
			";
		//echo $strSql;
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		$is_filtered = (IsFiltered($strSqlSearch));
		return $res;
	}

	function GetByID($ID)
	{
		$err_mess = (CAllFormAnswer::err_mess())."<br>Function: GetByID<br>Line: ";
		global $DB, $strError;
		$ID = intval($ID);
		$strSql = "
			SELECT
				A.ID,
				A.FIELD_ID,
				A.FIELD_ID as QUESTION_ID,
				".$DB->DateToCharFunction("A.TIMESTAMP_X")."	TIMESTAMP_X,
				A.MESSAGE,
				A.VALUE,
				A.FIELD_TYPE,
				A.FIELD_WIDTH,
				A.FIELD_HEIGHT,
				A.FIELD_PARAM,
				A.C_SORT,
				A.ACTIVE
			FROM
				b_form_answer A
			WHERE
				ID='$ID'
			";
		//echo $strSql;
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}

	// проверка ответа
	function CheckFields($arFields, $ANSWER_ID=false)
	{
		$err_mess = (CAllFormAnswer::err_mess())."<br>Function: CheckFields<br>Line: ";
		global $DB, $strError, $APPLICATION, $USER;
		$str = "";
		$ANSWER_ID = intval($ANSWER_ID);

		if (intval($arFields["QUESTION_ID"])>0) $arFields["FIELD_ID"] = $arFields["QUESTION_ID"];
		else $arFields["QUESTION_ID"] = $arFields["FIELD_ID"];

		if ($ANSWER_ID<=0 && intval($arFields["QUESTION_ID"])<=0)
		{
			$str .= GetMessage("FORM_ERROR_FORGOT_QUESTION_ID")."<br>";
		}

		if ($ANSWER_ID<=0 || ($ANSWER_ID>0 && is_set($arFields, "MESSAGE")))
		{
			if (strlen($arFields["MESSAGE"])<=0) $str .= GetMessage("FORM_ERROR_FORGOT_ANSWER_TEXT")."<br>";
		}

		$strError .= $str;
		if (strlen($str)>0) return false; else return true;
	}

	// добавление/обновление ответа
	function Set($arFields, $ANSWER_ID=false)
	{
		$err_mess = (CAllFormAnswer::err_mess())."<br>Function: Set<br>Line: ";
		global $DB, $USER, $strError, $APPLICATION;

		$ANSWER_ID = intval($ANSWER_ID);

		if (CFormAnswer::CheckFields($arFields, $ANSWER_ID))
		{
			$arFields_i = array();

			$arFields_i["TIMESTAMP_X"] = $DB->GetNowFunction();

			if (is_set($arFields, "MESSAGE"))
				$arFields_i["MESSAGE"] = "'".$DB->ForSql($arFields["MESSAGE"],2000)."'";

			if (is_set($arFields, "VALUE"))
				$arFields_i["VALUE"] = "'".$DB->ForSql($arFields["VALUE"],2000)."'";

			if (is_set($arFields, "ACTIVE"))
				$arFields_i["ACTIVE"] = ($arFields["ACTIVE"]=="Y") ? "'Y'" : "'N'";

			if (is_set($arFields, "C_SORT"))
				$arFields_i["C_SORT"] = "'".intval($arFields["C_SORT"])."'";

			if (is_set($arFields, "FIELD_TYPE"))
				$arFields_i["FIELD_TYPE"] = "'".$DB->ForSql($arFields["FIELD_TYPE"],255)."'";

			if (is_set($arFields, "FIELD_WIDTH"))
				$arFields_i["FIELD_WIDTH"] = "'".intval($arFields["FIELD_WIDTH"])."'";

			if (is_set($arFields, "FIELD_HEIGHT"))
				$arFields_i["FIELD_HEIGHT"] = "'".intval($arFields["FIELD_HEIGHT"])."'";

			if (is_set($arFields, "FIELD_PARAM"))
				$arFields_i["FIELD_PARAM"] = "'".$DB->ForSql($arFields["FIELD_PARAM"],2000)."'";

			if ($ANSWER_ID>0)
			{
				$DB->Update("b_form_answer", $arFields_i, "WHERE ID='".$ANSWER_ID."'", $err_mess.__LINE__);

				// обновим все результаты для данного ответа
				$arFields_u = array();
				$arFields_u["ANSWER_TEXT"] = $arFields_i["MESSAGE"];
				$arFields_u["ANSWER_VALUE"] = $arFields_i["VALUE"];
				if (intval($CURRENT_FIELD_ID)>0) $str = " FIELD_ID = ".intval($CURRENT_FIELD_ID)." and ";
				$DB->Update("b_form_result_answer", $arFields_u, "WHERE ".$str." ANSWER_ID='".$ANSWER_ID."'", $err_mess.__LINE__);
			}
			else
			{
				if (intval($arFields["QUESTION_ID"])>0) $arFields["FIELD_ID"] = $arFields["QUESTION_ID"];
				else $arFields["QUESTION_ID"] = $arFields["FIELD_ID"];

				$arFields_i["FIELD_ID"] = "'".intval($arFields["QUESTION_ID"])."'";
				$ANSWER_ID = $DB->Insert("b_form_answer", $arFields_i, $err_mess.__LINE__);
				$ANSWER_ID = intval($ANSWER_ID);
			}
			return $ANSWER_ID;
		}
		return false;
	}
}


/***************************************
	Статус результата веб-формы
***************************************/

class CAllFormStatus
{
	function err_mess()
	{
		$module_id = "form";
		@include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/install/version.php");
		return "<br>Module: ".$module_id." (".constant(strtoupper($module_id)."_VERSION").")<br>Class: CAllFormStatus<br>File: ".__FILE__;
	}

	// права на статус по группам
	function GetPermissionList($STATUS_ID, &$arPERMISSION_VIEW, &$arPERMISSION_MOVE, &$arPERMISSION_EDIT, &$arPERMISSION_DELETE)
	{
		$err_mess = (CAllFormStatus::err_mess())."<br>Function: GetPermissionList<br>Line: ";
		global $DB, $strError;
		$STATUS_ID = intval($STATUS_ID);
		$arPERMISSION_VIEW = $arPERMISSION_MOVE = $arPERMISSION_EDIT = $arPERMISSION_DELETE = array();
		$strSql = "
			SELECT
				GROUP_ID,
				PERMISSION
			FROM
				b_form_status_2_group
			WHERE
				STATUS_ID='$STATUS_ID'
			";
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		while ($zr=$z->Fetch())
		{
			if ($zr["PERMISSION"]=="VIEW")		$arPERMISSION_VIEW[] = $zr["GROUP_ID"];
			if ($zr["PERMISSION"]=="MOVE")		$arPERMISSION_MOVE[] = $zr["GROUP_ID"];
			if ($zr["PERMISSION"]=="EDIT")		$arPERMISSION_EDIT[] = $zr["GROUP_ID"];
			if ($zr["PERMISSION"]=="DELETE")	$arPERMISSION_DELETE[] = $zr["GROUP_ID"];
		}

	}

	// возвращает массив максимальных прав на результат
	function GetMaxPermissions()
	{
		return array("VIEW","MOVE","EDIT","DELETE");
	}

	// права на статус
	function GetPermissions($STATUS_ID)
	{
		$err_mess = (CAllFormStatus::err_mess())."<br>Function: GetPermissions<br>Line: ";
		global $DB, $USER, $strError;
		$USER_ID = $USER->GetID();
		$STATUS_ID = intval($STATUS_ID);
		$arReturn = array();
		$arGroups = $USER->GetUserGroupArray();
		if (!is_array($arGroups)) $arGroups[] = 2;
		if (CForm::IsAdmin()) return CFormStatus::GetMaxPermissions();
		else
		{
			$arr = array();
			if (is_array($arGroups) && count($arGroups)>0) $groups = implode(",",$arGroups);
			$strSql = "
				SELECT
					G.PERMISSION
				FROM
					b_form_status_2_group G
				WHERE
					G.STATUS_ID = $STATUS_ID
				";
			$z = $DB->Query($strSql, false, $err_mess.__LINE__);
			while ($zr = $z->Fetch()) $arReturn[] = $zr["PERMISSION"];
		}
		return $arReturn;
	}

	function GetNextSort($WEB_FORM_ID)
	{
		$err_mess = (CAllFormStatus::err_mess())."<br>Function: GetNextSort<br>Line: ";
		global $DB, $strError;
		$WEB_FORM_ID = intval($WEB_FORM_ID);
		$strSql = "SELECT max(C_SORT) MAX_SORT FROM b_form_status WHERE FORM_ID=$WEB_FORM_ID";
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		$zr = $z->Fetch();
		return intval($zr["MAX_SORT"])+100;
	}

	function GetDefault($WEB_FORM_ID)
	{
		$err_mess = (CAllFormStatus::err_mess())."<br>Function: GetDefault<br>Line: ";
		global $DB, $USER, $strError;
		$WEB_FORM_ID = intval($WEB_FORM_ID);
		$strSql = "SELECT ID FROM b_form_status WHERE FORM_ID=$WEB_FORM_ID and ACTIVE='Y' and DEFAULT_VALUE='Y'";
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		$zr = $z->Fetch();
		return intval($zr["ID"]);
	}

	// проверка статуса
	function CheckFields($arFields, $STATUS_ID, $CHECK_RIGHTS="Y")
	{
		$err_mess = (CAllFormStatus::err_mess())."<br>Function: CheckFields<br>Line: ";
		global $DB, $strError, $APPLICATION, $USER;
		$str = "";
		$STATUS_ID = intval($STATUS_ID);
		$FORM_ID = intval($arFields["FORM_ID"]);
		if ($FORM_ID<=0) $str .= GetMessage("FORM_ERROR_FORM_ID_NOT_DEFINED")."<br>";
		else
		{
			$RIGHT_OK = "N";
			if ($CHECK_RIGHTS!="Y" || CForm::IsAdmin()) $RIGHT_OK = "Y";
			{
				$FORM_RIGHT = $APPLICATION->GetGroupRight("form");
				$F_RIGHT = CForm::GetPermission($FORM_ID);
				if ($FORM_RIGHT>"D" && $F_RIGHT>=30) $RIGHT_OK = "Y";
			}
			if ($RIGHT_OK=="Y")
			{
				if ($STATUS_ID<=0 || ($STATUS_ID>0 && is_set($arFields, "TITLE")))
				{
					if (strlen(trim($arFields["TITLE"]))<=0) $str .= GetMessage("FORM_ERROR_FORGOT_TITLE")."<br>";
				}
			}
			else $str .= GetMessage("FORM_ERROR_ACCESS_DENIED");
		}
		$strError .= $str;
		if (strlen($str)>0) return false; else return true;
	}

	// добавление/обновление статуса
	function Set($arFields, $STATUS_ID=false, $CHECK_RIGHTS="Y")
	{
		$err_mess = (CAllFormStatus::err_mess())."<br>Function: Set<br>Line: ";
		global $DB, $USER, $strError, $APPLICATION;
		if (CFormStatus::CheckFields($arFields, $STATUS_ID, $CHECK_RIGHTS))
		{
			$arFields_i = array();

			$arFields_i["TIMESTAMP_X"] = $DB->GetNowFunction();

			if (is_set($arFields, "C_SORT"))
				$arFields_i["C_SORT"] = "'".intval($arFields["C_SORT"])."'";

			if (is_set($arFields, "ACTIVE"))
				$arFields_i["ACTIVE"] = ($arFields["ACTIVE"]=="Y") ? "'Y'" : "'N'";

			if (is_set($arFields, "TITLE"))
				$arFields_i["TITLE"] = "'".$DB->ForSql($arFields["TITLE"],255)."'";

			if (is_set($arFields, "DESCRIPTION"))
				$arFields_i["DESCRIPTION"] = "'".$DB->ForSql($arFields["DESCRIPTION"],2000)."'";

			if (is_set($arFields, "CSS"))
				$arFields_i["CSS"] = "'".$DB->ForSql($arFields["CSS"],255)."'";

			if (is_set($arFields, "HANDLER_OUT"))
				$arFields_i["HANDLER_OUT"] = "'".$DB->ForSql($arFields["HANDLER_OUT"],255)."'";

			if (is_set($arFields, "HANDLER_IN"))
				$arFields_i["HANDLER_IN"] = "'".$DB->ForSql($arFields["HANDLER_IN"],255)."'";

			$DEFAULT_STATUS_ID = intval(CFormStatus::GetDefault($arFields["FORM_ID"]));
			if ($DEFAULT_STATUS_ID<=0 || $DEFAULT_STATUS_ID==$STATUS_ID)
			{
				if (is_set($arFields, "DEFAULT_VALUE"))
					$arFields_i["DEFAULT_VALUE"] = ($arFields["DEFAULT_VALUE"]=="Y") ? "'Y'" : "'N'";
			}

			if ($STATUS_ID>0)
			{
				$DB->Update("b_form_status", $arFields_i, "WHERE ID='".$STATUS_ID."'", $err_mess.__LINE__);
			}
			else
			{
				$arFields_i["FORM_ID"] = "'".intval($arFields["FORM_ID"])."'";
				$STATUS_ID = $DB->Insert("b_form_status", $arFields_i, $err_mess.__LINE__);
			}

			$STATUS_ID = intval($STATUS_ID);

			if ($STATUS_ID>0)
			{
				// право на просмотр
				if (is_set($arFields, "arPERMISSION_VIEW"))
				{
					$DB->Query("DELETE FROM b_form_status_2_group WHERE STATUS_ID='".$STATUS_ID."' and PERMISSION='VIEW'", false, $err_mess.__LINE__);
					if (is_array($arFields["arPERMISSION_VIEW"]))
					{
						reset($arFields["arPERMISSION_VIEW"]);
						foreach($arFields["arPERMISSION_VIEW"] as $gid)
						{
							$arFields_i = array(
								"STATUS_ID"		=> "'".intval($STATUS_ID)."'",
								"GROUP_ID"		=> "'".intval($gid)."'",
								"PERMISSION"	=> "'VIEW'"
							);
							$DB->Insert("b_form_status_2_group",$arFields_i, $err_mess.__LINE__);
						}
					}
				}

				// право на перевод
				if (is_set($arFields, "arPERMISSION_MOVE"))
				{
					$DB->Query("DELETE FROM b_form_status_2_group WHERE STATUS_ID='".$STATUS_ID."' and PERMISSION='MOVE'", false, $err_mess.__LINE__);
					if (is_array($arFields["arPERMISSION_MOVE"]))
					{
						reset($arFields["arPERMISSION_MOVE"]);
						foreach($arFields["arPERMISSION_MOVE"] as $gid)
						{
							$arFields_i = array(
								"STATUS_ID"		=> "'".intval($STATUS_ID)."'",
								"GROUP_ID"		=> "'".intval($gid)."'",
								"PERMISSION"	=> "'MOVE'"
							);
							$DB->Insert("b_form_status_2_group",$arFields_i, $err_mess.__LINE__);
						}
					}
				}

				// право на редактирование
				if (is_set($arFields, "arPERMISSION_EDIT"))
				{
					$DB->Query("DELETE FROM b_form_status_2_group WHERE STATUS_ID='".$STATUS_ID."' and PERMISSION='EDIT'", false, $err_mess.__LINE__);
					if (is_array($arFields["arPERMISSION_EDIT"]))
					{
						reset($arFields["arPERMISSION_EDIT"]);
						foreach($arFields["arPERMISSION_EDIT"] as $gid)
						{
							$arFields_i = array(
								"STATUS_ID"		=> "'".intval($STATUS_ID)."'",
								"GROUP_ID"		=> "'".intval($gid)."'",
								"PERMISSION"	=> "'EDIT'"
							);
							$DB->Insert("b_form_status_2_group",$arFields_i, $err_mess.__LINE__);
						}
					}
				}

				// право на удаление
				if (is_set($arFields, "arPERMISSION_DELETE"))
				{
					$DB->Query("DELETE FROM b_form_status_2_group WHERE STATUS_ID='".$STATUS_ID."' and PERMISSION='DELETE'", false, $err_mess.__LINE__);
					if (is_array($arFields["arPERMISSION_DELETE"]))
					{
						reset($arFields["arPERMISSION_DELETE"]);
						foreach($arFields["arPERMISSION_DELETE"] as $gid)
						{
							$arFields_i = array(
								"STATUS_ID"		=> "'".intval($STATUS_ID)."'",
								"GROUP_ID"		=> "'".intval($gid)."'",
								"PERMISSION"	=> "'DELETE'"
							);
							$DB->Insert("b_form_status_2_group",$arFields_i, $err_mess.__LINE__);
						}
					}
				}

			}
			return $STATUS_ID;
		}
		return false;
	}

	// удаляет статус
	function Delete($ID, $CHECK_RIGHTS="Y")
	{
		global $DB, $APPLICATION, $strError;
		$ID = intval($ID);
		$rsStatus = CFormStatus::GetByID($ID);
		if ($arStatus = $rsStatus->Fetch())
		{
			$RIGHT_OK = "N";
			if ($CHECK_RIGHTS!="Y" || CForm::IsAdmin()) 
				$RIGHT_OK="Y";
			else
			{
				$F_RIGHT = CForm::GetPermission($arStatus["FORM_ID"]);
				if ($F_RIGHT>=30) $RIGHT_OK="Y";
			}
			if ($RIGHT_OK=="Y")
			{
				$strSql = "SELECT 'x' FROM b_form_result WHERE STATUS_ID='$ID'";
				$z = $DB->Query($strSql, false, $err_mess.__LINE__);
				if (!$zr = $z->Fetch())
				{
					if ($DB->Query("DELETE FROM b_form_status WHERE ID='$ID'", false, $err_mess.__LINE__))
					{
						if ($DB->Query("DELETE FROM b_form_status_2_group WHERE STATUS_ID='$ID'", false, $err_mess.__LINE__))
							return true;
					}
				}
				else 
					$strError .= GetMessage("FORM_ERROR_CANNOT_DELETE_STATUS")."<br>";
			}
		}
		else 
			$strError .= GetMessage("FORM_ERROR_STATUS_NOT_FOUND")."<br>";
		return false;
	}

	// копирует статус
	function Copy($ID, $CHECK_RIGHTS="Y", $NEW_FORM_ID=false)
	{
		global $DB, $APPLICATION, $strError;
		$err_mess = (CAllFormStatus::err_mess())."<br>Function: Copy<br>Line: ";
		$ID = intval($ID);
		$NEW_FORM_ID = intval($NEW_FORM_ID);
		$rsStatus = CFormStatus::GetByID($ID);
		if ($arStatus = $rsStatus->Fetch())
		{
			$RIGHT_OK = "N";
			if ($CHECK_RIGHTS!="Y" || CForm::IsAdmin()) $RIGHT_OK="Y";
			else
			{
				$F_RIGHT = CForm::GetPermission($arStatus["FORM_ID"]);
				// если имеем право на просмотр параметров формы
				if ($F_RIGHT>=25)
				{
					// если задана новая форма
					if ($NEW_FORM_ID>0)
					{
						$NEW_F_RIGHT = CForm::GetPermission($NEW_FORM_ID);
						// если имеем полный доступ на новую форму
						if ($NEW_F_RIGHT>=30) $RIGHT_OK = "Y";
					}
					elseif ($F_RIGHT>=30) // если имеем полный доступ на исходную форму
					{
						$RIGHT_OK = "Y";
					}
				}
			}

			// если права проверили то
			if ($RIGHT_OK=="Y")
			{
				CFormStatus::GetPermissionList($ID, $arPERMISSION_VIEW, $arPERMISSION_MOVE, $arPERMISSION_EDIT, $arPERMISSION_DELETE);
				// копируем
				$arFields = array(
					"FORM_ID"				=> ($NEW_FORM_ID>0) ? $NEW_FORM_ID : $arStatus["FORM_ID"],
					"C_SORT"				=> $arStatus["C_SORT"],
					"ACTIVE"				=> $arStatus["ACTIVE"],
					"TITLE"					=> $arStatus["TITLE"],
					"DESCRIPTION"			=> $arStatus["DESCRIPTION"],
					"CSS"					=> $arStatus["CSS"],
					"HANDLER_OUT"			=> $arStatus["HANDLER_OUT"],
					"HANDLER_IN"			=> $arStatus["HANDLER_IN"],
					"DEFAULT_VALUE"			=> $arStatus["DEFAULT_VALUE"],
					"arPERMISSION_VIEW"		=> $arPERMISSION_VIEW,
					"arPERMISSION_MOVE"		=> $arPERMISSION_MOVE,
					"arPERMISSION_EDIT"		=> $arPERMISSION_EDIT,
					"arPERMISSION_DELETE"	=> $arPERMISSION_DELETE,
					);
				$NEW_ID = CFormStatus::Set($arFields);
				return $NEW_ID;
			}
			else $strError .= GetMessage("FORM_ERROR_ACCESS_DENIED")."<br>";
		}
		else $strError .= GetMessage("FORM_ERROR_STATUS_NOT_FOUND")."<br>";
		return false;
	}
}
?>
