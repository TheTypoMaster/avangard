<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<style type="text/css">
.bx-gadgets-perfmon .bx-gadgets-top-title {background:url("/bitrix/gadgets/bitrix/admin_perfmon/images/gadgets-sprite.png") no-repeat 0 -84px; height:25px; margin-left:8px; padding-left:29px;}
* html .bx-gadgets-perfmon .bx-gadgets-top-title {height:33px;}
.bx-gadgets-perfmon .bx-gadgets-button {float:right; margin-top:15px;}
</style>
<?
$bPerfmonModuleInstalled = IsModuleInstalled("perfmon");
if($bPerfmonModuleInstalled):
	$mark_value = (double)COption::GetOptionString("perfmon", "mark_php_page_rate", "");
	if($mark_value > 0):
		$lamp_class = " bx-gadgets-info";	
		$text1 =  GetMessage("GD_PERFMON_INSTALLED");
		$text2 = GetMessage("GD_PERFMON_CURRENT").$mark_value;
		$text3 = GetMessage("GD_PERFMON_LEVEL", array("#LANGUAGE_ID#"=>LANGUAGE_ID));
		if($mark_value >= 5):
			$text2_class = "green";
		else:
			$text2_class = "red";
		endif;
	else:
		$lamp_class = " bx-gadgets-note";
		$text1 = GetMessage("GD_PERFMON_CHECK");
		$text2 = GetMessage("GD_PERFMON_NO_RESULT");
		$text2_class = "red";
		$text3 = '<p>'.GetMessage("GD_PERFMON_NO_RESULT_DESC").'</p><form method="get" action="perfmon_panel.php"><input type="hidden" name="lang" value="'.LANGUAGE_ID.'"><input type="submit" name="" value="'.GetMessage("GD_PERFMON_NO_TEST").'"'.($APPLICATION->GetGroupRight("perfmon")<"W" ? " disabled" : "").'></form>';
	endif;
else:
	$lamp_class = "";
	$text1 = GetMessage("GD_PERFMON_NOT_INSTALLED");
	$text2 = GetMessage("GD_PERFMON_NO_MODULE_INST");
	$text2_class = "red";
	$text3 = '<p>'.GetMessage("GD_PERFMON_NO_MODULE_INST_DESC").'</p><form method="get" action="module_admin.php"><input type="hidden" name="lang" value="'.LANGUAGE_ID.'"><input type="hidden" name="id" value="perfmon">'.bitrix_sessid_post().'<input type="submit" name="install" value="'.GetMessage("GD_PERFMON_MODULE_INSTALL").'"'.(!$USER->CanDoOperation('edit_other_settings') ? " disabled" : "").'></form>';
endif;
?>
<div class="bx-gadgets-warning<?=$lamp_class?>">
	<div class="bx-gadgets-warning-cont-ball"><?=$text1?></div>
	<div class="bx-gadgets-warning-bord"></div>
	<div class="bx-gadgets-warning-bord2"></div>
	<div class="bx-gadgets-warning-text-<?=$text2_class?>">
		<div class="bx-gadgets-warning-cont"><?=$text2?></div>
	</div>
	<div class="bx-gadgets-warning-bord2"></div>
	<div class="bx-gadgets-warning-bord"></div>
</div>
<div class="bx-gadgets-text"><?=$text3?></div>
