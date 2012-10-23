<?

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/include.php");

if($REQUEST_METHOD=="POST" && strlen($export)>0)
{
	?>
<?echo '<'.'?xml version="1.0"?'.'>'?>

<body>
<?
$res = $DB->Query("SELECT ID, NAME, USER_AGENT FROM b_stat_searcher WHERE ID<>1 ORDER BY ID");
while($arS = $res->Fetch()):
?>
	<searcher name="<?echo htmlspecialchars($arS["NAME"], ENT_QUOTES)?>" user_agent="<?echo htmlspecialchars($arS["USER_AGENT"], ENT_QUOTES)?>">
		<?
		$resSP = $DB->Query("SELECT DOMAIN, VARIABLE FROM b_stat_searcher_params WHERE SEARCHER_ID=".$arS["ID"]);
		while($arSP = $resSP->Fetch()):
		?>
		<param domain="<?echo htmlspecialchars($arSP["DOMAIN"], ENT_QUOTES)?>" variable="<?echo htmlspecialchars($arSP["VARIABLE"], ENT_QUOTES)?>"/>
		<?endwhile?>
	</searcher>
<?endwhile?>
</body>
<?
	die();
}

$APPLICATION->SetTitle("Ёкспорт-импорт поисковых систем");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form method="POST">
Ёкспорт поисковых систем
<input type="submit" name="export" value="ѕолучить XML">
</form>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>