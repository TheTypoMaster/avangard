<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Ваш запрос принят.");
$APPLICATION->SetTitle("Ваш запрос принят.");
?><br/>
<table width=100%>
<tr>
<td align="center">
Спасибо.<br/> Ваш запрос принят. <br/><br/>
<A HREF="javascript:history.back()"
 onMouseOver="window.status='Назад';return true">
Вернуться назад</A>

</td>
</tr>
</table>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>