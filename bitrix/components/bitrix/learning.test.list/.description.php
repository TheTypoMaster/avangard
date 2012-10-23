<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("LEARNING_TEST_LIST_NAME"),
	"DESCRIPTION" => GetMessage("LEARNING_TEST_LIST_DESC"),
	"ICON" => "/images/test_list.gif",
	"PATH" => array(
		"ID" => "service",
		"CHILD" => array(
			"ID" => "learning",
			"NAME" => GetMessage("LEARNING_SERVICE"),
			"CHILD" => array(
				"ID" => "test",
				"NAME" => GetMessage("LEARNING_TEST_SERVICE")
			),
		),
	)
);


?>