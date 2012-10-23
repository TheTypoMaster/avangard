<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);

$strErrorMessage = "";
$BLOG_USER_ID=intval($_POST["BLOG_USER_ID"]);
if(
	$_SERVER["REQUEST_METHOD"]=="POST" &&
	$_POST["save"]!="" &&
	check_bitrix_sessid()
)
{
	if(CModule::IncludeModule("blog"))
	{
		if($BLOG_USER_ID<=0)
			$BLOG_USER_ID=CBlogUser::Add(array(
				"USER_ID" => $USER->GetID(),
				"=LAST_VISIT" => $GLOBALS["DB"]->GetNowFunction(),
				"=DATE_REG" => $GLOBALS["DB"]->GetNowFunction(),
				"ALLOW_POST" => "Y",
			));

		$BlogUser = CBlogUser::GetByID($BLOG_USER_ID);
		if($BlogUser && ($USER->GetID()==$BlogUser["USER_ID"] || $USER -> IsAdmin()))
		{
			$arPICTURE = $_FILES["BLOG_USER_AVATAR"];
			$arPICTURE["old_file"] = $BlogUser["AVATAR"];
			$arPICTURE["del"] = $_POST["BLOG_USER_AVATAR_del"];
			$arHobbyDB=array();
			$arHobby=explode(",", $_POST["BLOG_USER_INTERESTS"]);
			foreach($arHobby as $Hobby)
			{
				$Hobby=trim($Hobby);
				$arHobbyDB[]=$Hobby;
			}
			$arHobbyDB=array_unique($arHobbyDB);
			if(count($arHobbyDB)>0)
				$Hobby=implode(", ", $arHobbyDB);
			else
				$Hobby="";
			$arFields = array(
				"ALIAS" => $_POST["BLOG_USER_ALIAS"],
				"DESCRIPTION" => $_POST["BLOG_USER_DESCRIPTION"],
				"AVATAR" => $arPICTURE,
				"INTERESTS" => $Hobby,
			);
			$DB->StartTransaction();
			$res = CBlogUser::Update($BLOG_USER_ID, $arFields);
			if($res)
			{
				$z = $DB->Query("SELECT PERSONAL_PHOTO FROM b_user WHERE ID='".$BlogUser["USER_ID"]."'");
				$zr = $z->Fetch();
				$arPICTURE = $_FILES["USER_PERSONAL_PHOTO"];
				$arPICTURE["old_file"] = $zr["PERSONAL_PHOTO"];
				$arPICTURE["del"] = $_POST["USER_PERSONAL_PHOTO_del"];

				$arFields = Array(
					"PERSONAL_WWW" => $_POST["USER_PERSONAL_WWW"],
					"PERSONAL_GENDER" => $_POST["USER_PERSONAL_GENDER"],
					"PERSONAL_BIRTHDAY" => $_POST["USER_PERSONAL_BIRTHDAY"],
					"PERSONAL_PHOTO" => $arPICTURE,
				);
				$res = $USER->Update($BlogUser["USER_ID"], $arFields);
				if ($res)
					$DB->Commit();
				else
				{
					$DB->Rollback();
					$strErrorMessage .= $USER->LAST_ERROR;
				}
			}
			else
			{
				$DB->Rollback();
				if($e = $APPLICATION->GetException())
					$strErrorMessage .= $e->GetString();
			}
		}
		else
			$strErrorMessage.= GetMessage("B_B_PU_NO_RIGHTS")."<br>";

	}
	unset($_SESSION["BLOG_USER_PROFILE_ERROR"]);
	if($strErrorMessage!="")
	{
		$_SESSION["BLOG_USER_PROFILE_ERROR"] = array(
			"MESSAGE" => $strErrorMessage,
			"FIELDS"  => $_POST,
		);
		LocalRedirect($_POST["back_url_error"]);
	}
	else
		LocalRedirect($_POST["back_url_ok"]);
}
?>
