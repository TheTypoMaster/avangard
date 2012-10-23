<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
//*******************************************************

$FID = IntVal($FID);
$NUM = IntVal($NUM);
$ORDER_BY = strtoupper(Trim($ORDER_BY));
$ORDER_DIRECTION = strtoupper(Trim($ORDER_DIRECTION));
$PATH2MESSAGES = Trim($PATH2MESSAGES);
$CACHE_TIME = IntVal($CACHE_TIME);

$cache = new CPHPCache; 
$cache_id = "forum_last_topic_".$FID."_".$NUM."_".$ORDER_BY."_".$ORDER_DIRECTION."_".$PATH2MESSAGES;

if ($CACHE_TIME>0 && $cache->InitCache($CACHE_TIME, $cache_id, "/forum/forum_pieces/last_topics.php/"))
{
	$cache->Output();
}
else
{
	if ($CACHE_TIME>0)
		$cache->StartDataCache($CACHE_TIME, $cache_id, "/forum/forum_pieces/last_topics.php/");

	if ($NUM<=0 || $NUM>100)
		$NUM = 100;

	if ($ORDER_BY!="TITLE" && $ORDER_BY!="POSTS" && $ORDER_BY!="USER_START_NAME" && $ORDER_BY!="VIEWS" && $ORDER_BY!="START_DATE")
		$ORDER_BY = "LAST_POST_DATE";

	if ($ORDER_DIRECTION != "ASC")
		$ORDER_DIRECTION = "DESC";

	$arOrder = array($ORDER_BY => $ORDER_DIRECTION);

	$arFilter = array("APPROVED" => "Y");
	if ($FID>0)
		$arFilter["FORUM_ID"] = $FID;

	$db_Topic = CForumTopic::GetListEx($arOrder, $arFilter, false, (($NUM>0) ? $NUM : false));

	$ind = 0;
	while ($ar_Topic = $db_Topic->Fetch())
	{
		$ind++;
		if ($NUM>0 && $ind>$NUM)
			break;
		?>
		<font class="text">
		<img src="/bitrix/templates/.default/forum/forum_pieces/images/news_bullet.gif" width="3" height="5" border="0">&nbsp;<a href="<?= $PATH2MESSAGES.(strpos($PATH2MESSAGES, "?")!==false ? "&amp;" : "?")."FID=".$ar_Topic["FORUM_ID"]."&amp;TID=".$ar_Topic["ID"]; ?>"><?= htmlspecialcharsEx($ar_Topic["TITLE"]); ?></a>
		<?
		if ($FID<=0)
		{
			$arForum = CForumNew::GetByID($ar_Topic["FORUM_ID"]);
			?>
			(<?= str_replace("#FORUM#", htmlspecialcharsEx($arForum["NAME"]), GetMessage("FTP_IN_FORUM")) ?>)
			<?
		}
		?>
		<a href="<?= $PATH2MESSAGES.(strpos($PATH2MESSAGES, "?")!==false ? "&amp;" : "?")."FID=".$ar_Topic["FORUM_ID"]."&amp;TID=".$ar_Topic["ID"]."&amp;MID=".$ar_Topic["LAST_MESSAGE_ID"]."#message".$ar_Topic["LAST_MESSAGE_ID"] ?>"><img src="/bitrix/templates/.default/forum/forum_pieces/images/icon_latest_reply.gif" width="18" height="9" border="0" alt="<?= GetMessage("FTP_LAST_MESS") ?>"></a>
		</font>
		<br>
		<?
	}

	if ($CACHE_TIME>0)
		$cache->EndDataCache(array());

}

//*******************************************************
endif;
?>