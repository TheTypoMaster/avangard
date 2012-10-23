<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


        <?foreach($arItems as $arElement):?>


        <table width="188" border="0" cellspacing="0" cellpadding="5">
                          <tr> 
                            <td align="left" bgcolor="#efefef"  style="padding-left: 10px"> <div class="hit">
<a href="/hits/">хиты продаж</a></div>
                              <img src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="167" height="79"><br> 
                              <span class="new"><li> <a href="<?=$arElement["DETAIL_PAGE_URL"]?>">
<?=$arElement["NAME"]?></a></li></span></td>
                          </tr>
                        </table>
<?endforeach?>



