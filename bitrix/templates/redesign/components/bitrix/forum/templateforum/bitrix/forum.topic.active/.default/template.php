<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// *****************************************************************************************
	$arParams["PATH_TO_ICON"] = (empty($arParams["PATH_TO_ICON"]) ? $templateFolder."/images/icon" : $arParams["PATH_TO_ICON"]);
	$arParams["PATH_TO_ICON"] = str_replace("//", "/", $arParams["PATH_TO_ICON"]."/");
		
	// For filter only
	$filter_value_fid = array(
		"0" => GetMessage("FL_ALL_FORUMS"));
	$filter_value_fid_active = "";
	if (is_array($arResult["find_forum"]["data"])):
		foreach ($arResult["find_forum"]["data"] as $value => $option):
			$filter_value_fid[$value] = $option["NAME"];
		endforeach;
	endif;
	
	$arFilter = array("prefix" => "forum_filter");
	$arFilter["params"] = array(
		array(
			"id" => "name",
			"title" => GetMessage("FL_FILTER_FORUM"),
			"body" => $select),
		array(
			"id" => "date_lu",
			"title" => GetMessage("FL_FILTER_LAST_MESSAGE_DATE"),
			"body" => $arResult["find_date1"],
		));
	
	$APPLICATION->IncludeComponent("bitrix:forum.interface", "filter", 
		array(
			"HEADER" => array(
				"TITLE" => GetMessage("F_TITLE")),
			"FIELDS" => array(
				array(
					"NAME" => "PAGE_NAME",
					"TYPE" => "HIDDEN",
					"VALUE" => "active"),
				array(
					"TITLE" => GetMessage("FL_FILTER_FORUM"),
					"NAME" => "find_forum",
					"TYPE" => "SELECT",
					"VALUE" => $filter_value_fid,
					"ACTIVE" => $_REQUEST["find_forum"]),
				array(
					"TITLE" => GetMessage("FL_FILTER_LAST_MESSAGE_DATE"),
					"NAME" => "find_date1",
					"NAME_TO" => "find_date2",
					"TYPE" => "PERIOD",
					"VALUE" => $_REQUEST["find_date1"],
					"VALUE_TO" => $_REQUEST["find_date2"])
			)),
			$component,
			array(
				"HIDE_ICONS" => "Y"));?><?
	?><br /><?
	
// *****************************************************************************************
	?><?ShowError($arResult["ERROR_MESSAGE"])?>
	<br />
	<?if($arResult["SHOW_RESULT"] == "Y"):?>
	<table class="forum-main">
		<tr>
			<th class="td-status"></th>
			<th class="td-icon"></th>
			<th class="td-topic-name"><?=GetMessage("FL_TOPIC_NAME")?><br/></th>
			<th class="td-topic-author"><?=GetMessage("FL_TOPIC_AUTHOR")?><br/></th>
			<th class="td-forum"><?=GetMessage("FL_FORUM_PREF")?><br/><?=$arResult["SortingEx"]["FORUM_ID"]?></th>
			<th class="td-posts"><?=GetMessage("FL_TOPIC_POSTS")?><br/><?=$arResult["SortingEx"]["POSTS"]?></th>
			<th class="td-views"><?=GetMessage("FL_TOPIC_VIEWS")?><br/><?=$arResult["SortingEx"]["VIEWS"]?></th>
			<th class="td-lm"><?=GetMessage("FL_TOPIC_LAST_MESS")?><br/><?=$arResult["SortingEx"]["LAST_POST_DATE"]?></th>
		</tr>
		<?
		foreach ($arResult["TOPICS"] as $arTopic):
		?>
		<tr>
			<td class="td-status">
				<?if ($arTopic["APPROVED"]!="Y" && $arTopic["UserPermission"]>="Q"):?>
					<div class="icon-na" title="N/A"></div>
				<?else:?>
					<div class="icon<?=($arTopic["STATE"]!="Y") ? "-closed" : ""?>-new-message" title="<?=GetMessage("FL_HAVE_NEW_MESS")?>" onclick="location.href='<?=$arTopic["read_unread"]?>'; return false"></div>
				<?endif;?>
			</td>
			<td class="td-icon">
				<?if (strlen($arTopic["IMAGE"])>0):
					?><img src="<?=$arParams["PATH_TO_ICON"].$arTopic["IMAGE"]?>" alt="<?=$arTopic["IMAGE_DESCR"]?>" border="0" width="15" height="15"/> <?
				else:
					?>&nbsp;<?
				endif;
				?>
			</td>
			<td class="td-topic-name"><a href="<?=$arTopic["read"]?>" title="<?=GetMessage("FL_TOPIC_START")?> <?=$arTopic["START_DATE"]?>"> <?=$arTopic["TITLE"]?></a>
				<?if (strLen(trim($arParams["TMPLT_SHOW_ADDITIONAL_MARKER"]))):?>
					<a href="<?=$arTopic["read"]?>" class="forum-attention"><?=$arParams["TMPLT_SHOW_ADDITIONAL_MARKER"]?></a>
				<?endif;?>
			<br />
				<?if (strLen($arTopic["DESCRIPTION"]) > 0 ):?>
					<?=$arTopic["DESCRIPTION"]?>
				<?endif;
				if (strLen($arTopic["ForumShowTopicPages"]) > 0 ):?>
					<?=$arTopic["ForumShowTopicPages"]?>
				<?endif;?>
			<td class="td-topic-author">
			<div class="f-author">
			<?if (intVal($arTopic["USER_START_ID"]) > 0):?>
			<a href="<?=$arTopic["USER_START_HREF"]?>"><?=$arTopic["USER_START_NAME"]?></a>
			<?else: ?>
			<?=$arTopic["USER_START_NAME"]?>
			<?endif;?>
			</div>
			</td>
			<td class="td-forum"><a href="<?=$arTopic["list"]?>"><?=$arResult["FORUMS"][$arTopic["FORUM_ID"]]["NAME"]?></a></td>
			<td class="td-posts"><?=$arTopic["POSTS"]?></td>
			<td class="td-views"><?=$arTopic["VIEWS"]?></td>
			<td class="td-lm">
			<a href="<?=$arTopic["read_last_message"]?>"><?=$arTopic["LAST_POST_DATE"]?></a>
			<div class="f-author">
				<?=GetMessage("FL_AUTHOR");?> 
				<?if (intVal($arTopic["LAST_POSTER_ID"]) > 0):?>
				<a href="<?=$arTopic["LAST_POSTER_HREF"]?>"><?=$arTopic["LAST_POSTER_NAME"]?></a>
				<?else: ?>
				<?=$arTopic["LAST_POSTER_NAME"]?>
				<?endif;?>
			</div>
			</td>
		</tr>
		<?endforeach;?>
	</table>
		<?if (!empty($arResult["NAV_STRING"])):?>
		<div class="forum-br"></div>
		<?=$arResult["NAV_STRING"]?>
		<?endif;?>
	<?endif;?>
