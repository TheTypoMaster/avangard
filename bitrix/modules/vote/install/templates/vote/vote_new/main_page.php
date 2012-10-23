<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/********************************************************************
Component "Poll form" (without poll description and with thin border for the form)".

This component displays Poll form (without poll description and with thin border for the form). This is standard template and it is supplied with the module.

Sample of usage:

$APPLICATION->IncludeFile("vote/vote_new/main_page.php", array(
	"VOTE_ID" => $VOTE_ID,
	"BACK_REDIRECT_URL" => "vote_result.php"
));

Parameters:

$VOTE_ID - Poll ID
$BACK_REDIRECT_URL - Page URL where user will be redirected after voting (no redirection if no value specified for this parameter)

********************************************************************/

global $USER, $APPLICATION, $VOTING_OK, $USER_ALREADY_VOTE, $VOTING_LAMP;
$APPLICATION->SetTemplateCSS("vote/vote.css");
if (CModule::IncludeModule("vote")) :

	IncludeTemplateLangFile(__FILE__);

	if (is_array($_REQUEST)) extract($_REQUEST, EXTR_SKIP);

	if ($VOTING_OK=="Y" || $VOTE_SUCCESSFULL=="Y") $strNote .= GetMessage("VOTE_OK")."<br>";
	if ($USER_ALREADY_VOTE=="Y") $strError .= GetMessage("VOTE_ALREADY_VOTE")."<br>";
	if ($VOTING_LAMP=="red") $strError .= GetMessage("VOTE_RED_LAMP")."<br>";

	if ($VOTING_OK=="Y" && strlen($arParams["BACK_REDIRECT_URL"])>0) 
	{
		$strNavQueryString = DeleteParam(array("VOTE_ID","VOTING_OK","VOTE_SUCCESSFULL"));
		if($strNavQueryString <> "") $strNavQueryString = "&".$strNavQueryString;
		LocalRedirect($arParams["BACK_REDIRECT_URL"]."?VOTE_ID=".$PUBLIC_VOTE_ID."&VOTE_SUCCESSFULL=Y&".$strNavQueryString);
	}

	$VOTE_ID = GetVoteDataByID($arParams["VOTE_ID"], $arChannel, $arVote, $arQuestions, $arAnswers, $arDropDown, $arMultiSelect, $arGroupAnswers, "N");
	if (intval($VOTE_ID)>0) :

		$VOTE_PERMISSION = CVoteChannel::GetGroupPermission($arChannel["ID"]);
		if (intval($VOTE_PERMISSION)>=2) :
			?>
			<p><?
			echo ShowError($strError);
			echo ShowNote($strNote);
			?></p>
			<form name="vote_form" action="<?=$APPLICATION->GetCurPageParam("", array("VOTE_ID","VOTING_OK","VOTE_SUCCESSFULL"))?>" method="POST">
			<input type="hidden" name="PUBLIC_VOTE_ID" value="<?=$VOTE_ID?>">
			<input type="hidden" name="VOTE_ID" value="<?=$VOTE_ID?>">
			<table cellspacing=0 cellpadding=0 width="100%">
				<tr>
					<td>
						<table border="0" cellspacing="0" cellpadding="1" width="100%"  class="tablehead">
							<tr>
								<td width="100%">
									<table cellspacing="0" cellpadding="5" class="tablebody" width="100%">
										<?
										while (list($key,$arQuestion)=each($arQuestions)):
											$QUESTION_ID = $arQuestion["ID"];
											//reset($arAnswers[$QUESTION_ID]);
											$show_multiselect = "N";
											$show_dropdown = "N";
										?>
										<tr>
											<td>
												<table cellspacing="0" cellpadding="3">
													<tr>
														<? if (intval($arQuestion["IMAGE_ID"])>0) : ?>
														<td valign="center" width="0%"><?echo ShowImage($arQuestion["IMAGE_ID"], 50, 50, "hspace='0' vspace='0' align='left' border='0'", "", true, GetMessage("VOTE_ENLARGE"));?></td>
														<? endif; ?>
														<td valign="center" width="100%" <? if (intval($arQuestion["IMAGE_ID"])<=0) echo "colspan='2'" ?>><font class="text"><b><?=$arQuestion["QUESTION"]?></b></font></td>
													</tr>
													<? 
													//while (list($key,$arAnswer)=each($arAnswers[$QUESTION_ID])) : 
													if (is_array($arAnswers[$QUESTION_ID])):

													foreach ($arAnswers[$QUESTION_ID] as $arAnswer):
														if ($arAnswer["FIELD_TYPE"]==2 && $show_dropdown=="Y") continue;
														if ($arAnswer["FIELD_TYPE"]==3 && $show_multiselect=="Y") continue;

													?>
													<tr>
														<td colspan=2><?
														switch ($arAnswer["FIELD_TYPE"]) :
															case 0:
																$field_name = "vote_radio_".$QUESTION_ID;
																?><input type="radio" name="<?=$field_name?>" value="<?=$arAnswer["ID"]?>" <?=$arAnswer["FIELD_PARAM"]?>><font class="text">&nbsp;<?=$arAnswer["MESSAGE"]?></font><?
																break;
															case 1:
																$field_name = "vote_checkbox_".$QUESTION_ID;
																?><input type="checkbox" name="<?=$field_name?>[]" value="<?=$arAnswer["ID"]?>" <?=$arAnswer["FIELD_PARAM"]?>><font class="text">&nbsp;<?=$arAnswer["MESSAGE"]?></font><?
																break;
															case 2:
																if ($show_dropdown!="Y")
																{
																	$field_name = "vote_dropdown_".$QUESTION_ID;
																	echo SelectBoxFromArray($field_name, $arDropDown[$QUESTION_ID], "", "", $arAnswer["FIELD_PARAM"]);
																	$show_dropdown = "Y";
																}
																break;
															case 3:
																if ($show_multiselect!="Y")
																{
																	$field_name = "vote_multiselect_".$QUESTION_ID;
																	echo SelectBoxMFromArray($field_name."[]", $arMultiSelect[$QUESTION_ID], array(), "", false, $arAnswer["FIELD_HEIGHT"], $arAnswer["FIELD_PARAM"]);
																	$show_multiselect = "Y";
																}
																break;
															case 4:
																$field_name = "vote_field_".$arAnswer["ID"];
																?><?if (strlen(trim($arAnswer["MESSAGE"]))>0):?><font class="text"><?=$arAnswer["MESSAGE"]?></font><br><?endif?><input type="text" name="<?=$field_name?>" value="" size="<?=$arAnswer["FIELD_WIDTH"]?>" <?=$arAnswer["FIELD_PARAM"]?>><?
																break;
															case 5:
																$field_name = "vote_memo_".$arAnswer["ID"];
																?><font class="text"><?if (strlen(trim($arAnswer["MESSAGE"]))>0) echo $arAnswer["MESSAGE"]."<br>"?></font><textarea name="<?=$field_name?>" <?=$arAnswer["FIELD_PARAM"]?> cols="<?=$arAnswer["FIELD_WIDTH"]?>" rows="<?=$arAnswer["FIELD_HEIGHT"]?>"></textarea><?
																break;
														endswitch;
														?></td>
													</tr>
													<? endforeach;endif; ?>
												</table>
											</td>
										</tr>
										<?endwhile?>
										<tr>
											<td><input type="submit" name="vote" class="inputbuttonflat" value="<?=GetMessage("VOTE_SUBMIT_BUTTON")?>"><input type="hidden" name="vote" value="Y"></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
					<td><img src="/bitrix/images/1.gif" width="6" height="1" border=0 alt=""></td>
				</tr>
			</table>
			</form>
			<?
		endif;
	endif;
endif;
?>