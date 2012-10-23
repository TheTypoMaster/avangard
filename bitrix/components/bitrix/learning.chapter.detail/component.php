<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//Params
$arParams["COURSE_ID"] = (isset($arParams["COURSE_ID"]) && intval($arParams["COURSE_ID"]) > 0 ? intval($arParams["COURSE_ID"]) : intval($_REQUEST["COURSE_ID"]));
$arParams["CHAPTER_ID"] = (isset($arParams["CHAPTER_ID"]) && intval($arParams["CHAPTER_ID"]) > 0 ? intval($arParams["CHAPTER_ID"]) : intval($_REQUEST["CHAPTER_ID"]));
$arParams["CHAPTER_DETAIL_TEMPLATE"] = (strlen($arParams["CHAPTER_DETAIL_TEMPLATE"]) > 0 ? htmlspecialchars($arParams["CHAPTER_DETAIL_TEMPLATE"]): "chapter.php?CHAPTER_ID=#CHAPTER_ID#");
$arParams["LESSON_DETAIL_TEMPLATE"] = (strlen($arParams["LESSON_DETAIL_TEMPLATE"]) > 0 ? htmlspecialchars($arParams["LESSON_DETAIL_TEMPLATE"]) : "lesson.php?LESSON_ID=#LESSON_ID#");
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

	//Chapter
	$rsChapter = CChapter::GetList(
		Array(), 
		Array(
			"ID" => $arParams["CHAPTER_ID"], 
			"COURSE_ID" => $arParams["COURSE_ID"],
			"ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => $arParams["CHECK_PERMISSIONS"]
		)
	);

	if (!$arChapter = $rsChapter->GetNext())
	{
		$this->AbortResultCache();
		ShowError(GetMessage("LEARNING_CHAPTER_DENIED"));
		return;
	}

	//Images
	$arChapter["PREVIEW_PICTURE_ARRAY"] = CFile::GetFileArray($arChapter["PREVIEW_PICTURE"]);
	$arChapter["DETAIL_PICTURE_ARRAY"] = CFile::GetFileArray($arChapter["DETAIL_PICTURE"]);

	$arResult = Array(
		"CHAPTER" => $arChapter,
		"CONTENTS" => Array()
	);


	//Included chapters and lessons
	$rsContent = CCourse::GetCourseContent($arChapter["COURSE_ID"], Array());
	$foundChapter = false;
	while ($arContent = $rsContent->GetNext())
	{
		if ($foundChapter)
		{
			if ($arContent["DEPTH_LEVEL"] <= $baseDepthLevel)
				break;

			$arContent["DEPTH_LEVEL"] -= $baseDepthLevel;

			if ($arContent["TYPE"] == "CH")
				$arContent["URL"] = CComponentEngine::MakePathFromTemplate(
					$arParams["CHAPTER_DETAIL_TEMPLATE"],
					Array(
						"CHAPTER_ID" => $arContent["ID"],
						"COURSE_ID" => $arChapter["COURSE_ID"]
					)
				);
			else
				$arContent["URL"] = CComponentEngine::MakePathFromTemplate(
					$arParams["LESSON_DETAIL_TEMPLATE"],
					Array(
						"LESSON_ID" => $arContent["ID"],
						"COURSE_ID" => $arChapter["COURSE_ID"]
					)
				);

			$arResult["CONTENTS"][] = $arContent;
		}

		if ($arContent["ID"]==$arChapter["ID"] && $arContent["TYPE"]=="CH")
		{
			$foundChapter = true;
			$baseDepthLevel = $arContent["DEPTH_LEVEL"];
		}

	}

	unset($rsContent);
	unset($arContent);
	unset($rsChapter);
	unset($arChapter);

	$this->IncludeComponentTemplate();
}

//Set Title
$arParams["SET_TITLE"] = ($arParams["SET_TITLE"] == "N" ? "N" : "Y" );
if ($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle($arResult["CHAPTER"]["NAME"]);
?>