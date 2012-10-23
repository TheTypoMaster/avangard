<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
/*
$APPLICATION->IncludeFile("blog/blog/rss.php", 
	Array(
		"BLOG_URL" => $arFolders[0],
		"VERTICAL" => "N",
		"RSS1" => "Y",
		"RSS2" => "Y",
		"ATOM" => "Y"
	)
);
*/
$VERTICAL = ($VERTICAL== "Y") ? true : false;
$RSS1 = ($RSS1== "Y") ? true : false;
$RSS2 = ($RSS2== "Y") ? true : false;
$ATOM = ($ATOM== "Y") ? true : false;
$BLOG_URL = trim($BLOG_URL);
$is404 = ($is404=='N') ? false: true;

if(CModule::IncludeModule("blog"))
{
	$dbBlog = CBlog::GetList(
		Array(),
		Array("URL"=>$BLOG_URL),
		false,
		Array("nTopCount" => 1),
		$arSelectedFields
	);
	if($arBlog = $dbBlog->Fetch())
	{
		$url = CBlog::PreparePath($arBlog["URL"], SITE_ID, $is404);
		$sitePath = CBlogSitePath::GetBySiteID(SITE_ID);
		if($is404)
		{
			if($RSS1)
				$arExport["RSS1"] = '<a href="'.$url.'rss/rss1" title="RSS 0.92"><img src="/bitrix/templates/.default/blog/images/RSS1.gif" width="80" height="15" border="0"></a>';
			if($RSS2)
				$arExport["RSS2"] = '<a href="'.$url.'rss/rss2" title="RSS 2.0"><img src="/bitrix/templates/.default/blog/images/RSS2.gif" width="80" height="15" border="0"></a>';
			if($ATOM)
				$arExport["ATOM"] = '<a href="'.$url.'rss/atom" title="Atom 0.3"><img src="/bitrix/templates/.default/blog/images/ATOM.gif" width="80" height="15" border="0"></a>';
		}
		else
		{
			if($RSS1)
				$arExport["RSS1"] = '<a href="'.$sitePath["PATH"].'/rss.php?blog='.$BLOG_URL.'&type=rss1" title="RSS 0.92"><img src="/bitrix/templates/.default/blog/images/RSS1.gif" width="80" height="15" border="0"></a>';
			if($RSS2)
				$arExport["RSS2"] = '<a href="'.$sitePath["PATH"].'/rss.php?blog='.$BLOG_URL.'&type=rss2" title="RSS 2.0"><img src="/bitrix/templates/.default/blog/images/RSS2.gif" width="80" height="15" border="0"></a>';
			if($ATOM)
				$arExport["ATOM"] = '<a href="'.$sitePath["PATH"].'/rss.php?blog='.$BLOG_URL.'&type=atom" title="Atom 0.3"><img src="/bitrix/templates/.default/blog/images/ATOM.gif" width="80" height="15" border="0"></a>';
		}
		?><p>
		<table width="0" cellpadding="0" cellspacing="5" border="0" align="center">
		<?if($VERTICAL):
			foreach($arExport as $key => $value)
				echo '<tr><td>'.$value.'</td></tr>';
		else:
			echo '<tr>';
			foreach($arExport as $key => $value)
				echo '<td>'.$value.'</td>';
			echo '</tr>';
		endif;?>
		</table>
		</p>
		<?
	}
}
else
	echo ShowError(GetMessage("B_B_RSS_NO_MODULE"));?>