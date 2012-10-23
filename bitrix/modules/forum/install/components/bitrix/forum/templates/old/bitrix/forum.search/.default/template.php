<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/********************************************************************
				Input params
********************************************************************/
/***************** BASE ********************************************/
$arParams["SHOW_TAGS"] = ($arParams["SHOW_TAGS"] == "N" ? "N" : "Y");
$res =  $_COOKIE[COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_FORUM_INFO"];
$arResult["USER"] = array(
	"SHOW_FILTER" => (strpos($res, "searchf=Y") !== false ? "Y" : "N")); 
if ($arResult["USER"]["SHOW_FILTER"] == "N")
{
	$arResult["USER"]["SHOW_FILTER"] = (!empty($_REQUEST["FORUM_ID"]) || !empty($_REQUEST["DATE_CHANGE"]) || 
		$_REQUEST["order"] != "relevance" ? "Y" : "N");
}
/********************************************************************
				/Input params
********************************************************************/
	$filter_value_fid = array(
		"0" => GetMessage("F_ALL_FORUMS"));
if (is_array($arResult["GROUPS_FORUMS"])):
	foreach ($arResult["GROUPS_FORUMS"] as $key => $res):
		if ($res["TYPE"] == "GROUP"):
			$filter_value_fid["GROUP_".$res["ID"]] = array(
				"NAME" => ($res["DEPTH"] > 0 ? str_pad("", ($res["DEPTH"] - 1)*4, " ") : "").$res["~NAME"], 
				"CLASS" => "forums level".$res["DEPTH"], 
				"TYPE" => "OPTGROUP");
		else:
			$filter_value_fid[$res["ID"]] = array(
				"NAME" => ($res["DEPTH"] > 0 ? str_pad("", ($res["DEPTH"] + 1)*4, " ") : "").$res["~NAME"], 
				"CLASS" => "forums level".$res["DEPTH"], 
				"TYPE" => "OPTION");
		endif;
	endforeach;
endif;
?><div class="forum-search-page"><?
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
					"TITLE" => GetMessage("F_KEYWORDS"),
					"NAME" => "q",
					"TYPE" => "TEXT",
					"VALUE" => $_REQUEST["q"]),
				array(
					"TITLE" => GetMessage("F_FORUM"),
					"NAME" => "FORUM_ID",
					"TYPE" => "SELECT",
					"MULTIPLE" => "Y", 
					"VALUE" => $filter_value_fid,
					"ACTIVE" => $_REQUEST["FORUM_ID"]),
				array(
					"TITLE" => GetMessage("F_INTERVAL"),
					"NAME" => "DATE_CHANGE",
					"TYPE" => "SELECT",
					"VALUE" => 	array("0" => GetMessage("F_INTERVAL_ALL"), "1" => GetMessage("F_INTERVAL_TODAY"), "7" => "7 ".GetMessage("F_INTERVAL_DAYS"), 
						"30" => "7 ".GetMessage("F_INTERVAL_DAYS"), "60" => "60 ".GetMessage("F_INTERVAL_DAYS"), "90" => "90 ".GetMessage("F_INTERVAL_DAYS"), 
						"180" => "180 ".GetMessage("F_INTERVAL_DAYS"), "365" => "365 ".GetMessage("F_INTERVAL_DAYS")), 
					"ACTIVE" => $_REQUEST["DATE_CHANGE"]),
				array(
					"TITLE" => GetMessage("F_SORT"),
					"NAME" => "order",
					"TYPE" => "SELECT",
					"VALUE" => 	array("relevance" => GetMessage("F_RELEVANCE"), "date" => GetMessage("F_DATE"), "topic" => GetMessage("F_TOPIC")), 
					"ACTIVE" => $_REQUEST["order"])),
			"BUTTONS" => array(
				array(
					"NAME" => "s",
					"VALUE" => GetMessage("F_DO_SEARCH")))),
			$component,
			array(
				"HIDE_ICONS" => "Y"));?><?
?></div><?
if ($_GET["show_help"] == "Y"):
?>
	<table class="forum-main"><tr><td><?=GetMessage("F_PHRASE_ERROR_CORRECT")?><br />
			<?=GetMessage("F_PHRASE_ERROR_SYNTAX")?><br /><?=GetMessage("F_SEARCH_DESCR")?>
	</td></tr></table>
<?
elseif ($arResult["ERROR_MESSAGE"] != ""):
?>
	<table class="forum-main"><tr><td><?ShowError($arResult["ERROR_MESSAGE"])?><?=GetMessage("F_PHRASE_ERROR_CORRECT")?><br />
			<?=GetMessage("F_PHRASE_ERROR_SYNTAX")?><br /><?=GetMessage("F_SEARCH_DESCR")?>
	</td></tr></table>
<?
elseif ($arResult["EMPTY"] == "Y"):
?>
	<table class="forum-main"><tr><td><?=ShowNote(GetMessage("F_EMPTY"))?></td></tr></table>
<?
elseif ($arResult["SHOW_RESULT"] != "N"):
	foreach ($arResult["TOPICS"] as $res)
	{
?>
	<div class="forum-search ">
		<a href="<?=$res["URL"]?>" class="forum-name"><?=$res["TITLE_FORMATED"]?></a>
		<div class="forum-text"><?=$res["BODY_FORMATED"]?></div>

<?
		if (!empty($res["TAGS"]))
		{
?>		<div class="forum-tags"><?=GetMessage("F_TAGS")?>: <?
			$first = true;
			foreach ($res["TAGS"] as $tags):
				if (!$first)
				{
					?>, <?
				}
				?><a href="<?=$tags["URL"]?>"><?=$tags["TAG_NAME"]?></a><?
				$first = false;
			endforeach;
		?></div><?
		}
		?><div class="forum-date"><?=GetMessage("F_CHANGE")?> <?=$res["DATE_CHANGE"]?></div>
<?
	if ($res["~URL"] != $res["SITE_URL"]):
?>
		<?=str_replace(array("#MESSAGE_URL#", "#SITE_URL#"), 
			array($res["URL"], $res["SITE_URL"]), GetMessage("F_DIFF_URLS"))?><br />
<?
	endif;
?>
	</div>
<?
	}
	
	if (!empty($arResult["NAV_STRING"])):
?>
	<div class="forum-br"></div>
	<div class="forum-navigation"><?=$arResult["NAV_STRING"]?></div>
<?
	endif;
endif;
?>
<script>
function ShowFilter(oObj)
{
	if (typeof(oObj) != "object" || oObj == null)
		return false;
	var c = {'name' : '<?=COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_FORUM_INFO"?>',
		'start' : 0, 'end' : 0, 'data' : ''};
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
	var arRes = new Array();
	if (c['data'].length > 0)
		arRes = c['data'].split('/');
	for (var ii = 0; ii < arRes.length; ii++)
	{
		var res = arRes[ii].split("=");
		arCookie[res[0]] = res[1];
	}
	arCookie["searchf"] = (oObj.className == 'filter-opened' ? false : "Y");
	for (var ii in arCookie)
	{
		if (arCookie[ii] != false)
			arCookieForSave.push(ii + '=' + arCookie[ii]);
	}
	
	if (arCookieForSave.length > 0)
		document.cookie = c['name']+'=' + arCookieForSave.join('/')+'; expires=Thu, 31 Dec 2030 23:59:59 GMT; path=/;';
	else
		document.cookie = c['name']+"=Y; expires=Sun, 31 Dec 2000 23:59:59 GMT; path=/;";
		
	document.getElementById('forum-filter-container').style.display=(oObj.className=='filter-opened'?'none':'block');
	oObj.className=(oObj.className=='filter-opened'?'filter-closed':'filter-opened');
	return false;	
}

function ShowHelp()
{
	var sizer = false;
	
	var src = "";
	sizer = window.open("",'',"height=600,width=800,top=0,left=0");
	var text = '<HTML><BODY><table class="forum-main"><tr><td>' + 
		'<?=CUtil::JSEscape(GetMessage("F_PHRASE_ERROR_SYNTAX"))?><br />' + 
		'<?=CUtil::JSEscape(GetMessage("F_SEARCH_DESCR"))?></td></tr></table></BODY></HTML>';
	sizer.document.write(text);
	return false;
}
</script>