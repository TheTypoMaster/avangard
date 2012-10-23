<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
/***********************************************************************
Component for web-form result editing page displaying

This universal component enables to edit result of some created web-forms. This is a standard component and it is included to the module distributive set.

Example of using:

$APPLICATION->IncludeFile("form/result_edit/default.php", array(
	"RESULT_ID" => $_REQUEST["RESULT_ID"],
	"EDIT_ADDITIONAL" => "N",
	"EDIT_STATUS" => "Y",
	"LIST_URL" => "result_list.php",
	"VIEW_URL" => "result_view.php",
	"CHAIN_ITEM_TEXT" => "Forms List",
	"CHAIN_ITEM_LINK" => "result_list.php?WEB_FORM_ID=".$_REQUEST["WEB_FORM_ID"],
	));

Parameters:

$RESULT_ID			- result ID
$EDIT_ADDITIONAL	- [Y|N] - Y - show result fields in the edit form (do not mix up with "questions").
$EDIT_STATUS		- [Y|N] - Y - show dropdown list of result statuses
$LIST_URL			- page URL for redirecting user after "Save" button submitting (no redirection if empty)
$VIEW_URL			- page URL fot result veiwing
$CHAIN_ITEM_TEXT	- additional item name in the navigation chain (no items added if empty)
$CHAIN_ITEM_LINK	- additional item link in the navigation chain

***********************************************************************/

global $USER, $APPLICATION;
$APPLICATION->SetTemplateCSS("form/form.css");
if (CModule::IncludeModule("form"))
{
	IncludeTemplateLangFile(__FILE__);

	// create form output class
	$FORM = new CFormOutput();
	//initialize&check form
	if ($FORM->Init($arParams))
	{
		// output form
		$FORM->Out();
	}
	else
	{
		echo ShowError(GetMessage($FORM->ShowErrorMsg()));
	}
}
else
{
	echo ShowError(GetMessage("FORM_MODULE_NOT_INSTALLED"));
} //endif (CModule::IncludeModule("form"));
?>