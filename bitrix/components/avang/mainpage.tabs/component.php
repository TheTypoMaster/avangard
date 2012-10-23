<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

/*************************************************************************
	Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);



/*************************************************************************
			Work with cache
*************************************************************************/
if($this->StartResultCache($arParams["CACHE_TIME"]))
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	//Выборка новинок
	$arFilter = Array(
		"IBLOCK_ID" => IntVal($arParams["IBLOCK_ID"]),
		"ACTIVE" => "Y",
		"PROPERTY_NOVELTY" => 3,
	);
	$res = CIBlockElement::GetList(
			Array("SORT" => "ASC", "PROPERTY_PRIORITY" => "ASC"), 
			$arFilter, 
			false, 
			false, 
			array("ID", "NAME", "PROPERTY_COLLECTION.NAME", "PROPERTY_FULLCOLOR_PIC", "DETAIL_PAGE_URL")
		);
	while($novelty_el = $res->GetNext()){
		$novelty_el["PROPERTY_FULLCOLOR_PIC_VALUE_SRC"] = CFile::GetPath($novelty_el["PROPERTY_FULLCOLOR_PIC_VALUE"]);
		$arResult["NOVELTIES"][]= $novelty_el;
	}
	//var_dump($arResult["NOVELTIES"]);
	//Выборка хитов продаж
	$arFilter = Array(
		"IBLOCK_ID" => IntVal($arParams["IBLOCK_ID"]),
		"ACTIVE" => "Y",
		"PROPERTY_HIT" => 4,
	);
	$res = CIBlockElement::GetList(
			Array("SORT" => "ASC", "PROPERTY_PRIORITY" => "ASC"), 
			$arFilter, 
			false, 
			false, 
			array("ID", "NAME", "PROPERTY_COLLECTION.NAME", "PROPERTY_FULLCOLOR_PIC", "DETAIL_PAGE_URL")
		);
	while($hit_el = $res->GetNext()){
		$hit_el["PROPERTY_FULLCOLOR_PIC_VALUE_SRC"] = CFile::GetPath($hit_el["PROPERTY_FULLCOLOR_PIC_VALUE"]);
		$arResult["HITS"][]= $hit_el;
	}

	$this->IncludeComponentTemplate();
}


?>
