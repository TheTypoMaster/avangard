<?
/**************************************************************************
RSS news export

The given component is intended for creation of page which exports the chosen site news in RSS format.
 
Example:

$APPLICATION->IncludeFile("iblock/rss/rss_out.php", Array(
	"ID"		=> "1",
	"NUM_NEWS"	=> "20",
	"NUM_DAYS"	=> "30",
	"YANDEX"	=> "N",
	"CACHE_TIME"	=> "600"
	));

Parameters:

"	ID - ID of informational block to export
"	NUM_NEWS - number of news
"	NUM_DAYS - number of days
"	YANDEX - export to Yandex dialect
"	CACHE_TIME - (sec) caching time (0 - do not cache)

**************************************************************************/

if (CModule::IncludeModule("iblock")):
//*******************************************************

$ID = IntVal($ID);		// ID


$NUM_DAYS = (IntVal($NUM_DAYS) > 0 ? IntVal($NUM_DAYS) : false);
$bYandex = ($YANDEX == "Y") ? True : False;

$CACHE_TIME = IntVal($CACHE_TIME);


$cache = new CPHPCache; 
$cache_id = "rss_out_".$ID."_".$NUM_NEWS."_".$NUM_DAYS."_".$YANDEX;

global $APPLICATION;
$APPLICATION->RestartBuffer();
header("Content-Type: text/xml");
header("Pragma: no-cache");

if ($CACHE_TIME>0 && $cache->InitCache($CACHE_TIME, $cache_id, "/iblock/rss_out/"))
{
	$cache->Output();
}
else
{
	if ($CACHE_TIME>0)
		$cache->StartDataCache($CACHE_TIME, $cache_id, "/iblock/rss_out/");

	$db_res_iblock = CIBlock::GetList(array(), array("ACTIVE" => "Y", "ID" => $ID));
	$bAccessable = False;
	if (($arIBlock = $db_res_iblock->Fetch()) && ($arIBlock["RSS_ACTIVE"]=="Y"))
		$bAccessable = True;

	echo "<?xml version=\"1.0\" encoding=\"Windows-1251\"?>\n";
	echo "<rss version=\"2.0\"";
	if ($bYandex) echo " xmlns:yandex=\"http://news.yandex.ru\"";
	echo ">\n";

	if ($bAccessable)
		echo CIBlockRSS::GetRSSText($arIBlock, $NUM_NEWS, $NUM_DAYS, $bYandex);

	echo "</rss>\n";

	if ($CACHE_TIME>0)
		$cache->EndDataCache(array());
}
die();

//*******************************************************
endif;
?>