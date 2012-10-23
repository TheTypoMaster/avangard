<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Ваш запрос принят.");
$APPLICATION->SetTitle("Ваш запрос принят.");
?> 
<br />
 
<table width="100%"> 
  <tbody> 
    <tr> <td align="center">Спасибо. 
        <br />
       Ваш запрос принят. 
        <br />
       
        <br />
       <a href="javascript:history.back()" onmouseover="window.status='Назад';return true" > Вернуться назад</a> </td> </tr>
   </tbody>
 </table>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>