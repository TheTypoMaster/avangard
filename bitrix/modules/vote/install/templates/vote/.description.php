<?
IncludeTemplateLangFile(__FILE__);

$arrChannels = array();
$arrVotes = array();
$arrQuestions = array();
if (CModule::IncludeModule("vote"))
{
	$arr = CMainAdmin::GetTemplateList(COption::GetOptionString("vote", "VOTE_TEMPLATE_PATH_QUESTION_NEW"));
	if(is_array($arr))
	{
		reset($arr);
		$arrTemplates = array();
		$arrTemplates[" "] = GetMessage("VOTE_BY_DEFAULT");
		foreach($arr as $template) $arrTemplates[$template] = $template;
	}
	$rs = CVoteChannel::GetList($v1, $v2, array(), $v3);
	while ($arChannel=$rs->Fetch()) 
	{
		$arrChannels[$arChannel["SID"]] = "[".$arChannel["SID"]."] ".$arChannel["TITLE"];	

		$rsVotes = CVote::GetList($v1, $v2, array("CHANNEL_ID" => $arChannel["ID"]), $v3);
		while ($arVote = $rsVotes->Fetch())
		{
			$arrVotes[$arVote["ID"]] = "[".$arVote["ID"]."] (".$arChannel["SID"].") ".TruncateText($arVote["TITLE"],40);
		}
	}	
	
	if (intval($arCurrentValues["VOTE_ID"])>0)
	{
		$rsQuestions = CVoteQuestion::GetList($arCurrentValues["VOTE_ID"], $vv1, $vv2, array(), $vv3);
		while ($arQuestion = $rsQuestions->Fetch())
		{
			$QUESTION = ($arQuestion["QUESTION_TYPE"]=="html") ? strip_tags($arQuestion["QUESTION"]) : $arQuestion["QUESTION"];
			$QUESTION = TruncateText($QUESTION, 40);
			$arrQuestions["QUESTION_TEMPLATE_".$arQuestion["ID"]] = array(
				"NAME"				=> str_replace("#QUESTION#",$QUESTION,GetMessage("VOTE_TEMPLATE_FOR_QUESTION")),
				"TYPE"				=> "LIST",
				"ADDITIONAL_VALUES"	=> "N",
				"VALUES"			=> $arrTemplates
				);
		}
	}
}
$sSectionName = GetMessage("VOTE_TEMPLATE_SECTION_NAME");

$arTemplateDescription["vote_new"] = array(
	"NAME"          => GetMessage("VOTE_NEW_NAME"),
	"DESCRIPTION"   => GetMessage("VOTE_NEW_DECSRIPTION"),
	"SEPARATOR"     => "Y",
	);
	   
$arTemplateDescription["vote_new/default.php"] = array(
	"NAME" => GetMessage("VOTE_NEW_DEFAULT_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("VOTE_NEW_DEFAULT_TEMPLATE_DESCRIPTION"),
	"ICON" => "/bitrix/images/vote/components/vote_form.gif",
	"PARAMS" => array(			
		"VOTE_ID" => array(
			"NAME" => GetMessage("VOTE_POLL_ID"), 
			"TYPE" => "LIST",
			"VALUES" => $arrVotes,
			"DEFAULT" => "={\$_REQUEST[\"VOTE_ID\"]}"
			),
		"BACK_REDIRECT_URL" => array(
			"NAME" => GetMessage("VOTE_REDIRECT_PAGE"), 
			"TYPE" => "STRING",
			"DEFAULT" => "vote_result.php"
			)
		)
	);

$arTemplateDescription["vote_new/main_page.php"] = array(
	"NAME" => GetMessage("VOTE_NEW_MAIN_PAGE_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("VOTE_NEW_MAIN_PAGE_TEMPLATE_DESCRIPTION"),
	"ICON" => "/bitrix/images/vote/components/vote_form.gif",
	"PARAMS" => array(			
		"VOTE_ID" => array(
			"NAME" => GetMessage("VOTE_POLL_ID"), 
			"TYPE" => "LIST",
			"VALUES" => $arrVotes,
			"DEFAULT" => "={\$_REQUEST[\"VOTE_ID\"]}"
			),
		"BACK_REDIRECT_URL" => array(
			"NAME" => GetMessage("VOTE_REDIRECT_PAGE"), 
			"TYPE" => "STRING",
			"DEFAULT" => "vote_result.php"
			)
		)
	);

$arTemplateDescription["vote_new/current_channel.php"] = array(
	"NAME"			=> GetMessage("VOTE_CURRENT_CHANNEL_TEMPLATE_NAME"),
	"DESCRIPTION"	=> GetMessage("VOTE_CURRENT_CHANNEL_TEMPLATE_DESCRIPTION"),
	"ICON"			=> "/bitrix/images/vote/components/current_channel.gif",
	"PARENT"		=> "vote_new",
	"PARAMS"		=> array(			
		"CHANNEL_SID" => array(
			"NAME" => GetMessage("VOTE_CHANNEL_SID"), 
			"TYPE" => "LIST",
			"VALUES" => $arrChannels
			)
		)
	);

$arTemplateDescription["vote_result"] = array(
	"NAME"          => GetMessage("VOTE_RESULT_NAME"),
	"DESCRIPTION"   => GetMessage("VOTE_RESULT_DECSRIPTION"),
	"SEPARATOR"     => "Y",
	);

$arTemplateDescription["vote_result/default.php"] = array(
	"NAME" => GetMessage("VOTE_RESULT_DEFAULT_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("VOTE_RESULT_DEFAULT_TEMPLATE_DESCRIPTION"),
	"ICON" => "/bitrix/images/vote/components/vote_result.gif",
	"PARAMS" => array(			
		"VOTE_ID" => array(
			"NAME" => GetMessage("VOTE_POLL_ID"), 
			"TYPE" => "LIST",
			"VALUES" => $arrVotes,
			"REFRESH" => "Y",
			"DEFAULT" => "={\$_REQUEST[\"VOTE_ID\"]}"
			)
		)
	);
$arTemplateDescription["vote_result/default.php"]["PARAMS"] = array_merge($arTemplateDescription["vote_result/default.php"]["PARAMS"], $arrQuestions);
		
$arTemplateDescription["vote_result/main_page.php"] = array(
	"NAME" => GetMessage("VOTE_RESULT_MAIN_PAGE_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("VOTE_RESULT_MAIN_PAGE_TEMPLATE_DESCRIPTION"),
	"ICON" => "/bitrix/images/vote/components/vote_result.gif",
	"PARAMS" => array(			
		"VOTE_ID" => array(
			"NAME" => GetMessage("VOTE_POLL_ID"), 
			"TYPE" => "LIST",
			"VALUES" => $arrVotes,
			"REFRESH" => "Y",
			"DEFAULT" => "={\$_REQUEST[\"VOTE_ID\"]}"
			)
		)
	);
$arTemplateDescription["vote_result/main_page.php"]["PARAMS"] = array_merge($arTemplateDescription["vote_result/main_page.php"]["PARAMS"], $arrQuestions);

$arTemplateDescription["vote_result/border.php"] = array(
	"NAME" => GetMessage("VOTE_RESULT_BORDER_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("VOTE_RESULT_BORDER_TEMPLATE_DESCRIPTION"),
	"ICON" => "/bitrix/images/vote/components/vote_result.gif",
	"PARAMS" => array(			
		"VOTE_ID" => array(
			"NAME" => GetMessage("VOTE_POLL_ID"), 
			"TYPE" => "LIST",
			"VALUES" => $arrVotes,
			"REFRESH" => "Y",
			"DEFAULT" => "={\$_REQUEST[\"VOTE_ID\"]}"
			)
		)
	);
$arTemplateDescription["vote_result/border.php"]["PARAMS"] = array_merge($arTemplateDescription["vote_result/border.php"]["PARAMS"], $arrQuestions);

$arTemplateDescription["vote_list"] = array(
	"NAME"          => GetMessage("VOTE_LIST_NAME"),
	"DESCRIPTION"   => GetMessage("VOTE_LIST_DECSRIPTION"),
	"SEPARATOR"     => "Y",
	);

$arTemplateDescription["vote_list/default.php"] = array(
	"NAME" => GetMessage("VOTE_LIST_DEFAULT_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("VOTE_LIST_DEFAULT_TEMPLATE_DESCRIPTION"),
	"ICON" => "/bitrix/images/vote/components/vote_list.gif",
	"PARAMS" => array(			
		"CHANNEL_SID" => array(
			"NAME" => GetMessage("VOTE_CHANNEL_SID"), 
			"TYPE" => "LIST",
			"VALUES" => $arrChannels,
			"DEFAULT" => ""
			),
		"VOTE_URL" => array(
			"NAME" => GetMessage("VOTE_EMPTY_FORM_PAGE"), 
			"TYPE" => "STRING",
			"DEFAULT" => "vote_new.php"
			),
		"RESULTS_URL" => array(
			"NAME" => GetMessage("VOTE_RESULT_PAGE"), 
			"TYPE" => "STRING",
			"DEFAULT" => "vote_result.php"
			)
		)
	);
?>