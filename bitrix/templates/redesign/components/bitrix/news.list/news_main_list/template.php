<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<ul>
<?foreach($arResult["ITEMS"] as $arItem):?>
<li>
<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
        <?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
                <a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><b><?echo $arItem["NAME"]?></b></a><br />
        <?else:?>
                <b><?echo $arItem["NAME"]?></b><br />
        <?endif;?>
<?endif;?>
<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
        <?echo $arItem["PREVIEW_TEXT"];?>
<?endif;?>
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
</li>
<?endforeach;?>
</ul>