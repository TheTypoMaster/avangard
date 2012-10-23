<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/********************************************************************
Component "Polls list".

This component is intended for displaying the polls list. This is standard component that is supplied with the module.

Sample of usage:

$APPLICATION->IncludeFile("vote/vote_list/default.php", array(
	"CHANNEL_SID" => "MY_POLLS",
	"VOTE_URL" => "vote_new.php",
	"RESULTS_URL" => "vote_result.php"
));

Parameters:

$CHANNEL_SID - mnemonic code of the polls group (polls from all groups binded to the current site will be displayed if no value set)
$VOTE_URL - Page URL for displaying the Poll form (for participating in poll)
$RESULTS_URL - Page URL for displaying the poll result diagrams

********************************************************************/

global $USER, $APPLICATION;
$APPLICATION->SetTemplateCSS("vote/vote.css");
if (CModule::IncludeModule("vote")):

	IncludeTemplateLangFile(__FILE__);

	if (is_array($_REQUEST)) extract($_REQUEST, EXTR_SKIP);
	
	?><p><?
	$rsVotes = GetVoteList($arParams["CHANNEL_SID"]);
	$rsVotes->NavStart(10); 
	echo $rsVotes->NavPrint(GetMessage("VOTE_PAGES"));
	?></p>
	<?while ($arVote=$rsVotes->GetNext(true,false)) :?>
	<table border="0" cellspacing="0" cellpadding="7" width="100%">
		<tr>
			<td>
				<table border="0" cellspacing="0" cellpadding="0" width="100%"  class="tableborder">
					<tr>
						<td>
							<table border="0" cellspacing="1" cellpadding="4" width="100%">
								<tr>
									<td class="tablebody">
										<table border="0" cellspacing="0" cellpadding="0" width="100%">
											<tr>
												<? if (strlen($arVote["TITLE"])>0) : ?>
												<td width="100%"><font class="h2"><b><?echo $arVote["TITLE"];?></b></font></td>
												<? endif; ?>
												<td width="0%" nowrap><font class="tablebodytext"><?
												if ($arVote["LAMP"]=="green" && $arVote["MAX_PERMISSION"]>=2) :
												?>[&nbsp;<a class="tablebodylink" href="<?=$arParams["VOTE_URL"]?>?VOTE_ID=<?=$arVote["ID"]?>"><?=GetMessage("VOTE_VOTING")?></a>&nbsp;]<?
												endif;
												?><?
												if ($arVote["MAX_PERMISSION"]>=1) :
												?>&nbsp;&nbsp;[&nbsp;<a class="tablebodylink" href="<?=$arParams["RESULTS_URL"]?>?VOTE_ID=<?=$arVote["ID"]?>"><?=GetMessage("VOTE_RESULTS")?></a>&nbsp;]<?
												endif;
												?></font></td>
											</tr>
										</table><font class="smalltext"><br><?
										if ($arVote["DATE_START"]):
										?><br><?=GetMessage("VOTE_START_DATE")?>&nbsp;<?echo $arVote["DATE_START"]?><?
										endif;
										if ($arVote["DATE_END"] && $arVote["DATE_END"]!="31.12.2030 23:59:59"):
										?><br><?=GetMessage("VOTE_END_DATE")?>&nbsp;<?echo $arVote["DATE_END"]?><?
										endif;
										?><br><?=GetMessage("VOTE_VOTES")?>&nbsp;<?=$arVote["COUNTER"]?><?
										if ($arVote["LAMP"]=="green") :
											?><br><font class="pointed"><?=GetMessage("VOTE_IS_ACTIVE")?></font><?
										elseif ($arVote["LAMP"]=="red") :
											?><br><font class="required"><?=GetMessage("VOTE_IS_NOT_ACTIVE")?></font><?
										endif;
										?><br></font><br><font class="text">
										<?if ($arVote["IMAGE_ID"]):?>
										<table cellpadding="0" cellspacing="0" border="0" align="left">
											<tr>
												<td><?echo ShowImage($arVote["IMAGE_ID"], 253, 300, "hspace='3' vspace='3' align='left' border='0'", "", true, GetMessage("VOTE_ENLARGE"));?></td>
												<td valign="top" width="0%"><img src="/images/1.gif" width="10" height="1"></td>
											</tr>
										</table>
										<? endif;
										echo $arVote["DESCRIPTION"];
										?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<?endwhile;?>
	<p><?echo $rsVotes->NavPrint(GetMessage("VOTE_PAGES"));?></p>
	<?
else:
	echo ShowError(GetMessage("VOTE_MODULE_IS_NOT_INSTALLED"));
endif;
?>
