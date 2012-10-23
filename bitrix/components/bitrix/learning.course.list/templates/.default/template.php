<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult["COURSES"])):?>
<div class="learning-course-list">

	<?foreach($arResult["COURSES"] as $arCourse):?>
		<?if ($arCourse["PREVIEW_PICTURE_ARRAY"]!==false):?>
			<?echo ShowImage($arCourse["PREVIEW_PICTURE_ARRAY"]["SRC"], 200, 200, "hspace='6' vspace='6' align='left' border='0'", "", true);?>
		<?endif;?>

		<a href="<?=$arCourse["COURSE_DETAIL_URL"]?>"><?=$arCourse["NAME"]?></a>
		<?if(strlen($arCourse["PREVIEW_TEXT"])>0):?>
			<br /><?=$arCourse["PREVIEW_TEXT"]?>
		<?endif?>
		<br clear="all"><br />
	<?endforeach;?>

</div>
	<?=$arResult["NAV_STRING"]?>
<?endif?>

