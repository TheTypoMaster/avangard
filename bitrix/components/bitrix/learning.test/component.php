<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require_once($_SERVER["DOCUMENT_ROOT"].$componentPath."/functions.php");

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
$arParams["PAGE_WINDOW"] = (isset($arParams["PAGE_WINDOW"]) && intval($arParams["PAGE_WINDOW"]) > 0 ? intval($arParams["PAGE_WINDOW"]) : "10");
$arParams["SHOW_TIME_LIMIT"] = (isset($arParams["SHOW_TIME_LIMIT"]) && $arParams["SHOW_TIME_LIMIT"] == "N" ? "N" : "Y");
$arParams["GRADEBOOK_TEMPLATE"] = (strlen($arParams["GRADEBOOK_TEMPLATE"]) > 0 ? htmlspecialchars($arParams["GRADEBOOK_TEMPLATE"]) : "../gradebook.php?TEST_ID=#TEST_ID#");
//$arParams["CHECK_PERMISSIONS"] = (isset($arParams["CHECK_PERMISSIONS"]) && $arParams["CHECK_PERMISSIONS"]=="N" ? "N" : "Y");
$arParams["TEST_ID"] = (isset($arParams["TEST_ID"]) && intval($arParams["TEST_ID"]) > 0 ? intval($arParams["TEST_ID"]) : intval($_REQUEST["TEST_ID"]));
$arParams["COURSE_ID"] = (isset($arParams["COURSE_ID"]) && intval($arParams["COURSE_ID"]) > 0 ? intval($arParams["COURSE_ID"]) : intval($_REQUEST["COURSE_ID"]));

if (strlen($arParams["PAGE_NUMBER_VARIABLE"]) <=0 || !preg_match("#^[A-Za-z_][A-Za-z01-9_]*$#", $arParams["PAGE_NUMBER_VARIABLE"]))
	$arParams["PAGE_NUMBER_VARIABLE"] = "PAGE";

//Test
$rsTest = CTest::GetList(
	Array(), 
	Array(
		"ID" => $arParams["TEST_ID"],
		"ACTIVE" => "Y",
		"COURSE_ID" => $arParams["COURSE_ID"],
		//"CHECK_PERMISSIONS" => $arParams["CHECK_PERMISSIONS"]
	)
);



if (!$arTest = $rsTest->GetNext())
{
	ShowError(GetMessage("LEARNING_TEST_DENIED"));
	return;
}


//Session variables
$sessAttemptID =& $_SESSION["LEARN_".$arParams["TEST_ID"]."_ATTEMPT_ID"];
$sessAttemptFinished =& $_SESSION["LEARN_".$arParams["TEST_ID"]."_FINISHED"];
$sessAttemptError =& $_SESSION["LEARN_".$arParams["TEST_ID"]."_ERROR"];

//Pàge url template
$currentPage = GetPagePath(false, false);
$queryString= htmlspecialchars(DeleteParam(array($arParams["PAGE_NUMBER_VARIABLE"], "SEF_APPLICATION_CUR_PAGE_URL")));
$pageTemplate = (
	$queryString == "" ? 
	$currentPage."?".$arParams["PAGE_NUMBER_VARIABLE"]."=#PAGE_ID#" :
	$currentPage."?".$queryString."&amp;".$arParams["PAGE_NUMBER_VARIABLE"]."=#PAGE_ID#"
);

//arResult
$arResult = Array(
	"TEST" => $arTest,
	"QUESTION" => Array(),
	"QBAR" => Array(),
	"NAV" => Array(
		"PAGE_COUNT" => 0, //pages count
		"PAGE_NUMBER" => 1, //current page id
		"NEXT_QUESTION" => 0, //next question page id
		"PREV_QUESTION" => 0, //previous question page id
		"FIRST_NOANSWER" => 0,
		"NEXT_NOANSWER" => 0,
		"PREV_NOANSWER" => 0,
		"START_PAGE" => 0,
		"END_PAGE" => 0,
	),
	"PAGE_TEMPLATE" => $pageTemplate,
	"GRADEBOOK_URL" => CComponentEngine::MakePathFromTemplate($arParams["GRADEBOOK_TEMPLATE"], Array("FOR_TEST_ID" => $arParams["TEST_ID"],"COURSE_ID" => $arTest["COURSE_ID"])),
	"PREVIOUS_PAGE" => "",
	"NEXT_PAGE" => "",
	"ACTION_PAGE" => "",
	"REDIRECT_PAGE" => "",
	"TEST_FINISHED" => $sessAttemptFinished,
	"ERROR_MESSAGE" => $sessAttemptError,
	"SECONDS_TO_END" => 0,
	"SECONDS_TO_END_STRING" => 0,
);

//Action form page
if ($_SERVER['REDIRECT_STATUS'] == '404' || isset($_REQUEST["SEF_APPLICATION_CUR_PAGE_URL"]))
	$arResult["ACTION_PAGE"] = POST_FORM_ACTION_URI;
else
	$arResult["ACTION_PAGE"] = $currentPage.($queryString == "" ? "" : "?".$queryString);

//Page number
if (array_key_exists($arParams["PAGE_NUMBER_VARIABLE"], $_REQUEST) && intval($_REQUEST[$arParams["PAGE_NUMBER_VARIABLE"]]) > 1)
	$arResult["NAV"]["PAGE_NUMBER"] = intval($_REQUEST[$arParams["PAGE_NUMBER_VARIABLE"]]);

//Redirect page
if (!empty($_REQUEST["back_page"]))
	$arResult["REDIRECT_PAGE"] = $_REQUEST["back_page"];
else
	$arResult["REDIRECT_PAGE"] = str_replace(
		"#PAGE_ID#", 
		(array_key_exists($arParams["PAGE_NUMBER_VARIABLE"], $_REQUEST) ? $arResult["NAV"]["PAGE_NUMBER"]+1 : $arResult["NAV"]["PAGE_NUMBER"]), 
		$arResult["PAGE_TEMPLATE"]
	);

$sessAttemptError = null;
$sessAttemptFinished = null;

//Title
$arParams["SET_TITLE"] = ($arParams["SET_TITLE"] == "N" ? "N" : "Y" );
if ($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle($arResult["TEST"]["NAME"]);

//Actions
$bTestCreate = ($_SERVER["REQUEST_METHOD"]=="POST" && !isset($sessAttemptID));
$bPostAnswer = ($_SERVER["REQUEST_METHOD"]=="POST" && isset($sessAttemptID) && $_POST["ANSWERED"] == "Y");

if ($bTestCreate)
{
	//If old attempt exists?
	if ($arAttempt = _AttemptExists($arParams["TEST_ID"]))
	{
		$sessAttemptID = $arAttempt["ID"];
		LocalRedirect($arResult["REDIRECT_PAGE"]);
	}

	//Check attempt limit
	if ($arTest["ATTEMPT_LIMIT"] > 0 && $arTest["ATTEMPT_LIMIT"] <= CTestAttempt::GetCount($arParams["TEST_ID"], $USER->GetID()))
	{
		$sessAttemptError = GetMessage("LEARNING_LIMIT_ERROR");
		LocalRedirect($arResult["REDIRECT_PAGE"]);
	}

	//Add new attempt
	$rsAttempt = new CTestAttempt();
	$attemptID = $rsAttempt->Add(Array("TEST_ID" => $arParams["TEST_ID"], "STUDENT_ID" => $USER->GetID(), "STATUS" => "B"));
	if(!$attemptID)
	{
		$sessAttemptError = ( ($ex = $APPLICATION->GetException()) ? $ex->GetString() : GetMessage("LEARNING_ATTEMPT_CREATE_ERROR"));
		LocalRedirect($arResult["REDIRECT_PAGE"]);
	}

	//Create test questions
	$success = CTestAttempt::CreateAttemptQuestions($attemptID);
	if(!$success)
	{
		$sessAttemptError = ( ($ex = $APPLICATION->GetException()) ? $ex->GetString() : GetMessage("LEARNING_ATTEMPT_CREATE_ERROR"));
		CTestAttempt::Delete($attemptID);
		LocalRedirect($arResult["REDIRECT_PAGE"]);
	}

	$sessAttemptID = $attemptID;
	LocalRedirect($arResult["REDIRECT_PAGE"]);
}
elseif ($bPostAnswer)
{
	//Check attempt from session
	if (_AttemptExists($arParams["TEST_ID"], $sessAttemptID) === false)
	{
		$sessAttemptID = null;
		$sessAttemptError = GetMessage("LEARNING_ATTEMPT_NOT_FOUND_ERROR");
		LocalRedirect($arResult["REDIRECT_PAGE"]);
	}

	//User wants to finish
	if (strlen($_REQUEST["finish"])>0)
	{
		$rsAttempt = new CTestAttempt;
		$rsAttempt->AttemptFinished($sessAttemptID);
		$sessAttemptID = null;
		$sessAttemptFinished = true;
		LocalRedirect($arResult["REDIRECT_PAGE"]);
	}

	//Check test result
	$arFields = Array("ID" => intval($_REQUEST["TEST_RESULT"]),"ATTEMPT_ID" => $sessAttemptID);

	if ($arTest["PASSAGE_TYPE"] < 2)
		$arFields["ANSWERED"] = "N";

	$rsTestResult = CTestResult::GetList(array(),$arFields);
	if(!$arTestResult = $rsTestResult->GetNext())
	{
		$sessAttemptID = null;
		$sessAttemptError = GetMessage("LEARNING_RESPONSE_SAVE_ERROR");
		LocalRedirect($arResult["REDIRECT_PAGE"]);
	}

	//Save User answer
	if ($arTest["PASSAGE_TYPE"] == 0 || array_key_exists("answer", $_REQUEST))
	{
		$success = CTestResult::AddResponse(intval($_REQUEST["TEST_RESULT"]), $_REQUEST["answer"]);
		if(!$success)
		{
			$sessAttemptID = null;
			$sessAttemptError = ( ($ex = $APPLICATION->GetException()) ? $ex->GetString() : GetMessage("LEARNING_RESPONSE_SAVE_ERROR"));
			LocalRedirect($arResult["REDIRECT_PAGE"]);
		}
	}

	//If it was the last question, finish the attempt
	if ($arTest["PASSAGE_TYPE"] < 2)
	{
		$arProgress = CTestResult::GetProgress($sessAttemptID);
		if($arProgress["TODO"]==0)
		{
			$rsTestAttempt = new CTestAttempt;
			$rsTestAttempt->AttemptFinished($sessAttemptID);
			$sessAttemptID = null;
			$sessAttemptFinished = true;
		}
	}

	LocalRedirect($arResult["REDIRECT_PAGE"]);
}
elseif (isset($sessAttemptID))
{
	//Check attempt from session
	if (!$arAttempt = _AttemptExists($arParams["TEST_ID"], $sessAttemptID))
	{
		$sessAttemptID = null;
		$sessAttemptError = GetMessage("LEARNING_ATTEMPT_NOT_FOUND_ERROR");
		LocalRedirect($arResult["REDIRECT_PAGE"]);
	}

	//Check time limit
	if ( $arTest["TIME_LIMIT"]>0 && ( ($arTest["TIME_LIMIT"]*60) < (time()-MakeTimeStamp($arAttempt["DATE_START"]))) )
	{
		$rsTestAttempt = new CTestAttempt;
		$rsTestAttempt->AttemptFinished($sessAttemptID);
		$sessAttemptID = null;
		$sessAttemptFinished = true;
		$sessAttemptError = GetMessage("LEARNING_TIME_LIMIT");
		LocalRedirect($arResult["REDIRECT_PAGE"]);
	}
	elseif($arTest["TIME_LIMIT"]>0)
	{
		$arResult["SECONDS_TO_END"] = $arTest["TIME_LIMIT"]*60 - (time()-MakeTimeStamp($arAttempt["DATE_START"]));
		$arResult["SECONDS_TO_END_STRING"] = _TimeToStringFormat($arResult["SECONDS_TO_END"]);
	}

	//If there are no questions, finish the attempt
	if ($arTest["PASSAGE_TYPE"] < 2)
	{
		$arProgress = CTestResult::GetProgress($sessAttemptID);
		if($arProgress["TODO"]==0)
		{
			$rsTestAttempt = new CTestAttempt;
			$rsTestAttempt->AttemptFinished($sessAttemptID);
			$sessAttemptID = null;
			$sessAttemptFinished = true;
			LocalRedirect($arResult["REDIRECT_PAGE"]);
		}
	}

	//Get questions
	$rsTestResult = CTestResult::GetList(Array("ID"=>"ASC"), Array("ATTEMPT_ID" => $sessAttemptID));
	$rsTestResult->NavStart(10000);
	$arResult["NAV"]["PAGE_COUNT"] = $rsTestResult->SelectedRowsCount();

	//If no questions
	if ($arResult["NAV"]["PAGE_COUNT"] <= 0)
	{
		$rsTestAttempt = new CTestAttempt;
		$rsTestAttempt->AttemptFinished($sessAttemptID);
		$sessAttemptID = null;
		$sessAttemptFinished = true;
		LocalRedirect($arResult["REDIRECT_PAGE"]);
	}

	if ($arResult["NAV"]["PAGE_NUMBER"] > $arResult["NAV"]["PAGE_COUNT"])
		$arResult["NAV"]["PAGE_NUMBER"] = 1;

	$questionPageIndex = 1;
	while ($arAttemptQuestion = $rsTestResult->GetNext())
	{
		if (!$arResult["NAV"]["FIRST_NOANSWER"] && $arAttemptQuestion["ANSWERED"] == "N")
			$arResult["NAV"]["FIRST_NOANSWER"] = $questionPageIndex;

		$inaccessible = (
			($arTest["PASSAGE_TYPE"] < 2  && $arAttemptQuestion["ANSWERED"] == "Y") || 
			($arTest["PASSAGE_TYPE"] == 0 && $arAttemptQuestion["ANSWERED"] == "N")
		);

		if ($arResult["NAV"]["FIRST_NOANSWER"] == $questionPageIndex )
			$inaccessible = false;

		if (!$inaccessible)
		{
			if ($questionPageIndex < $arResult["NAV"]["PAGE_NUMBER"])
				$arResult["NAV"]["PREV_QUESTION"] = $questionPageIndex;
			elseif (!$arResult["NAV"]["NEXT_QUESTION"] && $questionPageIndex > $arResult["NAV"]["PAGE_NUMBER"])
				$arResult["NAV"]["NEXT_QUESTION"] = $questionPageIndex;

			if ($arAttemptQuestion["ANSWERED"] == "N")
			{
				if (!$arResult["NAV"]["NEXT_NOANSWER"] && $questionPageIndex > $arResult["NAV"]["PAGE_NUMBER"])
					$arResult["NAV"]["NEXT_NOANSWER"] = $questionPageIndex;
				elseif ($questionPageIndex < $arResult["NAV"]["PAGE_NUMBER"])
					$arResult["NAV"]["PREV_NOANSWER"] = $questionPageIndex;
			}
		}

		$arResult["QBAR"][$questionPageIndex] = Array(
			"ID" => $arAttemptQuestion["ID"], 
			"URL" => str_replace("#PAGE_ID#", $questionPageIndex, $arResult["PAGE_TEMPLATE"]),
			"ANSWERED" => $arAttemptQuestion["ANSWERED"], 
			"QUESTION_ID" => $arAttemptQuestion["QUESTION_ID"], 
			"RESPONSE" => explode(",",$arAttemptQuestion["RESPONSE"]),
			"INACCESSIBLE" => $inaccessible,
		);

		$questionPageIndex++;
	}

	//Pages
	if ($arResult["NAV"]["PREV_QUESTION"])
		$arResult["PREVIOUS_PAGE"] = str_replace("#PAGE_ID#", $arResult["NAV"]["PREV_QUESTION"], $arResult["PAGE_TEMPLATE"]);
	if ($arResult["NAV"]["NEXT_QUESTION"])
		$arResult["NEXT_PAGE"] = str_replace("#PAGE_ID#", $arResult["NAV"]["NEXT_QUESTION"], $arResult["PAGE_TEMPLATE"]);



	//$arResult["ACTION_PAGE"] = str_replace("#PAGE_ID#", $arResult["NAV"]["PAGE_NUMBER"] + 1, $arResult["PAGE_TEMPLATE"]);

	if (!empty($arResult["QBAR"]) && array_key_exists($arResult["NAV"]["PAGE_NUMBER"], $arResult["QBAR"]))
	{
		//If user get inaccessible question
		if ($arResult["QBAR"][$arResult["NAV"]["PAGE_NUMBER"]]["INACCESSIBLE"])
		{
			if ($arResult["NAV"]["NEXT_QUESTION"] || $arResult["NAV"]["PREV_QUESTION"] || $arResult["NAV"]["FIRST_NOANSWER"])
			{
				$page = (
					$arResult["NAV"]["NEXT_QUESTION"] ? 
						$arResult["NAV"]["NEXT_QUESTION"] : 
						(
							$arResult["NAV"]["FIRST_NOANSWER"] ? 
							$arResult["NAV"]["FIRST_NOANSWER"] :
							$arResult["NAV"]["PREV_QUESTION"]
						)
				);

				LocalRedirect(
					str_replace("#PAGE_ID#", $page, $arResult["PAGE_TEMPLATE"])
				);

			}
		}

		if ($arResult["NAV"]["PAGE_NUMBER"] > floor($arParams["PAGE_WINDOW"]/2) + 1 && $arResult["NAV"]["PAGE_COUNT"] > $arParams["PAGE_WINDOW"])
			$arResult["NAV"]["START_PAGE"] = $arResult["NAV"]["PAGE_NUMBER"] - floor($arParams["PAGE_WINDOW"]/2);
		else
			$arResult["NAV"]["START_PAGE"] = 1;

		if ( ($arResult["NAV"]["PAGE_NUMBER"] <= $arResult["NAV"]["PAGE_COUNT"] - floor($arParams["PAGE_WINDOW"]/2) ) &&
			 ($arResult["NAV"]["START_PAGE"] + $arParams["PAGE_WINDOW"]-1 <= $arResult["NAV"]["PAGE_COUNT"])
			)
		{
			
				$arResult["NAV"]["END_PAGE"] = $arResult["NAV"]["START_PAGE"] + $arParams["PAGE_WINDOW"] - 1;
		}
		else
		{
			$arResult["NAV"]["END_PAGE"] = $arResult["NAV"]["PAGE_COUNT"];
			if ( ($arResult["NAV"]["END_PAGE"] - $arParams["PAGE_WINDOW"] + 1) >= 1)
				$arResult["NAV"]["START_PAGE"] = $arResult["NAV"]["END_PAGE"] - $arParams["PAGE_WINDOW"] + 1;
		}


		$rsQuestion = CLQuestion::GetList(array(),array("ID"=>$arResult["QBAR"][$arResult["NAV"]["PAGE_NUMBER"]]["QUESTION_ID"]));
		$arResult["QUESTION"] = $rsQuestion->GetNext();
		$arResult["QUESTION"]["FILE"] = CFile::GetFileArray($arResult["QUESTION"]["FILE_ID"]);

		//Answers
		$arResult["QUESTION"]["ANSWERS"] = Array();

		$arSort = (
			$arTest["RANDOM_ANSWERS"] == "Y" ? 
			Array("RAND" => "RAND", "SORT" => "ASC") : 
			Array("SORT" => "ASC")
		);
		$rsAnswer = CLAnswer::GetList($arSort, Array("QUESTION_ID" => $arResult["QUESTION"]["ID"]));
		while($arAnswer = $rsAnswer->GetNext())
		{
			$arResult["QUESTION"]["ANSWERS"][] = $arAnswer;
		}
	}
}

$this->IncludeComponentTemplate();
?>