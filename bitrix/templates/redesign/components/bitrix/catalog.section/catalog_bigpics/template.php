<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<table width="738" align="center">
	<?
	$collection_count = count($arResult['CAT_ARRAY']);
	$kol = 0;
	foreach($arResult['CAT_ARRAY'] as $category){
		$kol++;
		?>
		<?if(($kol > 1) && ($kol <= $collection_count)): ?>
			<tr><td colspan="5" class="gray_line_small"></td></tr>
		<?endif ?>
		<tr>
			<td colspan="5"><b><?=$category["name"] ?></b></td>
		</tr>
		<?$i=0;
		foreach($arResult['ITEMS'] as $el){
			if($el["DISPLAY_PROPERTIES"]["COLLECTION"]["VALUE"]!=$category["id"])//Пропускаем если это не текущая коллекция
				continue;
			$i++;
			$rows_count--;
			if($i == 1)
				echo "<tr class='divan_row ".($rows_count<3 ? 'last_row' : '')."'>";
			if($el["DISPLAY_PROPERTIES"]["FULLCOLOR_PIC"]["FILE_VALUE"]["SRC"]!='')
				$img_path = $el["DISPLAY_PROPERTIES"]["FULLCOLOR_PIC"]["FILE_VALUE"]["SRC"]; 
			else
				$img_path = $el["PREVIEW_PICTURE"]["SRC"];
		$skidka_div = '';    
		if($el["DISPLAY_PROPERTIES"]["SKIDKA"]["VALUE"]) {
			$skidka_value = $el["DISPLAY_PROPERTIES"]["SKIDKA"]["VALUE"]; /* если есть скидка, то выводится введенное значение */
			$skidka_div .='<div style="margin: 0px; padding: 0px; position: absolute; z-index: 90;"><div style="text-align:center; background: url(/images/skidka.gif) no-repeat center center; position: relative; left: 200px; top: -12px; color: #ffffff; height: 36px; width: 36px; " height=36 width=36 ><img src="/images/gif.gif" height="10" width="20"><br>-'.$skidka_value.'%</div></div>';
		}
		?>
			<td class="catalog_td">
				<? echo $skidka_div; ?>
				<a href="/catalog/divan<?=$el["ID"] ?>.htm"><!--
					--><img onMouseOver="this.src='<?=$img_path ?>';" onMouseOut="this.src='<?=$el["DISPLAY_PROPERTIES"]["BLACKWHITE_PIC"]["FILE_VALUE"]["SRC"]?>';" class="catalog_picture" src="<?=$el["DISPLAY_PROPERTIES"]["BLACKWHITE_PIC"]["FILE_VALUE"]["SRC"]?>" alt="<?=$el["NAME"] ?>"><!--
				--></a><br>
				<table width="100%">
					<tr>
						<td>
							<a class="catalog_name" href="/catalog/divan<?=$el["ID"] ?>.htm"><?=$el["NAME"] ?></a>
						</td>
						<td id="price_new1">
							<?=$el["DISPLAY_PROPERTIES"]["PRICE"]["VALUE"][0]?>
						</td>
					</tr>
				</table>
			<?if($i < 3){ ?>
				<td width="26"></td> 
			<? } ?>
<?
			if($i == 3){
				$i = 0;
				echo "</tr>";
			}
		}
	}
	?>
</table>
<div class="seo_text">
<?if($collection_count<=1){
	$collection= array_shift(array_values($arResult['CAT_ARRAY']));
	echo $collection["detail_text"];
}else{?>
	<p>За 12 лет существования, фабрика мягкой мебели &laquo;Авангард&raquo; заняла достойное место на отечественном мебельном рынке, демонстрируя высокое качество своих диванов и кресел. Верхние строчки в рейтинге продаж в разные годы занимали 70 моделей мебели (из 200 производимых фабрикой). В интернет-магазине фабрики можно подобрать, заказать и купить дешевые, мягкие и жесткие диваны для отдыха различных форм, с количеством спальных мест на ваш выбор, каркасом из дерева или металла. Мягкая мебель &laquo;Авангард&raquo; не только экологична (что подтверждено Экологическим Сертификатом Соответствия), но и оказывает полезное воздействие на окружающую среду и человека. Сомневаетесь? Вот доказательства. </p>
	<p>В производстве наших диванов и кресел используется натуральная древесина &ndash; массив бука высокогорных районов Северного Кавказа. Этот бук отличается своей экологической чистотой, прочностью и красотой. Натуральное дерево позволит Вам каждый день соприкасаться с природой, а его энергетика создаст в вашем доме атмосферу стабильности и уверенности, снимет стресс, успокоит, а значит, повысит иммунитет.</p>
	<p>В нашем онлайн каталоге с картинками, мягкая мебель описана в четырёх коллекциях: &laquo;EKKA&raquo;, &laquo;Искусства и ремёсла&raquo;, &laquo;Le Roi&raquo;, &laquo;Mix Line&raquo; (смотрите фотографии выше). Коллекции различаются своей направленностью, стилями.</p>
	<p>&laquo;Искусства и ремёсла&raquo; - одна из самых востребованных коллекций на сегодняшний день. Здесь мы возвращаемся к народным истокам творчества, ко времени, когда каждый дом был уникален, неповторим. Потому что и сам дом, и мебель в нём, и домашняя утварь, и украшения - всё было сделано руками хозяина и хозяйки. Дом, где атмосфера пропитана любовью, желанием жить и творить на благо семьи.</p>
	<p>Коллекция &laquo;ЕККА&raquo; создана по эскизам итальянских дизайнеров. Пока здесь две модели. Дизайн модульной системы &laquo;Анталия&raquo; с округлыми изгибами, отсутствием острых углов внесёт мягкость и гармонию в Вашу жизнь. &laquo;Неаполь&raquo; придаст пространству комнаты ясность, упорядоченность. Эта экологичная, комфортная и качественная мебель доставит вам истинное удовольствие.</p>
	<p>&laquo;Mix Line&raquo; - Различные стили комбинируются друг с другом и образуют эклектическое смешение. Создаётся оригинальный дизайн мебели не только для того, чтобы радовать глаз, но быть удобной и практичной. Это самый популярный стиль у современных дизайнеров.</p>
	<p>&laquo;Lе Roi&raquo; . Мягкая мебель чутко реагирует на изменения моды. Но мода изменчива: то, что было модным вчера, завтра утрачивает свою новизну. Только классика остаётся неизменной. Совершенствуясь, она приобретает новые очертания, цвет, фактуру. Классическая мягкая мебель &laquo;Le Roi&raquo; своими формами и отделкой напоминает старинную мебель, а отличается тем, что изготавливается из современных материалов с использованием современных технологий, что делает её более долговечной и приспособленной к нашей жизни.</p>
<?}?>
</div>