<?
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
				F.ID, F.TIMESTAMP_X, F.NAME, F.SID, F.BUTTON, F.C_SORT, F.IMAGE_ID, F.DESCRIPTION, F.DESCRIPTION_TYPE, F.SHOW_TEMPLATE, F.MAIL_EVENT_TYPE, F.SHOW_RESULT_TEMPLATE, F.PRINT_RESULT_TEMPLATE, F.EDIT_RESULT_TEMPLATE, F.FILTER_RESULT_TEMPLATE, F.TABLE_RESULT_TEMPLATE, F.STAT_EVENT1, F.STAT_EVENT2, F.STAT_EVENT3, F.USE_CAPTCHA, F.USE_DEFAULT_TEMPLATE, F.USE_RESTRICTIONS, F.RESTRICT_USER, F.RESTRICT_TIME, F.RESTRICT_STATUS,
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
				F.ID, F.TIMESTAMP_X, F.NAME, F.SID, F.BUTTON, F.C_SORT, F.IMAGE_ID, F.DESCRIPTION, F.DESCRIPTION_TYPE, F.SHOW_TEMPLATE, F.MAIL_EVENT_TYPE, F.SHOW_RESULT_TEMPLATE, F.PRINT_RESULT_TEMPLATE, F.EDIT_RESULT_TEMPLATE, F.FILTER_RESULT_TEMPLATE, F.TABLE_RESULT_TEMPLATE, F.STAT_EVENT1, F.STAT_EVENT2, F.STAT_EVENT3, F.FIRST_SITE_ID, F.USE_CAPTCHA, F.USE_DEFAULT_TEMPLATE , F.USE_RESTRICTIONS, F.RESTRICT_USER, F.RESTRICT_TIME, F.RESTRICT_STATUS
			";
		//echo "<pre>$strSql</pre>";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}
	
	function GetFormTemplateByID($ID, $GET_BY_SID="N")
	{
		$err_mess = (CForm::err_mess())."<br>Function: GetFormTemplateByID<br>Line: ";
		global $DB, $strError;

		$where = ($GET_BY_SID=="N") ? " F.ID = '".intval($ID)."' " : " F.SID='".$DB->ForSql($ID,50)."' ";
		
		$strSql = "
			SELECT
				F.FORM_TEMPLATE FT
			FROM b_form F
			WHERE
				$where
			";
		//echo "<pre>".$strSql."</pre>";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		if ($arRes = $res->Fetch()) 
		{
			return $arRes["FT"];
		}
		else return "";
	}
}

?>