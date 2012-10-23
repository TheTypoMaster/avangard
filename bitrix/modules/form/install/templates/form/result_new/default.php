<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
/***********************************************************************
Component for empty form representation for filling

This universal component is for a web-form output. This is a standard component, it is included to module distributive

example of using:

$APPLICATION->IncludeFile("form/result_new/default.php", array(
	"WEB_FORM_ID" => $_REQUEST["WEB_FORM_ID"],
	"LIST_URL" => "result_list.php",
	"EDIT_URL" => "result_edit.php",
	"CHAIN_ITEM_TEXT" => "Forms List",
	"CHAIN_ITEM_LINK" => "result_list.php?WEB_FORM_ID=".$_REQUEST["WEB_FORM_ID"]
));

Parameters:

$WEB_FORM_ID		- web-form ID
$LIST_URL			- page URL for redirecting user after "Save" button press (if empty, there is no redirecting)
$EDIT_URL			- page URL for redirecting user after "Apply" button press (if empty, there is no redirecting)
$CHAIN_ITEM_TEXT	- additional item name in the navigation chain (if empty, no item is added)
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