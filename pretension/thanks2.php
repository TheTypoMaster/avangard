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
       Копия письма отправлена на указанную Вами электронную почту.
        <br />
       
        <br />
       <a href="http://www.avangard.biz/" onmouseover="window.status='Назад';return true" > Вернуться на главную страницу</a> </td> </tr>
   </tbody>
 </table>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>