<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? /*
<script>
	$(function() {
		$( "#tabs" ).tabs();
	});
</script>

<div class="mainpage_tabs">
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1">НОВИНКИ</a></li>
			<li><a href="#tabs-3">ХИТЫ ПРОДАЖ</a></li>
		</ul>
		<div id="tabs-1">
			<div class="tab_slider">
				<div class="slider_container"><!--
				<?foreach($arResult["NOVELTIES"] as $novelty){ ?>
					--><div class="slider_item">
					<div class="item_head"><a href="<?=$novelty["DETAIL_PAGE_URL"]?>"><?=$novelty["NAME"]?></a></div>
					<div class="item_collection">Коллекция: <b><?=$novelty["PROPERTY_COLLECTION_NAME"]?></b><!--
					<?if($novelty["PROPERTY_SKIDKA_VALUE"]) {?>
					--><span style="color: white; font-size: 150%; position: relative; top: -10px; left: 5px; background-color: red; ">&nbsp;-<?=$novelty["PROPERTY_SKIDKA_VALUE"]?>%&nbsp;</span><!--
					<?}?>
					--></div>
						<div class="item_image">
							<a href="<?=$novelty["DETAIL_PAGE_URL"]?>">
								<img src="<?=($novelty["PROPERTY_FULLCOLOR_PIC_VALUE_SRC"]!='' ? $novelty["PROPERTY_FULLCOLOR_PIC_VALUE_SRC"] : '/images/no_photo.png')?>" />
							</a>
						</div>
					</div><!--
				<?}?>
					--><div class="clearall"></div>
				</div>
			</div>
		</div>
		<div id="tabs-3">
			<div class="tab_slider">
				<div class="slider_container"><!--
				<?foreach($arResult["HITS"] as $novelty){?>
					--><div class="slider_item">
						<div class="item_head"><a href="<?=$novelty["DETAIL_PAGE_URL"]?>"><?=$novelty["NAME"]?></a></div>
					<div class="item_collection">Коллекция: <b><?=$novelty["PROPERTY_COLLECTION_NAME"]?></b><!--
					<?if($novelty["PROPERTY_SKIDKA_VALUE"]) {?>
					--><span style="color: white; font-size: 150%; position: relative; top: -10px; left: 5px; background-color: red; ">&nbsp;-<?=$novelty["PROPERTY_SKIDKA_VALUE"]?>%&nbsp;</span><!--
					<?}?>
					--></div>
						<div class="item_image">
							<a href="<?=$novelty["DETAIL_PAGE_URL"]?>">
								<img src="<?=($novelty["PROPERTY_FULLCOLOR_PIC_VALUE_SRC"]!='' ? $novelty["PROPERTY_FULLCOLOR_PIC_VALUE_SRC"] : '/images/no_photo.png')?>" />
							</a>
						</div>
					</div><!--
				<?}?>
					--><div class="clearall"></div>
				</div>
			</div>
		</div>
	</div>
</div>
*/ ?>