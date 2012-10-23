<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!empty($arResult["ERROR_MESSAGE"]))
	ShowError($arResult["ERROR_MESSAGE"]);
/********************************************************************
				Input params
********************************************************************/
/***************** BASE ********************************************/
$arParams["IMAGE_SIZE"] = (intVal($arParams["IMAGE_SIZE"]) > 0 ? $arParams["IMAGE_SIZE"] : 100);
/********************************************************************
				/Input params
********************************************************************/

if ($arResult["VIEW"] == "Y"):
?><a name="postform"></a>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="forum-message">
	<tr>
		<td class="forum-message-user-info" rowspan="2"><div class="forum-message-name"><?=GetMessage("F_VIEW")?></div></td>
		<td class="border-bottom"><div class="forum-hr"></div>
		<div class="forum-message-text"><?=$arResult["MESSAGE_VIEW"]["TEXT"]?></div><?
		foreach ($arResult["MESSAGE_VIEW"]["FILES"] as $arFile): 
		?><div class="forum-message-img"><?
			?><?$GLOBALS["APPLICATION"]->IncludeComponent(
				"bitrix:forum.interface", "show_file",
				Array(
					"FILE" => $arFile,
					"WIDTH" => $arParams["IMAGE_SIZE"],
					"HEIGHT" => $arParams["IMAGE_SIZE"],
					"CONVERT" => "N",
					"FAMILY" => "FORUM",
					"SINGLE" => "Y",
					"RETURN" => "N",
					"SHOW_LINK" => "Y"),
				null,
				array("HIDE_ICONS" => "Y"));
		?></div><?
		endforeach;
		?></td></tr>
	<tr><td class="border-top"><div class="forum-hr"></div></td></tr>
</table>
<div class="forum-br"></div><?
elseif ($arResult["SHOW_MESSAGE_FOR_AJAX"] == "Y"):

	ob_end_clean();
	ob_start();
	$GLOBALS["bShowImageScriptPopup"] = true;
	
	?><div class="forum-message-text" id="message_text_<?=$arResult["MESSAGE"]["ID"]?>"><?
		?><?=$arResult["MESSAGE"]["POST_MESSAGE_TEXT"]?></div><?
	foreach ($arResult["MESSAGE"]["FILES"] as $arFile): 
	?><div class="forum-message-img"><?
		?><?$GLOBALS["APPLICATION"]->IncludeComponent(
			"bitrix:forum.interface", "show_file",
			Array(
				"FILE" => $arFile,
				"WIDTH" => $arParams["IMAGE_SIZE"],
				"HEIGHT" => $arParams["IMAGE_SIZE"],
				"CONVERT" => "N",
				"FAMILY" => "FORUM",
				"SINGLE" => "Y",
				"RETURN" => "N",
				"SHOW_LINK" => "Y"),
			null,
			array("HIDE_ICONS" => "Y"));
	?></div><?
	endforeach;
		
	if (!empty($arResult["MESSAGE"]["EDITOR_NAME"])):
	?><div class="forum-message-edit">
		<div class="head"><span class="head"><?=GetMessage("F_EDIT_HEAD")?></span> <?
		?><span class="forum-user editor-name"><?
		if (!empty($arResult["MESSAGE"]["EDITOR_LINK"])):
			?><a href="<?=$arResult["MESSAGE"]["EDITOR_LINK"]?>"><?=$arResult["MESSAGE"]["EDITOR_NAME"]?></a><?
		else:
			?><?=$arResult["MESSAGE"]["EDITOR_NAME"]?><?
		endif;
		?></span> <span class="edit-date"><?=$arResult["MESSAGE"]["EDIT_DATE"]?></span></div><?
		?><div class="body"><?=$arResult["MESSAGE"]["EDIT_REASON"]?></div><?
	?></div><?
	endif;
	
	if(!function_exists("__ConverData"))
	{
		function __ConverData(&$item, $key)
		{
			static $search = array("&#92;");
			static $replace = array("&amp;#92;");
			if(is_array($item))
				array_walk($item, "__ConverData");
			else
			{
				$item = htmlspecialcharsEx($item);
				$item = str_replace($search, $replace, $item);
			}
		}
	}
	
	$post = ob_get_contents();
	ob_end_clean();
	$res = array(
		"id" => $arParams["MID"],
		"post" => $post);
	if ($_REQUEST["CONVERT_DATA"] == "Y")
		array_walk($res, "__ConverData");
		
	$GLOBALS["APPLICATION"]->RestartBuffer();
	?><?=CUtil::PhpToJSObject($res)?><?
	die();

endif;
?>