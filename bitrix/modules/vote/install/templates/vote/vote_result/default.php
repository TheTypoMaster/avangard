<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/********************************************************************
Component "Poll results".

Displays diagrams for the poll results. Each question has its own result diagram which is being specified in the question settings through the administrative part of the module. This is standard component that is supplied with the module.

Sample of usage:

$APPLICATION->IncludeFile("vote/vote_result/default.php", array(
	"VOTE_ID" => $_REQUEST["VOTE_ID"]
));

Parameters:

$VOTE_ID - Poll ID

********************************************************************/

global $USER, $APPLICATION;
$APPLICATION->SetTemplateCSS("vote/vote.css");
if (CModule::IncludeModule("vote")) :

	IncludeTemplateLangFile(__FILE__);
	if (is_array($_REQUEST)) extract($_REQUEST, EXTR_SKIP);

	if ($VOTING_OK=="Y" || $VOTE_SUCCESSFULL=="Y") $strNote .= GetMessage("VOTE_OK")."<br>";
	if ($USER_ALREADY_VOTE=="Y") $strError .= GetMessage("VOTE_ALREADY_VOTE")."<br>";

	$VOTE_ID = GetVoteDataByID($arParams["VOTE_ID"], $arChannel, $arVote, $arQuestions, $arAnswers, $arDropDown, $arMultiSelect, $arGroupAnswers, "Y");
	if (intval($VOTE_ID)>0) :

		$VOTE_PERMISSION = CVoteChannel::GetGroupPermission($arChannel["ID"]);
		if (intval($VOTE_PERMISSION)>=1) :

			?><p><?
			echo ShowError($strError);
			echo ShowNote($strNote);
			?></p>

			<table cellspacing=0 cellpadding=0 width="100%">
				<tr>
					<td width="100%"><?

					/***********************************************************************************
													Poll header
					***********************************************************************************/ 

					if (strlen($arVote["TITLE"])>0):
					?><font class="h2"><b><?echo $arVote["TITLE"];?></b></font><br><img src="/bitrix/images/1.gif" width="1" height="6" border=0 alt=""><?
					endif;
					?><font class="smalltext"><?
					if ($arVote["DATE_START"]):
					?><br><?=GetMessage("VOTE_START_DATE")?>&nbsp;<?echo $arVote["DATE_START"]?><?
					endif;
					if ($arVote["DATE_END"] && $arVote["DATE_END"]!="31.12.2030 23:59:59"):
					?><br><?=GetMessage("VOTE_END_DATE")?>&nbsp;<?echo $arVote["DATE_END"]?><?
					endif;
					?><br><?=GetMessage("VOTE_COUNT")?>&nbsp;<?=$arVote["COUNTER"]?><?
					if ($arVote["LAMP"]=="green") :
						?><br><font class="pointed"><?=GetMessage("VOTE_IS_ACTIVE")?></font><?
					elseif ($arVote["LAMP"]=="red") :
						?><br><font class="required"><?=GetMessage("VOTE_IS_NOT_ACTIVE")?></font><?
					endif;
					?></p></font><font class="text">
					<?if ($arVote["IMAGE_ID"]):?>
					<table cellpadding="0" cellspacing="0" border="0" align="left">
						<tr>
							<td><?echo ShowImage($arVote["IMAGE_ID"], 253, 300, "hspace='3' vspace='3' align='left' border='0'", "", true, GetMessage("VOTE_ENLARGE"));?></td>
							<td valign="top" width="0%"><img src="/images/1.gif" width="10" height="1"></td>
						</tr>
						<tr>
							<td colspan=2><img src="/images/1.gif" width="1" height="10"></td>
						</tr>
					</table>
					<? endif;
					echo $arVote["DESCRIPTION"];
					?></td>
				<tr>
					<td><?

					/***********************************************************************************
														Questions
					***********************************************************************************/ 

					?>
					<p>
					<table cellspacing="0" cellpadding="10" width="100%">
						<?
						while (list($key,$arQuestion)=each($arQuestions)):
							if ($arQuestion["DIAGRAM"]!="Y") continue;
							$QUESTION_ID = $arQuestion["ID"];
							$template = "";
						?>
						<tr>
							<td><?
							// If template of question result displaying was specified in component's parameters then
							if (strlen(trim($arParams["QUESTION_TEMPLATE_".$QUESTION_ID]))>0)
							{
								// use this template 
								$template = trim($arParams["QUESTION_TEMPLATE_".$QUESTION_ID]);
							}
							else // else
							{
								// set template name specified by default in the database
								$template = trim($arQuestion["TEMPLATE_NEW"]);
								if (strlen(trim($template))<=0) $template = "default.php";
							}
							$template = COption::GetOptionString("vote", "VOTE_TEMPLATE_PATH_QUESTION_NEW")."/".$template;
							$APPLICATION->IncludeFile($template, array("QUESTION_ID" => $QUESTION_ID, "arVote" => $arVote, "arQuestion" => $arQuestion, "arAnswers" => $arAnswers, "arGroupAnswers" => $arGroupAnswers));
							?></td>
						</tr>
						<?endwhile?>
					</table></td>
				</tr>
			</table>
			<?
		else :
			echo ShowError(GetMessage("VOTE_ACCESS_DENIED"));
		endif;
	else :
		echo ShowError(GetMessage("VOTE_NOT_FOUND"));
	endif;
else:
	echo ShowError(GetMessage("VOTE_MODULE_IS_NOT_INSTALLED"));
endif;
?>
