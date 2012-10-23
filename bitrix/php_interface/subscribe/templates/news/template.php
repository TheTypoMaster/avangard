<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $SUBSCRIBE_TEMPLATE_RESULT;
$SUBSCRIBE_TEMPLATE_RESULT=false;
global $SUBSCRIBE_TEMPLATE_RUBRIC;
$SUBSCRIBE_TEMPLATE_RUBRIC=$arRubric;
global $APPLICATION;
?>
<STYLE type=text/css>
.text {font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12px; color: #1C1C1C; font-weight: normal;}
.newsdata{font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #346BA0; text-decoration:none;}
H1 {font-family: Verdana, Arial, Helvetica, sans-serif; color:#346BA0; font-size:15px; font-weight:bold; line-height: 16px; margin-bottom: 1mm;}/*Текст заголовка*/
</STYLE>

<P>Добрый день!</P>
<P><?$APPLICATION->IncludeFile("subscribe/subscr_news.php", Array(
	"SITE_ID"	=>	"ru",		// Сайт
	"IBLOCK_TYPE"	=>	"news",	// Тип информационного блока
	"ID"	=>	"-",		// Код информационного блока
	"SORT_BY"	=>	"ACTIVE_FROM",// Поле для сортировки новостей
	"SORT_ORDER"	=>	"DESC",	// Направление сортировки новостей
	)
);?></P>
<P>Всего хорошего</P><?
if($SUBSCRIBE_TEMPLATE_RESULT)
	return array(
		"SUBJECT"=>$SUBSCRIBE_TEMPLATE_RUBRIC["NAME"]
		,"BODY_TYPE"=>"html"
		,"CHARSET"=>"Windows-1251"
		,"DIRECT_SEND"=>"Y"
		,"FROM_FIELD"=>$SUBSCRIBE_TEMPLATE_RUBRIC["FROM_FIELD"]
	);
else
	return false;
?>