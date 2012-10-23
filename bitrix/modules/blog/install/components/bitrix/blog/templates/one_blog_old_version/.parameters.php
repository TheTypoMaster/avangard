<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arTemplateParameters = array(
	"USER_PROPERTY_NAME"=>array(
		"NAME" => GetMessage("USER_PROPERTY_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "",	
	),
	"BLOG_URL"=>array(
		"NAME" => GetMessage("ONE_BLOG_BLOG_URL"),
		"TYPE" => "STRING",
		"DEFAULT" => "",	
		"PARENT" => "BASE",
	),
	

);
?>