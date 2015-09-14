<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html>
	<head>
		<meta  http-equiv="Content-Type" content="text/html; charset='<?=$arResult["SITE_CHARSET"]?>'">
		<title><?=GetMessage("PM_TITLE")?></title>
		<style type=text/css>
			table.tableborder {border:none; border-collapse:collapse;}
			table.tableborder td, table.tableborder th, table.tableborder td a {font-family: Verdana,Arial,Hevetica,Sans-Serif; font-size:12px; padding:4px;}
			table.tableborder td, table.tableborder th {color:#456A74;}
			table.tableborder th {border:1px solid #8FB0D2; padding:6px; background-color:#F1F5FA;}
			table.tableborder td.nav_string, table.tableborder td.nav_string a {color:#456A74; padding:0px;}
			H1{font-family: Verdana,Arial,Helvetica,Sans-Serif; color:#3A84C4; font-size:13px; font-weight:bold;}
		</style>
	</head>
	
	<?if ($arResult["SHOW_SELF_CLOSE"] == "Y"):?>
	<script type="text/javascript">
		<?if ($arResult["SHOW_MODE"] == "none"):?>
			window.parent.document.getElementById("div_USER_ID").innerHTML='<i><?=GetMessage("PM_NOT_FINED");?></i>';
		<?elseif ($arResult["SHOW_MODE"] == "light"):?>
			window.parent.document.getElementById("div_USER_ID").innerHTML='<?=GetMessage("PM_IS_FINED");?>';
		<?elseif ($arResult["SHOW_MODE"] == "full"):?>
			window.parent.document.getElementById("div_USER_ID").innerHTML='<?=Cutil::JSEscape("[<a href=\"".$arResult["profile_view"]."\">".$arResult["SHOW_NAME"]."</a>]")?>';
		<?else:?>
		opener.switcher='<?=$arResult["SHOW_NAME"]?>';
		var handler = opener.document.getElementById('USER_ID');
		if (handler)
			handler.value = '<?=$arResult["UID"]?>';
		handler = opener.document.getElementById('div_USER_ID');
		if (handler)
			handler.innerHTML = '[<a href="<?=$arResult["profile_view"]?>"><?=$arResult["SHOW_NAME"]?></a>]';
		handler = opener.document.getElementById('input_USER_ID');
		if (handler)
			handler.value = '<?=$arResult["SHOW_NAME"]?>';
		<?endif;?>
		self.close();
	</script>
	<?
	die();
	endif;?>
		<form action="<?=$APPLICATION->GetCurPageParam("", array(BX_AJAX_PARAM_ID))?>" method=GET>
			<input type="hidden" name="PAGE_NAME" value="pm_search">
			<?=$arResult["sessid"]?>
			<h1><?=GetMessage("PM_SEARCH_USER")?></h1>
			<table class="tableborder">
				<?/*?><tr><td><?=GetMessage("PM_SEARCH_PATTERN")?></td></tr><?*/?>
				<tr><th nowrap="nowrap">
						<?=GetMessage("PM_SEARCH_INSERT")?>:
						<input type=text name="search_template" value="<?=$arResult["search_template"]?>"></th>
				</tr>
				<tr><td nowrap="nowrap" align="center">
					<input type=hidden value="Y" name="do_search"><input type=submit value="<?=GetMessage("PM_SEARCH")?>" name=do_search class=inputbutton>
					<input type=button value="<?=GetMessage("PM_CANCEL")?>" onclick='self.close();' class=inputbutton></td></tr>
			</table>
			
			
			<?if ($arResult["SHOW_SEARCH_RESULT"] == "Y"):?>
			<table class="tableborder">
			<?if (strLen($arResult["NAV_STRING"]) > 0):?>
				<tr><td class="nav_string"><?=$arResult["NAV_STRING"]?></td></tr>
			<?endif;?>
				<tr><td>
					<?if (!empty($arResult["SEARCH_RESULT"]))
					{
						foreach ($arResult["SEARCH_RESULT"] as $key => $res)
						{
							?><a href="<?=$res["link"]?>"><?=$res["SHOW_ABC"]?></a><br /><?
						}
					}
					else 
					{
						?><?=GetMessage("PM_SEARCH_NOTHING")?><?
					}
				?>
			<?if (strLen($arResult["NAV_STRING"]) > 0):?>
				<tr><td class="nav_string"><?=$arResult["NAV_STRING"]?></td></tr>
			<?endif;?>
			</table>
			<?endif;?>
		</form>
</html>