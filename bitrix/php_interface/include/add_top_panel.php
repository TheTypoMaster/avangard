<?
//Example how to add a button to top panel
//This file is included when function
//$APPLICATION->ShowPanel(); is executed in prolog

if ($APPLICATION->GetCurPage() == SITE_DIR."about/contacts.php")
{
	$FORM_RIGHT = $APPLICATION->GetGroupRight("form");
	if($FORM_RIGHT>"D")
	{
		// sorting index
		$main_sort = 300;
		// alt-text of button
		$alt = "Edit Web Form for this page";
		// button is hyperlink
		$link = "Y";
		// hyperlink
	if (SITE_DIR=="/")
		$href = "/bitrix/admin/form_edit.php?ID=8";
	if (SITE_DIR=="/en/")
		$href = "/bitrix/admin/form_edit.php?ID=10";
		// button image (normal)
		$src_0 = "/bitrix/images/fileman/panel/web_form.gif";
		// button image (unavailable)
		$src_1 = "/bitrix/images/fileman/panel/web_form_t.gif";

		// Adding button to top panel
		$APPLICATION->AddPanelButton(array("LINK"=>$link, "HREF"=>$href, "SRC_0"=>$src_0, "SRC_1"=>$src_1, "ALT"=>$alt, "MAIN_SORT"=>$main_sort, "SORT"=>100));
	}
}

?>