<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<br /><br />
<?$url = $APPLICATION->GetCurPage();?>
Адрес страницы: <b><?=SITE_SERVER_NAME.htmlspecialchars($url);?></b>
<br />
<br />
<a href=javascript:window.print()>Распечатать</a> || <a href=javascript:window.close()>Закрыть окно</a>
<br />
<br />

<b>&copy; <?=date(Y)?> Мебельная фабрика &quot;Авангард&quot; <a href="http://www.avangard.biz">www.avangard.biz</a></b>

</body>
</html>