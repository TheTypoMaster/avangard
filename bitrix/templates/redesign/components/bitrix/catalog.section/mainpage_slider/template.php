<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/scripts/jquery.nivo.slider.js"></script>
<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/nivoSlider/default.css">

<div class="main_slider_cont slider-wrapper theme-default ">
<script>
	$(window).load(function(){
		$('#main_slider').nivoSlider({
			effect: 'fade', // Specify sets like: 'fold,fade,sliceDown'
			slices: 15, // For slice animations
			boxCols: 8, // For box animations
			boxRows: 4, // For box animations
			animSpeed: 500, // Slide transition speed
			pauseTime: 3000, // How long each slide will show
			directionNav: false, // Next & Prev navigation
			controlNavThumbsFromRel: false // Use image rel for thumbs
		});
	});
</script>
	<div id="main_slider" class="nivoSlider">
	<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
	<?
		$src= CFile::GetPath($arElement["PROPERTIES"]["image"]["VALUE"]);
	?>
		<a href="<?=$arElement["PROPERTIES"]["link"]["VALUE"]?>"><img src="<?=$src?>" alt="" /></a>
	<?endforeach; // foreach($arResult["ITEMS"] as $cell=>$arElement):?>
	</div>
</div>
