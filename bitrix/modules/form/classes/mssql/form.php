<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/form/classes/general/form_old.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/form/classes/general/form.php");

/***************************************
			Веб-форма
***************************************/

class CForm extends CAllForm
{
	function err_mess()
	{
		$module_id = "form";
		@include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/install/version.php");
		return "<br>Module: ".$module_id." (".constant(strtoupper($module_id)."_VERSION").")<br>Class: CForm<br>File: ".__FILE__;
	}

	// список веб-форм
	function GetList(&$by, &$order, $arFilter=Array(), &$is_filtered, $min_permission=10)
	{
		$err_mess = (CForm::err_mess())."<br>Function: GetList<br>Line: ";
		global $DB, $USER, $strError;
		$min_permission = intval($min_permission);
		$arSqlSearch = array();
		$arSqlSearch_1 = array();
		$strSqlSearch = "";
		$strSqlSearch_1 = "";
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
					case "NAME":
					case "DESCRIPTION":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("F.".$key, $val, $match);
						break;
					case "SITE":
						if (is_array($val)) $val = implode(" | ", $val);
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch_1[] = GetFilterQuery("FS.SITE_ID", $val, $match);
						$strSqlSearch_1 = GetFilterSqlSearch($arSqlSearch_1);
						$where = " and exists (SELECT 'x' FROM b_form_2_site FS WHERE $strSqlSearch_1 and F.ID = FS.FORM_ID) ";
						break;
				}
			}
		}

		if ($by == "s_id")								$strSqlOrder = "ORDER BY F.ID";
		elseif ($by == "s_c_sort" || $by == "s_sort")	$strSqlOrder = "ORDER BY F.C_SORT";
		elseif ($by == "s_name")						$strSqlOrder = "ORDER BY F.NAME";
		elseif ($by == "s_varname" || $by == "s_sid")	$strSqlOrder = "ORDER BY F.SID";		
		else 
		{
			$by = "s_c_sort";
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
		if (CForm::IsAdmin())
		{
			$strSql = "
				SELECT 
					F.ID, F.TIMESTAMP_X, F.NAME, F.SID, F.BUTTON, F.C_SORT, F.IMAGE_ID, F.DESCRIPTION, F.DESCRIPTION_TYPE, F.SHOW_TEMPLATE, F.MAIL_EVENT_TYPE, F.SHOW_RESULT_TEMPLATE, F.PRINT_RESULT_TEMPLATE, F.EDIT_RESULT_TEMPLATE, 
					F.FILTER_RESULT_TEMPLATE, F.TABLE_RESULT_TEMPLATE, F.STAT_EVENT1, F.STAT_EVENT2, F.STAT_EVENT3,
					F.SID											VARNAME,
					F.FIRST_SITE_ID,
					F.FIRST_SITE_ID									LID,
					".$DB->DateToCharFunction("F.TIMESTAMP_X")."	TIMESTAMP_X,
					count(distinct D1.ID)							C_FIELDS,
					count(distinct D2.ID)							QUESTIONS,
					count(distinct S.ID)							STATUSES
				FROM 
					b_form F
				LEFT JOIN b_form_status S ON (S.FORM_ID = F.ID)
				LEFT JOIN b_form_field D1 ON (D1.FORM_ID = F.ID and D1.ADDITIONAL='Y')
				LEFT JOIN b_form_field D2 ON (D2.FORM_ID = F.ID and D2.ADDITIONAL<>'Y')
				WHERE 
				$strSqlSearch
				$where
				GROUP BY
					F.ID, F.TIMESTAMP_X, F.NAME, F.SID, F.BUTTON, F.C_SORT, F.IMAGE_ID, F.DESCRIPTION, F.DESCRIPTION_TYPE, F.SHOW_TEMPLATE, F.MAIL_EVENT_TYPE, F.SHOW_RESULT_TEMPLATE, F.PRINT_RESULT_TEMPLATE, F.EDIT_RESULT_TEMPLATE, F.FILTER_RESULT_TEMPLATE, F.TABLE_RESULT_TEMPLATE, F.STAT_EVENT1, F.STAT_EVENT2, F.STAT_EVENT3, F.FIRST_SITE_ID
				$strSqlOrder
				";
		}
		else
		{
			$arGroups = $USER->GetUserGroupArray();
			if (!is_array($arGroups)) $arGroups[] = 2;
			$groups = implode(",",$arGroups);
			$def_permission = COption::GetOptionInt("form", "FORM_DEFAULT_PERMISSION", 10);
			$strSql = "
				SELECT 
					F.ID, F.TIMESTAMP_X, F.NAME, F.SID, F.BUTTON, F.C_SORT, F.IMAGE_ID, F.DESCRIPTION, F.DESCRIPTION_TYPE, F.SHOW_TEMPLATE, F.MAIL_EVENT_TYPE, F.SHOW_RESULT_TEMPLATE, F.PRINT_RESULT_TEMPLATE, F.EDIT_RESULT_TEMPLATE, F.FILTER_RESULT_TEMPLATE, F.TABLE_RESULT_TEMPLATE, F.STAT_EVENT1, F.STAT_EVENT2, F.STAT_EVENT3,
					F.SID											VARNAME,
					F.FIRST_SITE_ID,
					F.FIRST_SITE_ID									LID,
					".$DB->DateToCharFunction("F.TIMESTAMP_X")."	TIMESTAMP_X,
					count(distinct D1.ID)							C_FIELDS,
					count(distinct D2.ID)							QUESTIONS,
					count(distinct S.ID)							STATUSES
				FROM 
					b_form F
					".
					($def_permission >=$min_permission?
					"	LEFT JOIN b_form_2_group G ON (G.FORM_ID=F.ID and G.GROUP_ID in ($groups)) "
					:
					"	INNER JOIN b_form_2_group G ON (G.FORM_ID=F.ID and G.PERMISSION>=$min_permission and G.GROUP_ID in ($groups))	"
					)."
				LEFT JOIN b_form_status S ON (S.FORM_ID = F.ID)
				LEFT JOIN b_form_field D1 ON (D1.FORM_ID = F.ID and D1.ADDITIONAL='Y')
				LEFT JOIN b_form_field D2 ON (D2.FORM_ID = F.ID and D2.ADDITIONAL<>'Y')
				WHERE $strSqlSearch 
				$where".
				($def_permission >=$min_permission?
				"	AND (G.FORM_ID IS NULL OR G.PERMISSION>=$min_permission) "
				:
				""
				).
				"
				GROUP BY 
					F.ID, F.TIMESTAMP_X, F.NAME, F.SID, F.BUTTON, F.C_SORT, F.IMAGE_ID, F.DESCRIPTION, F.DESCRIPTION_TYPE, F.SHOW_TEMPLATE, F.MAIL_EVENT_TYPE, F.SHOW_RESULT_TEMPLATE, F.PRINT_RESULT_TEMPLATE, F.EDIT_RESULT_TEMPLATE, F.FILTER_RESULT_TEMPLATE, F.TABLE_RESULT_TEMPLATE, F.STAT_EVENT1, F.STAT_EVENT2, F.STAT_EVENT3, F.FIRST_SITE_ID
				$strSqlOrder
				";
		}
		//echo "<pre>".$strSql."</pre>";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		$is_filtered = (IsFiltered($strSqlSearch));
		return $res;
	}

	function GetByID($ID, $GET_BY_SID="N")
	{
		$err_mess = (CForm::err_mess())."<br>Function: GetByID<br>Line: ";
		global $DB, $strError;
		$where = ($GET_BY_SID=="N") ? " F.ID = '".intval($ID)."' " : " F.SID='".$DB->ForSql($ID,50)."' ";
		$strSql = "
			SELECT
				F.ID, F.TIMESTAMP_X, F.NAME, F.SID, F.BUTTON, F.C_SORT, F.IMAGE_ID, F.DESCRIPTION, F.DESCRIPTION_TYPE, F.SHOW_TEMPLATE, F.MAIL_EVENT_TYPE, F.SHOW_RESULT_TEMPLATE, F.PRINT_RESULT_TEMPLATE, F.EDIT_RESULT_TEMPLATE, F.FILTER_RESULT_TEMPLATE, F.TABLE_RESULT_TEMPLATE, F.STAT_EVENT1, F.STAT_EVENT2, F.STAT_EVENT3,
				".$DB->DateToCharFunction("F.TIMESTAMP_X")."	TIMESTAMP_X,
				F.FIRST_SITE_ID,
				F.FIRST_SITE_ID									LID,
				F.SID											VARNAME,
				count(distinct D1.ID)							C_FIELDS,
				count(distinct D2.ID)							QUESTIONS,
				count(distinct S.ID)							STATUSES
			FROM 
				b_form F
			LEFT JOIN b_form_status S ON (S.FORM_ID = F.ID)
			LEFT JOIN b_form_field D1 ON (D1.FORM_ID = F.ID and D1.ADDITIONAL='Y')
			LEFT JOIN b_form_field D2 ON (D2.FORM_ID = F.ID and D2.ADDITIONAL<>'Y')
			WHERE
				$where
			GROUP BY
				F.ID, F.TIMESTAMP_X, F.NAME, F.SID, F.BUTTON, F.C_SORT, F.IMAGE_ID, F.DESCRIPTION, F.DESCRIPTION_TYPE, F.SHOW_TEMPLATE, F.MAIL_EVENT_TYPE, F.SHOW_RESULT_TEMPLATE, F.PRINT_RESULT_TEMPLATE, F.EDIT_RESULT_TEMPLATE, F.FILTER_RESULT_TEMPLATE, F.TABLE_RESULT_TEMPLATE, F.STAT_EVENT1, F.STAT_EVENT2, F.STAT_EVENT3, F.FIRST_SITE_ID
			";
		//echo $strSql;
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}
}


/***************************************
		Результат веб-формы
***************************************/

class CFormResult extends CAllFormResult
{
	function err_mess()
	{
		$module_id = "form";
		@include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/install/version.php");
		return "<br>Module: ".$module_id." (".constant(strtoupper($module_id)."_VERSION").")<br>Class: CFormResult<br>File: ".__FILE__;
	}

	// список результатов
	function GetList($WEB_FORM_ID, &$by, &$order, $arFilter=Array(), &$is_filtered, $CHECK_RIGHTS="Y", $records_limit=false)
	{
		$err_mess = (CFormResult::err_mess())."<br>Function: GetList<br>Line: ";
		global $DB, $USER, $strError;
		$CHECK_RIGHTS = ($CHECK_RIGHTS=="Y") ? "Y" : "N";
		$WEB_FORM_ID = intval($WEB_FORM_ID);
		$F_RIGHT = CForm::GetPermission($WEB_FORM_ID);
		$USER_ID = intval($USER->GetID());
		$arSqlSearch = array();
		$arrSEARCH = array();
		$arrFIELDS = array();
		$strSqlSearch = "";
		if (is_array($arFilter))
		{
			$arFilter = CFormResult::PrepareFilter($WEB_FORM_ID, $arFilter);
			$z = CForm::GetByID($WEB_FORM_ID);
			$form = $z->Fetch();
			$z = CFormField::GetList($WEB_FORM_ID, "", $v1, $v2, array(), $v3);
			while ($zr=$z->Fetch()) 
			{
				$arPARAMETER_NAME = array("ANSWER_TEXT", "ANSWER_VALUE", "USER");
				CFormField::GetFilterTypeList($arrUSER, $arrANSWER_TEXT, $arrANSWER_VALUE, $arrFIELD);
				foreach ($arPARAMETER_NAME as $PARAMETER_NAME)
				{
					switch ($PARAMETER_NAME)
					{
						case "ANSWER_TEXT":
							$arFILTER_TYPE = $arrANSWER_TEXT["reference_id"];
							break;
						case "ANSWER_VALUE":
							$arFILTER_TYPE = $arrANSWER_VALUE["reference_id"];
							break;
						case "USER":
							$arFILTER_TYPE = $arrUSER["reference_id"];
							break;
					}
					foreach ($arFILTER_TYPE as $FILTER_TYPE)
					{
						$arrUF = array();
						$arrUF["ID"] = $zr["ID"];
						$arrUF["PARAMETER_NAME"] = $PARAMETER_NAME;
						$arrUF["FILTER_TYPE"] = $FILTER_TYPE;
						$FID = $form["SID"]."_".$zr["SID"]."_".$PARAMETER_NAME."_".$FILTER_TYPE;
						if ($FILTER_TYPE=="date" || $FILTER_TYPE=="integer")
						{
							$arrUF["SIDE"] = "1";
							$arrFORM_FILTER[$FID."_1"] = $arrUF;
							$arrUF["SIDE"] = "2";
							$arrFORM_FILTER[$FID."_2"] = $arrUF;
							$arrUF["SIDE"] = "0";
							$arrFORM_FILTER[$FID."_0"] = $arrUF;
						}
						else $arrFORM_FILTER[$FID] = $arrUF;
					}
				}
			}
			if (is_array($arrFORM_FILTER)) $arrFORM_FILTER_KEYS = array_keys($arrFORM_FILTER);

			//echo "arFilter:<pre>"; print_r($arFilter); echo "</pre>";
			//echo "arrFORM_FILTER:<pre>"; print_r($arrFORM_FILTER); echo "</pre>";
			//echo "arrFORM_FILTER_KEYS:<pre>"; print_r($arrFORM_FILTER_KEYS); echo "</pre>";

			$t = 0;
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
						$arSqlSearch[] = GetFilterQuery("R.ID", $val, $match);
						break;
					case "STATUS":
						$arSqlSearch[] = "R.STATUS_ID='".intval($val)."'";
						break;
					case "STATUS_ID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("R.STATUS_ID",$val,$match);
						break;
					case "TIMESTAMP_1":
						$arSqlSearch[] = "R.TIMESTAMP_X >= ".$DB->CharToDateFunction($val, "SHORT");
						break;
					case "TIMESTAMP_2":
						$arSqlSearch[] = "R.TIMESTAMP_X < dateadd(day, 1, ".$DB->CharToDateFunction($val, "SHORT").")";
						break;
					case "DATE_CREATE_1":
						$arSqlSearch[] = "R.DATE_CREATE >= ".$DB->CharToDateFunction($val, "SHORT");
						break;
					case "DATE_CREATE_2":
						$arSqlSearch[] = "R.DATE_CREATE < dateadd(day, 1, ".$DB->CharToDateFunction($val, "SHORT").")";
						break;
					case "REGISTERED":
						$arSqlSearch[] = ($val=="Y") ? "R.USER_ID>0" : "(R.USER_ID<=0 or R.USER_ID is null)";
						break;
					case "USER_AUTH":
						$arSqlSearch[] = ($val=="Y") ? "(R.USER_AUTH='Y' and R.USER_ID>0)" : "(R.USER_AUTH='N' and R.USER_ID>0)";
						break;
					case "USER_ID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("R.USER_ID", $val, $match);
						break;
					case "GUEST_ID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("R.STAT_GUEST_ID",$val,$match);
						break;
					case "SESSION_ID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("R.STAT_SESSION_ID",$val,$match);
						break;
					default:
						if (is_array($arrFORM_FILTER))
						{
							$key = $filter_keys[$i];
							if (in_array($key, $arrFORM_FILTER_KEYS))
							{
								$arrF = $arrFORM_FILTER[$key];
								if (!in_array($arrF["ID"],$arrFIELDS)) 
								{
									$t++;
									$arSqlSearch_f = array();
									$A = "A".$t;
									$arrFIELDS[$t] = $arrF["ID"];
								}
								switch(strtoupper($arrF["FILTER_TYPE"]))
								{
									case "EXIST":

										if ($arrF["PARAMETER_NAME"]=="ANSWER_TEXT")
											$arSqlSearch_f[] = "len(convert(varchar(8000),isnull(".$A.".ANSWER_TEXT,'')))>0";

										elseif ($arrF["PARAMETER_NAME"]=="ANSWER_VALUE")
											$arSqlSearch_f[] = "len(convert(varchar(8000),isnull(".$A.".ANSWER_VALUE,'')))>0";

										elseif ($arrF["PARAMETER_NAME"]=="USER")
											$arSqlSearch_f[] = "len(convert(varchar(8000),isnull(".$A.".USER_TEXT,'')))>0";

										break;

									case "TEXT":
										$match = ($arFilter[$key."_exact_match"]=="Y") ? "N" : "Y";
										$sql = "";

										if ($arrF["PARAMETER_NAME"]=="ANSWER_TEXT")
											$sql = GetFilterQuery($A.".ANSWER_TEXT_SEARCH", ToUpper($val), $match);

										elseif ($arrF["PARAMETER_NAME"]=="ANSWER_VALUE")
											$sql = GetFilterQuery($A.".ANSWER_VALUE_SEARCH", ToUpper($val), $match);

										elseif ($arrF["PARAMETER_NAME"]=="USER")
											$sql = GetFilterQuery($A.".USER_TEXT_SEARCH", ToUpper($val), $match);

										if ($sql!=="0" && strlen(trim($sql))>0) $arSqlSearch_f[] = $sql;
										break;
									case "DROPDOWN":
									case "ANSWER_ID":
											$arSqlSearch_f[] = $A.".ANSWER_ID=".intval($val);
										break;
									case "DATE":
										if ($arrF["PARAMETER_NAME"]=="USER")
										{
											if (CheckDateTime($val))
											{
												if ($arrF["SIDE"]=="1")
													$arSqlSearch_f[] = $A.".USER_DATE >= ".$DB->CharToDateFunction($val, "SHORT");

												elseif ($arrF["SIDE"]=="2")
													$arSqlSearch_f[] = $A.".USER_DATE < dateadd(day, 1, ".$DB->CharToDateFunction($val, "SHORT").")";

												elseif ($arrF["SIDE"]=="0")
													$arSqlSearch_f[] = $A.".USER_DATE=".$DB->CharToDateFunction($val);
											}
										}
										break;

									case "INTEGER":
										if ($arrF["PARAMETER_NAME"]=="USER")
										{
											if ($arrF["SIDE"]=="1")
												$arSqlSearch_f[] = "dbo.CONVERT_TO_NUMBER(convert(varchar(8000), ".$A.".USER_TEXT))>=".intval($val);

											elseif ($arrF["SIDE"]=="2")
												$arSqlSearch_f[] = "dbo.CONVERT_TO_NUMBER(convert(varchar(8000), ".$A.".USER_TEXT))<=".intval($val);

											elseif ($arrF["SIDE"]=="0")
												$arSqlSearch_f[] = "dbo.CONVERT_TO_NUMBER(convert(varchar(8000), ".$A.".USER_TEXT))=".intval($val);
										}
										elseif ($arrF["PARAMETER_NAME"]=="ANSWER_TEXT")
										{
											if ($arrF["SIDE"]=="1")
												$arSqlSearch_f[] = "dbo.CONVERT_TO_NUMBER(convert(varchar(8000), ".$A.".ANSWER_TEXT))>=".intval($val);
											elseif ($arrF["SIDE"]=="2")
												$arSqlSearch_f[] = "dbo.CONVERT_TO_NUMBER(convert(varchar(8000), ".$A.".ANSWER_TEXT))<=".intval($val);
											elseif ($arrF["SIDE"]=="0")
												$arSqlSearch_f[] = "dbo.CONVERT_TO_NUMBER(convert(varchar(8000), ".$A.".ANSWER_TEXT))=".intval($val);
										}
										elseif ($arrF["PARAMETER_NAME"]=="ANSWER_VALUE")
										{
											if ($arrF["SIDE"]=="1")
												$arSqlSearch_f[] = "dbo.CONVERT_TO_NUMBER(convert(varchar(8000), ".$A.".ANSWER_VALUE))>=".intval($val);
											elseif ($arrF["SIDE"]=="2")
												$arSqlSearch_f[] = "dbo.CONVERT_TO_NUMBER(convert(varchar(8000), ".$A.".ANSWER_VALUE))<=".intval($val);
											elseif ($arrF["SIDE"]=="0")
												$arSqlSearch_f[] = "dbo.CONVERT_TO_NUMBER(convert(varchar(8000), ".$A.".ANSWER_VALUE))=".intval($val);
										}
										break;
								}
								if (is_array($arSqlSearch_f) && count($arSqlSearch_f)>0)
									$arrSEARCH[$t] = $arSqlSearch_f;

							}					
						}
				}
			}
		}
		if ($by == "s_id")				$strSqlOrder = "ORDER BY R.ID";
		elseif ($by == "s_date_create")	$strSqlOrder = "ORDER BY R.DATE_CREATE";
		elseif ($by == "s_timestamp")	$strSqlOrder = "ORDER BY R.TIMESTAMP_X";
		elseif ($by == "s_user_id")		$strSqlOrder = "ORDER BY R.USER_ID";		
		elseif ($by == "s_guest_id")	$strSqlOrder = "ORDER BY R.STAT_GUEST_ID";
		elseif ($by == "s_session_id")	$strSqlOrder = "ORDER BY R.STAT_SESSION_ID";
		elseif ($by == "s_valid")		$strSqlOrder = "ORDER BY R.VALID";
		else 
		{
			$by = "s_timestamp";
			$strSqlOrder = "ORDER BY R.TIMESTAMP_X";
		}
		if ($order!="asc")
		{
			$strSqlOrder .= " desc ";
			$order="desc";
		}
		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		$strSqlSearch_F = "";
		if (is_array($arrSEARCH) && count($arrSEARCH)>0)
		{
			while (list($index,$arrS)=each($arrSEARCH))
			{
				$field = intval($arrFIELDS[$index]);
				if ($field>0)
				{
					$str = implode(" and ",$arrS);
					$strSqlSearch_F .= "
						and EXISTS (
							SELECT 'x' FROM b_form_result_answer A$index 
							WHERE
								A$index.RESULT_ID=R.ID 
							and A$index.FIELD_ID=$field
							and $str
						)
						";
				}
			}
		}

		$records_limit = ($records_limit===false) ? intval(COption::GetOptionString("form","RECORDS_LIMIT")) : intval($records_limit);
		if ($CHECK_RIGHTS!="Y" || CForm::IsAdmin())
		{
			$strSql = "
				SELECT /*TOP*/
					R.ID, R.USER_ID, R.USER_AUTH, R.STAT_GUEST_ID, R.STAT_SESSION_ID, R.STATUS_ID,
					".$DB->DateToCharFunction("R.DATE_CREATE")."	DATE_CREATE,
					".$DB->DateToCharFunction("R.TIMESTAMP_X")."	TIMESTAMP_X,
					S.TITLE				STATUS_TITLE,
					S.CSS				STATUS_CSS
				FROM 
					b_form_result R, 
					b_form_status S
				WHERE 
				$strSqlSearch
				$strSqlSearch_F
				and R.FORM_ID='$WEB_FORM_ID'
				and S.ID = R.STATUS_ID
				GROUP BY 
					R.ID, R.USER_ID, R.USER_AUTH, R.STAT_GUEST_ID, R.STAT_SESSION_ID, R.DATE_CREATE, R.TIMESTAMP_X, R.STATUS_ID, S.ID, S.TITLE, S.CSS
				$strSqlOrder
				";
			if ($records_limit>0)
			{
				$strSql = str_replace("/*TOP*/", "TOP ".$records_limit, $strSql);
			}
			$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		}
		elseif ($F_RIGHT>=15)
		{
			$arGroups = $USER->GetUserGroupArray();
			if (!is_array($arGroups)) $arGroups[] = 2;
			if (is_array($arGroups) && count($arGroups)>0) $groups = implode(",",$arGroups);
			if ($F_RIGHT<20) $str3 = "and isnull(R.USER_ID,0) = $USER_ID";

			$strSql = "
				SELECT /*TOP*/
					R.ID, R.USER_ID, R.USER_AUTH, R.STAT_GUEST_ID, R.STAT_SESSION_ID, R.STATUS_ID,
					".$DB->DateToCharFunction("R.DATE_CREATE")."	DATE_CREATE,
					".$DB->DateToCharFunction("R.TIMESTAMP_X")."	TIMESTAMP_X,
					S.TITLE				STATUS_TITLE,
					S.CSS				STATUS_CSS
				FROM 
					b_form_result R, 
					b_form_status S, 
					b_form_status_2_group G
				WHERE 
				$strSqlSearch
				$strSqlSearch_F
				and R.FORM_ID='$WEB_FORM_ID'
				and S.ID = R.STATUS_ID
				and G.STATUS_ID = S.ID
				and (
					(G.GROUP_ID in ($groups)) or
					(G.GROUP_ID in ($groups,0) and isnull(R.USER_ID,0) = $USER_ID and $USER_ID>0)
					)
				and G.PERMISSION in ('VIEW', 'EDIT', 'DELETE')
				GROUP BY 
					R.ID, R.USER_ID, R.USER_AUTH, R.STAT_GUEST_ID, R.STAT_SESSION_ID, R.DATE_CREATE, R.TIMESTAMP_X, R.STATUS_ID, S.ID, S.TITLE, S.CSS
				$strSqlOrder
				";
			if ($records_limit>0)
			{
				$strSql = str_replace("/*TOP*/", "TOP ".$records_limit, $strSql);
			}
			$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		}
		//echo "<pre>".$strSqlSearch."</pre>";
		//echo "<pre>".$strSql."</pre>";
		$is_filtered = (IsFiltered($strSqlSearch) || strlen($strSqlSearch_F)>0);
		return $res;
	}

	function GetByID($ID)
	{
		global $DB, $strError;
		$err_mess = (CFormResult::err_mess())."<br>Function: GetByID<br>Line: ";
		$ID = intval($ID);
		$strSql = "
			SELECT 
				R.*,
				".$DB->DateToCharFunction("R.DATE_CREATE")."	DATE_CREATE,
				".$DB->DateToCharFunction("R.TIMESTAMP_X")."	TIMESTAMP_X,
				F.NAME, F.IMAGE_ID, F.DESCRIPTION, F.DESCRIPTION_TYPE, F.SHOW_RESULT_TEMPLATE, F.PRINT_RESULT_TEMPLATE, F.EDIT_RESULT_TEMPLATE, 
				F.SID,
				F.SID											VARNAME,
				S.TITLE											STATUS_TITLE,
				S.DESCRIPTION									STATUS_DESCRIPTION,
				S.CSS											STATUS_CSS
			FROM 
				b_form_result R
			INNER JOIN b_form_status S ON (S.ID = R.STATUS_ID)
			INNER JOIN b_form F ON (F.ID = R.FORM_ID)
			WHERE
				R.ID = $ID
			";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}

	// права на результат
	function GetPermissions($RESULT_ID, &$CURRENT_STATUS_ID)
	{
		$err_mess = (CFormResult::err_mess())."<br>Function: GetPermissions<br>Line: ";
		global $DB, $USER, $strError;
		$USER_ID = intval($USER->GetID());
		$RESULT_ID = intval($RESULT_ID);
		$arrReturn = array();
		$arGroups = $USER->GetUserGroupArray();
		if (!is_array($arGroups)) $arGroups[] = 2;
		if (CForm::IsAdmin()) return CFormStatus::GetMaxPermissions();
		else
		{
			$arr = array();
			if (is_array($arGroups) && count($arGroups)>0) $groups = implode(",",$arGroups);
			$strSql = "
				SELECT
					G.PERMISSION,
					R.STATUS_ID
				FROM
					b_form_result R,
					b_form_status_2_group G
				WHERE
					R.ID = $RESULT_ID
				and R.STATUS_ID = G.STATUS_ID
				and (
					(G.GROUP_ID in ($groups) and isnull(R.USER_ID,0) <> $USER_ID) or
					(G.GROUP_ID in ($groups,0) and isnull(R.USER_ID,0) = $USER_ID)
					)
				";
			$z = $DB->Query($strSql, false, $err_mess.__LINE__);
			while ($zr = $z->Fetch()) 
			{
				$arrReturn[] = $zr["PERMISSION"];
				$CURRENT_STATUS_ID = $zr["STATUS_ID"];
			}
		}
		return $arrReturn;
	}

	function AddAnswer($arFields)
	{
		$err_mess = (CFormResult::err_mess())."<br>Function: AddAnswer<br>Line: ";
		global $DB, $strError;
		$arInsert = $DB->PrepareInsert("b_form_result_answer", $arFields, "form");
		$strSql = "INSERT INTO b_form_result_answer (".$arInsert[0].") VALUES (".$arInsert[1].")";
		$DB->Query($strSql, false, $err_mess.__LINE__);
		return intval($DB->LastID());
	}

	function UpdateField($arFields, $RESULT_ID, $FIELD_ID)
	{
		$err_mess = (CFormResult::err_mess())."<br>Function: UpdateField<br>Line: ";
		global $DB, $strError;
		$RESULT_ID = intval($RESULT_ID);
		$FIELD_ID = intval($FIELD_ID);
		$strUpdate = $DB->PrepareUpdate("b_form_result_answer", $arFields, "form");
		$strSql = "UPDATE b_form_result_answer SET ".$strUpdate." WHERE RESULT_ID=".$RESULT_ID." and FIELD_ID=".$FIELD_ID;
		$DB->Query($strSql, false, $err_mess.__LINE__);
	}
}

/***************************************
		Вопрос (поле) веб-формы
***************************************/

class CFormField extends CAllFormField
{
	function err_mess()
	{
		$module_id = "form";
		@include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/install/version.php");
		return "<br>Module: ".$module_id." (".constant(strtoupper($module_id)."_VERSION").")<br>Class: CFormField<br>File: ".__FILE__;
	}
}

/***************************************
		Ответ на вопрос веб-формы
***************************************/

class CFormAnswer extends CAllFormAnswer
{
	function err_mess()
	{
		$module_id = "form";
		@include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/install/version.php");
		return "<br>Module: ".$module_id." (".constant(strtoupper($module_id)."_VERSION").")<br>Class: CFormAnswer<br>File: ".__FILE__;
	}
}

/***************************************
		Статус результата веб-формы
***************************************/

class CFormStatus extends CAllFormStatus
{
	function err_mess()
	{
		$module_id = "form";
		@include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/install/version.php");
		return "<br>Module: ".$module_id." (".constant(strtoupper($module_id)."_VERSION").")<br>Class: CFormStatus<br>File: ".__FILE__;
	}

	// список статусов
	function GetList($FORM_ID, &$by, &$order, $arFilter=array(), &$is_filtered)
	{
		$err_mess = (CFormStatus::err_mess())."<br>Function: GetList<br>Line: ";
		global $DB, $strError;
		$FORM_ID = intval($FORM_ID);
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
						$arSqlSearch[] = GetFilterQuery("S.ID",$val,$match);
						break;
					case "ACTIVE":
						$arSqlSearch[] = ($val=="Y") ? "S.ACTIVE='Y'" : "S.ACTIVE='N'";
						break;
					case "TITLE":
					case "DESCRIPTION":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("S.".$key, $val, $match);
						break;
					case "RESULTS_1":
						$arSqlSearch_h[] = "count(R.ID)>='".intval($val)."'";
						break;
					case "RESULTS_2":
						$arSqlSearch_h[] = "count(R.ID)<='".intval($val)."'";
						break;
				}
			}
			for($i=0; $i<count($arSqlSearch_h); $i++) $strSqlSearch_h .= " and (".$arSqlSearch_h[$i].") ";
		}

		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		if ($by == "s_id")					$strSqlOrder = "ORDER BY S.ID";
		elseif ($by == "s_timestamp")		$strSqlOrder = "ORDER BY S.TIMESTAMP_X";
		elseif ($by == "s_active")			$strSqlOrder = "ORDER BY S.ACTIVE";
		elseif ($by == "s_c_sort" || 
				$by == "s_sort")			$strSqlOrder = "ORDER BY S.C_SORT";
		elseif ($by == "s_default")			$strSqlOrder = "ORDER BY S.DEFAULT_VALUE";		
		elseif ($by == "s_title")			$strSqlOrder = "ORDER BY S.TITLE ";
		elseif ($by == "s_description")		$strSqlOrder = "ORDER BY S.DESCRIPTION";
		elseif ($by == "s_results")			$strSqlOrder = "ORDER BY count(R.ID)";
		else 
		{
			$by = "s_sort";
			$strSqlOrder = "ORDER BY S.C_SORT";
		}
		if ($order!="desc")
		{
			$strSqlOrder .= " asc ";
			$order="asc";
		}
		else $strSqlOrder .= " desc ";

		$strSql = "
			SELECT 
				S.ID, S.CSS, S.FORM_ID, S.C_SORT, S.ACTIVE, S.TITLE, S.DESCRIPTION, S.DEFAULT_VALUE, S.HANDLER_OUT, S.HANDLER_IN,
				".$DB->DateToCharFunction("S.TIMESTAMP_X")."	TIMESTAMP_X,
				count(distinct R.ID) RESULTS
			FROM 
				b_form_status S
			LEFT JOIN b_form_result R ON (R.STATUS_ID=S.ID and R.FORM_ID=S.FORM_ID)
			WHERE
			$strSqlSearch
			and S.FORM_ID = $FORM_ID			
			GROUP BY 
				S.ID, S.CSS, S.FORM_ID, S.C_SORT, S.ACTIVE, S.TITLE, S.DESCRIPTION, S.TIMESTAMP_X, S.DEFAULT_VALUE, S.HANDLER_OUT, S.HANDLER_IN
			HAVING
				1=1
				$strSqlSearch_h
			$strSqlOrder
			";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		$is_filtered = (IsFiltered($strSqlSearch));
		return $res;
	}

	function GetByID($ID)
	{
		$err_mess = (CFormStatus::err_mess())."<br>Function: GetByID<br>Line: ";
		global $DB, $strError;
		$ID = intval($ID);
		$strSql = "
			SELECT 
				S.ID, S.CSS, S.FORM_ID, S.C_SORT, S.ACTIVE, S.TITLE, S.DESCRIPTION, S.DEFAULT_VALUE, S.HANDLER_OUT, S.HANDLER_IN,
				".$DB->DateToCharFunction("S.TIMESTAMP_X")."	TIMESTAMP_X,
				count(distinct R.ID) RESULTS
			FROM 
				b_form_status S
			LEFT JOIN b_form_result R ON (R.STATUS_ID=S.ID and R.FORM_ID=S.FORM_ID)
			WHERE
				S.ID = $ID
			GROUP BY 
				S.ID, S.CSS, S.FORM_ID, S.C_SORT, S.ACTIVE, S.TITLE, S.DESCRIPTION, S.TIMESTAMP_X, S.DEFAULT_VALUE, S.HANDLER_OUT, S.HANDLER_IN
			";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}

	function GetDropdown($FORM_ID, $PERMISSION = array("MOVE"), $OWNER_ID=0)
	{
		$err_mess = (CFormStatus::err_mess())."<br>Function: GetDropdown<br>Line: ";
		global $DB, $USER, $strError;
		$FORM_ID = intval($FORM_ID);
		if (CForm::IsAdmin())
		{
			$strSql = "
				SELECT
					S.ID											REFERENCE_ID,
					'['+convert(varchar(8000), S.ID)+'] '+S.TITLE	REFERENCE
				FROM
					b_form_status S
				WHERE
					S.FORM_ID = $FORM_ID
				and S.ACTIVE = 'Y'
				ORDER BY S.C_SORT
				";
		}
		else
		{
			if (is_array($PERMISSION)) $arrPERMISSION = $PERMISSION;
			else
			{
				if (intval($PERMISSION)==2) $PERMISSION = "MOVE";
				if (intval($PERMISSION)==1) $PERMISSION = "VIEW, MOVE";
				$arrPERMISSION = explode(",",$PERMISSION);
			}
			$str = "''";
			$arrPERM = array();
			if (is_array($arrPERMISSION) && count($arrPERMISSION)>0)
			{
				foreach ($arrPERMISSION as $perm)
				{
					$arrPERM[] = trim($perm);
					$str .= ",'".$DB->ForSql(trim($perm))."'";
				}
			}
			$arGroups = $USER->GetUserGroupArray();
			if (!is_array($arGroups)) $arGroups[] = 2;
			if ($OWNER_ID==$USER->GetID() || (in_array("VIEW",$arrPERM) && in_array("MOVE",$arrPERM))) $arGroups[] = 0;
			if (is_array($arGroups) && count($arGroups)>0) $groups = implode(",",$arGroups);
			$strSql = "
				SELECT
					S.ID											REFERENCE_ID,
					'['+convert(varchar(8000), S.ID)+'] '+S.TITLE	REFERENCE
				FROM
					b_form_status S,
					b_form_status_2_group G
				WHERE
					S.FORM_ID = $FORM_ID
				and S.ACTIVE = 'Y'
				and G.STATUS_ID = S.ID
				and G.GROUP_ID in ($groups)
				and G.PERMISSION in ($str)
				GROUP BY 
					S.ID, S.TITLE, S.C_SORT
				ORDER BY S.C_SORT
				";
		}
		//echo $strSql;
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $z;
	}
}
?>
