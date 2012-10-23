<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arMode = array(
	"ICON" => GetMessage("GD_INDEX_P_MODE_ICON"),
	"LIST" => GetMessage("GD_INDEX_P_MODE_LIST"),
);

$arParameters = Array(
	"PARAMETERS"=> Array(),
	"USER_PARAMETERS"=> Array(
		"MODE" => Array(
			"NAME" => GetMessage("GD_INDEX_P_MODE"),
			"TYPE" => "LIST",
			"VALUES" => $arMode,
			"MULTIPLE" => "N",
			"DEFAULT" => "ICON"
		),
	)
);
?>