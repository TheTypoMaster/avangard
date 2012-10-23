<?
$arUrlRewrite = array(
	array(
		"CONDITION"	=>	"#^/shop/catalog/subject/([0-9]+)\\.html.*#",
		"RULE"	=>	"ELEMENT_ID=$1",
		"ID"	=>	"",
		"PATH"	=>	"/shop/catalog/subject.php",
	),
	array(
		"CONDITION"	=>	"#^/news/news_([0-9]*)\\.html.*#",
		"RULE"	=>	"ID=$1",
		"ID"	=>	"",
		"PATH"	=>	"/news/detail.php",
	),
);

?>