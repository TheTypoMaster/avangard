<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;
$cp = $this->__component; // объект компонента

if (is_object($cp)){
	global $$arParams["FILTER_NAME"];
	$arrFilter = ${$arParams["FILTER_NAME"]};
	$collection_id= (int)$arrFilter["PROPERTY_COLLECTION"];
	//Выбираем коллекции
	$arSelect = Array("ID", "NAME", "PREVIEW_TEXT", "DETAIL_TEXT", "DATE_ACTIVE_FROM");
	$arFilter = Array("IBLOCK_ID" => 9, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
	if($collection_id!=0){
		$arFilter["ID"]= $collection_id;
	}else{
		$arFilter["!ID"]= 2761;
	}
	$res = CIBlockElement::GetList(Array("SORT" => "ASC", "PROPERTY_PRIORITY" => "ASC"), $arFilter, false, Array("nPageSize" => 50), $arSelect);
	$cat_array = array();
	while($ob = $res->GetNextElement()){
		$arFields = $ob->GetFields();
		$cat_array[$arFields["ID"]]["name"] = $arFields["NAME"];
		$cat_array[$arFields["ID"]]["text"] = $arFields["PREVIEW_TEXT"];
		$cat_array[$arFields["ID"]]["detail_text"] = $arFields["DETAIL_TEXT"];
		$cat_array[$arFields["ID"]]['id'] = $arFields["ID"];
	}
	
	
	// добавим в arResult компонента поля
	$cp->arResult['CAT_ARRAY'] = $cat_array;
	
	//Добавляем ключи arResult, которые мы добавили в result_modifier.php и которые необходимо сохранить в кеше.
	$cp->SetResultCacheKeys(array('CAT_ARRAY'));
	// сохраним их в копии arResult, с которой работает шаблон (с учетом версии main 10.0 и выше)
	if(!isset($arResult['CAT_ARRAY'])){
		$arResult['CAT_ARRAY'] = $cp->arResult['CAT_ARRAY'];
	}

}

?>