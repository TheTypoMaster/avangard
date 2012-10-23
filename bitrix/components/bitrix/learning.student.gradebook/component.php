<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//Authorized?
if (!$USER->IsAuthorized())
	$APPLICATION->AuthForm(GetMessage("LEARNING_NO_AUTHORIZE"));

//Module
if (!CModule::IncludeModule("learning"))
{
	ShowError(GetMessage("LEARNING_MODULE_NOT_FOUND"));
	return;
}

//Params
$arParams["TEST_DETAIL_TEMPLATE"] = (strlen($arParams["TEST_DETAIL_TEMPLATE"]) > 0 ? htmlspecialchars($arParams["TEST_DETAIL_TEMPLATE"]) : "course/test.php?TEST_ID=#TEST_ID#");
$arParams["COURSE_DETAIL_TEMPLATE"] = (strlen($arParams["COURSE_DETAIL_TEMPLATE"]) > 0 ? htmlspecialchars($arParams["COURSE_DETAIL_TEMPLATE"]) : "course/index.php?COURSE_ID=#COURSE_ID#");

if (strlen($arParams["TEST_ID_VARIABLE"]) <=0 || !preg_match("#^[A-Za-z_][A-Za-z01-9_]*$#", $arParams["TEST_ID_VARIABLE"]))
	$arParams["TEST_ID_VARIABLE"] = "TEST_ID";

//Set Title
$arParams["SET_TITLE"] = ($arParams["SET_TITLE"] == "N" ? "N" : "Y" );
if ($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle(GetMessage("LEARNING_PROFILE_TITLE"));

$currentPage = GetPagePath(false, false);
$queryString= htmlspecialchars(DeleteParam(array($arParams["TEST_ID_VARIABLE"])));

$arResult = Array(
	"RECORDS" => Array(),
	"ATTEMPTS" => Array(),
	"CURRENT_PAGE" => $currentPage.($queryString == "" ? "":"?").$queryString,
);

//GradeBook
$rsGradebook = CGradeBook::GetList(
	Array("ID"=>"DESC"),
	Array(
		"STUDENT_ID"=>intval($USER->GetID()),
		"SITE_ID" => LANG,
		"TEST_ID" => (array_key_exists($arParams["TEST_ID_VARIABLE"], $_REQUEST) ? intval($_REQUEST[$arParams["TEST_ID_VARIABLE"]]) : "")
	)
);
while ($arGradebook = $rsGradebook->GetNext())
{
	//Test Url
	$arGradebook["TEST_DETAIL_URL"] = CComponentEngine::MakePathFromTemplate(
		$arParams["TEST_DETAIL_TEMPLATE"],
		Array(
			"TEST_ID" => $arGradebook["TEST_ID"],
			"COURSE_ID" => $arGradebook["COURSE_ID"],
		)
	);

	//Course Url
	
	$arGradebook["COURSE_DETAIL_URL"] = CComponentEngine::MakePathFromTemplate($arParams["COURSE_DETAIL_TEMPLATE"], Array("COURSE_ID" => $arGradebook["COURSE_ID"]));
	$arGradebook["ATTEMPT_DETAIL_URL"] = $arResult["CURRENT_PAGE"].($queryString == "" ? "?":"&").$arParams["TEST_ID_VARIABLE"]."=".$arGradebook["TEST_ID"];

	$arResult["RECORDS"][] = $arGradebook;
}

unset($rsGradebook);
unset($arGradebook);


//Attempts
if (array_key_exists($arParams["TEST_ID_VARIABLE"], $_REQUEST) && intval($_REQUEST[$arParams["TEST_ID_VARIABLE"]]) > 0)
{
	$rsAttempt = CTestAttempt::GetList(
		Array("ID" => "DESC"), 
		Array(
			"TEST_ID"=> intval($_REQUEST[$arParams["TEST_ID_VARIABLE"]]), 
			"STUDENT_ID" => intval($USER->GetID())
		)
	);

	while ($arAttempt = $rsAttempt->GetNext())
	{
		$arResult["ATTEMPTS"][] = $arAttempt;
	}

	unset($arAttempt);
	unset($rsAttempt);
}

$this->IncludeComponentTemplate();
?>