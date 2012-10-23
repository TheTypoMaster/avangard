<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/***********************************************************************
Component for form result viewing

This universal component is for a viewing of filled web-form result. This is a standard component, it is included to module distributive

example of using:

$APPLICATION->IncludeFile("form/result_view/default.php", array(
	"RESULT_ID" => $_REQUEST["RESULT_ID"],
	"SHOW_ADDITIONAL" => "N",
	"SHOW_ANSWER_VALUE" => "N",
	"SHOW_STATUS" => "Y",
	"EDIT_URL" => "result_edit.php",
	"CHAIN_ITEM_TEXT" => "Forms List",
	"CHAIN_ITEM_LINK" => "result_list.php?WEB_FORM_ID=".$_REQUEST["WEB_FORM_ID"],
	));

Parameters:

$RESULT_ID				- result ID
$SHOW_ADDITIONAL		- [Y|N] - Y - show result fields at the edit form (don't mix up with "questions").
$SHOW_ANSWER_VALUE		- [Y|N] - Y - show value of parameter "ANSWER_VALUE" of web-form question (shows in round brackets near answer value).
$SHOW_STATUS - [Y|N]	- [Y|N] - Y - show curremt status of web-form result
$EDIT_URL				- page URL for result editing
$CHAIN_ITEM_TEXT		- additional item name in the navigation chain (if empty, no item is added)
$CHAIN_ITEM_LINK		- additional item link in the navigation chain

***********************************************************************/

global $USER, $APPLICATION;
$APPLICATION->SetTemplateCSS("form/form.css");
if (CModule::IncludeModule("form")):
	$bSimple = (COption::GetOptionString("form", "SIMPLE", "Y") == "Y") ? true : false;
	IncludeTemplateLangFile(__FILE__);

	if (is_array($_REQUEST)) extract($_REQUEST, EXTR_SKIP);

	$SHOW_ADDITIONAL = ($SHOW_ADDITIONAL=="Y") ? "Y" : "N";
	$SHOW_STATUS = ($SHOW_STATUS=="Y" && !$bSimple) ? "Y" : "N";
	$SHOW_ANSWER_VALUE = ($SHOW_ANSWER_VALUE=="Y") ? "Y" : "N";

	$RESULT_ID = intval($arParams["RESULT_ID"]);
	if (intval($RESULT_ID)<=0) $RESULT_ID = intval($_REQUEST["RESULT_ID"]);

	$z = CFormResult::GetByID($RESULT_ID);
	if ($arrResult=$z->Fetch()) :

		if (strlen($arParams["CHAIN_ITEM_TEXT"])>0)
			$APPLICATION->AddChainItem($arParams["CHAIN_ITEM_TEXT"], $arParams["CHAIN_ITEM_LINK"]);

		$WEB_FORM_ID = intval($arrResult["FORM_ID"]);
		$F_RIGHT = intval(CForm::GetPermission($WEB_FORM_ID));
		if ($F_RIGHT>=20 || ($F_RIGHT>=15 && $USER->GetID()==$arrResult["USER_ID"])) :

			$arrRESULT_PERMISSION = CFormResult::GetPermissions($RESULT_ID, $v);
			if (in_array("VIEW",$arrRESULT_PERMISSION)) :

				$additional = ($SHOW_ADDITIONAL=="Y") ? "ALL" : "N";
				CForm::GetDataByID($WEB_FORM_ID, $arForm, $arQuestions, $arAnswers, $arDropDown, $arMultiSelect, $additional);
				CForm::GetResultAnswerArray($WEB_FORM_ID, $arrResultColumns, $arrResultAnswers, $arrResultAnswersSID, array("RESULT_ID" => $RESULT_ID));
				$arrResultAnswers = $arrResultAnswers[$RESULT_ID];

				echo ShowError($strError);
				echo ShowNote($strFormNote);

				if (!(defined("ADMIN_SECTION") && ADMIN_SECTION===true)):
				?>
				<table cellspacing="0" cellpadding="3" class="tablebody">
				<tr>
					<?
					// check user permission for access to web-form
					if ($F_RIGHT>=20 || ($F_RIGHT>=15 && $arrResult["USER_ID"]==$USER->GetID())) :
						// check user permission to access in dependence of status
						$arrRESULT_PERMISSION = CFormResult::GetPermissions($RESULT_ID, $v);
						if (in_array("EDIT",$arrRESULT_PERMISSION)) :
							?>
							<td><font class="tablebodytext">[&nbsp;<a class="tablebodylink" href="<?=$EDIT_URL?>?RESULT_ID=<?=$RESULT_ID?>&WEB_FORM_ID=<?echo $WEB_FORM_ID?>"><?=GetMessage("FORM_EDIT")?></a>&nbsp;]</font></td>
							<td><img src="/bitrix/images/1.gif" width="2" height="1" border=0 alt=""></td>
							<?
						endif;
					endif;
					?>
				</tr>
				</table>
				<br>
				<?endif;?>
				<table cellspacing="0" cellpadding="2">
					<?if ($F_RIGHT>=25):?>
					<tr>
						<td valign="top"><font class="tablebodytext"><b>ID:</b></font></td>
						<td valign="top"><font class="tablebodytext"><?=$arrResult["ID"]?></font></td>
					</tr>
					<tr>
						<td valign="top"><font class="tablebodytext"><b><?=GetMessage("FORM_FORM_NAME")?></b></font></td>
						<td valign="top"><font class="tablebodytext"><?
						echo "[<a class='tablebodylink' href='/bitrix/admin/form_edit.php?lang=".LANGUAGE_ID."&ID=".$arrResult["FORM_ID"]."'>". $arrResult["FORM_ID"]."</a>]&nbsp;(".htmlspecialchars($arrResult["SID"]).")&nbsp;".htmlspecialchars($arrResult["NAME"]);
						?></font></td>
					</tr>
					<?
						if (intval($arrResult["USER_ID"])>0)
						{
							$rsUser = CUser::GetByID($arrResult["USER_ID"]);
							$arUser = $rsUser->Fetch();
							$arrResult["LOGIN"] = $arUser["LOGIN"];
							$arrResult["EMAIL"] = $arUser["USER_EMAIL"];
							$arrResult["USER_NAME"] = $arUser["NAME"]." ".$arUser["LAST_NAME"];
						}
					endif;
					?>
					<tr>
						<td valign="top"><font class="tablebodytext"><b><?=GetMessage("FORM_DATE_CREATE")?></b></font></td>
						<td valign="top"><font class="tablebodytext"><?=$arrResult["DATE_CREATE"]?><?
							if ($F_RIGHT>=25):
								?>&nbsp;&nbsp;&nbsp;<?
								if (intval($arrResult["USER_ID"])>0) :
									echo "<font class='tablebodytext'>[</font><a class='tablebodylink' title='".GetMessage("FORM_EDIT_USER")."' href='/bitrix/admin/user_edit.php?lang=".LANGUAGE_ID."&ID=".$arrResult["USER_ID"]."'>".$arrResult["USER_ID"]."</a><font class='tablebodytext'>] (".htmlspecialchars($arrResult["LOGIN"]).") ".htmlspecialchars($arrResult["USER_NAME"])."</font>";
									echo ($arrResult["USER_AUTH"]=="N") ? " <font class='pointed'>".GetMessage("FORM_NOT_AUTH")."</font>" : "";
								else :
									echo "<font class='tablebodytext'>".GetMessage("FORM_NOT_REGISTERED")."</font>";
								endif;
							endif;
							?></font></td>
					</tr>
					<tr>
						<td valign="top"><font class="tablebodytext"><b><?=GetMessage("FORM_TIMESTAMP")?></b></font></td>
						<td valign="top"><font class="tablebodytext"><?=$arrResult["TIMESTAMP_X"]?></font></td>
					</tr>
					<?
					if ($F_RIGHT>=25):
					if (CModule::IncludeModule("statistic")):
					?>
					<tr>
						<td valign="top"><font class="tablebodytext"><b><?=GetMessage("FORM_GUEST")?></b></font></td>
						<td valign="top"><font class="tablebodytext">[<a title="<?=GetMessage("FORM_GUEST_ALT")?>" class="tablebodylink" href="/bitrix/admin/guest_list.php?lang=<?=LANGUAGE_ID?>&find_id=<?=$arrResult["STAT_GUEST_ID"]?>&set_filter=Y"><?=$arrResult["STAT_GUEST_ID"]?></a>]</font></td>
					</tr>
					<tr>
						<td valign="top"><font class="tablebodytext"><b><?=GetMessage("FORM_SESSION")?></b></font></td>
						<td valign="top"><font class="tablebodytext">[<a title="<?=GetMessage("FORM_SESSION_ALT")?>" class="tablebodylink" href="/bitrix/admin/session_list.php?lang=<?=LANGUAGE_ID?>&find_id=<?=$arrResult["STAT_SESSION_ID"]?>&set_filter=Y"><?=$arrResult["STAT_SESSION_ID"]?></a>]</font></td>
					</tr>
					<?endif;?>
					<?endif;?>
				</table>
				<br>
				<?if (!$bSimple):?>
				<table border="0" cellspacing="0" cellpadding="1" width="0%" class="tableborder">
					<tr>
						<td>
							<table border="0" cellspacing="0" cellpadding="3" width="100%">
								<tr>
									<td class="tablebody" nowrap><font class="tablebodytext"><?
									echo "<b><font class='tablebodytext'>".GetMessage("FORM_CURRENT_STATUS")."</font></b>";
									echo "&nbsp;[<font class='".$arrResult["STATUS_CSS"]."'>".$arrResult["STATUS_TITLE"]."</font>]";
									?></font></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<br>
				<?endif;?>

				<table border="0" cellspacing="0" cellpadding="0" width="100%"  class="tableborder">
					<tr>
						<td>
							<table border="0" cellspacing="1" cellpadding="5" width="100%">
								<?
								reset($arQuestions);
								while (list($key,$arQuestion)=each($arQuestions)):
								?>
								<tr>
									<td width="40%" class="tablebody" valign="top" align="right">
										<table cellspacing="0" cellpadding="0" width="100%">
											<tr>
												<td align="left" valign="top" width="100%"><?

												if (strlen($arQuestion["RESULTS_TABLE_TITLE"])<=0)
												{
													$title = ($arQuestion["TITLE_TYPE"]=="html") ? strip_tags($arQuestion["TITLE"]) : $arQuestion["TITLE"];
												}
												else $title = htmlspecialchars($arQuestion["RESULTS_TABLE_TITLE"]);

												if ($arQuestion["ADDITIONAL"]=="Y")
												{
													echo "<font class='tablebodytext'><b>".$title."</b></font>";
												}
												else
												{
													echo "<font class='tablebodytext'>".$title."</font>";
												}										
												?></td>
											</tr>
										</table>
									</td>
									<td width="60%" class="tablebody" valign="top">
										<table cellspacing=0 cellpadding=0 border=0 width="100%">
										<?
										$arrResultAnswer = $arrResultAnswers[$arQuestion["ID"]];
										if (is_array($arrResultAnswer)) :
											reset($arrResultAnswer);
											$count = count($arrResultAnswer);
											$i=0;
											while (list($key,$arrA) = each($arrResultAnswer)):
												$i++;
											?>
												<tr>
													<td width="100%"><font class="tablebodytext"><?

														if (strlen(trim($arrA["USER_TEXT"]))>0)
														{
															if (intval($arrA["USER_FILE_ID"])>0)
															{
																if ($arrA["USER_FILE_IS_IMAGE"]=="Y" && $USER->IsAdmin())
																	echo htmlspecialchars($arrA["USER_TEXT"])."<br>";
															}
															else echo TxtToHTML($arrA["USER_TEXT"],true,50)."<br>";
														}

														if (strlen(trim($arrA["ANSWER_TEXT"]))>0)
														{
															$answer = "[<font class='anstext'>".TxtToHTML($arrA["ANSWER_TEXT"],true,50)."</font>]";
															if (strlen(trim($arrA["ANSWER_VALUE"]))>0) $answer .= "&nbsp;"; else $answer .= "<br>";
															echo $answer;
														}
														if ($SHOW_ANSWER_VALUE=="Y")
														{
															if (strlen(trim($arrA["ANSWER_VALUE"]))>0)
																echo "(<font class='ansvalue'>".TxtToHTML($arrA["ANSWER_VALUE"],true,50)."</font>)<br>";
														}
														if (intval($arrA["USER_FILE_ID"])>0) :
															if ($arrA["USER_FILE_IS_IMAGE"]=="Y") :
																echo CFile::ShowImage($arrA["USER_FILE_ID"], 0, 0, "border=0", "", true);
															else :
																?><a title="<?=GetMessage("FORM_VIEW_FILE")?>" target="_blank" class="tablebodylink" href="/bitrix/tools/form_show_file.php?rid=<?echo $RESULT_ID?>&hash=<?echo $arrA["USER_FILE_HASH"]?>&lang=<?=LANGUAGE_ID?>"><?echo htmlspecialchars($arrA["USER_FILE_NAME"])?></a><br>(<?
																$a = array("b", "Kb", "Mb", "Gb");
																$pos = 0;
																$size = $arrA["USER_FILE_SIZE"];
																while($size>=1024) {$size /= 1024; $pos++;}
																echo round($size,2)." ".$a[$pos];
																?>)<br>[&nbsp;<a title="<?echo str_replace("#FILE_NAME#", $arrA["USER_FILE_NAME"], GetMessage("FORM_DOWNLOAD_FILE"))?>" class="tablebodylink" href="/bitrix/tools/form_show_file.php?rid=<?echo $RESULT_ID?>&hash=<?echo $arrA["USER_FILE_HASH"]?>&lang=<?=LANGUAGE_ID?>&action=download"><?echo GetMessage("FORM_DOWNLOAD")?></a>&nbsp;]<?
															endif;
														endif;
														?></font></td>
												</tr>
											<?
											endwhile;
										endif;
										?></table></td>
								</tr>
								<?endwhile?>
							</table>
						</td>
					</tr>
				</table>
			<?
			else:
				echo ShowError(GetMessage("FORM_RESULT_ACCESS_DENIED"));
			endif;
		else:
			echo ShowError(GetMessage("FORM_ACCESS_DENIED"));
		endif;
	else:
		echo ShowError(GetMessage("FORM_RECORD_NOT_FOUND"));
	endif;
else:
	echo ShowError(GetMessage("FORM_MODULE_NOT_INSTALLED"));
endif;
?>
