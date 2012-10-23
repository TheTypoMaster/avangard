<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if ($arResult["ALL"] <= 0)
	return 0;
if (!function_exists("endingTopicsPosts"))
{
	function endingTopicsPosts($mark, $count)
	{
		$text = "";
		$count = intVal($count%10);
		if ($count==1)
			$text = "_1";
		elseif (($count>1) && ($count<5))
			$text = "_2_4";
		return GetMessage("F_STAT_".strToUpper($mark).$text);
	}
}

	
?><table width="100%" class="forum-stat"><?

if (in_array("USERS_ONLINE", $arParams["SHOW"])):

if ($arParams["TID"] > 0)
	$text = GetMessage("F_NOW_TOPIC_READ");
else if ($arParams["FID"] > 0)
	$text = str_replace(
		array("#TIME_INTERVAL#", "#COUNT_USERS#"),
		array(intVal($arParams["PERIOD"] / 60), $arResult["ALL"]),
		GetMessage("F_NOW_FORUM_READ"));
else 
	$text = str_replace(
		array("#TIME_INTERVAL#", "#COUNT_USERS#"),
		array(intVal($arParams["PERIOD"] / 60), $arResult["ALL"]),
		GetMessage("F_NOW_ONLINE"));
?><tr><th colspan="2"><?=$text?></th></tr>
	<tr><td><div class="icon-users"></div></td>
		<td width="100%"><?=GetMessage("F_COUNT_GUEST")?>: <span class="forum-attention"><?=intVal($arResult["GUEST"])?></span>, 
	<?=GetMessage("F_COUNT_USER")?>: <span class="forum-attention"><?=intVal($arResult["REGISTER"])?></span>, 
	<?=GetMessage("F_FROM_THIS")?> <?=GetMessage("F_COUNT_USER_HIDEFROMONLINE")?>: 
	<span class="forum-attention"><?=count($arResult["USERS_HIDDEN"])?></span><br /><?
	$first = true;
	foreach ($arResult["USERS"] as $res)
	{
		?><?=((!$first) ? ", ": "")?><?
		?><a href="<?=$res["profile_view"]?>" title="<?=GetMessage("F_USER_PROFILE")?>"><?
			if(($arParams["WORD_WRAP_CUT"] > 0) && (strLen($res["~SHOW_NAME"])>$arParams["WORD_WRAP_CUT"]))
				$res["SHOW_NAME"] = htmlspecialcharsEx(subStr($res["~SHOW_NAME"], 0, $arParams["WORD_WRAP_CUT"]))."...";
			?><?=$res["SHOW_NAME"]?></a><?
		$first = false;
	}
	?></td></tr><?
endif;

if (in_array("BIRTHDAY", $arParams["SHOW"]) && !empty($arResult["USERS_BIRTHDAY"])):
	?><tr><th colspan="2"><?=GetMessage("F_TODAY_BIRTHDAY")?></th></tr>
	<tr>
		<td><div class="icon-birth"></div></td>
		<td><?
			$first = true;
			foreach ($arResult["USERS_BIRTHDAY"] as $res)
			{
				?><?=((!$first)? ", ":"")?><?
				?><a href="<?=$res["profile_view"]?>" title="<?=GetMessage("F_USER_PROFILE")?>"><?=$res["SHOW_NAME"]?></a> <?
					?>(<span class="forum-attention"><?=$res["AGE"]?></span>)<?
				$first = false;
			}
		?></td>
	</tr><?
endif;

if (in_array("STATISTIC", $arParams["SHOW"])):

if ($arParams["TID"] > 0)
	$text = GetMessage("F_NOW_TOPIC_READ");
else if ($arParams["FID"] > 0)
	$text = str_replace(
		array("#TIME_INTERVAL#", "#COUNT_USERS#"),
		array(intVal($arParams["PERIOD"] / 60), $arResult["ALL"]),
		GetMessage("F_NOW_FORUM_READ"));

?>
	<tr>
		<th colspan="2"><?=GetMessage("F_FORUM_STATISTIC")?></th>
	</tr>
	<tr>
		<td><div class="icon-stat"></div></td>
		<td><?
		
		if (empty($arParams["FID"])):
			?><?=str_replace(
				array(
					"#USERS#",
					"#USERS_ON_FORUM#", 
					"#USERS_ON_FORUM_ACTIVE#"),
				array(
					"<span class=\"forum-attention\">".$arResult["STATISTIC"]["USERS"]."</span> ".endingTopicsPosts("users", $arResult["STATISTIC"]["USERS"]), 
					"<span class=\"forum-attention\">".$arResult["STATISTIC"]["USERS_ON_FORUM"]."</span>",
					"<span class=\"forum-attention\">".$arResult["STATISTIC"]["USERS_ON_FORUM_ACTIVE"]."</span>"),
					GetMessage("F_STAT_TEXT"))?><br />
			<?=str_replace("#FORUMS#", "<span class=\"forum-attention\">".$arResult["STATISTIC"]["FORUMS"]."</span>", GetMessage("F_STAT_FORUMS"))?> <?
		else:?>
			<?=GetMessage("F_STAT_FORUM")?> <?
		endif;
		?>
			<span class="forum-attention"><?=$arResult["STATISTIC"]["TOPICS"]?></span> <?=endingTopicsPosts("topics", $arResult["STATISTIC"]["TOPICS"])?>, 
			<span class="forum-attention"><?=$arResult["STATISTIC"]["POSTS"]?></span> <?=endingTopicsPosts("posts", $arResult["STATISTIC"]["POSTS"])?></br>
		</td>
	</tr><?
endif;
?></table>