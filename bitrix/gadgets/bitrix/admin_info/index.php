<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$bxProductConfig = array();
if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/.config.php"))
	include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/.config.php");

if(isset($bxProductConfig["admin"]["index"]))
	$sProduct = $bxProductConfig["admin"]["index"];
else
	$sProduct = GetMessage("GD_INFO_product").' &quot;'.GetMessage("GD_INFO_product_name_".COption::GetOptionString("main", "vendor", "1c_bitrix")).'#VERSION#&quot;.';
$sVer = ($GLOBALS['USER']->CanDoOperation('view_other_settings')? " ".SM_VERSION : "");
$sProduct = str_replace("#VERSION#", $sVer, $sProduct);
?>
<div class="bx-gadgets-info">
	<table class="bx-gadgets-info-site-table">
	<tr>
		<td align="left" valign="top">
		<div class="bx-gadgets-text">
		<?=$sProduct ?><br>
		<?$last_updated = COption::GetOptionString("main", "update_system_update", "-");?>
		<?=str_replace("#VALUE#", $last_updated, GetMessage("GD_INFO_LASTUPDATE"));?><br>

		<?if(IsModuleInstalled("perfmon") && $GLOBALS["APPLICATION"]->GetGroupRight("perfmon") != "D"):
			$mark_value = (double)COption::GetOptionString("perfmon", "mark_php_page_rate", "");
			if($mark_value < 5)
				$mark_value = GetMessage("GD_PERFMON_NO_RESULT");
			?><?=str_replace("#VALUE#", $mark_value, GetMessage("GD_INFO_PERFMON"));?><br><?
		endif;?>

		<?
		if ($GLOBALS["USER"]->CanDoOperation('view_all_users')):
			$user_count = CUser::GetCount();
			?><?=str_replace("#VALUE#", $user_count, GetMessage("GD_INFO_USERS"));?><br><?
		endif;
		?>
		</div>
		</td>
		<td align="right" valign="top">
			<img src="/bitrix/gadgets/bitrix/admin_info/images/<?=(in_array(LANGUAGE_ID, array("ru", "en", "de"))?LANGUAGE_ID:"en")?>/logo.gif">
		</td>
	</tr>
	</table>
</div>