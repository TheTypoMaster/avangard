<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

if ($_GET[order]) {
	if (sizeof($basket) > 0) {
		foreach ($basket as $key => $val) {
			$APPLICATION->set_cookie("basket[" . $key . "]", "");
			//echo "удаление $key; ";
		}
	}
	//print_r($basket);
}
?>
<link rel="stylesheet" type="text/css" href="/shadowbox/shadowbox.css">
<script type="text/javascript" src="/shadowbox/shadowbox.js"></script>
<script type="text/javascript">
	Shadowbox.init({
		handleOversize: "drag",
		modal: true
	});
</script>
<?
$APPLICATION->SetTitle("Корзина");
global $APPLICATION;
$APPLICATION->SetTitle("Корзина");
$APPLICATION->SetPageProperty("up_inc_file", "none");
$APPLICATION->SetPageProperty("right_inc_file", "right_nomain_inc_file.php");


echo "<br><h1>Корзина</h1><br>";

$basket = $APPLICATION->get_cookie("basket");
if (sizeof($basket) > 0 && is_array($basket)) {
	echo "<table id=\"t_basket\"><tr><th>Наименование</th><th>Изображение</th><th>Комбинация</th><th>Материалы</th><th>Цена</th><th>Удалить</th></tr>";
	foreach ($basket as $key_in_basket => $val) {
		//$APPLICATION->set_cookie("basket[".$key."]", " ");
		//continue;
		$srcTexture = $arFileRecommTexture = $resCombinacProperty = array();
		$subject = unserialize($val);
		if (!$subject[subj_id])
			continue;
//		echo "subject - ";		print_r($subject);
		$priceRecommend = 0;
		if ($_GET[del_texture] && $key_in_basket == $_GET['key']) { // удаление выбранного пользователем материала
			$APPLICATION->set_cookie("basket[" . $key . "]", "");
			unset($subject[texture_id][$_GET[del_texture]]);
			$all_edit_param = array("subj_id" => $subject[subj_id], "combinac_id" => $subject[combinac_id], "texture_id" => $subject[texture_id]);
			$APPLICATION->set_cookie("basket[" . $key_in_basket . "]", serialize($all_edit_param));
			/* echo "<pre>";
			  print_r(unserialize($basket));
			  echo "</pre>"; */
		}
		if ($_GET[rec_color] == 0 && $subject[subj_id] == $_GET[subj_id]) { // стираем из сессии рекомендуемые цвета
			$APPLICATION->set_cookie("basket[" . $key_in_basket . "]", "");
			$subject[texture_id][$_GET[num]] = $_GET[texture_id];
			$all_edit_param = array("subj_id" => $subject[subj_id], "combinac_id" => $subject[combinac_id], "texture_id" => $subject[texture_id]);
			$APPLICATION->set_cookie("basket[" . $key_in_basket . "]", serialize($all_edit_param));
			/* echo "<pre>";
			  print_r($basket);
			  echo "</pre>"; */
		}
		if ($_GET[texture_id] > 0 && $key_in_basket == $_GET['key']) { // добавляем рекомендумый материал выбранный пользоватлем
			$APPLICATION->set_cookie("basket[" . $key_in_basket . "]", "");
			$subject[texture_id][$_GET[num]] = $_GET[texture_id];
			$all_edit_param = array("subj_id" => $subject[subj_id], "combinac_id" => $subject[combinac_id], "texture_id" => $subject[texture_id]);
			$APPLICATION->set_cookie("basket[" . $key_in_basket . "]", serialize($all_edit_param));
			/* echo "<pre>";
			  print_r($basket);
			  echo "</pre>"; */
		}


		//print_r($subject);
		$resSubj = CIBlockElement::GetByID($subject[subj_id]);
		$arSubj = $resSubj->Fetch();
		//echo "<pre>$key_in_basket:"; print_r($arSubj); echo "</pre>";

		$resFileSubj = CFile::GetById($arSubj[PREVIEW_PICTURE]);
		$resFileSubjBIG = CFile::GetById($arSubj[DETAIL_PICTURE]);

		$arFileSubj = $resFileSubj->Fetch();
		$arFileSubjBIG = $resFileSubjBIG->Fetch();

		$srcSubj = "/upload/" . $arFileSubj[SUBDIR] . "/" . $arFileSubj[FILE_NAME];
		$srcSubjBIG = "/upload/" . $arFileSubjBIG[SUBDIR] . "/" . $arFileSubjBIG[FILE_NAME];
		//echo $srcSubjBIG. " | ".$srcSubj;

		$srcCombinac = "";
		if ($subject[combinac_id] > 0) {
			$resCombinac = CIBlockElement::GetByID($subject[combinac_id]);
			$arCombinac = $resCombinac->Fetch();
			$resCombinacProperty = CIBlockElement::GetByID($subject[combinac_id]);
			if ($obResCombinacProperty = $resCombinacProperty->GetNextElement()) {
				$resCombinacProperty = $obResCombinacProperty->GetProperties();
			}
			//echo "<pre>resCombinacProperty - ";
			//print_r($resCombinacProperty);
			//print_r($arSubj);
			//print_r($srcTexture);
			//print_r($arPropsCombinac);
			//print_r($arPropsRecommend);
			//echo "</pre>";
			$resFileCombinac = CFile::GetById($arCombinac[PREVIEW_PICTURE]);
			$arFileCombinac = $resFileCombinac->Fetch();
			$srcCombinac = "/upload/" . $arFileCombinac[SUBDIR] . "/" . $arFileCombinac[FILE_NAME];

			$resPropsCombinac = CIBlockElement::GetProperty(24, $subject[combinac_id], array("sort" => "asc"), Array("CODE" => "SUBJECT")); // выбираем свойства комбинации
			$arPropsCombinac = $resPropsCombinac->Fetch();
		}

		if ($subject[recommend_id] > 0 && $arCombinac[ID] && !isset($_GET[rec_color])) { // выбираем рекомендуемые цвета  |||   && ($_GET[rec_color]<>0 && isset($_GET[rec_color]) )
			$arSelect = Array("ID", "NAME", "PROPERTY_TEXTURE", "PROPERTY_PRICE", "PREVIEW_PICTURE", "DETAIL_PICTURE");
			//print_r($subject);
			$arFilter = Array("IBLOCK_ID" => 21, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "PROPERTY_COMBINAC" => $arCombinac[ID], "ID" => $subject[recommend_id]);
			$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
			while ($ob = $res->GetNextElement()) {
				$arFields = $ob->GetFields();
				//echo "<pre>arRecommend - ";				print_r($arFields);				echo "</pre>";
				/**
				 * 	select picture from recommended
				 */
				if ($arFields[PREVIEW_PICTURE]) {
					$resFileSubj = CFile::GetById($arFields[PREVIEW_PICTURE]);
					$arFileSubj = $resFileSubj->Fetch();
					$srcSubj = "/upload/" . $arFileSubj[SUBDIR] . "/" . $arFileSubj[FILE_NAME];
				}
				if ($arFields[DETAIL_PICTURE]) {
					$resFileSubjBIG = CFile::GetById($arFields[DETAIL_PICTURE]);
					$arFileSubjBIG = $resFileSubjBIG->Fetch();
					$srcSubjBIG = "/upload/" . $arFileSubjBIG[SUBDIR] . "/" . $arFileSubjBIG[FILE_NAME];
				}
				/*				 * ************************** */
				$priceRecommend = $arFields[PROPERTY_PRICE_VALUE];
				$resRecommTexture = CIBlockElement::GetByID($arFields[PROPERTY_TEXTURE_VALUE]);
				$arRecommTexture = $resRecommTexture->Fetch();

				############  получаем картинку ################################
				$resFileRecommTexture = CFile::GetById($arRecommTexture[PREVIEW_PICTURE]);
				$arFileRecommTexture = $resFileRecommTexture->Fetch();
				#################################################################
				array_push($arFileRecommTexture, $arFields[PROPERTY_TEXTURE_VALUE]);
				$srcTexture[] = $arFileRecommTexture;
//				echo "$key_in_basket srcTexture12 - ";				print_r($srcTexture);
			}
		} elseif (is_array($subject[texture_id]) && sizeof($subject[texture_id]) > 0) { // выбираем кол-во материалов в комбинации, т.к. не выбраны рекомендованные цвета
		}
		$key = key($subject[texture_id]);
		$err = (key($subject[texture_id]) == '' && sizeof($subject[texture_id]) == 1) ? 1 : 0; // массив $subject[texture_id] с пустым ключом и пустым значением
		/* echo "arFileRecommTexture - <br>";
		  print_r($arFileRecommTexture);
		  print_r($subject[texture_id]);
		  echo "<br> size - ".sizeof($subject[texture_id])."<br>";
		  echo "key - $key<br>";
		  echo "err - $err<br>"; */
		/* if ((sizeof($subject[texture_id])>0 && !is_array($arFileRecommTexture) && sizeof($arFileRecommTexture)>0) || $err) {
		  $srcSubj = ""; // не показываем изобржение если выбрана комбинация, и нет рекомендуемых цветов и нет материалов

		  } */
		/* echo "srcCombinac - ";
		  print_r($srcCombinac);
		  echo "$key_in_basket srcTexture11 - ";
		  print_r($srcTexture); */
		//echo "<br> sizeof(srcCombinac) - ".sizeof($srcCombinac)."<br> subject[recommend_id] - $subject[recommend_id]<br>";
		if ($subject[recommend_id]) { // если нет рекомендуемой картинки, то показываем картинку с комбинацией
			$srcCombinac = ""; // не показываем изобржение если выбрана комбинация, и нет рекомендуемых цветов и нет материалов
		}
		//echo "файл - ".$arSubj[PREVIEW_PICTURE]."<br>";
		//echo "<pre>VALUES - ";
		//print_r($VALUES);
		//print_r($arSubj);
		//print_r($srcTexture);
		//print_r($arPropsCombinac);
		//print_r($arPropsRecommend);
		//echo "</pre>";
		?>
		<tr id="tr_<?= $key_in_basket ?>">
			<td valign="middle"><a href="/catalog/subject/<?= $arSubj[ID] ?>.html"><?= $arSubj[NAME] ?></a></td>
			<td> <? if (!empty($srcSubj) && empty($srcCombinac)) { ?><a href="<?= $srcSubjBIG ?>" rel="shadowbox[dop<?= $arItem["ID"] ?>]"><img class="detail_picture" src="<?= $srcSubj ?>"  /></a> <? } ?></td>
			<td> <? if ($srcCombinac) { ?><img src="<?= $srcCombinac ?>" /><? } else echo "-"; ?></td>
			<td> <?
		if (is_array($srcTexture) && sizeof($srcTexture) > 0) {  // выводим рекоммендуемые цвета
			foreach ($srcTexture as $keyTexture => $valTexture) {
				$src_texture = "/upload/" . $valTexture[SUBDIR] . "/" . $valTexture[FILE_NAME];
				?>
						<a href="javascript:void(winPop('/catalog/texture/<?= $valTexture[0] ?>.html',%20'mww',%20550,%20410));"><img src="<?= $src_texture ?>" align="" width="40" /></a>
				<?
			}
			//echo '<div style="clear:left"></div><a href="?rec_color=0&subj_id='.$subject[subj_id].'">выбрать другие</a>';
		} else { // пользователь выбирает материалы вручную
			if (is_array($resCombinacProperty)) {
				for ($i = 1; $i <= $resCombinacProperty[COUNT_COLOR][VALUE]; $i++) {
					if ($subject[texture_id][$i] > 0) { // если пользователем был выбран материал, выводим картинку из сессии
						$resSelectedTexture = CIBlockElement::GetByID($subject[texture_id][$i]);
						$arSelectedTexture = $resSelectedTexture->Fetch();

						############  получаем картинку ################################
						$resFileTexture = CFile::GetById($arSelectedTexture[PREVIEW_PICTURE]);
						$arFileTexture = $resFileTexture->Fetch();
						$srcTexture = "/upload/" . $arFileTexture[SUBDIR] . "/" . $arFileTexture[FILE_NAME];
						echo '' . $i . '. <a href="javascript:void(winPop(\'/catalog/texture/' . $subject[texture_id][$i] . '.html\',%20\'mww\',%20550,%20410));"><img src="' . $srcTexture . '"  width="40" /></a>&nbsp;<a href="/shop/basket/?del_texture=' . $i . '&key=' . $key_in_basket . '" title="удалить материал"><font color="red"><b>х</b></font></a><br>';
					} else { // предлагаем выбрать цвет
						echo '' . $i . '. <a href="/texture/?key=' . $key_in_basket . '&num=' . $i . '">выбрать</a><br>';
					}
				}
			}
		}
		?>
			</td>
			<td> <? if ($priceRecommend) { ?> <?= $priceRecommend ?> руб. <? } else echo "-"; ?></td>
			<td> <a onclick="delSubject(<?= $key_in_basket ?>); return true;" href="#" >удалить</a></td>
		</tr>
				<?
			}
			echo "</table>";
			?>
	<script language="javascript"> countSubject(); </script>
			<? ?><?
			$APPLICATION->IncludeComponent("avang:iblock.element.add.form", "order", array(
	"IBLOCK_TYPE" => "order",
	"IBLOCK_ID" => "25",
	"STATUS_NEW" => "N",
	"LIST_URL" => "",
	"USE_CAPTCHA" => "N",
	"USER_MESSAGE_EDIT" => "",
	"USER_MESSAGE_ADD" => "Спасибо, Ваш заказ принят. Наши менеджеры свяжутся с Вами в ближайшее время.",
	"DEFAULT_INPUT_SIZE" => "30",
	"RESIZE_IMAGES" => "N",
	"PROPERTY_CODES" => array(
		0 => "NAME",
		1 => "105",
		2 => "104",
		3 => "106",
		4 => "113",
		5 => "114",
	),
	"PROPERTY_CODES_REQUIRED" => array(
		0 => "105",
		1 => "104",
		2 => "113",
		3 => "114",
	),
	"GROUPS" => array(
		0 => "1",
		1 => "2",
	),
	"STATUS" => "ANY",
	"ELEMENT_ASSOC" => "CREATED_BY",
	"MAX_USER_ENTRIES" => "100000",
	"MAX_LEVELS" => "100000",
	"LEVEL_LAST" => "Y",
	"MAX_FILE_SIZE" => "0",
	"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
	"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
	"SEF_MODE" => "N",
	"SEF_FOLDER" => "/basket/",
	"CUSTOM_TITLE_NAME" => "",
	"CUSTOM_TITLE_TAGS" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
	"CUSTOM_TITLE_IBLOCK_SECTION" => "",
	"CUSTOM_TITLE_PREVIEW_TEXT" => "",
	"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
	"CUSTOM_TITLE_DETAIL_TEXT" => "",
	"CUSTOM_TITLE_DETAIL_PICTURE" => ""
	),
	false
);
			?>
	<?
}
elseif ($_GET[strIMessage]) {
	echo $_GET[strIMessage];
}
else
	echo "Корзина пуста";
?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>