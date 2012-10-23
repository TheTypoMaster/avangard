<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Диван в интерьере");
?>
<p align="center"><font color="#000000"><strong>
      <br />
    Уважаемые счастливые обладатели 
      <br />
    </strong><strong>мягкой мебели фабрики &laquo;Авангард&raquo;!</strong></font></p>

<p align="center"><font color="#000000">На нашем сайте с 10 февраля по 31 марта 2009 г. (</font><font color="#ff0000">по многочисленным просьбам продлен до 30 апреля</font><font color="#000000">) проводится открытый конкурс:</font></p>

<p align="center"><strong><a href="/mydivan/inter_foto.php" ><font color="#ff0000">«Мягкая мебель фабрики «Авангард» в интерьере Вашего Дома»</font></a></strong> 
  <br />
<a href="/mydivan/inter_foto.php" >смотреть фото</a></p>

<p align="center"><font color="#000000">Присылайте нам фотографии мягкой мебели фабрики «Авангард» в интерьере Вашего дома на наш сайт, заполнив форму:</font></p>

<p align="center"></p>

<p><?$APPLICATION->IncludeComponent(
	"bitrix:form.result.new",
	"",
	Array(
		"SEF_MODE" => "N", 
		"WEB_FORM_ID" => "8", 
		"LIST_URL" => "/mydivan/thanks.php", 
		"EDIT_URL" => "", 
		"SUCCESS_URL" => "", 
		"CHAIN_ITEM_TEXT" => "", 
		"CHAIN_ITEM_LINK" => "", 
		"IGNORE_CUSTOM_TEMPLATE" => "N", 
		"USE_EXTENDED_ERRORS" => "N", 
		"CACHE_TYPE" => "A", 
		"CACHE_TIME" => "3600", 
		"VARIABLE_ALIASES" => Array(
			"WEB_FORM_ID" => "WEB_FORM_ID",
			"RESULT_ID" => "RESULT_ID"
		)
	)
);?></p>

<p align="center"><font color="#000000">Лучшие фотографии будут размещены на нашем сайте. Под фотографией будут указаны Ваше имя, фамилия и город.</font></p>

<p align="center"><strong><font color="#ff0000">Победители конкурса получают призы:</font></strong></p>

<p align="center"><strong><font color="#000000">1 место - цифровой фотоаппарат;</font></strong></p>

<p align="center"><strong><font color="#000000">2 место - мобильный телефон;</font></strong></p>

<p align="center"><strong><font color="#000000">3 место - МП3 плеер.</font></strong></p>

<p align="center"><strong><font color="#000000">Приглашаем всех желающих принять участие!</font></strong></p>

<p align="left"><a href="/mydivan/inter_foto.php" ><font color="#000000">Фотографии, присланные на конкурс</font></a></p>
<blockquote></blockquote><blockquote></blockquote><blockquote></blockquote><blockquote></blockquote><blockquote></blockquote><blockquote></blockquote><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>