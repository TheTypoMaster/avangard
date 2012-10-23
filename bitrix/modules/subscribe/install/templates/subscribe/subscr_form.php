<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
if(CModule::IncludeModule("subscribe")):

//variables from component
if(empty($PAGE))
	$PAGE = COption::GetOptionString("subscribe", "subscribe_section")."subscr_edit.php";
$SHOW_HIDDEN = ($SHOW_HIDDEN <> "Y"? "N":"Y");

//get current user subscription from cookies
$aSubscr = CSubscription::GetUserSubscription();

//get user's newsletter categories
$aSubscrRub = CSubscription::GetRubricArray(intval($aSubscr["ID"]));

//get site's newsletter categories
$arFilter = array("ACTIVE"=>"Y", "LID"=>LANG);
if($SHOW_HIDDEN<>"Y")
	$arFilter["VISIBLE"]="Y";

$rub = CRubric::GetList(array("SORT"=>"ASC", "NAME"=>"ASC"), $arFilter);

if(($rub_arr = $rub->Fetch())):
?>
<table border="0" cellspacing="2" cellpadding="0" align="center">
<form action="<?echo htmlspecialchars(str_replace("#SITE_DIR#", LANG_DIR, $PAGE))?>">
<?
$nRubric = 1;
do
{
	$bChecked = (
		!is_array($_GET["sf_RUB_ID"]) && in_array($rub_arr["ID"], $aSubscrRub) ||// user is already subscribed
		!is_array($_GET["sf_RUB_ID"]) && intval($aSubscr["ID"])==0 || 	// there is no information about user subscription
		is_array($_GET["sf_RUB_ID"]) && in_array($rub_arr["ID"], $_GET["sf_RUB_ID"])		// user has checked the category and posted the form
	);
?>
<tr>
	<td width="0%" valign="top"><input type="checkbox" name="sf_RUB_ID[]" id="sf_RUB_ID_<?echo $nRubric?>" value="<?echo $rub_arr["ID"]?>"<?if($bChecked) echo " checked"?>></td>
	<td width="100%"><font face="Arial, Helvetica, sans-serif" style="font-size:12px;"><label for="sf_RUB_ID_<?echo $nRubric?>"><?echo htmlspecialchars($rub_arr["NAME"])?></label></font></td>
</tr>
<?
	$nRubric++;
}
while(($rub_arr = $rub->Fetch()));
?>
<tr>
	<td colspan="2" align="center"><font size="-1"><input type="text" name="sf_EMAIL" size="15" value="<?echo htmlspecialchars(strlen($_GET["sf_EMAIL"])>0? $_GET["sf_EMAIL"] : (strlen($aSubscr["EMAIL"])>0? $aSubscr["EMAIL"]:""));?>" title="<?echo GetMessage("subscr_form_email_title")?>"></font></td>
</tr>
<tr>
	<td colspan="2" align="center"><font face="Arial, Helvetica, sans-serif" size="-1"><input type="submit" name="" value="<?echo GetMessage("subscr_form_button")?>" style="font-size:12px"></font></td>
</tr>
</form>
</table>
<?endif;//$rub->Fetch()?>
<?endif;//IncludeModule("subscribe")?>