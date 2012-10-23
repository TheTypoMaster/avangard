<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $USER, $APPLICATION, $FORM;

$APPLICATION->SetTemplateCSS("form/form.css");
if (CModule::IncludeModule("form")):
	$bSimple = (COption::GetOptionString("form", "SIMPLE", "Y") == "Y") ? true : false;

	IncludeTemplateLangFile(__FILE__);

//	extract($ GLOBALS);
//	if (is_array($_REQUEST)) extract($_REQUEST, EXTR_SKIP);

	$EDIT_ADDITIONAL = ($FORM->EDIT_ADDITIONAL=="Y" && !$bSimple) ? "Y" : "N";
	$EDIT_STATUS = ($FORM->EDIT_STATUS=="Y" && !$bSimple) ? "Y" : "N";

	$RESULT_ID = intval($arParams["RESULT_ID"]);
	if (intval($RESULT_ID)<=0) $RESULT_ID = intval($_REQUEST["RESULT_ID"]);
	
	$z = CFormResult::GetByID($RESULT_ID);
	if ($arrResult=$z->Fetch()) :

		if (strlen($arParams["CHAIN_ITEM_TEXT"])>0)
			$APPLICATION->AddChainItem($arParams["CHAIN_ITEM_TEXT"], $arParams["CHAIN_ITEM_LINK"]);

		$WEB_FORM_ID = $FORM->WEB_FORM_ID;
		$F_RIGHT = intval(CForm::GetPermission($WEB_FORM_ID));
		if ($F_RIGHT>=20 || ($F_RIGHT>=15 && $USER->GetID()==$arrResult["USER_ID"])) :

			$arrRESULT_PERMISSION = CFormResult::GetPermissions($RESULT_ID, $v);
			if (in_array("EDIT",$arrRESULT_PERMISSION)) :

				if (strlen($web_form_submit)>0 || strlen($web_form_apply)>0)
				{
					$arrVALUES = $_REQUEST;
					$error = CForm::Check($WEB_FORM_ID, $arrVALUES, $RESULT_ID);
					if (strlen($error)<=0) 
					{
						//echo "<pre>"; print_r($arrVALUES); echo "</pre>";
						CFormResult::Update($RESULT_ID, $arrVALUES, $EDIT_ADDITIONAL);
						$strFormNote = GetMessage("FORM_DATA_SAVED");
						if (strlen($web_form_submit)>0 && !(defined("ADMIN_SECTION") && ADMIN_SECTION===true)) 
						{
							LocalRedirect($LIST_URL."?WEB_FORM_ID=".$WEB_FORM_ID."&strFormNote=".urlencode($strFormNote));
						}
						else
						{
							$z = CFormResult::GetByID($RESULT_ID);
							$arrResult = $z->Fetch();
						}
					}
					else $strError .= $error;
				}
				else $arrVALUES = CFormResult::GetDataByIDForHTML($RESULT_ID, $EDIT_ADDITIONAL);

				$additional = ($EDIT_ADDITIONAL=="Y") ? "ALL" : "N";
				CForm::GetDataByID($arrResult["FORM_ID"], $arForm, $arQuestions, $arAnswers, $arDropDown, $arMultiSelect, $additional);

				echo ShowError($strError);
				echo ShowNote($strFormNote);

				//echo "<pre>"; print_r($arrVALUES); echo "</pre>";

				?>

				<?if (!(defined("ADMIN_SECTION") && ADMIN_SECTION===true)):?>
				<table cellspacing=0 cellpadding=3 class="tablebody">
					<tr>
						<td><font class="tablebodytext">[&nbsp;<a class="tablebodylink" href="<?=$FORM->arParams["VIEW_URL"]?>?RESULT_ID=<?echo $FORM->RESULT_ID?>&WEB_FORM_ID=<?echo $FORM->WEB_FORM_ID?>"><?=GetMessage("FORM_VIEW")?></a>&nbsp;]</font></td>
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
						echo "[<a class='tablebodylink' href='/bitrix/admin/form_edit.php?lang=".LANGUAGE_ID."&ID=".$WEB_FORM_ID."'>". $WEB_FORM_ID."</a>]&nbsp;(".$arrResult["SID"].")&nbsp;".$arrResult["NAME"];
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
						<td valign="top"><font class="tablebodytext">[<a title="<?=GetMessage("FORM_GUEST_ALT")?>" class="tablebodylink" href="/bitrix/admin/guest_list.php?lang=<?=LANGUAGE_ID?>&find_id=<?=$arrResult["STAT_GUEST_ID"]?>&find_id_exact_match=Y&set_filter=Y"><?=$arrResult["STAT_GUEST_ID"]?></a>]</font></td>
					</tr>
					<tr>
						<td valign="top"><font class="tablebodytext"><b><?=GetMessage("FORM_SESSION")?></b></font></td>
						<td valign="top"><font class="tablebodytext">[<a class="tablebodylink" href="/bitrix/admin/session_list.php?lang=<?=LANGUAGE_ID?>&find_id=<?=$arrResult["STAT_SESSION_ID"]?>&find_id_exact_match=Y&set_filter=Y"><?=$arrResult["STAT_SESSION_ID"]?></a>]</font></td>
					</tr>
					<?endif;?>
					<?endif;?>
				</table>				
				
				<?=$FORM->ShowFormHeader()?>
				
				<?if($FORM->isResultStatusChangeAccess()):?>
				<table border="0" cellspacing="0" cellpadding="1" width="0%" class="tableborder">
					<tr>
						<td>
							<table border="0" cellspacing="0" cellpadding="3" width="100%">
								<tr>
									<td class="tablebody" nowrap><div class='tablebodytext'>
										<b><?=GetMessage("FORM_CURRENT_STATUS")?></b>
										[<?=$FORM->ShowResultStatus()?>]
										<?=GetMessage("FORM_CHANGE_TO")?>
										<?=$FORM->ShowResultStatusForm()?>
									</div></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<br />
				<?endif;?>
				
				<table cellspacing="0" cellpadding="0" width="100%">
<?
			if ($FORM->isFormDescription() || $FORM->isFormTitle() || $FORM->isFormImage()) 
			{
?>

					<tr>
						<td width="100%"><?

/***********************************************************************************
									Form header
***********************************************************************************/ 

						if ($FORM->isFormTitle())
						{
						?><p class="h2"><b><?=$FORM->ShowFormTitle("titletext")?></b></font></p><?
						} // endif
						?>
						<?if ($FORM->isFormImage())
						{
						?>

						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td><?=$FORM->ShowFormImage()?></td>
							</tr>
						</table>
						
						<? 
						} //endif; 
						?>
						<p class="text">
						<?=$FORM->ShowFormDescription()?>
						</p>
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
<?
			} //endif (descr);
?>
					<tr>
						<td><?

/***********************************************************************************
									Form questions
***********************************************************************************/ 

						?>
						<p>
						<table border="0" cellspacing="0" cellpadding="1" width="100%"  class="tableborder">
							<tr>
								<td width="100%">
									<table cellspacing="0" cellpadding="10" border="0" class="tablebody" width="100%">
										<?
										reset($FORM->arQuestions);
										while (list($key,$arQuestion)=each($FORM->arQuestions)):
											$FIELD_SID = $arQuestion["SID"];
										?>
										<tr>
											<td valign="top" width="30%" class="tablebodytext">
											<?=$FORM->ShowInputCaption($FIELD_SID);?>
											<?=$FORM->isInputCaptionImage($FIELD_SID) ? "<br />".$FORM->ShowInputCaptionImage($FIELD_SID) : ""?>
</td>
											<td width="70%" valign="top" class="tablebodytext">
											<?=$FORM->ShowInput($FIELD_SID);?>
											</td>
										</tr>
										<?endwhile?>
									</table>
								</td>
							</tr>
						</table></td>
					</tr>
				</table>
				<p class="text">
				<?=$FORM->ShowRequired()?> - <?=GetMessage("FORM_REQUIRED_FIELDS")?>
				</p>
				<p align="left">
				<?=$FORM->ShowSubmitButton("", "inputbuttonflat")?>&nbsp;&nbsp;<?=$FORM->ShowApplyButton("", "inputbuttonflat")?>&nbsp;&nbsp;<?=$FORM->ShowResetButton("", "inputbuttonflat");?>
				</p>
				</form>
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
