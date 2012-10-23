<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
// *****************************************************************************************
	IncludeAJAX();
	$arResult["GROUPS_SHOW"] = "N";
	/*if ($GLOBALS["USER"]->IsAuthorized())
	{
		require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".strToLower($GLOBALS["DB"]->type)."/favorites.php");
		$arResult["GROUPS_SHOWED"] = @unserialize(CUserOptions::GetOption("forum", "GroupHidden", ""));
		$arResult["GROUPS_SHOW"] = "Y";
	}
	else*/if (COption::GetOptionString("forum", "USE_COOKIE", "N") == "Y")
	{
		$sCookie = $_COOKIE[COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_FORUM_GROUP"];
		$arResult["GROUPS_SHOWED"] = explode("/", $sCookie);
		$arResult["GROUPS_SHOW"] = "Y";
	}
	$arResult["GROUPS_SHOWED"] = (is_array($arResult["GROUPS_SHOWED"]) ? $arResult["GROUPS_SHOWED"] : array());
// *****************************************************************************************
	if (strLen(trim($arResult["NAV_STRING"])) > 0)
	{
		?><div class="forum-navigation"><?=$arResult["NAV_STRING"]?></div><?
		?><div class="forum-br"></div><?
	}
	?><div class="forum-title"><a href="<?=$arResult["index"]?>"><?=GetMessage("F_INDEX")?></a></div>
	<div class="forum-br"></div><?
		
	?><table class="forum-main">
		<thead>
			<tr>
		<?if ($arResult["DrawAddColumn"] == "Y"):?>
				<th class="td-moderate"><div class="icon-attention" title="<?=GetMessage("F_MESSAGE_NOT_APPROVED")?>"></div></th>
		<?endif;?>
				<th class="td-status">&nbsp;</th>
				<th class="td-name"><?=GetMessage("F_FORUM_NAME")?></th>
				<th class="td-topics"><?=GetMessage("F_FORUM_TOPICS")?></th>
				<th class="td-posts"><?=GetMessage("F_FORUM_MESS")?></th>
				<th class="td-lm"><?=GetMessage("F_FORUM_LAST_MESS")?></th>
			</tr>
		</thead><?
		
	foreach ($arResult["FORUM"] as $arGroup)
	{
		if (is_array($arGroup["FORUM"]) && !empty($arGroup["FORUM"]))
		{
			if (intVal($arGroup["ID"]) > 0)
			{	/* Drawing group */
		?><tbody>
			<tr>
				<td class="forum-group" colspan="<?=(($arResult["DrawAddColumn"] == "Y") ? "6" : "5")?>"><?
				if ($arResult["GROUPS_SHOW"] == "Y"):
					?><span class="icon-switcher<?=(in_array($arGroup["ID"], $arResult["GROUPS_SHOWED"]) ? "-hidden" : "")?>" onclick="return SectionSH('<?=$arGroup["ID"]?>');" id="forum_switch_<?=$arGroup["ID"]?>" title="<?=GetMessage("F_SHOW_HIDE_GROUP")?>"></span><?
				endif;
				
				?><div class="forum-group-name"><?=$arGroup["~NAME"];?></div><?
				if (strLen($arGroup["~DESCRIPTION"])>0):?>
					<div class="forum-group-description"><?=$arGroup["~DESCRIPTION"];?></div><?
				endif;?>
				</td>
			</tr>
		</tbody>
		<tbody id="forum_group_<?=$arGroup["ID"]?>" <?=(in_array($arGroup["ID"], $arResult["GROUPS_SHOWED"]) ? " style=\"display:none;\"" : "")?>><?
			}
			else 
			{
		?><tbody><?
			}
			
			foreach ($arGroup["FORUM"] as $res)
			{
			?><tr><?
				if ($arResult["DrawAddColumn"] == "Y")
				{
					?><td class="td-moderate"><?
					if (intVal($res["mCnt"]) > 0)
					{
						?><a href="<?=$res["message_appr"]?>" title="<?=GetMessage("F_MESSAGE_NOT_APPROVED")?> (<?=$res["mCnt"]?>)"><div class="icon-attention"></div></a><?
					}
					?></td><?
				}
					?><td class="td-status"><?
						if ($res["ACTIVE"] != "Y")
						{
							?><div class="icon-na" title="N/A"></div><?
						}
						elseif ($res["NewMessage"] == "Y")
						{
							?><div class="icon-new-message" title="<?=GetMessage("F_HAVE_NEW_MESS")?>" onclick="location.href='<?=$res["topic_list"]?>'; return false"></div><?
						}
						else
						{
							?><div class="icon-no-message" title="<?=GetMessage("F_NO_NEW_MESS")?>"></div><?
						}
					?></td>
					<td class="td-name">
						<a href="<?=$res["topic_list"]?>"><?=$res["~NAME"];?></a>
						<?if (($res["NewMessage"] == "Y") && (strLen(trim($arParams["TMPLT_SHOW_ADDITIONAL_MARKER"])) > 0)):?>
							<a href="<?=$res["topic_list"]?>" class="forum-attention"><?=$arParams["TMPLT_SHOW_ADDITIONAL_MARKER"]?></a>
						<?endif;?>
						<br />
						<?=$res["~DESCRIPTION"]?>
					</td>
					<td class="td-topics"><?=$res["TOPICS"]?></td>
					<td class="td-posts"><?=$res["POSTS"]?></td>
					<td class="td-lm">
						<?if (strLen($res["LAST_POST_DATE"])>0):?>
							<a href="<?=$res["message_list"]?>"><?=$res["LAST_POST_DATE"]?></a>
							<?if (strLen($res["TITLE"])>0):
								if ($arParams["WORD_WRAP_CUT"] > 0)
									$res["TITLE"] = ((strLen($res["~TITLE"])>$arParams["WORD_WRAP_CUT"]) ? htmlspecialcharsEx(substr($res["~TITLE"], 0, $arParams["WORD_WRAP_CUT"]))."..." : $res["TITLE"]);
							?>
							<div class="f-topic"><?=GetMessage("F_TOPIC")?>&nbsp;<a href="<?=$res["message_list"]?>"><?=$res["TITLE"]?></a></div>
							<?endif;?>
							<?if (strLen($res["LAST_POSTER_NAME"])>0):
								if ($arParams["WORD_WRAP_CUT"] > 0)
									$res["LAST_POSTER_NAME"] = ((strLen($res["~LAST_POSTER_NAME"])>$arParams["WORD_WRAP_CUT"]) ? htmlspecialcharsEx(substr($res["~LAST_POSTER_NAME"], 0, $arParams["WORD_WRAP_CUT"]))."..." : $res["LAST_POSTER_NAME"]);?>
							<?=GetMessage("F_AUTHOR")?>
								<?if (intVal($res["LAST_POSTER_ID"])>0):?>
									<a href="<?=$res["profile_view"]?>"><?=$res["LAST_POSTER_NAME"]?></a>
								<?else:?>
									<?=$res["LAST_POSTER_NAME"]?>
								<?endif;?>
							<?endif;?>
						<?endif;?>
					</td>
				</tr><?
				}
			?></tbody><?
		}
	}
?></table><?

if (!empty($arResult["NAV_STRING"]))
{
	?><div class="forum-br"></div><?
	?><div class="forum-navigation"><?=$arResult["NAV_STRING"]?></div><?
}
if ($arResult["GROUPS_SHOW"] == "Y"):
/*	if ($GLOBALS["USER"]->IsAuthorized()):
?><script language="JavaScript">
function SectionSH(id)
{
	var section = document.getElementById('forum_group_'+id);
	var switcher = document.getElementById('forum_switch_'+id);
	if (typeof section != "object" || typeof switcher != "object")
		return false;
	var TID = CPHttpRequest.InitThread();
	CPHttpRequest.Send(TID, '/bitrix/components/bitrix/forum.index/user_settings.php', {"group" : id});
	if (section.style.display != 'none')
	{
		section.style.display = 'none';
		switcher.className = switcher.className.replace(/\-hidden/gi, "") + '-hidden';
	}
	else
	{
		section.style.display = '';
		switcher.className = switcher.className.replace(/\-hidden/gi, "");
	}
	return;
}
</script><?
	else:
*/	
?><script>
function SectionSH(id)
{
	var section = document.getElementById('forum_group_'+id);
	var switcher = document.getElementById('forum_switch_'+id);
	var c = {
		'name' : '<?=COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_FORUM_GROUP"?>',
		'start' : 0, 'end' : 0,
		'data' : ''};
	var bFined = false;
	
	
	var arCookie = new Array();
	var arCookieForSave = new Array();
	
	if (document.cookie.length>0)
	{
		c['start'] = document.cookie.indexOf(c['name'] + "=");
		if (c['start'] != -1)
		{
			c['start'] = c['start'] + c['name'].length + 1;
			c['end'] = document.cookie.indexOf(";", c['start']);
			if (c['end'] == -1) 
				c['end'] = document.cookie.length;
			c['data'] = unescape(document.cookie.substring(c['start'], c['end']));
		}
	}
	if (c['data'].length > 0)
		arCookie = c['data'].split('/');

	if (section.style.display != 'none')
	{
		section.style.display = 'none';
		switcher.className = switcher.className.replace(/\-hidden/gi, "") + '-hidden';
		arCookieForSave = arCookie;
		arCookieForSave.push(id);
	}
	else
	{
		section.style.display = '';
		switcher.className = switcher.className.replace(/\-hidden/gi, "");
		for (var ii = 0; ii < arCookie.length; ii++)
		{
			if (arCookie[ii] == id)
				continue;
			arCookieForSave.push(arCookie[ii]);
		}
	}

	
	if (arCookieForSave.length > 0)
		document.cookie = c['name']+'=' + arCookieForSave.join('/')+'; expires=Thu, 31 Dec 2030 23:59:59 GMT; path=/;';
	else
		document.cookie = c['name']+"=Y; expires=Sun, 31 Dec 2000 23:59:59 GMT; path=/;";
		

	return false;
}
</script><?

/*	endif;*/
endif;
?>