<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
// ************************* Input params***************************************************************
$arParams["AJAX_CALL"] = ($_REQUEST["AJAX_CALL"] == "Y" ? "Y" : "N");
// ************************* Input params***************************************************************
if ($arParams["AJAX_CALL"] == "Y"):
	if ($arResult["TOPIC"] == "L")
	{
		?><?=CUtil::PhpToJSObject(array(
			"TOPIC_ID" => $arResult["TID"],
			"TOPIC_TITLE" => '&laquo;<a href="'.$arResult["TOPIC"]["LINK"].'">'.htmlspecialChars($arResult["TOPIC"]["~TITLE"]).
				'</a>&raquo; ( '.GetMessage("FMM_ON_FORUM").': <a href="'.$arResult["FORUM"]["LINK"].'">'.$arResult["FORUM"]["NAME"].'</a>)'));
		?><?
		
	}
	elseif (!empty($arResult["TOPIC"]))
	{
		?><?=CUtil::PhpToJSObject(array(
			"TOPIC_ID" => $arResult["TID"],
			"TOPIC_TITLE" => '&laquo;<a href="'.$arResult["TOPIC"]["LINK"].'">'.htmlspecialChars($arResult["TOPIC"]["~TITLE"]).
				'</a>&raquo; ( '.GetMessage("FMM_ON_FORUM").': <a href="'.$arResult["FORUM"]["LINK"].'">'.$arResult["FORUM"]["NAME"].'</a>)'));
		?><?
	}
	die();
endif;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta  http-equiv="Content-Type" content="text/html; charset=<?=$arResult["SITE_CHARSET"]?>">
		<title><?=GetMessage("FMM_SEARCH_TITLE")?></title>
	</head>
<style type=text/css>
	td.tableborder, table.tableborder {background-color:#8FB0D2;}
	table.tablehead, td.tablehead {background-color:#F1F5FA;}
	table.tablebody, td.tablebody {background-color:#FFFFFF;}
	.tableheadtext, .tablebodylink {font-family: Verdana,Arial,Hevetica,sans-serif; font-size:12px;}
	.tableheadtext {color:#456A74}
	H1, H2, H3, H4 {font-family: Verdana, Arial, Helvetica, sans-serif; color:#3A84C4; font-size:13px; font-weight:bold; line-height: 16px; margin-bottom: 1px;}
	input.inputradio, input.inputfile, input.inputbutton, input.inputbodybutton {font-family:Verdana,Arial,Helvetica; font-size:11px;}
	.errortext, .oktext, .notetext {font-family:Verdana,Arial,Hevetica,sans-serif; font-size:13px; font-weight:bold;}
	.errortext {color:red;}
</style>
	<body>
<?if ($arResult["SELF_CLOSE"] == "Y"):
	if (!empty($arResult["TOPIC"])):
	?>
	<script type="text/javascript">
		opener.document.MESSAGES['newTID'].value = '<?=$arResult["TID"]?>';
		opener.document.getElementById('TOPIC_INFO').innerHTML = '<?=CUtil::JSEscape(
			'&laquo;<a href="'.$arResult["TOPIC"]["LINK"].'">'.htmlspecialChars($arResult["TOPIC"]["~TITLE"]).
			'</a>&raquo; ( '.GetMessage("FMM_ON_FORUM").': <a href="'.$arResult["FORUM"]["LINK"].'">'.$arResult["FORUM"]["NAME"].'</a>)')?>';
		self.close();
	</script>
<?
	endif;
else:?>

<form action="<?=$APPLICATION->GetCurPageParam()?>" method="GET">
<input type=hidden name="PAGE_NAME" value="topic_search"/>
<?=$arResult["sessid"]?>
	<h1><?=GetMessage("FMM_SEARCH_TITLE")?></h1>
	<table border=0 cellspacing=1 cellpadding=3 class=tableborder>
		<tr><td class=tablebody valign=top align=center colspan=3>
			<font class=tableheadtext><?=GetMessage("FMM_SEARCH_TITLE")?></font></td></tr>
	<tr>
		<td class=tablehead valign=top align=right nowrap>
		<font class=tableheadtext>
		<b><?=GetMessage("FMM_SEARCH_IN_FORUM")?></b></font></td>
		<td class=tablebody colspan=2>
		<select name="FID">
			<option value=""><?=GetMessage("FMM_FORUM_ALL")?></option>
		<?foreach ($arResult["FORUM"]["data"] as $res):?>
			<option value="<?=$res["ID"]?>" <?=(($res["ID"] == $arResult["FORUM"]["active"]) ? " selected": "")?>><?=$res["NAME"]?></option>
		<?endforeach;?>
		</select>
		</td>
	</tr>
	<tr>
		<td class=tablehead valign=top align=right nowrap>
		<font class=tableheadtext>
		<b><?=GetMessage("FMM_SEARCH_INSERT")?></b></font></td>
		<td class=tablebody colspan=2 nowrap>
			<input type="text" name="search_template" value="<?=$_REQUEST["search_template"]?>" style="width:180px;"/>
			<select name="search_field">
				<option value="">&nbsp;</option>
				<option value="title"><?=GetMessage("FMM_TITLE")?></option>
				<option value="description"><?=GetMessage("FMM_DESCRIPTION")?></option></select></td>
	</tr>
	</table>
<br/>
<table border=0 width=100%>
<tr><td align="right"><input type=hidden value="Y" name="do_search"><input type=submit value="<?=GetMessage("FMM_SEARCH")?>" name="do_search" /></td><td align="left"><input type=button value="<?=GetMessage("FMM_CANCEL")?>" onclick="self.close()"/></td></tr>
</table><br/>
</form>

	<?if ($arResult["SHOW_RESULT"] == "Y"):?>
		<table border=0 cellspacing=1 cellpadding=3 class=tableborder width='100%'>
			<tr><td class=tablehead><font class=tableheadtext><?=$arResult["NAV_STRING"]?></td></tr>
			<tr><td class=tablebody><ul><?
				foreach ($arResult["TOPIC"] as $res)
				{
					?><li>
						<a class='tableheadtext' href="<?=$res["topic_id_search"]?>"><?=$res["TITLE"]?></a><?
					if (strLen(trim($res["DESCRIPTION"])) > 0)
					{
						?>, <?=$res["DESCRIPTION"]?><?
					}
		
				}
				?></ul></td></tr>
			<tr><td class=tablehead><font class=tableheadtext><?=$arResult["NAV_STRING"]?></font></td></tr>
		</table>
	<?endif;?>
<?endif;?>
</body>
</html>