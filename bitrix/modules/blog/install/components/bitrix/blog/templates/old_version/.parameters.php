<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!CModule::IncludeModule("blog"))
	return false;

$arGroupList = Array();
$dbGroup = CBlogGroup::GetList(Array("SITE_ID" => "ASC", "NAME" => "ASC"));
while($arGroup = $dbGroup->GetNext())
{
	$arGroupList[$arGroup["ID"]] = "(".$arGroup["SITE_ID"].") [".$arGroup["ID"]."] ".$arGroup["NAME"];
}

$arTemplateParameters = array(
	"GROUP_ID"=>array(
		"NAME" => GetMessage("GENERAL_PAGE_GROUP_ID"),
		"TYPE" => "LIST",
		"VALUES" => $arGroupList,
		"MULTIPLE" => "N",
		"DEFAULT" => "",	
		"ADDITIONAL_VALUES" => "Y",
	),

	"USER_PROPERTY_NAME"=>array(
		"NAME" => GetMessage("USER_PROPERTY_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "",	
	),

    "FONT_MAX" => array(
		"NAME" => GetMessage("SEARCH_FONT_MAX"),
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"DEFAULT" => "20"
    ),
    "FONT_MIN" => array(
		"NAME" => GetMessage("SEARCH_FONT_MIN"),
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"DEFAULT" => "10"
    ),
    "COLOR_NEW" => array(
		"NAME" => GetMessage("SEARCH_COLOR_NEW"),
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"DEFAULT" => "3f75a2"
    ),
    "COLOR_OLD" => array(
		"NAME" => GetMessage("SEARCH_COLOR_OLD"),
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"DEFAULT" => "8D8D8D"
    ),
    "PERIOD_NEW_TAGS" => array(
		"NAME" => GetMessage("SEARCH_PERIOD_NEW_TAGS"),
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"DEFAULT" => ""
    ),
    "PERIOD" => array(
		"NAME" => GetMessage("SEARCH_PERIOD"),
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"DEFAULT" => ""
    ),
	"COLOR_TYPE" => array(
		"NAME" => GetMessage("SEARCH_COLOR_TYPE"),
		"TYPE" => "LIST",
		"TYPE" => "CHECKBOX",
		"MULTIPLE" => "N",
		"DEFAULT" => "Y",
	),
    "WIDTH" => array(
		"NAME" => GetMessage("SEARCH_WIDTH"),
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"DEFAULT" => "100%"
    ),
);
?>