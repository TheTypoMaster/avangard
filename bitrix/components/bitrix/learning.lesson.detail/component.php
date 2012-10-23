<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//Params
$arParams["COURSE_ID"] = (isset($arParams["COURSE_ID"]) && intval($arParams["COURSE_ID"]) > 0 ? intval($arParams["COURSE_ID"]) : intval($_REQUEST["COURSE_ID"]));
$arParams["LESSON_ID"] = (isset($arParams["LESSON_ID"]) && intval($arParams["LESSON_ID"]) > 0 ? intval($arParams["LESSON_ID"]) : intval($_REQUEST["LESSON_ID"]));
$arParams["SELF_TEST_TEMPLATE"] = (strlen($arParams["SELF_TEST_TEMPLATE"]) > 0 ? htmlspecialchars($arParams["SELF_TEST_TEMPLATE"]) : "self.php?SELF_TEST_ID=#SELF_TEST_ID#");
$arParams["CHECK_PERMISSIONS"] = (isset($arParams["CHECK_PERMISSIONS"]) && $arParams["CHECK_PERMISSIONS"]=="N" ? "N" : "Y");

if($this->StartResultCache(false, $USER->GetGroups()))
{
	//Module
	if (!CModule::IncludeModule("learning"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("LEARNING_MODULE_NOT_FOUND"));
		return;
	}

	//Lesson
	$rsLesson = CLesson::GetList(
		Array(), 
		Array(
			"ID" => $arParams["LESSON_ID"],
			"COURSE_ID" => $arParams["COURSE_ID"], 
			"ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => $arParams["CHECK_PERMISSIONS"]
		)
	);

	if (!$arLesson = $rsLesson->GetNext())
	{
		$this->AbortResultCache();
		ShowError(GetMessage("LEARNING_LESSON_DENIED"));
		return;
	}

	//Images
	$arLesson["PREVIEW_PICTURE_ARRAY"] = CFile::GetFileArray($arLesson["PREVIEW_PICTURE"]);
	$arLesson["DETAIL_PICTURE_ARRAY"] = CFile::GetFileArray($arLesson["DETAIL_PICTURE"]);

	//Self test page URL
	$arLesson["SELF_TEST_URL"] = CComponentEngine::MakePathFromTemplate(
		$arParams["SELF_TEST_TEMPLATE"],
		Array(
			"LESSON_ID" => $arParams["LESSON_ID"],
			"SELF_TEST_ID" => $arParams["LESSON_ID"],
			"COURSE_ID" => $arLesson["COURSE_ID"],
		)
	);

	//Self test exists?
	$rsQuestion = CLQuestion::GetList(
		Array(),
		Array(
			"LESSON_ID" => $arParams["LESSON_ID"], 
			"ACTIVE" => "Y",
			"SELF" => "Y",
		)
	);

	$arLesson["SELF_TEST_EXISTS"] = (bool)($rsQuestion->Fetch());

	$arResult = Array(
		"LESSON" => $arLesson
	);

	unset($arLesson);
	unset($rsLesson);
	unset($rsQuestion);

	$this->IncludeComponentTemplate();

}

//Set Title
$arParams["SET_TITLE"] = ($arParams["SET_TITLE"] == "N" ? "N" : "Y" );
if ($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle($arResult["LESSON"]["NAME"]);

?>