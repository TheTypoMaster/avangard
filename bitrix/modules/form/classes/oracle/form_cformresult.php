<?
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
						$arSqlSearch[] = "R.TIMESTAMP_X>=".$DB->CharToDateFunction($val, "SHORT");
						break;
					case "TIMESTAMP_2":
						$arSqlSearch[] = "R.TIMESTAMP_X<".$DB->CharToDateFunction($val, "SHORT")."+1";
						break;
					case "DATE_CREATE_1":
						$arSqlSearch[] = "R.DATE_CREATE>=".$DB->CharToDateFunction($val, "SHORT");
						break;
					case "DATE_CREATE_2":
						$arSqlSearch[] = "R.DATE_CREATE<".$DB->CharToDateFunction($val, "SHORT")."+1";
						break;
					case "TIME_CREATE_1":
						$arSqlSearch[] = "R.DATE_CREATE>=".$DB->CharToDateFunction($val, "FULL");
						break;
					case "TIME_CREATE_2":
						$arSqlSearch[] = "R.DATE_CREATE<".$DB->CharToDateFunction($val, "FULL");
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
											$arSqlSearch_f[] = $A.".ANSWER_TEXT is not null";

										elseif ($arrF["PARAMETER_NAME"]=="ANSWER_VALUE")
											$arSqlSearch_f[] = $A.".ANSWER_VALUE is not null";

										elseif ($arrF["PARAMETER_NAME"]=="USER")
											$arSqlSearch_f[] = $A.".USER_TEXT is not null";

										break;

									case "TEXT":
										$match = ($arFilter[$key."_exact_match"]=="Y") ? "N" : "Y";
										$sql = "";

										if ($arrF["PARAMETER_NAME"]=="ANSWER_TEXT")
											$sql = GetFilterQuery($A.".ANSWER_TEXT_SEARCH", ToUpper($val), $match, array(),"Y","Y","Y");

										elseif ($arrF["PARAMETER_NAME"]=="ANSWER_VALUE")
											$sql = GetFilterQuery($A.".ANSWER_VALUE_SEARCH", ToUpper($val), $match, array(),"Y","Y","Y");

										elseif ($arrF["PARAMETER_NAME"]=="USER")
											$sql = GetFilterQuery($A.".USER_TEXT_SEARCH",ToUpper($val), $match, array(),"Y","Y","Y");

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
													$arSqlSearch_f[] = $A.".USER_DATE>=".$DB->CharToDateFunction($val, "SHORT");

												elseif ($arrF["SIDE"]=="2")
													$arSqlSearch_f[] = $A.".USER_DATE<".$DB->CharToDateFunction($val, "SHORT")."+1";

												elseif ($arrF["SIDE"]=="0")
													$arSqlSearch_f[] = $A.".USER_DATE=".$DB->CharToDateFunction($val);
											}
										}
										break;

									case "INTEGER":
										if ($arrF["PARAMETER_NAME"]=="USER")
										{
											if ($arrF["SIDE"]=="1")
												$arSqlSearch_f[] = "
													CONVERT_TO_NUMBER(
														DBMS_LOB.SUBSTR(".$A.".USER_TEXT, 
															DBMS_LOB.GETLENGTH(".$A.".USER_TEXT),
															1)
													)>=".intval($val);
											elseif ($arrF["SIDE"]=="2")
												$arSqlSearch_f[] = "
													CONVERT_TO_NUMBER(
															DBMS_LOB.SUBSTR(".$A.".USER_TEXT,
															DBMS_LOB.GETLENGTH(".$A.".USER_TEXT),
															1)
													)<=".intval($val);
											elseif ($arrF["SIDE"]=="0")
												$arSqlSearch_f[] = "
													CONVERT_TO_NUMBER(
															DBMS_LOB.SUBSTR(".$A.".USER_TEXT,
															DBMS_LOB.GETLENGTH(".$A.".USER_TEXT),
															1)
													)=".intval($val);
										}
										elseif ($arrF["PARAMETER_NAME"]=="ANSWER_TEXT")
										{
											if ($arrF["SIDE"]=="1")
												$arSqlSearch_f[] = "CONVERT_TO_NUMBER(".$A.".ANSWER_TEXT)>=".intval($val);
											elseif ($arrF["SIDE"]=="2")
												$arSqlSearch_f[] = "CONVERT_TO_NUMBER(".$A.".ANSWER_TEXT)<=".intval($val);
											elseif ($arrF["SIDE"]=="0")
												$arSqlSearch_f[] = "CONVERT_TO_NUMBER(".$A.".ANSWER_TEXT)=".intval($val);
										}
										elseif ($arrF["PARAMETER_NAME"]=="ANSWER_VALUE")
										{
											if ($arrF["SIDE"]=="1")
												$arSqlSearch_f[] = "CONVERT_TO_NUMBER(".$A.".ANSWER_VALUE)>=".intval($val);
											elseif ($arrF["SIDE"]=="2")
												$arSqlSearch_f[] = "CONVERT_TO_NUMBER(".$A.".ANSWER_VALUE)<=".intval($val);
											elseif ($arrF["SIDE"]=="0")
												$arSqlSearch_f[] = "CONVERT_TO_NUMBER(".$A.".ANSWER_VALUE)=".intval($val);
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
			reset($arrSEARCH);
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
				SELECT 
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
				$strSql = "SELECT * FROM ($strSql) WHERE ROWNUM<=".$records_limit;
			}
			$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		}
		elseif ($F_RIGHT>=15)
		{
			$arGroups = $USER->GetUserGroupArray();
			if (!is_array($arGroups)) $arGroups[] = 2;
			if (is_array($arGroups) && count($arGroups)>0) $groups = implode(",",$arGroups);
			if ($F_RIGHT<20) $str3 = "and nvl(R.USER_ID,0) = $USER_ID";

			$strSql = "
				SELECT 
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
					(G.GROUP_ID in ($groups,0) and nvl(R.USER_ID,0) = $USER_ID and $USER_ID>0)
					)
				and G.PERMISSION in ('VIEW', 'EDIT', 'DELETE')
				GROUP BY 
					R.ID, R.USER_ID, R.USER_AUTH, R.STAT_GUEST_ID, R.STAT_SESSION_ID, R.DATE_CREATE, R.TIMESTAMP_X, R.STATUS_ID, S.ID, S.TITLE, S.CSS
				$strSqlOrder
				";
			if ($records_limit>0)
			{
				$strSql = "SELECT * FROM ($strSql) WHERE ROWNUM<=".$records_limit;
			}
			$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		}
		else
		{
			$res = new CDBResult();
			$res->InitFromArray(array());
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
				b_form_result R,
				b_form_status S,
				b_form F
			WHERE
				R.ID = $ID
			and F.ID = R.FORM_ID
			and R.STATUS_ID = S.ID
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
					(G.GROUP_ID in ($groups) and nvl(R.USER_ID,0) <> $USER_ID) or
					(G.GROUP_ID in ($groups,0) and nvl(R.USER_ID,0) = $USER_ID)
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
		$arBinds = array();
		$arInsert = $DB->PrepareInsert("b_form_result_answer", $arFields, "form");
		$AID = $DB->NextID("sq_b_form_result_answer");
		$strSql="INSERT INTO b_form_result_answer (ID,".$arInsert[0].") VALUES ($AID,".$arInsert[1].")";
		if (is_set($arFields, "USER_TEXT")) 
			$arBinds["USER_TEXT"] = $arFields["USER_TEXT"];
		if (is_set($arFields, "ANSWER_TEXT_SEARCH")) 
			$arBinds["ANSWER_TEXT_SEARCH"] = $arFields["ANSWER_TEXT_SEARCH"];
		if (is_set($arFields, "ANSWER_VALUE_SEARCH"))
			$arBinds["ANSWER_VALUE_SEARCH"] = $arFields["ANSWER_VALUE_SEARCH"];
		if (is_set($arFields, "USER_TEXT_SEARCH"))
			$arBinds["USER_TEXT_SEARCH"] = $arFields["USER_TEXT_SEARCH"];
		$DB->QueryBind($strSql, $arBinds);
		return $AID;
	}

	function UpdateField($arFields, $RESULT_ID, $FIELD_ID)
	{
		$err_mess = (CFormResult::err_mess())."<br>Function: UpdateField<br>Line: ";
		global $DB, $strError;
		$arBinds = array();
		$RESULT_ID = intval($RESULT_ID);
		$FIELD_ID = intval($FIELD_ID);
		$DB->StartTransaction();
		$strSql = "SELECT ID FROM b_form_result_answer WHERE RESULT_ID=".$RESULT_ID." and FIELD_ID=".$FIELD_ID." FOR UPDATE ";
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		while ($zr=$z->Fetch())
		{
			$strUpdate = $DB->PrepareUpdate("b_form_result_answer", $arFields, "form");
			$strSql = "UPDATE b_form_result_answer SET ".$strUpdate." WHERE ID=".$zr["ID"];
			if (is_set($arFields, "ANSWER_TEXT_SEARCH"))
				$arBinds["ANSWER_TEXT_SEARCH"] = $arFields["ANSWER_TEXT_SEARCH"];
			if (is_set($arFields, "ANSWER_VALUE_SEARCH"))
				$arBinds["ANSWER_VALUE_SEARCH"] = $arFields["ANSWER_VALUE_SEARCH"];
			if (is_set($arFields, "USER_TEXT_SEARCH"))
				$arBinds["USER_TEXT_SEARCH"] = $arFields["USER_TEXT_SEARCH"];
			if (is_set($arFields, "USER_TEXT"))
				$arBinds["USER_TEXT"] = $arFields["USER_TEXT"];
			$DB->QueryBind($strSql, $arBinds);
		}
		$DB->Commit();
	}
}
?>