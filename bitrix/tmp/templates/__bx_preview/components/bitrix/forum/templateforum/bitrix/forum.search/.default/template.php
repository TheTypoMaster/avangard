<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// *****************************************************************************************
	$filter_value_fid = array(
		"0" => GetMessage("F_ALL_FORUMS"));
	$filter_value_fid_active = "";
	if (is_array($arResult["FORUMS"])):
		foreach ($arResult["FORUMS"] as $res):
			$filter_value_fid[$res["ID"]] = $res["~NAME"];
		endforeach;
	endif;
	
	$APPLICATION->IncludeComponent("bitrix:forum.interface", "filter", 
		array(
			"HEADER" => array(
				"TITLE" => GetMessage("F_TITLE")),
			"FIELDS" => array(
				array(
					"NAME" => "PAGE_NAME",
					"TYPE" => "HIDDEN",
					"VALUE" => "search"),
				array(
					"NAME" => "order",
					"TYPE" => "HIDDEN",
					"VALUE" => $_REQUEST["order"]),
				array(
					"TITLE" => GetMessage("F_FORUM"),
					"NAME" => "FORUM_ID",
					"TYPE" => "SELECT",
					"VALUE" => $filter_value_fid,
					"ACTIVE" => $_REQUEST["FORUM_ID"]),
				array(
					"TITLE" => GetMessage("F_KEYWORDS"),
					"NAME" => "q",
					"TYPE" => "TEXT",
					"VALUE" => $_REQUEST["q"])),
			"BUTTONS" => array(
				array(
					"NAME" => "s",
					"VALUE" => GetMessage("F_DO_SEARCH")))),
			$component,
			array(
				"HIDE_ICONS" => "Y"));?><?
	?><br /><?

if ($arResult["SHOW_RESULT"] == "Y"):
	if ($arResult["ERROR_MESSAGE"] != "")
	{
		?><table class="forum-main"><tr><td><?
			ShowError($arResult["ERROR_MESSAGE"])?>
			<?=GetMessage("F_PHRASE_ERROR_CORRECT")?><br />
			<?=GetMessage("F_PHRASE_ERROR_SYNTAX")?><br />
			<?=GetMessage("F_SEARCH_DESCR")?><?
		?></td></tr></table><?
	}
	else
	{
		if ($arResult["EMPTY"] != "Y")
		{
			?><div class="forum-search-sorting"><?=GetMessage("F_SORT")?><?
			if ($arResult["order"]["active"] != "relevance"):
				?><a href="<?=$arResult["order"]["relevance"]?>"><?=GetMessage("F_RELEVANCE")?></a><?
			else:
				?><b><?=GetMessage("F_RELEVANCE")?></b><?
			endif;
			?> | <?
			if ($arResult["order"]["active"] != "date"):
				?><a href="<?=$arResult["order"]["date"]?>"><?=GetMessage("F_DATE")?></a><?
			else:
				?><b><?=GetMessage("F_DATE")?></b><?
			endif;
			?> | <?
			if ($arResult["order"]["active"] != "topic"):?>
				<a href="<?=$arResult["order"]["topic"]?>"><?=GetMessage("F_TOPIC")?></a><?
			else:
				?><b><?=GetMessage("F_TOPIC")?></b><?
			endif;
			?></div><?
			foreach ($arResult["TOPICS"] as $res)
			{
				?><div class="forum-search">
					<a href="<?=$res["URL"]?>"><?=$res["TITLE_FORMATED"] ?></a>
					<?if (!empty($res["BODY_FORMATED"])):?>
					<div class="forum-search-body"><?=$res["BODY_FORMATED"]?></div>
					<?endif;?>
					<div class="forum-search-date"><?=GetMessage("F_CHANGE")?> <?=$res["DATE_CHANGE"]?></div>
				</div><?
			}
			if (!empty($arResult["NAV_STRING"])):
			?><div class="forum-br"></div><?
			?><?=$arResult["NAV_STRING"]?><br /><?
			endif;
			?><div class="forum-search-sorting"><?=GetMessage("F_SORT")?> <?
			if ($arResult["order"]["active"] != "relevance"):
				?><a href="<?=$arResult["order"]["relevance"]?>"><?=GetMessage("F_RELEVANCE")?></a><?
			else:
				?><b><?=GetMessage("F_RELEVANCE")?></b><?
			endif;
			?> | <?
			if ($arResult["order"]["active"] != "date"):
				?><a href="<?=$arResult["order"]["date"]?>"><?=GetMessage("F_DATE")?></a><?
			else:
				?><b><?=GetMessage("F_DATE")?></b><?
			endif;
			?> | <?
			if ($res != $arResult["order"]["active"]):?>
				<a href="<?=$arResult["order"]["topic"]?>"><?=GetMessage("F_TOPIC")?></a><?
			else:
				?><b><?=GetMessage("F_TOPIC")?></b><?
			endif;
			?></div><?
		}
		else
		{
		?><table class="forum-main"><tr><td><?
			?><?=ShowNote(GetMessage("F_EMPTY"))?><?
		?></td></tr></table><?
		}
	}
endif;?>