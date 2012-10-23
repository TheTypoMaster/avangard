<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
//*******************************************************

$APPLICATION->SetTitle(GetMessage("FH_FTITLE"));
$APPLICATION->SetTemplateCSS("forum/forum_tmpl_1/forum.css");

$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");
?>
<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr><td>
		<FONT class=text><?= GetMessage("FH_HELP_CONT") ?></FONT>
	</td></tr>
</table>
<br>
<?
$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");

//*******************************************************
else:
	?>
	<font class="text"><b><?= GetMessage("FH_NO_MODULE") ?></b></font>
	<?
endif;
?>