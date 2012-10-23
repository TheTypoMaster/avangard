<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/***********************************************************************
Component for web-form results list representation

This universal component is for list of web-form results representing. This is a standard component, it is included to Web Forms module distributive

example of using:

$APPLICATION->IncludeFile("form/result_list/default.php", array(
	"WEB_FORM_ID" => $_REQUEST["WEB_FORM_ID"], 
	"WEB_FORM_NAME" => $WEB_FORM_NAME,
	"VIEW_URL" => "result_view.php",
	"EDIT_URL" => "result_edit.php",
	"NEW_URL" => "result_new.php",
	"SHOW_ADDITIONAL" => "N",
	"SHOW_ANSWER_VALUE" => "N",
	"SHOW_STATUS" => "Y",
	"NOT_SHOW_FILTER" => "",
	"NOT_SHOW_TABLE" => ""
	));

Parameters:

$WEB_FORM_ID		- web-form ID
$VIEW_URL			- page URL fot result veiwing (if empty, no view link will show)
$EDIT_URL			- page URL for result editing (if empty, no edit link will show)
$NEW_URL			- page URL for form filling and new result creating
$SHOW_ADDITIONAL	- [Y|N] - Y - show result fields at the edit form (don't mix up with "questions")
$SHOW_ANSWER_VALUE	- [Y|N] - Y - show value of parameter "ANSWER_VALUE" of web-form question (shows in round brackets near answer value).
$SHOW_STATUS		- [Y|N] - Y - show curremt status of web-form result
$NOT_SHOW_FILTER	- symbolic links of questions and fields those are not needed to show in the filter (use comma to separate)
$NOT_SHOW_TABLE		- symbolic links of questions and fields those are not needed to show in the table (use comma to separate)

***********************************************************************/

global $USER, $APPLICATION;

function CheckFilter(&$str_error) // check of filter values
{
	global $strError, $MESS, $HTTP_GET_VARS, $arrFORM_FILTER;
	global $find_date_create_1, $find_date_create_2;
	$str = "";
	
	CheckFilterDates($find_date_create_1, $find_date_create_2, $date1_wrong, $date2_wrong, $date2_less);
	if ($date1_wrong=="Y") $str.= GetMessage("FORM_WRONG_DATE_CREATE_FROM")."<br>";
	if ($date2_wrong=="Y") $str.= GetMessage("FORM_WRONG_DATE_CREATE_TO")."<br>";
	if ($date2_less=="Y") $str.= GetMessage("FORM_FROM_TILL_DATE_CREATE")."<br>";
	
	if (is_array($arrFORM_FILTER)) 
	{
		reset($arrFORM_FILTER);
		foreach ($arrFORM_FILTER as $arrF)
		{
			if (is_array($arrF))
			{
				foreach ($arrF as $arr)
				{
					$title = ($arr["TITLE_TYPE"]=="html") ? strip_tags(htmlspecialcharsback($arr["TITLE"])) : $arr["TITLE"];
					if ($arr["FILTER_TYPE"]=="date")
					{
						$date1 = $HTTP_GET_VARS["find_".$arr["FID"]."_1"];
						$date2 = $HTTP_GET_VARS["find_".$arr["FID"]."_2"];
						CheckFilterDates($date1, $date2, $date1_wrong, $date2_wrong, $date2_less);
						if ($date1_wrong=="Y") 
							$str .= str_replace("#TITLE#", $title, GetMessage("FORM_WRONG_DATE1"))."<br>";
						if ($date2_wrong=="Y") 
							$str .= str_replace("#TITLE#", $title, GetMessage("FORM_WRONG_DATE2"))."<br>";
						if ($date2_less=="Y") 
							$str .= str_replace("#TITLE#", $title, GetMessage("FORM_DATE2_LESS"))."<br>";
					}
					if ($arr["FILTER_TYPE"]=="integer")
					{
						$int1 = intval($HTTP_GET_VARS["find_".$arr["FID"]."_1"]);
						$int2 = intval($HTTP_GET_VARS["find_".$arr["FID"]."_2"]);
						if ($int1>0 && $int2>0 && $int2<$int1)
						{
							$str .= str_replace("#TITLE#", $title, GetMessage("FORM_INT2_LESS"))."<br>";
						}
					}
				}
			}
		}
	}
	$strError .= $str;
	$str_error .= $str;
	if (strlen($str)>0) return false; else return true;
}

if (CModule::IncludeModule("form")) 
{
	$bSimple = (COption::GetOptionString("form", "SIMPLE", "Y") == "Y") ? true : false;
	if ($bSimple)
		$SHOW_STATUS='N';

	IncludeTemplateLangFile(__FILE__);

	if (is_array($_REQUEST)) extract($_REQUEST, EXTR_SKIP);
	$APPLICATION->SetTemplateCSS("form/form.css");

	/***************************************************************************
								   get data
	****************************************************************************/

	$WEB_FORM_ID = intval($arParams["WEB_FORM_ID"]);
	$WEB_FORM_NAME = $arParams["WEB_FORM_NAME"];

	$arrNOT_SHOW_FILTER = explode(",",$arParams["NOT_SHOW_FILTER"]);
	if (is_array($arrNOT_SHOW_FILTER)) array_walk($arrNOT_SHOW_FILTER, create_function("&\$item", "\$item=trim(\$item);"));
	else $arrNOT_SHOW_FILTER=array();
	
	$arrNOT_SHOW_TABLE = explode(",",$arParams["NOT_SHOW_TABLE"]);
	if (is_array($arrNOT_SHOW_TABLE)) array_walk($arrNOT_SHOW_TABLE, create_function("&\$item", "\$item=trim(\$item);"));
	else $arrNOT_SHOW_TABLE=array();

	if (intval($WEB_FORM_ID)>0) $z = CForm::GetByID($WEB_FORM_ID); else $z = CForm::GetBySID($WEB_FORM_NAME);
	if ($zr=$z->Fetch()) 
	{

		$GLOBALS["WEB_FORM_ID"] = $WEB_FORM_ID = $zr["ID"];
		$GLOBALS["WEB_FORM_NAME"] = $WEB_FORM_NAME = $zr["SID"];

		$USER_ID = $USER->GetID();
		$F_RIGHT = CForm::GetPermission($WEB_FORM_ID);
		if($F_RIGHT<15) $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

		/*
		if (is_array($ARR_RESULT) && count($ARR_RESULT)>0 && strlen($_GET['delete'])>0 && check_bitrix_sessid()) 
		{
			foreach($ARR_RESULT as $rid) CFormResult::Delete($rid);
		}
		*/

		// deleting single form result
		$del_id = intval($del_id);
		if ($del_id>0 && check_bitrix_sessid()) CFormResult::Delete($del_id);
		
		// deleting multiple form results
		if ($delete && is_array($ARR_RESULT) && count($ARR_RESULT) > 0 && check_bitrix_sessid()) 
		{
			reset($ARR_RESULT);
			while (list($num, $del_id) = each($ARR_RESULT)) 
			{
				$del_id = intval($del_id);
				if ($del_id > 0) CFormResult::Delete($del_id);
			}
		}
		
		$FilterArr = Array(
			"find_id",
			"find_id_exact_match",
			"find_status",
			"find_status_id",
			"find_status_id_exact_match",
			"find_timestamp_1",
			"find_timestamp_2",
			"find_date_create_2",
			"find_date_create_1",
			"find_date_create_2",
			"find_registered",
			"find_user_auth",
			"find_user_id",
			"find_user_id_exact_match",
			"find_guest_id",
			"find_guest_id_exact_match",
			"find_session_id",
			"find_session_id_exact_match"
			);
		$z = CFormField::GetFilterList($WEB_FORM_ID, array("ACTIVE" => "Y"));
		while ($zr=$z->Fetch()) 
		{
			$FID = $WEB_FORM_NAME."_".$zr["SID"]."_".$zr["PARAMETER_NAME"]."_".$zr["FILTER_TYPE"];
			$zr["FID"] = $FID;
			$arrFORM_FILTER[$zr["SID"]][] = $zr;
			$fname = "find_".$FID;
			if ($zr["FILTER_TYPE"]=="date" || $zr["FILTER_TYPE"]=="integer")
			{
				$FilterArr[] = $fname."_1";
				$FilterArr[] = $fname."_2";
				$FilterArr[] = $fname."_0";
			}
			elseif ($zr["FILTER_TYPE"]=="text")
			{
				$FilterArr[] = $fname;
				$FilterArr[] = $fname."_exact_match";
			}
			else $FilterArr[] = $fname;
		}
		
		$sess_filter = "FORM_RESULT_LIST_".$WEB_FORM_NAME;
		if (strlen($set_filter)>0) 
			InitFilterEx($FilterArr,$sess_filter,"set"); 
		else 
			InitFilterEx($FilterArr,$sess_filter,"get");
		if (strlen($del_filter)>0) 
		{
			DelFilterEx($FilterArr,$sess_filter);
		}
		else 
		{
			InitBVar($find_id_exact_match);
			InitBVar($find_status_id_exact_match);
			InitBVar($find_user_id_exact_match);
			InitBVar($find_guest_id_exact_match);
			InitBVar($find_session_id_exact_match);
			$str_error = "";
			if (CheckFilter($str_error))
			{
				$arFilter = Array(
					"ID"						=> $find_id,
					"ID_EXACT_MATCH"			=> $find_id_exact_match,
					"STATUS"					=> $find_status,
					"STATUS_ID"					=> $find_status_id,
					"STATUS_ID_EXACT_MATCH"		=> $find_status_id_exact_match,
					"TIMESTAMP_1"				=> $find_timestamp_1,
					"TIMESTAMP_2"				=> $find_timestamp_2,
					"DATE_CREATE_1"				=> $find_date_create_1,
					"DATE_CREATE_2"				=> $find_date_create_2,
					"REGISTERED"				=> $find_registered,
					"USER_AUTH"					=> $find_user_auth,
					"USER_ID"					=> $find_user_id,
					"USER_ID_EXACT_MATCH"		=> $find_user_id_exact_match,
					"GUEST_ID"					=> $find_guest_id,
					"GUEST_ID_EXACT_MATCH"		=> $find_guest_id_exact_match,
					"SESSION_ID"				=> $find_session_id,
					"SESSION_ID_EXACT_MATCH"	=> $find_session_id_exact_match
					);
				if (is_array($arrFORM_FILTER)) 
				{
					foreach ($arrFORM_FILTER as $arrF)
					{
						foreach ($arrF as $arr)
						{
							if ($arr["FILTER_TYPE"]=="date" || $arr["FILTER_TYPE"]=="integer")
							{
								$arFilter[$arr["FID"]."_1"] = ${"find_".$arr["FID"]."_1"};
								$arFilter[$arr["FID"]."_2"] = ${"find_".$arr["FID"]."_2"};
								$arFilter[$arr["FID"]."_0"] = ${"find_".$arr["FID"]."_0"};
							}
							elseif ($arr["FILTER_TYPE"]=="text")
							{
								$arFilter[$arr["FID"]] = ${"find_".$arr["FID"]};
								$exact_match = (${"find_".$arr["FID"]."_exact_match"}=="Y") ? "Y" : "N";
								$arFilter[$arr["FID"]."_exact_match"] = $exact_match;
							}
							else $arFilter[$arr["FID"]] = ${"find_".$arr["FID"]};
						}
					}
				}
			}
		}

		// if "Save" button has been pressed
		if (strlen($_POST['save'])>0 && $GLOBALS['REQUEST_METHOD']=="POST" && check_bitrix_sessid())
		{
			// update results
			if (isset($RESULT_ID) && is_array($RESULT_ID))
			{
				foreach ($RESULT_ID as $rid)
				{
					$rid = intval($rid);
					$var_STATUS_PREV = "STATUS_PREV_".$rid;
					$var_STATUS = "STATUS_".$rid;
					if (intval($$var_STATUS)>0 && $$var_STATUS_PREV!=$$var_STATUS)
					{
						CFormResult::SetStatus($rid, $$var_STATUS);
					}
				}
			}
		}

		$rsResults = CFormResult::GetList($WEB_FORM_ID, $by, $order, $arFilter, $is_filtered);
		//echo "<pre>"; print_r($rsResults); echo "</pre>";

		/***************************************************************************
									   HTML form
		****************************************************************************/

		if (strlen($tf)<=0) $tf = ${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_FORM_RESULT_FILTER"};
		if (strlen($tf)<=0) $tf = "none";
		$is_ie = IsIE();
		//if($is_ie):
		?>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		function Form_Filter_Click()
		{
			sName = "<?echo COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_FORM_RESULT_FILTER"?>";
			if (document.getElementById("form_filter").style.display!="none") 
			{
				document.getElementById("form_filter").style.display = "none";
				document.cookie = sName+"="+"none"+"; expires=Fri, 31 Dec 2030 23:59:59 GMT;";
			}
			else 
			{
				document.getElementById("form_filter").style.display = "inline";
				document.cookie = sName+"="+"inline"+"; expires=Fri, 31 Dec 2030 23:59:59 GMT;";
			}
		}
		//-->
		</SCRIPT>
		<table border="0" cellspacing="1" cellpadding="0" >
			<tr>
				<td valign="center" nowrap><font class="smalltxt"><? echo ($is_filtered) ? "<font class='filteron'>".GetMessage("FORM_FILTER_ON") : "<font class='filteroff'>".GetMessage("FORM_FILTER_OFF")?></font></font></td>
				<td valign="center" nowrap><img src="/bitrix/images/1.gif" width="10" height="1"><font class="tablebodytext">[ <a class="tablebodylink" href="javascript:void(0)" OnClick="javascript:Form_Filter_Click()"><?=GetMessage("FORM_FILTER")?></a> ]</font></td>
			</tr>
			<tr><td></td></tr>
		</table>
		<?//endif;?>
		<?
		/***********************************************
						  filter
		************************************************/
		?>
		<form name="form1" method="GET" action="<?=$APPLICATION->GetCurPage()?>?" id="form_filter" <? 
		//if($is_ie): ?>style="display:<?=$tf?>"<?//endif;?>>
		<?/*if(!$is_ie):?>
		<table border="0" cellspacing="1" cellpadding="0" >
			<tr valign="top">
				<td nowrap><font class="smalltxt"><? echo ($is_filtered) ? "<font class='filteron'>".GetMessage("FORM_FILTER_ON") : "<font class='filteroff'>".GetMessage("FORM_FILTER_OFF")?></font></font><img src="/bitrix/images/1.gif" width="1" height="2"><br><img src="/bitrix/images/1.gif" width="1" height="3"></td>
			</tr>
		</table>
		<?endif; */?>
		<input type="hidden" name="WEB_FORM_ID" value="<?=$WEB_FORM_ID?>">
		<table border="0" cellspacing="1" cellpadding="0" class="tableborder" >
			<tr valign="top">
				<td width="100%">
					<table border="0" cellspacing="0" cellpadding="3" class="tablebody" width="100%">
						<tr>
							<td valign="center" colspan="2" align="center" nowrap><img src="/bitrix/images/1.gif" width="1" height="3"></td>
						</tr>
						<? if (strlen($str_error) > 0) {?><tr>
							<td valign="top" colspan="2" class="tablebodytext statusred" align="center"><?=$str_error; ?></td>
						</tr><? } // endif (strlen($str_error) > 0)?>
						<tr> 
							<td align="right"><font class="tablebodytext"><?=GetMessage("FORM_F_ID")?></font></td>
							<td><font class="tablebodytext"><?=CForm::GetTextFilter("id", 45, "class='inputtext'", "class='inputcheckbox'")?></font></td>
						</tr>
						<?if ($SHOW_STATUS=="Y") {?>
						<tr> 
							<td align="right" valign="top"><font class="tablebodytext"><?echo GetMessage("FORM_F_STATUS")?></font></td>
							<td><font class="tablebodytext"><?
								echo SelectBox("find_status", CFormStatus::GetDropdown($WEB_FORM_ID, array("MOVE")), GetMessage("FORM_ALL"), htmlspecialchars($find_status), "class='inputselect'", "class='inputcheckbox'");
								?></font></td>
						</tr>
						<tr> 
							<td align="right" valign="top"><font class="tablebodytext"><?echo GetMessage("FORM_F_STATUS_ID")?></font></td>
							<td><font class="tablebodytext"><?echo CForm::GetTextFilter("status_id", 45, "class='inputtext'", "class='inputcheckbox'");?></font></td>
						</tr>
						<? } //endif ($SHOW_STATUS=="Y");?>
						<tr valign="center">
							<td align="right" width="0%" nowrap><font class="tablebodytext"><?echo GetMessage("FORM_F_DATE_CREATE")." (".CSite::GetDateFormat("SHORT")."):"?></font></td>
							<td align="left" width="0%" nowrap><font class="tablebodytext"><?=CForm::GetDateFilter("date_create", "form1", "Y", "class='inputselect'", "class='inputtext'")?></font></td>
						</tr>
						<tr valign="center">
							<td align="right" width="0%" nowrap><font class="tablebodytext"><?echo GetMessage("FORM_F_TIMESTAMP")." (".CSite::GetDateFormat("SHORT")."):"?></font></td>
							<td align="left" width="0%" nowrap><font class="tablebodytext"><?=CForm::GetDateFilter("timestamp", "form1", "Y", "class='inputselect'", "class='inputtext'")?></font></td>
						</tr>
						<?if ($F_RIGHT>=25) {?>
						<tr> 
							<td align="right"><font class="tablebodytext"><?echo GetMessage("FORM_F_REGISTERED")?></font></td>
							<td><font class="tablebodytext"><?
								$arr = array("reference"=>array(GetMessage("FORM_YES"), GetMessage("FORM_NO")), "reference_id"=>array("Y","N"));
								echo SelectBoxFromArray("find_registered", $arr, htmlspecialchars($find_registered), GetMessage("FORM_ALL"), "class='inputselect'");
								?></font></td>
						</tr>
						<tr> 
							<td align="right"><font class="tablebodytext"><?echo GetMessage("FORM_F_AUTH")?></font></td>
							<td><font class="tablebodytext"><?
								$arr = array("reference"=>array(GetMessage("FORM_YES"), GetMessage("FORM_NO")), "reference_id"=>array("Y","N"));
								echo SelectBoxFromArray("find_user_auth", $arr, htmlspecialchars($find_user_auth), GetMessage("FORM_ALL"), "class='inputselect'");
								?></font></td>
						</tr>
						<tr> 
							<td align="right"><font class="tablebodytext"><?echo GetMessage("FORM_F_USER")?></font></td>
							<td><font class="tablebodytext"><?=CForm::GetTextFilter("user_id", 45, "class='inputtext'", "class='inputcheckbox'")?></font></td>
						</tr>
						<?if (CModule::IncludeModule("statistic")) {?>
						<tr> 
							<td align="right"><font class="tablebodytext"><?echo GetMessage("FORM_F_GUEST")?></font></td>
							<td><font class="tablebodytext"><?=CForm::GetTextFilter("guest_id", 45, "class='inputtext'", "class='inputcheckbox'")?></font></td>
						</tr>
						<tr> 
							<td align="right"><font class="tablebodytext"><?echo GetMessage("FORM_F_SESSION")?></font></td>
							<td><font class="tablebodytext"><?=CForm::GetTextFilter("session_id", 45, "class='inputtext'", "class='inputcheckbox'")?></font></td>
						</tr>
						<? } // endif(CModule::IncludeModule("statistic"));?>
						<? } // endif($F_RIGHT>=25);?>
						<?
						if (is_array($arrFORM_FILTER) && count($arrFORM_FILTER)>0) {
						reset($arrFORM_FILTER);
						?>
						<?if ($F_RIGHT>=25) { ?>
						<tr>
							<td></td>
							<td valign="center" width="0%" nowrap><img src="/bitrix/images/1.gif" width="1" height="10"><br><font class="tablebodytext"><b><?=GetMessage("FORM_ENTERED_BY_GUEST")?></b></font><br><img src="/bitrix/images/1.gif" width="1" height="5"></td>
						</tr>
						<? } // endif ($F_RIGHT>=25);?>
						<?
						while (list($key, $arrFILTER) = each($arrFORM_FILTER)) 
						{
							reset($arrFILTER);
							while (list($key, $arrF) = each($arrFILTER)) 
							{

							$fname = $arrF["SID"];

							if (!is_array($arrNOT_SHOW_FILTER) || !in_array($fname,$arrNOT_SHOW_FILTER))
							{
							
							if (($arrF["ADDITIONAL"]=="Y" && $SHOW_ADDITIONAL=="Y") || $arrF["ADDITIONAL"]!="Y")
							{
							$i++;
							if ($fname!=$prev_fname) 
							{
								if ($i>1) {
								?></font></td></tr><?
								} //endif($i>1);
								?>
						<tr>
							<td align="right" valign="top" width="40%"><font class="tablebodytext"><?
							if (strlen($arrF["FILTER_TITLE"])<=0)
							{
								$title = ($arrF["TITLE_TYPE"]=="html" ? strip_tags($arrF["TITLE"]) : htmlspecialchars($arrF["TITLE"]));
								echo TruncateText($title, 100);
							}
							else 
							{
								echo htmlspecialchars($arrF["FILTER_TITLE"]);
							}

							if ($arrF["FILTER_TYPE"]=="date") echo " (".CSite::GetDateFormat("SHORT").")";
							?></font></td>
							<td nowrap valign="top" width="60%"><font class="tablebodytext"><?
							} //endif ($fname!=$prev_fname) ;
							switch($arrF["FILTER_TYPE"]){
								case "text":
									echo CForm::GetTextFilter($arrF["FID"]);
									break;
								case "date":
									echo CForm::GetDateFilter($arrF["FID"]);
									break;
								case "integer":
									echo CForm::GetNumberFilter($arrF["FID"]);
									break;
								case "dropdown":
									echo CForm::GetDropDownFilter($arrF["ID"], $arrF["PARAMETER_NAME"], $arrF["FID"]);
									break;
								case "exist":
									echo CForm::GetExistFlagFilter($arrF["FID"]);
									break;
							} // endswitch
							if ($arrF["PARAMETER_NAME"]=="ANSWER_TEXT") 
							{
								echo "&nbsp;<sup>[<font class='anstext'>...</font>]</sup>";
								$f_anstext = "Y";
							}
							elseif ($arrF["PARAMETER_NAME"]=="ANSWER_VALUE") 
							{
								echo "&nbsp;<sup>(<font class='ansvalue'>...</font>)</sup>";
								$f_ansvalue = "Y";
							}
							echo "<br>";
							$prev_fname = $fname;
							} //endif (($arrF["ADDITIONAL"]=="Y" && $SHOW_ADDITIONAL=="Y") || $arrF["ADDITIONAL"]!="Y");
							}// endif(!is_array($arrNOT_SHOW_FILTER) || !in_array($fname,$arrNOT_SHOW_FILTER));

							} // endwhile (list($key, $arrF) = each($arrFILTER));

						} // endwhile (list($key, $arrFILTER) = each($arrFORM_FILTER));
						} // endif(is_array($arrFORM_FILTER) && count($arrFORM_FILTER)>0);
						?></font></td>
						</tr>
						<tr>
							<td valign="center" colspan="2" align="center" width="0%" nowrap><img src="/bitrix/images/1.gif" width="1" height="3"></td>
						</tr>
						<tr>
							<td class="tablehead" colspan="2" valign="center" align="center" nowrap><img src="/bitrix/images/1.gif" width="1" height="3"><br><input type="submit" class="inputbutton" name="set_filter" value="<?echo GetMessage("FORM_F_SET_FILTER")?>"><input type="hidden" name="set_filter" value="Y">&nbsp;&nbsp;<input type="submit" class="inputbutton" name="del_filter" value="<?echo GetMessage("FORM_F_DEL_FILTER")?>"><br><img src="/bitrix/images/1.gif" width="1" height="3"></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</form>
		
		<?
		if ($FORM_ID>0 && $WEB_FORM_ID<=0) $WEB_FORM_ID = $FORM_ID;
		$page_split = intval(COption::GetOptionString("form", "RESULTS_PAGEN"));
		$res_counter = 0;
		$can_delete_some = false;
		while ($arR = $rsResults->Fetch())
		{
			$res_counter++;
			$arResult[] = $arR;
			$arRID[] = $arR["ID"]; // array of IDs of all results

			if (!$can_delete_some)
			{
				if ($F_RIGHT>=20 || ($F_RIGHT>=15 && $USER_ID==$arR["USER_ID"]))
				{
					$arrRESULT_PERMISSION = CFormResult::GetPermissions($arR["ID"], $v);
					if (in_array("DELETE",$arrRESULT_PERMISSION)) $can_delete_some = true;
				}
			}
		}
		
		$rsResults = new CDBResult;
		$rsResults->InitFromArray($arResult);
		//echo "****** ".$rsResults->SelectedRowsCount();
		?>

		<?if ($can_delete_some) { ?>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		function OnDelete()
		{
			var show_conf;
			var arCheckbox = document.forms.rform.elements["ARR_RESULT[]"];
			if(!arCheckbox) return;
			if(arCheckbox.length>0 || arCheckbox.value>0)
			{
				show_conf = false;
				if (arCheckbox.value>0 && arCheckbox.checked) show_conf = true;
				else
				{
					for(i=0; i<arCheckbox.length; i++)
					{
						if (arCheckbox[i].checked) 
						{
							show_conf = true;
							break;
						}
					}
				}
				if (show_conf)
					return confirm("<?=GetMessage("FORM_DELETE_CONFIRMATION")?>");
				else
					alert('<?=GetMessage("FORM_SELECT_RESULTS")?>');
			}
			return false;
		}

		function OnSelectAll(fl)
		{
			var arCheckbox = document.forms.rform.elements["ARR_RESULT[]"];
			if(!arCheckbox) return;
			if(arCheckbox.length>0)
				for(i=0; i<arCheckbox.length; i++)
					arCheckbox[i].checked = fl;
			else
				arCheckbox.checked = fl;
		}
		//-->
		</SCRIPT>
		<? } //endif($can_delete_some);?>

		<?
		echo ShowError($strError);
		echo ShowNote($strFormNote);
		?>

		<form method="POST" action="<?=$NEW_URL?>?WEB_FORM_ID=<?=$WEB_FORM_ID?>"><input type="submit" value="<?=GetMessage("FORM_ADD")?>&nbsp;&nbsp;>>" class="inputbutton"></form>

		<form name="rform" method="POST" action="<?=$APPLICATION->GetCurPage()?>?WEB_FORM_ID=<?=$WEB_FORM_ID?>&by=<?echo htmlspecialchars($by)?>&order=<?echo htmlspecialchars($order)?>#nav_start">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="WEB_FORM_ID" value="<?=$WEB_FORM_ID?>">

		<?if ($can_delete_some) {?>
		<p><input type="submit" name="delete" value="<?=GetMessage("FORM_DELETE_SELECTED")?>" class="inputbutton" onClick="return OnDelete()"></p>
		<? } // endif($can_delete_some);?>

		<?if (intval($res_counter)>0 && $SHOW_STATUS=="Y" && $F_RIGHT>=20) {?>
		<p><input type="submit" name="save" value="<? echo GetMessage("FORM_SAVE")?>" class="inputbutton"><input type="hidden" name="save" value="Y">&nbsp;<input type="reset" class="inputbutton" value="<?echo GetMessage("FORM_RESET")?>"></p>
		<? } //endif(intval($res_counter)>0 && $SHOW_STATUS=="Y" && $F_RIGHT>=20);?>
		<p>

		<?
		$rsResults->NavStart($page_split); echo $rsResults->NavPrint(GetMessage("FORM_PAGES"));
		if (!$rsResults->NavShowAll)
		{
			$pagen_from = (intval($rsResults->NavPageNomer)-1)*intval($rsResults->NavPageSize);
			$arRID_tmp = array();
			if (is_array($arRID) && count($arRID)>0)
			{
				$i=0;
				foreach($arRID as $rid)
				{
					if ($i>=$pagen_from && $i<$pagen_from+$page_split) 
					{
						$arRID_tmp[] = $rid; // array of IDs of results for the page
					}
					$i++;
				}
			}
			$arRID = $arRID_tmp;
		}
		?></p>
		<table border="0" cellspacing="0" cellpadding="0" width="100%"  class="tableborder">
			<tr>
				<td>
					<table border="0" cellspacing="1" cellpadding="3" width="100%">
						<?
						/***********************************************
									  table header
						************************************************/
						?>
						<tr>
							<td valign="top" align="center" class="tablehead" nowrap>								
								<table border="0" width="0%" cellspacing="0" cellpadding="0">
									<tr>
										<td <?if ($SHOW_STATUS!="Y") { ?> align="center" <? } //endif($SHOW_STATUS!="Y");?>valign="top" class="tablehead" nowrap><?
										if ($can_delete_some) {
											?><input class="inputcheckbox" type="checkbox" name="selectall" value="Y" onclick="OnSelectAll(this.checked)">&nbsp;<?
										} //endif ($can_delete_some);
										?><font class="tableheadtext">ID<?if ($SHOW_STATUS!="Y") { ?><br><?=SortingEx("s_id")?><? } //endif($SHOW_STATUS!="Y");?></font></td>
										<?if ($SHOW_STATUS=="Y") {?>
										<td><img src="/bitrix/images/1.gif" width="5" height="1"></td>
										<td><?=SortingEx("s_id")?></td>
										<? } //endif($SHOW_STATUS=="Y");?>
									</tr>
									<?if ($SHOW_STATUS=="Y") { ?>
									<tr>
										<td valign="top" class="tablehead" nowrap><font class="tableheadtext"><?=GetMessage("FORM_STATUS")?></font></td>
										<td><img src="/bitrix/images/1.gif" width="5" height="1"></td>
										<td><?=SortingEx("s_status")?></td>
									</tr>
									<? } //endif($SHOW_STATUS=="Y");?>
								</table>
							<td valign="top" align="center" class="tablehead">
							<font class="tableheadtext"><?=GetMessage("FORM_TIMESTAMP")?><br><?echo SortingEx("s_timestamp")?></font></td>
							<?if ($F_RIGHT>=25) { ?>
							<td valign="top" align="center" nowrap class="tablehead">
								<table border="0" width="0%" cellspacing="0" cellpadding="0">
									<?if (CModule::IncludeModule("statistic")) { ?>
									<tr>
										<td nowrap><font class="tableheadtext"><?echo GetMessage("FORM_USER")?></font></td>
										<td><img src="/bitrix/images/1.gif" width="5" height="1"></td>
										<td><?echo SortingEx("s_user_id")?></td>
									</tr>
									<tr>
										<td nowrap><font class="tableheadtext"><?echo GetMessage("FORM_GUEST_ID")?></font></td>
										<td><img src="/bitrix/images/1.gif" width="5" height="1"></td>
										<td><?echo SortingEx("s_guest_id")?></td>
									</tr>
									<tr>
										<td nowrap><font class="tableheadtext"><?echo GetMessage("FORM_SESSION_ID")?></font></td>
										<td><img src="/bitrix/images/1.gif" width="5" height="1"></td>
										<td><?echo SortingEx("s_session_id")?></td>
									</tr>
									<? } else /*(CModule::IncludeModule("statistic"))*/ {?>
									<tr>
										<td nowrap align="center"><font class="tableheadtext"><?echo GetMessage("FORM_USER")?></font></td>
									</tr>
									<tr>
										<td nowrap align="center"><?echo SortingEx("s_user_id")?></td>
									</tr>
									<? } //endif(CModule::IncludeModule("statistic"));?>
								</table></td>
							<? } //endif;($F_RIGHT>=25)?>
							<?
							if ($res_counter>0)
							{
								$arFilter = array(
									"IN_RESULTS_TABLE"	=> "Y",
									"RESULT_ID"			=> implode(" | ", $arRID)
									);
								CForm::GetResultAnswerArray($WEB_FORM_ID, $arrColumns, $arrAnswers, $arrAnswersSID, $arFilter);
							}
							else
							{
								$arFilter = array("IN_RESULTS_TABLE" => "Y");
								$rsFields = CFormField::GetList($WEB_FORM_ID, "ALL", ($v1="s_c_sort"), ($v2="asc"), $arFilter, $v3);
								while ($arField = $rsFields->Fetch()) 
								{
									$arrColumns[$arField["ID"]] = $arField;
								}
							}
							$colspan = 4;
							if (is_array($arrColumns)) 
							{
								reset($arrColumns);
								while (list($key, $arrCol) = each($arrColumns)) 
								{

									if (!is_array($arrNOT_SHOW_TABLE) || !in_array($arrCol["SID"],$arrNOT_SHOW_TABLE)) 
									{

									if (($arrCol["ADDITIONAL"]=="Y" && $SHOW_ADDITIONAL=="Y") || $arrCol["ADDITIONAL"]!="Y") 
									{
										$colspan++;
										if (strlen($arrCol["RESULTS_TABLE_TITLE"])<=0)
										{
											$title = ($arrCol["TITLE_TYPE"]=="html") ? strip_tags($arrCol["TITLE"]) : htmlspecialchars($arrCol["TITLE"]);
											$title = TruncateText($title,100);
										}
										else $title = htmlspecialchars($arrCol["RESULTS_TABLE_TITLE"]);
										?>
										<td valign="top" class="tablehead"><font class="tableheadtext"><?
										if ($F_RIGHT>=25) 
										{
										?>[<a title="<?=GetMessage("FORM_FIELD_PARAMS")?>" class="tablebodylink" href="/bitrix/admin/form_field_edit.php?lang=<?=LANGUAGE_ID?>&ID=<?=$arrCol["ID"]?>&FORM_ID=<?=$FORM_ID?>&WEB_FORM_ID=<?=$WEB_FORM_ID?>&additional=<?=$arrCol["ADDITIONAL"]?>"><?=$arrCol["ID"]?></a>]<br><?
										}//endif($F_RIGHT>=25);
										echo $title;
										?></td><?
									} //endif(($arrCol["ADDITIONAL"]=="Y" && $SHOW_ADDITIONAL=="Y") || $arrCol["ADDITIONAL"]!="Y");
									} //endif(!is_array($arrNOT_SHOW_TABLE) || !in_array($arrCol["SID"],$arrNOT_SHOW_TABLE));
								} //endwhile(list($key, $arrCol) = each($arrColumns)) ;
							} //endif(is_array($arrColumns)) ;
							?>
						</tr>
						<?
						/***********************************************
									  table body
						************************************************/
						$j=0;
						$arrUsers = array();
						while ($arResult = $rsResults->NavNext(true, "f_")) 
						{ 
							$j++;
							$arrRESULT_PERMISSION = CFormResult::GetPermissions($GLOBALS["f_ID"], $v);
							//echo "<tr><td colspan=10><pre>"; print_r($arrRESULT_PERMISSION); echo "</pre></td></tr>";

							$can_view = false;
							$can_edit = false;
							$can_delete = false;
							if ($F_RIGHT>=20 || ($F_RIGHT>=15 && $USER_ID==$GLOBALS["f_USER_ID"]))
							{
								if (in_array("VIEW",$arrRESULT_PERMISSION)) $can_view = true;
								if (in_array("EDIT",$arrRESULT_PERMISSION)) $can_edit = true;
								if (in_array("DELETE",$arrRESULT_PERMISSION)) $can_delete = true;
							}

						if ($SHOW_STATUS=="Y" || $can_delete_some && $can_delete) {
						if ($j>1) 
						{
						?>
						<tr><td colspan="<?=$colspan?>" class="tablehead">&nbsp;</td></tr>
						<?
						} //endif ($j>1);
						?>
						<tr>
							<td colspan="<?=$colspan?>" class="tablebody">
								<table cellspacing=0 cellpadding=2>
									<tr>
										<td><font class="tablebodytext"><?
											if ($can_delete_some && $can_delete) {
												?><input class="inputcheckbox" type="checkbox" name="ARR_RESULT[]" value="<?=$GLOBALS["f_ID"]?>"><?
											} //endif ($can_delete_some && $can_delete);
											?><input type="hidden" name="RESULT_ID[]" value="<?=$GLOBALS["f_ID"]?>">ID:&nbsp;<b><?
											echo ($USER_ID==$GLOBALS["f_USER_ID"]) ? "<font class='filteron'>".$GLOBALS["f_ID"]."</font>" : $GLOBALS["f_ID"];
											?></b></font></td>
									</tr>
									<? if ($SHOW_STATUS == "Y") {?>
									<tr>
										<td><font class="tablebodytext"><?echo GetMessage("FORM_STATUS")?>:&nbsp;</font><?
										echo "<font class='tablebodytext'>[&nbsp;</font><font class='".$GLOBALS["f_STATUS_CSS"]."'>".$GLOBALS["f_STATUS_TITLE"]."</font><font class='tablebodytext'>&nbsp;]</font>";
										?></td>
									<?if (in_array("EDIT",$arrRESULT_PERMISSION) && $F_RIGHT>=25) { ?>
										<td><font class="smalltext"><?=GetMessage("FORM_CHANGE_TO")?></font></td>
										<td><input type="hidden" name="STATUS_PREV_<?=$GLOBALS["f_ID"]?>" value="<?=$GLOBALS["f_STATUS_ID"]?>"><?
										echo SelectBox("STATUS_".$GLOBALS["f_ID"], CFormStatus::GetDropdown($WEB_FORM_ID, array("MOVE"), $GLOBALS["f_USER_ID"]), " ", "", "class='inputselect'");
										?></td>
									<? } // endif (in_array("EDIT",$arrRESULT_PERMISSION) && $F_RIGHT>=25);?>
									</tr>
									<? } // endif ($SHOW_STATUS == "Y")?>
								</table>
							</td>
						</tr>
						<? } //endif ($SHOW_STATUS=="Y");?>
						<tr valign="top">
							<td class="tablebody" nowrap>
								<table cellspacing=0 cellpadding=0>
									<?if ($SHOW_STATUS!="Y") { ?>
									<!--tr>
										<td align="center"><font class="tablebodytext"><?
											if ($USER_ID==$GLOBALS["f_USER_ID"] && $F_RIGHT>=20)
											{
												echo "<b><font class='filteron'>".$GLOBALS["f_ID"]."</font></b>";
											}
											else
											{
												echo $GLOBALS["f_ID"];
											}						
											?></font></td>
									</tr-->
									<? } //endif ($SHOW_STATUS!="Y");?>

									<?if ($can_edit) { ?>
									<?if (strlen(trim($EDIT_URL))>0) { ?>
									<tr>
										<td><font class="tablebodytext">[&nbsp;<a class="tablebodylink" title="<?=GetMessage("FORM_EDIT_ALT")?>" href="<?=$EDIT_URL?>?RESULT_ID=<?echo $GLOBALS["f_ID"]?>&WEB_FORM_ID=<?=$WEB_FORM_ID?>"><? echo GetMessage("FORM_EDIT")?></a>&nbsp;]</font></td>
									</tr>
									<? }// endif(strlen(trim($EDIT_URL))>0);?>
									<? }// endif($can_edit);?>


									<?if ($can_view) { ?>
									<?if (strlen(trim($VIEW_URL))>0) {?>
									<tr>
										<td><font class="tablebodytext">[&nbsp;<a class="tablebodylink" title="<?=GetMessage("FORM_VIEW_ALT")?>" href="<?=$VIEW_URL?>?RESULT_ID=<?echo $GLOBALS["f_ID"]?>&WEB_FORM_ID=<?=$WEB_FORM_ID?>"><? echo GetMessage("FORM_VIEW")?></a>&nbsp;]</font></td>
									</tr>
									<? } //endif (strlen(trim($VIEW_URL))>0);?>
									<? } //endif ($can_view);?>

									<?if ($can_delete) {?>
									<tr>
										<td><font class="tablebodytext">[&nbsp;<a class="tablebodylink" title="<?=GetMessage("FORM_DELETE_ALT")?>" href="javascript:if(confirm('<?=GetMessage("FORM_CONFIRM_DELETE")?>')) window.location='?WEB_FORM_ID=<?=$WEB_FORM_ID?>&del_id=<?echo $GLOBALS["f_ID"]?>&<?=bitrix_sessid_get()?>#nav_start'" class="tablebodytext"><?=GetMessage("FORM_DELETE")?></a>&nbsp;]</font></td>
									</tr>
									<? } //endif ($can_delete);?>

								</table></font></td>
							<td align="center" class="tablebody"><font class="tablebodytext"><?$arr = explode(" ",$GLOBALS["f_TIMESTAMP_X"]); echo $arr[0]."<br>".$arr[1]?></font></td>
							<?if ($F_RIGHT>=25) { ?>
							<td class="tablebody"><font class="tablebodytext"><?
								if ($GLOBALS["f_USER_ID"]>0) 
								{
									if (!in_array($GLOBALS["f_USER_ID"], array_keys($arrUsers)))
									{
										$rsU = CUser::GetByID($GLOBALS["f_USER_ID"]);
										$arU = $rsU->ExtractFields("u_");
										$GLOBALS["f_LOGIN"] = $GLOBALS["u_LOGIN"];
										$GLOBALS["f_USER_NAME"] = $GLOBALS["u_NAME"]." ".$GLOBALS["u_LAST_NAME"];
										$arrUsers[$GLOBALS["f_USER_ID"]]["USER_NAME"] = $GLOBALS["f_USER_NAME"];
										$arrUsers[$GLOBALS["f_USER_ID"]]["LOGIN"] = $GLOBALS["f_LOGIN"];
									}
									else
									{
										$GLOBALS["f_USER_NAME"] = $arrUsers[$GLOBALS["f_USER_ID"]]["USER_NAME"];
										$GLOBALS["f_LOGIN"] = $arrUsers[$GLOBALS["f_USER_ID"]]["LOGIN"];
									}
									echo "<font class='tablebodytext'>[</font><a class='tablebodylink' title='".GetMessage("FORM_EDIT_USER")."' href='/bitrix/admin/user_edit.php?lang=".LANGUAGE_ID."&ID=".$GLOBALS["f_USER_ID"]."'>". $GLOBALS["f_USER_ID"]."</a><font class='tablebodytext'>] (".$GLOBALS["f_LOGIN"].") ".$GLOBALS["f_USER_NAME"]."</font>";
									echo ($GLOBALS["f_USER_AUTH"]=="N") ? " <font class='filteroff'>".GetMessage("FORM_NOT_AUTH")."</font>" : "";
								}
								else 
								{
									echo "<font class='tablebodytext'>".GetMessage("FORM_NOT_REGISTERED")."</font>";
								} // endif ($GLOBALS["f_USER_ID"]>0);
								
								if (CModule::IncludeModule("statistic")) 
								{
									if (intval($GLOBALS["f_STAT_GUEST_ID"])>0) 
									{
										echo " <font class='tablebodytext'>[<a title='".GetMessage("FORM_GUEST")."' class='tablebodylink' href='/bitrix/admin/guest_list.php?lang=".LANGUAGE_ID."&find_id=". $GLOBALS["f_STAT_GUEST_ID"]."&set_filter=Y'>".$GLOBALS["f_STAT_GUEST_ID"]."</a>]</font>";
									} //endif ((intval($GLOBALS["f_STAT_GUEST_ID"])>0));
									if (intval($GLOBALS["f_STAT_SESSION_ID"])>0) 
									{
										echo " <font class='tablebodytext'>(<a title='".GetMessage("FORM_SESSION")."' class='tablebodylink' href='/bitrix/admin/session_list.php?lang=".LANGUAGE_ID."&find_id=". $GLOBALS["f_STAT_SESSION_ID"]."&set_filter=Y'>".$GLOBALS["f_STAT_SESSION_ID"]."</a>)</font>";
									} //endif ((intval($GLOBALS["f_STAT_SESSION_ID"])>0));
								} //endif (CModule::IncludeModule("statistic"));
							?></font></td>
							<? } //endif ($F_RIGHT>=25);?>
							<?
							reset($arrColumns);
							while (list($FIELD_ID,$arrC) = each($arrColumns))
							{

								if (!is_array($arrNOT_SHOW_TABLE) || !in_array($arrC["SID"],$arrNOT_SHOW_TABLE)) 
								{

								if (($arrC["ADDITIONAL"]=="Y" && $SHOW_ADDITIONAL=="Y") || $arrC["ADDITIONAL"]!="Y") 
								{						
							?>
							<td valign="top" align="left" class="tablebody" nowrap>
								<table cellspacing=0 cellpadding=0 border=0 width="100%">
								<?
								$arrAnswer = $arrAnswers[$GLOBALS["f_ID"]][$FIELD_ID];
								if (is_array($arrAnswer)) 
								{
									reset($arrAnswer);
									$count = count($arrAnswer);
									$i = 0;
									while (list($key,$arrA) = each($arrAnswer)) 
									{
										$i++;
									?>
										<tr>
											<td valign="top" width="100%" <?if ($i!=$count) echo "class='tline'"?>><font class="tablebodytext"><?

												if (strlen(trim($arrA["USER_TEXT"]))>0)
												{
													if (intval($arrA["USER_FILE_ID"])>0)
													{
														if ($arrA["USER_FILE_IS_IMAGE"]=="Y" && $USER->IsAdmin())
															echo htmlspecialchars($arrA["USER_TEXT"])."<br>";
													}
													else echo TxtToHTML($arrA["USER_TEXT"],true,100)."<br>";
												}

												if (strlen(trim($arrA["ANSWER_TEXT"]))>0)
												{
													$answer = "[<font class='anstext'>".TxtToHTML($arrA["ANSWER_TEXT"],true,100)."</font>]";
													if (strlen(trim($arrA["ANSWER_VALUE"]))>0 && $SHOW_ANSWER_VALUE=="Y") $answer .= "&nbsp;"; else $answer .= "<br>";
													echo $answer;
												}
												if (strlen(trim($arrA["ANSWER_VALUE"]))>0 && $SHOW_ANSWER_VALUE=="Y")
													echo "(<font class='ansvalue'>".TxtToHTML($arrA["ANSWER_VALUE"],true,100)."</font>)<br>";

												if (intval($arrA["USER_FILE_ID"])>0) {

													if ($arrA["USER_FILE_IS_IMAGE"]=="Y") 
													{
														echo CFile::ShowImage($arrA["USER_FILE_ID"], 0, 0, "border=0", "", true);
													}
													else 
													{
														?><a title="<?=GetMessage("FORM_VIEW_FILE")?>" target="_blank" class="tablebodylink" href="/bitrix/tools/form_show_file.php?rid=<?echo $GLOBALS["f_ID"]?>&hash=<?echo $arrA["USER_FILE_HASH"]?>&lang=<?=LANGUAGE_ID?>"><?echo htmlspecialchars($arrA["USER_FILE_NAME"])?></a><br>(<?
														$a = array("b", "Kb", "Mb", "Gb");
														$pos = 0;
														$size = $arrA["USER_FILE_SIZE"];
														while($size>=1024) {$size /= 1024; $pos++;}
														echo round($size,2)." ".$a[$pos];
														?>)<br>[&nbsp;<a title="<?echo str_replace("#FILE_NAME#", $arrA["USER_FILE_NAME"], GetMessage("FORM_DOWNLOAD_FILE"))?>" class="tablebodylink" href="/bitrix/tools/form_show_file.php?rid=<?echo $GLOBALS["f_ID"]?>&hash=<?echo $arrA["USER_FILE_HASH"]?>&lang=<?=LANGUAGE_ID?>&action=download"><?echo GetMessage("FORM_DOWNLOAD")?></a>&nbsp;]<?
													} // endif ($arrA["USER_FILE_IS_IMAGE"]=="Y");

												} //endif (intval($arrA["USER_FILE_ID"])>0);
												?></font></td>
										</tr>
									<? 
									} //endwhile (list($key,$arrA) = each($arrAnswer)); 
								} // endif (is_array($arrAnswer));
								?>
								</table></td>
							<?
								} //endif (($arrC["ADDITIONAL"]=="Y" && $SHOW_ADDITIONAL=="Y") || $arrC["ADDITIONAL"]!="Y") ;
								} // endif (!is_array($arrNOT_SHOW_TABLE) || !in_array($arrC["SID"],$arrNOT_SHOW_TABLE));
							} //endwhile (list($FIELD_ID,$arrC) = each($arrColumns));
							?>
						</tr>
						<? } //endwhile ($arResult = $rsResults->NavNext(true, "f_"));?>
						<?if ($HIDE_TOTAL!="Y") { ?>
						<tr valign="top">
							<td align="left" class="tablehead" colspan="<?=$colspan?>"><font class="tableheadtext"><?=GetMessage("FORM_TOTAL")?>&nbsp;<?echo intval($res_counter)?></font></td>
						</tr>
						<? } //endif ($HIDE_TOTAL!="Y");?>
					</table>
				</td>
			</tr>
		</table>
		<p><?echo $rsResults->NavPrint(GetMessage("FORM_PAGES"))?></p>
		<?if (intval($res_counter)>0 && $SHOW_STATUS=="Y" && $F_RIGHT>=20) { ?>
		<p><input type="submit" name="save" value="<?=GetMessage("FORM_SAVE")?>" class="inputbutton"><input type="hidden" name="save" value="Y">&nbsp;<input type="reset" class="inputbutton" value="<?=GetMessage("FORM_RESET")?>"></p>
		<? } //endif (intval($res_counter)>0 && $SHOW_STATUS=="Y" && $F_RIGHT>=20);?>

		<?if ($can_delete_some) { ?>
		<p><input type="submit" name="delete" value="<?=GetMessage("FORM_DELETE_SELECTED")?>" class="inputbutton" onClick="return OnDelete()"></p>
		<? } //endif ($can_delete_some);?>

		</form>
	<?
	} else {
		echo ShowError(GetMessage("FORM_INCORRECT_FORM_ID"));
	}//endif($zr=$z->Fetch());
} else {
	echo ShowError(GetMessage("FORM_MODULE_NOT_INSTALLED"));
} //endif(CModule::IncludeModule("form")) ;?>
