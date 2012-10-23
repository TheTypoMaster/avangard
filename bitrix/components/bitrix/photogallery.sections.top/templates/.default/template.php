<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

IncludeAJAX();

	?><div class="photo-controls"><?
	if (!empty($arResult["NEW_LINK"])):
		?><a href="<?=$arResult["NEW_LINK"]?>" title="<?=GetMessage("P_ADD_ALBUM_TITLE")?>" <?
		?>onclick="EditAlbum('<?=CUtil::JSEscape($arResult["~NEW_LINK"])?>'); return false;"<?
		?>><?=GetMessage("P_ADD_ALBUM")?></a><?
	endif;
	
	if (!empty($arResult["SECTION"]["UPLOAD_LINK"]) && !empty($arResult["SECTIONS"])):
		?><b><a href="<?=$arResult["SECTION"]["UPLOAD_LINK"]?>" title="<?=GetMessage("P_UPLOAD_TITLE")?>" <?
		?>><?=GetMessage("P_UPLOAD")?></a></b><?
	endif;
	?></div><?
	
	foreach($arResult["SECTIONS"] as $arSection):
	?><div class="photo-album" id="photo_album_info_<?=$arSection["ID"]?>"><?
		?><div class="photo-album-img"><?
			?><table cellpadding="0" cellspacing="0" class="shadow"><?
				?><tr class="t"><td colspan="2" rowspan="2"><?
					?><div class="outer" style="width:<?=($arParams["ALBUM_PHOTO"]["HEIGHT"] + 38)?>px;">
						<div class="tool" style="height:<?=$arParams["ALBUM_PHOTO"]["HEIGHT"]?>px;"></div>
						<div class="inner">
							<a href="<?=$arSection["SECTION_PAGE_URL"]?>"><?
								if (!empty($arSection["PICTURE"]["SRC"])):
									?><img src="<?=$arSection["PICTURE"]["SRC"]?>" border="0" <?
										?>width="<?=$arParams["ALBUM_PHOTO"]["WIDTH"]?>" <?
										?>height="<?=$arParams["ALBUM_PHOTO"]["HEIGHT"]?>"  <?
										?>alt="<?=htmlspecialchars($arSection["~NAME"])?>" /><?
								else:
									?><img src="<?=$templateFolder?>/images/no_image.gif" border="0" <?
										?>width="<?=$arParams["ALBUM_PHOTO"]["WIDTH"]?>" <?
										?>height="<?=$arParams["ALBUM_PHOTO"]["HEIGHT"]?>" alt="no image" /><?
								endif;
						?></a>
						</div>
					</div>
					</td>
					<td class="t-r"><div class="empty"></div></td></tr><?
				?><tr class="m"><td class="m-r"><div class="empty"></div></td></tr><?
				?><tr class="b"><td class="b-l"><div class="empty"></div></td><td class="b-c"><div class="empty"></div></td><?
					?><td class="b-r"><div class="empty"></div></td></tr><?
			?></table><?
		?></div><?
		
		?><div class="photo-album-info"><?
		
			?><a href="<?=$arSection["SECTION_PAGE_URL"]?>"><?
				?><div class="name<?=($arSection["ACTIVE"] != "Y" ? " nonactive" : "")?>" id="photo_album_name_<?=$arSection["ID"]?>"><?
					?><?=$arSection["NAME"]?><?
				?></div><?
			?></a><?
		
			?><div class="description" id="photo_album_description_<?=$arSection["ID"]?>"><?=$arSection["DESCRIPTION"]?></div><?
			
			?><div class="date" id="photo_album_date_<?=$arSection["ID"]?>"><?	
				?><?$APPLICATION->IncludeComponent(
					"bitrix:system.field.view", 
					$arSection["~DATE"]["USER_TYPE"]["USER_TYPE_ID"], 
					array("arUserField" => $arSection["~DATE"]), null, array("HIDE_ICONS"=>"Y"));
			?></div><?
			
			?><div class="photos"><?=GetMessage("P_PHOTOS_CNT")?>: <?=$arSection["ELEMENTS_CNT"]?></div><?
			
			if (intVal($arSection["SECTIONS_CNT"]) > 0):
				?><div class="photo-album-cnt-album"><?=GetMessage("P_ALBUMS_CNT")?>: <a href="<?=$arSection["SECTION_PAGE_URL"]?>"><?=$arSection["SECTIONS_CNT"]?></a></div><?
			endif;
		
			if (!empty($arSection["EDIT_LINK"])):
				?><br /><a href="<?=$arSection["EDIT_LINK"]?>" onclick="EditAlbum('<?=CUtil::JSEscape($arSection["~EDIT_LINK"])?>'); return false;" class="edit"><?=GetMessage("P_SECTION_EDIT")?></a><?
			endif;
			
			if (!empty($arSection["DROP_LINK"])):
				?><br /><a href="<?=$arSection["DROP_LINK"]?>" <?
				?>onclick="if (confirm('<?=GetMessage('P_SECTION_DELETE_ASK')?>')){DropAlbum('<?=CUtil::JSEscape($arSection["~DROP_LINK"])?>');} return false;" class="edit"><?=GetMessage("P_SECTION_DELETE")?></a><?
			endif;
		?></div><?
	?></div><?
	endforeach;
?><div class="empty-clear"></div>