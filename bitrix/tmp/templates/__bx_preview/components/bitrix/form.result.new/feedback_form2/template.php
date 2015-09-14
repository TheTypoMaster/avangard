<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<div class="gray_td"><h1>Обратная связь</h1></div>
<?=$arResult["FORM_ERRORS"]?><?=$arResult["FORM_NOTE"]?>

<?if ($arResult["isFormNote"] != "Y")
{
?>
<?=$arResult["FORM_HEADER"]?>
<table>
<?
if ($arResult["isFormDescription"] == "Y" || $arResult["isFormTitle"] == "Y" || $arResult["isFormImage"] == "Y")
{
?>
        <tr>
                <td><?
/***********************************************************************************
                                        form header
***********************************************************************************/

        if ($arResult["isFormImage"] == "Y")
        {
        ?>
        <a href="<?=$arResult["FORM_IMAGE"]["URL"]?>" target="_blank" alt="<?=GetMessage("FORM_ENLARGE")?>"><img src="<?=$arResult["FORM_IMAGE"]["URL"]?>" <?if($arResult["FORM_IMAGE"]["WIDTH"] > 300):?>width="300"<?elseif($arResult["FORM_IMAGE"]["HEIGHT"] > 200):?>height="200"<?else:?><?=$arResult["FORM_IMAGE"]["ATTR"]?><?endif;?> hspace="3" vscape="3" border="0" /></a>
        <?//=$arResult["FORM_IMAGE"]["HTML_CODE"]?>
        <?
        } //endif
        ?>

                        <p><?=$arResult["FORM_DESCRIPTION"]?></p>
                </td>
        </tr>
        <?
} // endif
        ?>
</table>
<br />
<?
/***********************************************************************************
                                                form questions
***********************************************************************************/
?>
<table class="form-table data-table">

        <tbody>
        <?
        foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion)
        {

        if ($arQuestion["CAPTION_UNFORM"]=="Категория сообщения")
          {

           ?>
                <tr>
                        <td>
                                <pre><?/*print_r($arQuestion)*/?></pre>
                                <?=$arQuestion["CAPTION"]?>
                                <?=$arQuestion["IS_INPUT_CAPTION_IMAGE"] == "Y" ? "<br />".$arQuestion["IMAGE"]["HTML_CODE"] : ""?>
                        </td>
                        <td>
              <script language="javascript">
              function main_mail(mail_1)
              {
              var arr_1 = [<? while (list($key, $val)=each($arQuestion["STRUCTURE"]))echo "\"".$val['FIELD_PARAM']."\",";?>"end"];
              document.forms["SIMPLE_FORM_5"].elements["form_hidden_644"].value=arr_1[mail_1];
              }
              </script>


           <select  class="inputselect"  name='form_dropdown_SIMPLE_QUESTION_989' id='form_dropdown_SIMPLE_QUESTION_989' size='1' onChange="main_mail(this.selectedIndex)">
           <?
           reset($arQuestion["STRUCTURE"]);
           while (list($key, $val)=each($arQuestion["STRUCTURE"]))
             {
              echo"<option value=\"".$val['ID']."\" >".$val['MESSAGE']."</options>";
             }
           ?></select>
                      </td>
                 </tr>      <?
          }
          else
          {
        ?>
                <tr>
                        <td>

                                <?=$arQuestion["CAPTION"]?>
                                <?=$arQuestion["IS_INPUT_CAPTION_IMAGE"] == "Y" ? "<br />".$arQuestion["IMAGE"]["HTML_CODE"] : ""?>
                        </td>
                        <td><?=$arQuestion["HTML_CODE"]?></td>
                </tr>
        <?
         }
        } //endwhile
        ?>
<?
if($arResult["isUseCaptcha"] == "Y")
{
?>
                <tr>
                        <th colspan="2"><b><?=GetMessage("FORM_CAPTCHA_TABLE_TITLE")?></b></th>
                </tr>
                <tr>
                        <td>&nbsp;</td>
                        <td><?=$arResult["CAPTCHA_IMAGE"]?></td>
                </tr>
                <tr>
                        <td><?=GetMessage("FORM_CAPTCHA_FIELD_TITLE")?><?=$arResult["REQUIRED_STAR"]?></td>
                        <td><?=$arResult["CAPTCHA_FIELD"]?></td>
                </tr>
<?
} // isUseCaptcha
?>
        </tbody>
        <tfoot>
                <tr>
                        <th colspan="2">
                                <input  type="submit" name="web_form_submit" value="Отправить сообщение" onClick="if (document.forms['SIMPLE_FORM_5'].elements['form_hidden_644'].value=='')document.forms['SIMPLE_FORM_5'].elements['form_hidden_644'].value='support@avangard.biz'"/>
                                <?/*=$arResult["SUBMIT_BUTTON"]*/?>&nbsp;&nbsp;<?=$arResult["RESET_BUTTON"]?>
                        </th>
                </tr>
        </tfoot>
</table>
<p><span class="bottext">
<?=$arResult["REQUIRED_STAR"]?> - <?=GetMessage("FORM_REQUIRED_FIELDS")?></span>
</p>
<?=$arResult["FORM_FOOTER"]?>
<?
} //endif (isFormNote)
?>
