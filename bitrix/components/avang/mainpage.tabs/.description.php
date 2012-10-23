<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Mainpage tabs",
	"DESCRIPTION" => "",
	"ICON" => "/images/cat_list.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 30,
	"PATH" => array(
		"ID" => "mainpage",
		"CHILD" => array(
			"ID" => "tabs",
			"NAME" => "mainpage.tabs",
			"SORT" => 30,
			"CHILD" => array(),
		),
	),
);

?>