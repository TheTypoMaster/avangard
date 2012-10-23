

<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?foreach($arResult["ITEMS"] as $arItem):?>
<table width="187" border="0" cellspacing="0" cellpadding="0">
 <tr>
  <td class="newsblock">

                <?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
                        <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" title="<?=$arItem["NAME"]?>" style="float:left" /></a>
                <?endif?>
                <?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
                        <span class="news-date-time"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></span>
                <?endif?>
                <?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
                        <?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
                                <a href="<?echo $arItem["DETAIL_PAGE_URL"]?>" class="more"><b><?echo $arItem["NAME"]?></b></a><br />
                        <?else:?>
                                <b><?echo $arItem["NAME"]?></b><br />
                        <?endif;?>
                <?endif;?>
                <?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
                        <?echo $arItem["PREVIEW_TEXT"];?>
                <?endif;?>
                <?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
                        <div style="clear:both"></div>
                <?endif?>
                <?foreach($arItem["FIELDS"] as $code=>$value):?>
                        <small>
                        <?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?>
                        </small><br />
                <?endforeach;?>
                <?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
                        <small>
                        <?=$arProperty["NAME"]?>:&nbsp;
                        <?if(is_array($arProperty["DISPLAY_VALUE"])):?>
                                <?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
                        <?else:?>
                                <?=$arProperty["DISPLAY_VALUE"];?>
                        <?endif?>
                        </small><br />
                <?endforeach;?>


</td>
                          </tr>
                     
                        </table>
<?endforeach;?>


