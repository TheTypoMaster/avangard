<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="bottext">


	
	<?if($arResult["DETAIL_TEXT"]):?>
		<br /><?=$arResult["DETAIL_TEXT"]?><br />
	<?elseif($arResult["PREVIEW_TEXT"]):?>
		<br /><?=$arResult["PREVIEW_TEXT"]?><br />
	<?endif;?>
	<?if(count($arResult["LINKED_ELEMENTS"])>0):?>
		<br /><b><?=$arResult["LINKED_ELEMENTS"][0]["IBLOCK_NAME"]?>:</b>
		<ul>
	<?foreach($arResult["LINKED_ELEMENTS"] as $arElement):?>
		<li><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a></li>
	<?endforeach;?>
		</ul>
	<?endif?>

                               



<br class="brspace">
<B><?=$arResult["NAME"]?></B><br />
				<?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
					<?=$arProperty["NAME"]?>:<b>&nbsp;<?
					if(is_array($arProperty["DISPLAY_VALUE"])):
						echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
					elseif($pid=="MANUAL"):
						?><a href="<?=$arProperty["VALUE"]?>"><?=GetMessage("CATALOG_DOWNLOAD")?></a><?
					else:
						echo $arProperty["DISPLAY_VALUE"];?></b><br />
					<?endif?>
				<?endforeach?>

	
	
 
<br class="brspace">

<?
// additional photos
	$LINE_ELEMENT_COUNT = 3; // number of elements in a row
	if(count($arResult["MORE_PHOTO"])>0):?>
		<b><a name="more_photo">Ñץולא ןנמוחהא:</a></b><br /><br class="brspace">
                <?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
			<a href="<?=$PHOTO["SRC"]?>" target="_blank"><img border="0" src="<?=$PHOTO["SRC"]?>" width="<?=$PHOTO["WIDTH"]*100/$PHOTO["HEIGHT"]?>" height="100" alt="<?=$PHOTO["ALT"]?>" title="<?=$arResult["NAME"]?>" /></a>
                  
		<?endforeach?>
	<?endif?>

<br class="brspace">
	<?if(is_array($arResult["SECTION"])):?>
		<br /><a href="<?=$arResult["SECTION"]["SECTION_PAGE_URL"]?>"><?=GetMessage("CATALOG_BACK")?></a>
	<?endif?>
</div>