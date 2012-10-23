<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Ваш запрос принят.");
$APPLICATION->SetTitle("Ваш запрос принят.");
?> 
<br />
 
<table width="100%"> 
  <tbody> 
    <tr> <td align="center"> <?$APPLICATION->IncludeComponent(
	"bitrix:form.result.list",
	"",
	Array(
		"SEF_MODE" => "N",
		"WEB_FORM_ID" => "10",
		"VIEW_URL" => "result_view.php",
		"EDIT_URL" => "result_edit.php",
		"NEW_URL" => "result_new.php",
		"SHOW_ADDITIONAL" => "N",
		"SHOW_ANSWER_VALUE" => "N",
		"SHOW_STATUS" => "Y",
		"NOT_SHOW_FILTER" => array("BP_ZAKAZ"),
		"NOT_SHOW_TABLE" => array(),
		"CHAIN_ITEM_TEXT" => "",
		"CHAIN_ITEM_LINK" => ""
	)
);?>Спасибо. 
        <br />
       Ваш запрос принят. 
        <br />
       
        <br />
       <a href="javascript:history.back()" onmouseover="window.status='Назад';return true" > Вернуться назад</a> </td> </tr>
   </tbody>
 </table>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>