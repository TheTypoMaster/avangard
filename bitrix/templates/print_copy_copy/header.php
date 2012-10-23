<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=LANG_CHARSET;?>" />
<meta name="robots" content="noindex, follow" />
<?$APPLICATION->ShowMeta("keywords")?>
<?$APPLICATION->ShowMeta("description")?>
<title><?$APPLICATION->ShowTitle()?></title>
<?$APPLICATION->ShowCSS();?>
<?$APPLICATION->ShowHeadStrings()?>
 <?$APPLICATION->ShowPanel();?>
 <? if(CModule::IncludeModule('iblock')) $incl="Y"; ?>
<script type="text/javascript" src="/libs/highslide.js"></script>
<link rel="stylesheet" type="text/css" href="/libs/highslide.css" />
<script type="text/javascript">
	hs.graphicsDir = '/libs/graphics/';
	hs.wrapperClassName = 'wide-border';
</script>

</head>
													<body onload="window.print();">
 