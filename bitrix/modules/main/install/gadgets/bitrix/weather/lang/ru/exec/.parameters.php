<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


include(dirname(__FILE__).'/city.php');

asort($arCity);

$arParameters = Array(
		"PARAMETERS"=> Array(
			"CACHE_TIME" => array(
				"NAME" => "����� �����������, ��� (0-�� ����������)",
				"TYPE" => "STRING",
				"DEFAULT" => "3600"
				),
			"SHOW_URL" => Array(
					"NAME" => "���������� ������ �� ��������� ����������",
					"TYPE" => "CHECKBOX",
					"MULTIPLE" => "N",
					"DEFAULT" => "N",
				),
		),
		"USER_PARAMETERS"=> Array(
			"CITY"=>Array(
				"NAME" => "�����",
				"TYPE" => "LIST",
				"MULTIPLE" => "N",
				"DEFAULT" => "c213",
				"VALUES"=>$arCity,
			),
		),
	);

?>
