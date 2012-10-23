<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/********************************************************************
Component "Form or Result for the current poll of the specified Poll group".

This component displays form of the current poll in the specified poll group. If user has already voted in this poll then result diagram will be displayed instead. Mainly used for the site Home page. This is standard template and it is supplied with the module.

Sample of usage:

$APPLICATION->IncludeFile("vote/vote_new/current_channel.php", array(
	"CHANNEL_SID" => "PHONE"
));

Parameters:

$CHANNEL_SID - mnemonic code for the Poll group 
********************************************************************/

global $USER, $APPLICATION, $VOTING_OK, $USER_ALREADY_VOTE, $VOTING_LAMP;
$APPLICATION->SetTemplateCSS("vote/vote.css");
if (CModule::IncludeModule("vote")) :

	IncludeTemplateLangFile(__FILE__);

	if (is_array($_REQUEST)) extract($_REQUEST, EXTR_SKIP);

	$VOTE_ID = GetCurrentVote($arParams["CHANNEL_SID"]);//2


	if ($VOTING_OK=="Y") $strNote .= GetMessage("VOTE_OK")."<br>";
	if ($USER_ALREADY_VOTE=="Y") $strError .= GetMessage("VOTE_ALREADY_VOTE")."<br>";
	if ($VOTING_LAMP=="red") $strError .= GetMessage("VOTE_RED_LAMP")."<br>";

	$IsUserVoted = "N";
	if ($VOTING_OK!="Y" && $USER_ALREADY_VOTE!="Y") $IsUserVoted = IsUserVoted($VOTE_ID) ? "Y" : "N";

	$VOTE_ID = GetVoteDataByID($VOTE_ID, $arChannel, $arVote, $arQuestions, $arAnswers, $arDropDown, $arMultiSelect, $arGroupAnswers, "N");
	if (intval($VOTE_ID)>0) :

		echo ShowError($strError);
		echo ShowNote($strNote);
		$VOTE_PERMISSION = CVoteChannel::GetGroupPermission($arChannel["ID"]);

		if ($IsUserVoted!="Y" && $VOTING_OK!="Y" && $USER_ALREADY_VOTE!="Y") :
		
			if (intval($VOTE_PERMISSION)>=2) :
				?>
				<table cellspacing="0" cellpadding="5" width="100%">
				<form name="vote_form" action="<?=$APPLICATION->GetCurPageParam("", array("VOTE_ID","VOTING_OK"))?>" method="POST">
				<input type="hidden" name="PUBLIC_VOTE_ID" value="<?=$VOTE_ID?>">
					<?
					while (list($key,$arQuestion)=each($arQuestions)):
						$QUESTION_ID = $arQuestion["ID"];
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
									<td valign="center" width="100%" <? if (intval($arQuestion["IMAGE_ID"])<=0) echo "colspan='2'" ?>><font class="newstext"><b><?=$arQuestion["QUESTION"]?></b></font></td>
								</tr>
								<? 
								if (is_array($arAnswers[$QUESTION_ID])):
									reset($arAnswers[$QUESTION_ID]);

								while (list($key,$arAnswer)=each($arAnswers[$QUESTION_ID])) : 
									if ($arAnswer["FIELD_TYPE"]==2 && $show_dropdown=="Y") continue;
									if ($arAnswer["FIELD_TYPE"]==3 && $show_multiselect=="Y") continue;

								?>
								<tr>
									<td colspan=2><?
									switch ($arAnswer["FIELD_TYPE"]) :
										case 0:
											$field_name = "vote_radio_".$QUESTION_ID;
											?><input type="radio" name="<?=$field_name?>" value="<?=$arAnswer["ID"]?>" <?=$arAnswer["FIELD_PARAM"]?>><font class="newstext">&nbsp;<?=$arAnswer["MESSAGE"]?></font><?
											break;
										case 1:
											$field_name = "vote_checkbox_".$QUESTION_ID;
											?><input type="checkbox" name="<?=$field_name?>[]" value="<?=$arAnswer["ID"]?>" <?=$arAnswer["FIELD_PARAM"]?>><font class="newstext">&nbsp;<?=$arAnswer["MESSAGE"]?></font><?
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
											?><?if (strlen(trim($arAnswer["MESSAGE"]))>0):?><font class="newstext"><?=$arAnswer["MESSAGE"]?></font><br><?endif?><input type="newstext" name="<?=$field_name?>" value="" size="<?=$arAnswer["FIELD_WIDTH"]?>" <?=$arAnswer["FIELD_PARAM"]?>><?
											break;
										case 5:
											$field_name = "vote_memo_".$arAnswer["ID"];
											?><font class="newstext"><?if (strlen(trim($arAnswer["MESSAGE"]))>0) echo $arAnswer["MESSAGE"]."<br>"?></font><textarea name="<?=$field_name?>" <?=$arAnswer["FIELD_PARAM"]?> cols="<?=$arAnswer["FIELD_WIDTH"]?>" rows="<?=$arAnswer["FIELD_HEIGHT"]?>"></textarea><?
											break;
									endswitch;
									?></td>
								</tr>
								<? endwhile; endif;?>
							</table>
						</td>
					</tr>
					<?endwhile?>
					<tr>
						<td><input class="inputbuttonflat" type="submit" name="vote" value="<?=GetMessage("VOTE_SUBMIT_BUTTON")?>"><input type="hidden" name="vote" value="Y"></td>
					</tr>
				</form>
				</table>
				<?
			endif;

		else:

			if (intval($VOTE_PERMISSION)>=1) :

				?>
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td width="100%">
							<table border="0" cellspacing="0" cellpadding="1" width="100%">
								<tr>
									<td width="100%">
										<table cellspacing="0" cellpadding="2" width="100%">
											<?
											while (list($key,$arQuestion)=each($arQuestions)):
												if ($arQuestion["DIAGRAM"]!="Y") continue;
												$QUESTION_ID = $arQuestion["ID"];
											?>
											<tr>
												<td><?
												$template = (strlen(trim($arQuestion["TEMPLATE_NEW"]))>0) ? $arQuestion["TEMPLATE_NEW"] : "default.php";
												$APPLICATION->IncludeFile(COption::GetOptionString("vote", "VOTE_TEMPLATE_PATH_QUESTION_NEW")."/".$template, array("QUESTION_ID" => $QUESTION_ID, "arVote" => $arVote, "arQuestion" => $arQuestion, "arAnswers" => $arAnswers, "arGroupAnswers" => $arGroupAnswers));
												?></td>
											</tr>
											<?endwhile?>
										</table>
									</td>
								</tr>
							</table></td>
						<td><img src="/bitrix/images/1.gif" width="6" height="1" border=0 alt=""></td>
					</tr>
				</table>
				<?
			endif;

		endif;
	endif;
endif;
?>