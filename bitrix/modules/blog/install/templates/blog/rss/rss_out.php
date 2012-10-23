<?
/**************************************************************************
RSS news export

The given component is intended for creation of page which exports the chosen blog posts
in RSS .92, RSS 2.0 and Atom .03 formats.
 
Example:

$APPLICATION->IncludeFile(
	"blog/rss/rss_out.php",
	array(
		"BLOG_URL" => "myblog",
		"NUM_POSTS" => "10",
		"TYPE" => "RSS1",
		"CACHE_TIME" => "600"
	)
);

Parameters:

"	BLOG_URL - URL of blog to export
"	NUM_POSTS - number of posts
"	TYPE - export format (RSS .92, RSS 2.0 or Atom .03)
"	CACHE_TIME - (sec) caching time (0 - do not cache)

**************************************************************************/

if (CModule::IncludeModule("blog")):
//*******************************************************

$BLOG_URL = Trim($BLOG_URL);
$BLOG_URL = preg_replace("/[^a-zA-Z0-9_-]/is", "", $BLOG_URL);

$NUM_POSTS = IntVal($NUM_POSTS);

$TYPE = Trim($TYPE);
$TYPE_CACHE = Trim($TYPE);
if (strtolower($TYPE) == "rss1")
	$TYPE = "RSS .92";
if (strtolower($TYPE) == "rss2")
	$TYPE = "RSS 2.0";
if (strtolower($TYPE) == "atom")
	$TYPE = "Atom .03";

$CACHE_TIME = IntVal($CACHE_TIME);


$cache = new CPHPCache; 
$cache_id = "blog_rss_out_".$BLOG_URL."_".$NUM_POSTS."_".$TYPE;
$cache_path = "/".SITE_ID."/blog/".$BLOG_URL."/rss_out/".strtolower($TYPE_CACHE)."/";

global $APPLICATION;
$APPLICATION->RestartBuffer();
header("Content-Type: text/xml");
header("Pragma: no-cache");

if ($CACHE_TIME > 0 && $cache->InitCache($CACHE_TIME, $cache_id, $cache_path))
{
	$cache->Output();
}
else
{
	$dbBlog = CBlog::GetList(array(), array("URL" => $BLOG_URL), false, false, array("ID"));
	if ($arBlog = $dbBlog->Fetch())
	{
		if ($textRSS = CBlog::BuildRSS($arBlog["ID"], $TYPE, $NUM_POSTS))
		{
			if ($CACHE_TIME > 0)
				$cache->StartDataCache($CACHE_TIME, $cache_id, $cache_path);

			echo $textRSS;

			if ($CACHE_TIME > 0)
				$cache->EndDataCache(array());
		}
	}
}
die();

//*******************************************************
endif;
?>