<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
if(CModule::IncludeModule("subscribe")):

//variables from component
$PAGE = (empty($PAGE)? "subscr_edit.php" : htmlspecialchars($PAGE));
$SHOW_COUNT = ($SHOW_COUNT <> "N"? "Y":"N");
$SHOW_HIDDEN = ($SHOW_HIDDEN <> "Y"? "N":"Y");

$APPLICATION->SetTitle(GetMessage("subscr_title"));
?>

<h4><?echo GetMessage("subscr_new_title")?></h4>

<p><font class="text"><?echo GetMessage("subscr_new_note")?></font></p>

<form action="<?echo $PAGE?>" method="GET">
<table width="100%" border="0" cellspacing="2" cellpadding="3">
<tr class="tablehead">
	<td><font class="tableheadtext">&nbsp;</font></td>
	<td><font class="tableheadtext"><?echo GetMessage("subscr_name")?></font></td>
	<td><font class="tableheadtext"><?echo GetMessage("subscr_desc")?></font></td>
<?if($SHOW_COUNT == "Y"):?>
	<td><font class="tableheadtext"><?echo GetMessage("subscr_cnt")?></font></td>
<?endif;?>
</tr>

<?
//get current user subscription from cookies
$aSubscr = CSubscription::GetUserSubscription();

//get site's newsletter categories
$arFilter = array("ACTIVE"=>"Y", "LID"=>LANG);
if($SHOW_HIDDEN<>"Y")
	$arFilter["VISIBLE"]="Y";
$rub = CRubric::GetList(array("SORT"=>"ASC", "NAME"=>"ASC"), $arFilter);
$nRubric = 1;
while(($rub_arr = $rub->Fetch())):
?>
<tr valign="top">
	<td><font class="tablebodytext"><input type="checkbox" class="inputcheckbox" name="sf_RUB_ID[]" id="sf_RUB_ID_<?echo $nRubric?>" value="<?echo $rub_arr["ID"]?>" checked></font></td>
	<td><font class="tablebodytext"><label for="sf_RUB_ID_<?echo $nRubric?>"><?echo htmlspecialchars($rub_arr["NAME"])?></label></font></td>
	<td><font class="tablebodytext"><?echo htmlspecialchars($rub_arr["DESCRIPTION"])?></font></td>
<?if($SHOW_COUNT == "Y"):?>
	<td align="right"><font class="tablebodytext"><?echo CRubric::GetSubscriptionCount($rub_arr["ID"]);?></font></td>
<?endif?>
</tr>
<?
	$nRubric++;
endwhile;
?>
</table>
<table border="0" cellspacing="2" cellpadding="2">
<tr>
	<td><font class="text"><?echo GetMessage("subscr_addr")?></font></td>
	<td><input type="text" class="inputtext" name="sf_EMAIL" size="20" value="<?if($aSubscr["EMAIL"] == "") echo htmlspecialchars($USER->GetParam("EMAIL"));?>" title="<?echo GetMessage("subscr_email_title")?>"></td>
	<td><input type="submit" class="inputbutton" name="" value="<?echo GetMessage("subscr_button")?>"></td>
</tr>
</table>
</form>

<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="tableborder"><img src="/bitrix/images/1.gif" width="1" height="2" alt=""></td></tr></table>
<br>	
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr valign="top">
	<td width="35%">
<h4><?echo GetMessage("subscr_edit_title")?></h4>	

<p><font class="text"><?echo GetMessage("subscr_edit_note")?></font></p>

<form action="<?echo $PAGE?>" method="GET">
<table border="0" cellspacing="2" cellpadding="0">
<tr>
	<td><font class="text">e-mail</font></td>
</tr>
<tr>
	<td><input type="text" class="inputtext" name="sf_EMAIL" size="20" value="<?echo htmlspecialchars($aSubscr["EMAIL"]);?>" title="<?echo GetMessage("subscr_email_title")?>"></td>
</tr>
<?
//check whether already authorized
$bShowPass = true;
if($aSubscr["ID"] > 0)
{
	//try to authorize user account's subscription
	if($aSubscr["USER_ID"]>0 && !CSubscription::IsAuthorized($aSubscr["ID"]))
		CSubscription::Authorize($aSubscr["ID"], "");
	//check authorization
	if(CSubscription::IsAuthorized($aSubscr["ID"]))
		$bShowPass = false;
}
?>
<?if($bShowPass):?>
<tr>
	<td><font class="text"><?echo GetMessage("subscr_edit_pass")?></font><font class="starrequired">*</font></td>
</tr>
<tr>
	<td><input type="password" class="inputtext" name="AUTH_PASS" size="20" value="" title="<?echo GetMessage("subscr_edit_pass_title")?>"></td>
</tr>
<?else:?>
<tr>
	<td><font class="text successcolor"><?echo GetMessage("subscr_edit_pass_entered")?></font><font class="starrequired">*</font><br>
	<font style="font-size:2px">&nbsp;<br></font></td>
</tr>
<?endif;?>
<tr>
	<td><input type="submit" class="inputbutton" name="" value="<?echo GetMessage("subscr_edit_button")?>"></td>
</tr>
</table>
<input type="hidden" name="action" value="authorize">
</form>


	</td>
	<td width="0%" class="text">&nbsp;</td>
	<td width="0%" class="tableborder"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td>
	<td width="0%" class="text">&nbsp;&nbsp;</td>
	<td width="35%">
<h4><?echo GetMessage("subscr_pass_title")?></h4>	

<p><font class="text"><?echo GetMessage("subscr_pass_note")?></font></p>
<form action="<?echo $PAGE?>" method="GET">
<table border="0" cellspacing="2" cellpadding="0">
<tr>
	<td><font class="text">e-mail</font></td>
</tr>
<tr>
	<td><input type="text" class="inputtext" name="sf_EMAIL" size="20" value="<?echo htmlspecialchars($aSubscr["EMAIL"]);?>" title="<?echo GetMessage("subscr_email_title")?>"></td>
</tr>
<tr>
	<td><input type="submit" class="inputbutton" name="" value="<?echo GetMessage("subscr_pass_button")?>"></td>
</tr>
</table>
<input type="hidden" name="action" value="sendpassword">
</form>

	</td>
	<td width="0%" class="text">&nbsp;</td>
	<td width="0%" class="tableborder"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td>
	<td width="0%" class="text">&nbsp;&nbsp;</td>
	<td width="30%">
<h4><?echo GetMessage("subscr_unsubscribe_title")?></h4>	
<p><font class="text"><?echo GetMessage("subscr_unsubscribe_note")?></font></p>
	</td>
</tr>
</table>

<br><br>
<table border="0" cellspacing="0" cellpadding="0">
<tr valign="top">
	<td><font class="starrequired">*&nbsp;</font></td>
	<td><font class="text"><?echo GetMessage("subscr_note")?></font></td>
</tr>
</table>

<?
else: //IncludeModule("subscribe")
	$APPLICATION->SetTitle(GetMessage("subscr_unavailable"));
	ShowError(GetMessage("subscr_unavailable2"));
endif;
?>