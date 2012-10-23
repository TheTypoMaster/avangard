<?
IncludeTemplateLangFile(__FILE__);
$sSectionName = GetMessage("subscr_descr_name");

if(!IsModuleInstalled("iblock") || !CModule::IncludeModule("iblock"))
	return;

$arSites=array();
$defSite="";
$sitesSort="SORT";
$sitesBy="ASC";
$rsSite = CSite::GetList($sitesSort, $sitesBy, array());
while($arSite = $rsSite->Fetch())
{
	$arSites[$arSite["ID"]] = $arSite["NAME"];
	if($arSite["DEF"]=="Y")
		$defSite = $arSite["ID"];
}

$arIBlockTypes=array();
$defIBlockType="news";
$rsIBlockType = CIBlockType::GetList(Array("SORT"=>"ASC"));
while($arIBlockType = $rsIBlockType->Fetch())
	if($arIBlockType = CIBlockType::GetByIDLang($arIBlockType["ID"], LANG))
		$arIBlockTypes[$arIBlockType["ID"]] = $arIBlockType["NAME"];

$arIBlocks=array("-"=>GetMessage("subscr_descr_subscrnews_all"));
$rsIBlock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$arCurrentValues["SITE_ID"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arIBlock = $rsIBlock->Fetch())
	$arIBlocks[$arIBlock["ID"]] = $arIBlock["NAME"];

$arSorts = array("ASC"=>GetMessage("subscr_descr_sort_order_asc"), "DESC"=>GetMessage("subscr_descr_sort_order_desc"));
$arSortFields = array(
		"ACTIVE_FROM"=>GetMessage("subscr_descr_act_date"),
		"SORT"=>GetMessage("subscr_descr_sort"),
	);

$arTemplateDescription = array(
	"subscr_form.php" => array(
		"NAME" => GetMessage("subscr_descr_form"),
		"DESCRIPTION" => GetMessage("subscr_descr_form_desc"),
		"ICON" => "/bitrix/images/subscribe/components/subscr_form.gif",
		"PARAMS" => array(
			"PAGE" => array(
				"NAME"=>GetMessage("subscr_descr_form_page"), 
				"TYPE"=>"STRING", 
				"DEFAULT"=>COption::GetOptionString("subscribe", "subscribe_section")."subscr_edit.php", 
				"COLS"=>"35"
			),
			"SHOW_HIDDEN" => array(
				"NAME"=>GetMessage("subscr_descr_show_hidden"), 
				"TYPE"=>"LIST", 
				"VALUES"=>array("Y"=>GetMessage("subscr_descr_yes"), "N"=>GetMessage("subscr_descr_no")),
				"DEFAULT"=>"N",
				"ADDITIONAL_VALUES"=>"N"
			)
		)
	),
	"subscribe.php" => array(
		"NAME" => GetMessage("subscr_descr_subscr"),
		"DESCRIPTION" => GetMessage("subscr_descr_subscr_desc"),
		"ICON" => "/bitrix/images/subscribe/components/subscr_rubrics.gif",
		"PARAMS" => array(
			"PAGE" => array(
				"NAME"=>GetMessage("subscr_descr_subscr_page"), 
				"TYPE"=>"STRING", 
				"DEFAULT"=>"subscr_edit.php", 
				"COLS"=>"35"
			),
			"SHOW_COUNT" => array(
				"NAME"=>GetMessage("subscr_descr_subscr_count"), 
				"TYPE"=>"LIST", 
				"VALUES"=>array("Y"=>GetMessage("subscr_descr_yes"), "N"=>GetMessage("subscr_descr_no")),
				"DEFAULT"=>"Y",
				"ADDITIONAL_VALUES"=>"N"
			),
			"SHOW_HIDDEN" => array(
				"NAME"=>GetMessage("subscr_descr_show_hidden"), 
				"TYPE"=>"LIST", 
				"VALUES"=>array("Y"=>GetMessage("subscr_descr_yes"), "N"=>GetMessage("subscr_descr_no")),
				"DEFAULT"=>"N",
				"ADDITIONAL_VALUES"=>"N"
			)
		)
	),
	"subscr_edit.php" => array(
		"NAME" => GetMessage("subscr_descr_subscredt_page"),
		"DESCRIPTION" => GetMessage("subscr_descr_subscredt_desc"),
		"ICON" => "/bitrix/images/subscribe/components/subscr_edit.gif",
		"PARAMS" => array(
			"ALLOW_ANONYMOUS" => array(
				"NAME"=>GetMessage("subscr_descr_subscredt_anon"), 
				"TYPE"=>"LIST", 
				"VALUES"=>array("Y"=>GetMessage("subscr_descr_yes"), "N"=>GetMessage("subscr_descr_no")),
				"DEFAULT"=>COption::GetOptionString("subscribe", "allow_anonymous"),
				"ADDITIONAL_VALUES"=>"N"
			),
			"SHOW_AUTH_LINKS" => array(
				"NAME"=>GetMessage("subscr_descr_subscredt_show"), 
				"TYPE"=>"LIST", 
				"VALUES"=>array("Y"=>GetMessage("subscr_descr_yes"), "N"=>GetMessage("subscr_descr_no")),
				"DEFAULT"=>COption::GetOptionString("subscribe", "show_auth_links"),
				"ADDITIONAL_VALUES"=>"N"
			),
			"SHOW_HIDDEN" => array(
				"NAME"=>GetMessage("subscr_descr_show_hidden"), 
				"TYPE"=>"LIST", 
				"VALUES"=>array("Y"=>GetMessage("subscr_descr_yes"), "N"=>GetMessage("subscr_descr_no")),
				"DEFAULT"=>"N",
				"ADDITIONAL_VALUES"=>"N"
			)
		)
	),
	"subscr_news.php" => array(
		"NAME" => GetMessage("subscr_descr_subscrnews"),
		"DESCRIPTION" => GetMessage("subscr_descr_subscrnews_desc"),
		"ICON" => "/bitrix/images/iblock/components/subscr_news_list.gif",
		"PARAMS" => array(
			"SITE_ID" => array(
				"NAME"=>GetMessage("subscr_descr_subscrnews_site")
				,"TYPE"=>"LIST"
				,"VALUES"=>$arSites
				,"DEFAULT"=>$defSite
				,"MULTIPLE"=>"N"
				,"ADDITIONAL_VALUES"=>"N"
				,"REFRESH" => "Y"
			)
			,"IBLOCK_TYPE" => array(
				"NAME"=>GetMessage("subscr_descr_subscrnews_ibtype")
				,"TYPE"=>"LIST"
				,"VALUES"=>$arIBlockTypes
				,"DEFAULT"=>$defIBlockType
				,"MULTIPLE"=>"N"
				,"ADDITIONAL_VALUES"=>"N"
				,"REFRESH" => "Y"
			)
			,"ID" => array(
				"NAME"=>GetMessage("subscr_descr_subscrnews_ibcode")
				,"TYPE"=>"LIST"
				,"VALUES"=>$arIBlocks
				,"MULTIPLE"=>"N"
				,"ADDITIONAL_VALUES"=>"N"
			)
			,"SORT_BY" => array(
				"NAME"=>GetMessage("subscr_descr_subscrnews_sort")
				,"TYPE"=>"LIST"
				,"DEFAULT"=>"ACTIVE_FROM"
				,"VALUES"=>$arSortFields
				,"ADDITIONAL_VALUES"=>"N"
			)
			,"SORT_ORDER" => array(
				"NAME"=>GetMessage("subscr_descr_subscrnews_sort_dir")
				,"TYPE"=>"LIST"
				,"MULTIPLE"=>"N"
				,"DEFAULT"=>"DESC"
				,"VALUES"=>$arSorts
				,"ADDITIONAL_VALUES"=>"N"
			)
		)
	)
);
?>
