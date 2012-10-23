<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/********************************************************************
Component "Poll result" (without poll description and with thin border on the form). 

Displays diagrams for the poll results (without poll description and with thin border on the form). Each question has its own result diagram which is being specified in the question settings through the administrative part of the module. This is standard component that is supplied with the module.

Sample of usage:

$APPLICATION->IncludeFile("vote/vote_result/main_page.php", array(
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
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td width="100%">
						<table border="0" cellspacing="0" cellpadding="1" width="100%"  class="tablehead">
							<tr>
								<td width="100%">
									<table cellspacing="0" cellpadding="2" width="100%" class="tablebody">
										<?
										while (list($key,$arQuestion)=each($arQuestions)):
											if ($arQuestion["DIAGRAM"]!="Y") continue;
											$QUESTION_ID = $arQuestion["ID"];
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
												// use template name specified by default in the database
												$template = trim($arQuestion["TEMPLATE_NEW"]);
												if (strlen(trim($template))<=0) $template = "default.php";
											}
											$template = COption::GetOptionString("vote", "VOTE_TEMPLATE_PATH_QUESTION_NEW")."/".$template;
											$APPLICATION->IncludeFile($template, array("QUESTION_ID" => $QUESTION_ID, "arVote" => $arVote, "arQuestion" => $arQuestion, "arAnswers" => $arAnswers, "arGroupAnswers" => $arGroupAnswers));
											?></td>
										</tr>
										<?endwhile?>
										<tr><td><font style="font-size: 10px;">&nbsp;</font></td></tr>
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
?>
