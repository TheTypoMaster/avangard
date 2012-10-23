<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="shortcut icon" href="http://www.avangard.biz/favicon.ico" type="image/x-icon">
<meta name='yandex-verification' content='462691a93b33875f' />
<?$APPLICATION->ShowMeta("robots")?>
<?$APPLICATION->ShowMeta("keywords")?>
<?$APPLICATION->ShowMeta("description")?>
<title><?$APPLICATION->ShowTitle()?></title>
<?$APPLICATION->ShowCSS();?>
<?$APPLICATION->ShowHeadStrings()?>
<?$APPLICATION->ShowHeadScripts()?>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/script_1.js"></script>
</head>
<body>
 <?$APPLICATION->ShowPanel();?>
 <? $APPLICATION->AddHeadScript("/basket/basket.js"); ?>
 <? if(CModule::IncludeModule('iblock')) $incl="Y"; ?>
<!--TOP MENU-->
<div id="top_menu"><div id="t_m_content">
<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
<td class="top_td_left">

<?$APPLICATION->IncludeComponent("bitrix:menu", "main_top_menu", array(
	"ROOT_MENU_TYPE" => "top_one",
	"MENU_CACHE_TYPE" => "N",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "2",
	"CHILD_MENU_TYPE" => "part",
	"USE_EXT" => "N",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);?>
  
</td>

<td>
<?$APPLICATION->IncludeComponent("bitrix:search.form", "top_search", Array(
	"PAGE"	=>	"/redesign/catalog/search.php"
	)
);?>
</td>
</tr></table>
</div></div>
<!--/TOP MENU-->

<div id="top_line"><div id="t_l_content">
<div id="t_l_logo"><a href="/"><img src="/images/logo.gif" alt="" border=0></a></div>
<div id="t_l_fone"><p class="p_fone"><span>(495)</span> <span id="ya-phone-1">981-66-44</span></p><p>многоканальный телефон</p></div>
</div></div>

<?$APPLICATION->IncludeComponent("bitrix:menu", "second_top_menu", Array(
	"ROOT_MENU_TYPE"	=>	"top_two",
	"MAX_LEVEL"	=>	"2",
	"CHILD_MENU_TYPE"	=>	"part",
	"USE_EXT"	=>	"N"
	)
);?>



    <div id="all_2">
<!--LEFT COLUMN-->
<div id="lc_2">
