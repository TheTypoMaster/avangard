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
	"PATH_TO_BLOG" => Array(
			"NAME" => GetMessage("GENERAL_PAGE_PATH_TO_BLOG"),
			"TYPE" => "STRING",
			"DEFAULT" => "/club/user/#user_id#/blog/",
		),		
	"PATH_TO_POST" => Array(
			"NAME" => GetMessage("GENERAL_PAGE_PATH_TO_POST"),
			"TYPE" => "STRING",
			"DEFAULT" => "/club/user/#user_id#/blog/#post_id#/",
		),		
	"PATH_TO_GROUP_BLOG" => Array(
			"NAME" => GetMessage("GENERAL_PAGE_PATH_TO_GROUP_BLOG"),
			"TYPE" => "STRING",
			"DEFAULT" => "/club/group/#group_id#/blog/",
		),		
	"PATH_TO_GROUP_BLOG_POST" => Array(
			"NAME" => GetMessage("GENERAL_PAGE_PATH_TO_GROUP_BLOG_POST"),
			"TYPE" => "STRING",
			"DEFAULT" => "/club/group/#group_id#/blog/#post_id#/",
		),		
	"PATH_TO_USER" => Array(
			"NAME" => GetMessage("GENERAL_PAGE_PATH_TO_USER"),
			"TYPE" => "STRING",
			"DEFAULT" => "/club/user/#user_id#/",
		),		
);
?>