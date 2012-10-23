<?
/**
 * Form output class
 *
 */
class CFormOutput extends CAllFormOutput 
{
	function err_mess()
	{
		$module_id = "form";
		@include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/install/version.php");
		return "<br>Module: ".$module_id." (".constant(strtoupper($module_id)."_VERSION").")<br>Class: CFormStatus<br>File: ".__FILE__;
	}	
	
}
?>