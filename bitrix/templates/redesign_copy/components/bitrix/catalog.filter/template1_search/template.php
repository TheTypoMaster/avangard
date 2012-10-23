<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

                      <pre><?//print_r($arResult)?></pre>

<form name="<?=$arResult["FILTER_NAME"]."_form"?>" action="" method="get">
        <?foreach($arResult["ITEMS"] as $arItem):
                if(array_key_exists("HIDDEN", $arItem)):
                        echo $arItem["INPUT"];
                endif;
        endforeach;?>
        <table class="data-table" cellspacing="0" cellpadding="2">
        <thead>
                <tr>
                        <td colspan="2" align="center">Поиск по коллекциям</td>
                </tr>
        </thead>
        <tbody>

              <table width="400" height="36" cellspacing="0" cellpadding="2">
	<tr>
		<td width="240" valign="middle" align="left"  style="BACKGROUND:
url(/bitrix/templates/avangard/images/search_bg.jpg) no-repeat left top">


	<input type="text" name="<?=$arrFilter["?NAME"];?>" value="" size="25" style="border: 0px; width: 220px; padding: 5px 5px 5px 5px" />

		</td>
		<td align="right">
	&nbsp;<input  type="image" src="/bitrix/templates/avangard/images/search_go.jpg" type="submit" value="<?=GetMessage("SEARCH_GO")?>" />
		</td>
	</tr>
			</table>



  <?
foreach($arResult["arrProp"]["12"]["VALUE_LIST"] as $ID => $cItem):?>

<input name="<?=$arrFilter_pf[W_SEARCH_PARAM]?>" type="checkbox" value=<?=$ID?>>

<?echo($ID." - ".$cItem."<br />");?>

<?endforeach;             ?>



<!---

                <?//foreach($arResult["ITEMS"] as $arItem):?>
                        <?///if(!array_key_exists("HIDDEN", $arItem)):?>
                                <tr>
                                        <td valign="top">Название<?//=$arItem["NAME"]?>:</td>
                                        <td valign="top"> </td>
                                        <?//=$arItem["INPUT"]?></td>
                                </tr>
                        <?//endif?>
                <?//endforeach;?>
-->
       </tbody>
        <tfoot>

                <tr>
                        <td colspan="2">
                                <input type="submit" name="set_filter" value="<?=GetMessage("IBLOCK_SET_FILTER")?>" />
                                <input type="hidden" name="set_filter" value="Y" />&nbsp;&nbsp;
                                <input type="submit" name="del_filter" value="<?=GetMessage("IBLOCK_DEL_FILTER")?>" /></td>
                </tr>
        </tfoot>
        </table>
</form>
