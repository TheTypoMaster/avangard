<?
/**************************************************************************
RSS news import

The given component is intended for RSS news import.
 
Example:

$APPLICATION->IncludeFile("iblock/rss/rss_show.php", Array(
	"SITE"		=> "www.bitrix.ru",
	"PORT"		=> "80",
	"PATH"		=> "/bitrix/rss.php",
	"QUERY_STR"	=> "ID=news_sm&LANG=ru&TYPE=news&LIMIT=5",
	"OUT_CHANNEL"	=> "N",
	"NUM_NEWS"	=> "10",
	"CACHE_TIME"	=> "600",
	));

Parameters:

"	SITE - site url
"	PORT - port (usually 80)
"	PATH - path to rss file
"	QUERY_STR - query string (it necessary)
"	OUT_CHANNEL - [Y|N] are news nodes outside of channel node (usually not)
"	NUM_NEWS - number of news to show (0 - do not limit)
"	CACHE_TIME - (sec) caching time (0 - do not cache)

**************************************************************************/
if (CModule::IncludeModule("iblock")):
//*******************************************************

$SITE = Trim($SITE);		// Site
$PORT = IntVal($PORT);	// Part
$PATH = Trim($PATH);		// Path
$QUERY_STR = Trim($QUERY_STR);		// Query
$bOutChannel = ($OUT_CHANNEL == "Y") ? True : False;
$NUM_NEWS = IntVal($NUM_NEWS);		// Number of news

$CACHE_TIME = IntVal($CACHE_TIME);


$cache = new CPHPCache; 
$cache_id = "rss_news_".$SITE."_".$PORT."_".$PATH."_".$QUERY_STR."_".$OUT_CHANNEL;

if ($CACHE_TIME>0 && $cache->InitCache($CACHE_TIME, $cache_id, "/iblock/rss/"))
{
	$cache->Output();
}
else
{
	if ($CACHE_TIME>0)
		$cache->StartDataCache($CACHE_TIME, $cache_id, "/iblock/rss/");

	$arRes = CIBlockRSS::GetNewsEx($SITE, $PORT, $PATH, $QUERY_STR, $bOutChannel);
	$arRes = CIBlockRSS::FormatArray($arRes, $bOutChannel);
	?>
	<font class="text"><b><?echo $arRes["title"] ?></b></font><br><br>

	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<?for ($i = 0; $i < (($NUM_NEWS<=0 || count($arRes["item"])<$NUM_NEWS)?count($arRes["item"]):$NUM_NEWS); $i++):?>
			<tr>
				<td rowspan="3" valign="top" style="padding-right: 3px; padding-top: 5px;">
					<?
					if (strlen($arRes["item"][$i]["enclosure"]["url"])>0):
						$i1 = $arRes["item"][$i]["enclosure"]["width"];
						$i2 = $arRes["item"][$i]["enclosure"]["height"];
						echo ShowImage($arRes["item"][$i]["enclosure"]["url"], 100, 150, "hspace='0' vspace='2' align='left' border='0'", $arRes["item"][$i]["link"], false, false, $i1, $i2);
					else:
						?><img src="/images/1.gif" width="1" height="10"><?
					endif;
					?>
				</td>
				<td width="100%">
					<font class="newsdatab">
					<?
					if (strlen($arRes["item"][$i]["pubDate"])>0)
						echo CIBlockRSS::XMLDate2Dec($arRes["item"][$i]["pubDate"]);
					?>
					</font>
				</td>
			</tr>
			<tr>
				<td width="100%">
					<?if (strlen($arRes["item"][$i]["link"])>0):?>
						<a href="<?echo $arRes["item"][$i]["link"]?>" class="text">
					<?endif;?>
					<?echo $arRes["item"][$i]["title"]?>
					<?if (strlen($arRes["item"][$i]["link"])>0):?>
						</a>
					<?endif;?>
				</td>
			</tr>	
			<tr>
				<td width="100%" valign="top">
					<font class="text">
					<?echo $arRes["item"][$i]["description"];?>
					</font>
				</td>
			</tr>
			<tr>
				<td colspan="3"><img src="/images/1.gif" width="1" height="10"></td>
			</tr>
		<?endfor;?>
	</table>

	<?
	if ($CACHE_TIME>0)
		$cache->EndDataCache(array());
}

//*******************************************************
endif;
?>