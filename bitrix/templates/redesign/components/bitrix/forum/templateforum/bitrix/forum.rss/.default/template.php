<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(($arParams["MODE"] == "link") && (!empty($arResult["rss_link"]))):
	$arResult["rss_link"] = array_reverse($arResult["rss_link"]);
	foreach($arResult["rss_link"] as $key => $val):
		?><a href="<?=$val["link"]?>" title="<?=GetMessage("F_RSS")?><?=$val["name"]?>" class="forum-rss-<?=$key?>" target="_self"><span class="empty"></span></a><?
	if(method_exists($APPLICATION, 'addheadstring'))
	{
		$APPLICATION->AddHeadString('<link rel="alternate" type="application/rss+xml" title="'.GetMessage("F_RSS").$val["name"].'" href="'.$val["link"].'" />');
	}	
	endforeach;
else:
	if ($arParams["TYPE"] == "rss1"):
?><<??>?xml version="1.0" encoding="<?=$arResult["CHARSET"]?>"?<??>>
<rss version=".92">
	<channel>
		<title><?=$arResult["TITLE"]?></title>
		<link>http://<?=$arResult["SERVER_NAME"]?></link>
		<description><?=$arResult["DESCRIPTION"]?></description>
		<language><?=$arResult["LANGUAGE"]?></language>
		<docs>http://backend.userland.com/rss092</docs>
		<pubDate><?=$arResult["NOW"]?></pubDate>
		<?foreach ($arResult["DATA"] as $fid => $forum):?>
			<?foreach ($forum["TOPICS"] as $tid => $topic):?>
		<item>
			<title><?=$topic["TITLE"]?> <?=GetMessage("F_ON_FORUM")?> <?=$forum["NAME"]?></title>
			<description><![CDATA[<?
				foreach ($topic["MESSAGES"] as $mid => $message):
					echo $message["TEMPLATE"];
					if (empty($message["TEMPLATE"])):
?><b><?=GetMessage("F_TITLE")?></b> <a href="http://<?=$arResult["SERVER_NAME"].$topic["TOPIC_LINK"]?>"><?=$topic["TITLE"]?></a>  <?=$topic["TOPIC_DESCRIPTION"]?><br />
<b><?=GetMessage("F_NAME")?></b> <a href="http://<?=$arResult["SERVER_NAME"].$forum["FORUM_LINK"]?>"><?=$forum["~NAME"]?></a> <br />
<b><?=GetMessage("F_POST_MESSAGE")?></b><br /> 
<?=$message["POST_MESSAGE"]?> <br />
<b><?=GetMessage("F_POST_LINK")?></b> <a href="http://<?=$arResult["SERVER_NAME"].$message["POST_LINK"]?>">http://<?=$arResult["SERVER_NAME"].$message["POST_LINK"]?></a> <br />
<b><?=GetMessage("F_AUTHOR_NAME")?></b> <a href="http://<?=$arResult["SERVER_NAME"].$message["AUTHOR_LINK"]?>"><?=$message["AUTHOR_NAME"]?></a> <br />
<?=$message["~POST_DATE"]?><br />
<?=$message["ATTACH_IMG"]?><br /><br /><?
					endif;
				endforeach;
				?>]]></description>
			<link>http://<?=$arResult["SERVER_NAME"].$topic["TOPIC_LINK"]?></link>
		</item>
			<?endforeach;?>
		<?endforeach;?>
	</channel>
</rss>
		
	<?elseif ($arParams["TYPE"] == "rss2"):
?><<??>?xml version="1.0" encoding="<?=$arResult["CHARSET"]?>"?<??>>
<rss version="2.0">
	<channel>
		<title><?=$arResult["TITLE"]?></title>
		<link>http://<?=$arResult["SERVER_NAME"]?></link>
		<description><?=$arResult["DESCRIPTION"]?></description>
		<language><?=$arResult["LANGUAGE"]?></language>
		<docs>http://backend.userland.com/rss2</docs>
		<pubDate><?=$arResult["NOW"]?></pubDate>
		<?foreach ($arResult["DATA"] as $fid => $forum):?>
			<?foreach ($forum["TOPICS"] as $tid => $topic):?>
		<item>
			<title><?=$topic["TITLE"]?> <?=GetMessage("F_ON_FORUM")?> <?=$forum["NAME"]?></title>
			<description><![CDATA[<?
				foreach ($topic["MESSAGES"] as $mid => $message):
					?><?=$message["TEMPLATE"]?>
					<?if (empty($message["TEMPLATE"])):
?><b><?=GetMessage("F_TITLE")?></b> <a href="http://<?=$arResult["SERVER_NAME"].$topic["TOPIC_LINK"]?>"><?=$topic["TITLE"]?></a>  <?=$topic["TOPIC_DESCRIPTION"]?><br />
<b><?=GetMessage("F_NAME")?></b> <a href="http://<?=$arResult["SERVER_NAME"].$forum["FORUM_LINK"]?>"><?=$forum["~NAME"]?></a> <br />
<b><?=GetMessage("F_POST_MESSAGE")?></b><br /> 
<?=$message["POST_MESSAGE"]?> <br />
<b><?=GetMessage("F_POST_LINK")?></b> <a href="http://<?=$arResult["SERVER_NAME"].$message["POST_LINK"]?>">http://<?=$arResult["SERVER_NAME"].$message["POST_LINK"]?></a> <br />
<b><?=GetMessage("F_AUTHOR_NAME")?></b> <a href="http://<?=$arResult["SERVER_NAME"].$message["AUTHOR_LINK"]?>"><?=$message["AUTHOR_NAME"]?></a> <br />
<?=$message["~POST_DATE"]?><br />
<?=$message["ATTACH_IMG"]?><br /><br /><?
					endif;
				endforeach;
				?>]]></description>
			<link>http://<?=$arResult["SERVER_NAME"].$topic["TOPIC_LINK"]?></link>
			<guid>http://<?=$arResult["SERVER_NAME"].$topic["TOPIC_LINK"]?></guid>
			<pubDate><?=$topic["START_DATE"]?></pubDate>
			<category><?=$forum["NAME"]?></category>
		</item>
			<?endforeach;?>
		<?endforeach;?>
	</channel>
</rss>
	<?elseif  ($arParams["TYPE"] == "atom"):
?><<??>?xml version="1.0" encoding="<?=$arResult["CHARSET"]?>"?<??>>
<feed version="0.3" xmlns="http://purl.org/atom/ns" xml:lang="<?=$arResult["LANGUAGE"]?>">
	<title><?=$arResult["TITLE"]?></title>
	<tagline>http://<?=$arResult["SERVER_NAME"]?></tagline>
	<id>tag:<?=htmlspecialchars($arResult["SERVER_NAME"]).",".time()?></id>
	<link rel="alternate" type="text/html" href="http://<?=$arResult["SERVER_NAME"]?>" />
	<copyright>Copyright (c) http://<?=$arResult["SERVER_NAME"]?></copyright>
	<modified><?=$arResult["NOW"]?></modified>
		<?foreach ($arResult["DATA"] as $fid => $forum):?>
			<?foreach ($forum["TOPICS"] as $tid => $topic):?>
	<entry>
		<title type="text/html"><?=$topic["TITLE"]?> <?=GetMessage("F_ON_FORUM")?> <?=$forum["NAME"]?></title>
		<link rel="alternate" type="text/html" href="http://<?=$arResult["SERVER_NAME"].$topic["TOPIC_LINK"]?>" />
		<issued><?=$topic["START_DATE"]?></issued>
		<modified><?=$arResult["NOW"]?></modified>
		<id>tag:<?=htmlspecialchars($arResult["SERVER_NAME"]).":".$topic["TOPIC_LINK"]?></id>
		<content type="text/html" mode="escaped" xml:lang="<?=$arResult["LANGUAGE"]?>" xml:base="http://<?=$arResult["SERVER_NAME"].$topic["TOPIC_LINK"]?>"><![CDATA[<?
				foreach ($topic["MESSAGES"] as $mid => $message):
					?><?=$message["TEMPLATE"]?><?
					if (empty($message["TEMPLATE"])):
?><b><?=GetMessage("F_TITLE")?></b> <a href="http://<?=$arResult["SERVER_NAME"].$topic["TOPIC_LINK"]?>"><?=$topic["TITLE"]?></a>  <?=$topic["TOPIC_DESCRIPTION"]?><br />
<b><?=GetMessage("F_NAME")?></b> <a href="http://<?=$arResult["SERVER_NAME"].$forum["FORUM_LINK"]?>"><?=$forum["~NAME"]?></a> <br />
<b><?=GetMessage("F_POST_MESSAGE")?></b><br /> 
<?=$message["POST_MESSAGE"]?> <br />
<b><?=GetMessage("F_POST_LINK")?></b> <a href="http://<?=$arResult["SERVER_NAME"].$message["POST_LINK"]?>">http://<?=$arResult["SERVER_NAME"].$message["POST_LINK"]?></a> <br />
<b><?=GetMessage("F_AUTHOR_NAME")?></b> <a href="http://<?=$arResult["SERVER_NAME"].$message["AUTHOR_LINK"]?>"><?=$message["AUTHOR_NAME"]?></a> <br />
<?=$message["ATTACH_IMG"]?><br /><br /><?
					endif;
				endforeach;
		?>]]></content>
		<link rel="related" type="text/html" href="http://<?=$arResult["SERVER_NAME"].$topic["TOPIC_LINK"]?>" />
		<author>
		  <name><?=$topic["AUTHOR_NAME"]?></name>
		  <url>http://<?=$arResult["SERVER_NAME"]?><?=$topic["AUTHOR_LINK"]?></url>
		</author>
	</entry>
			<?endforeach;?>
		<?endforeach;?>
</feed>
	<?endif;?>
<?endif;?>