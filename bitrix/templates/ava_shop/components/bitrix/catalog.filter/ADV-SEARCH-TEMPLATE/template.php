<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<form name="<?=$arResult["FILTER_NAME"]."_form"?>" action="" method="get">


<table class="bottext" cellspacing="0" cellpadding="2">

        	<tr>
                <td>
	<table width="400" height="36" cellspacing="0" cellpadding="2">
	<tr>
		<td width="240" valign="middle" align="left"  style="BACKGROUND:
url(/bitrix/templates/avangard/images/search_bg.jpg) no-repeat left top">

  <?if(!array_key_exists("HIDDEN", $arItem)):?>

	<input type="text" name="arrFilter_ff[NAME]" value="<?=$_GET["arrFilter_ff"][NAME]?>" size="25" style="border: 0px; width: 220px; padding: 5px 5px 5px 5px" />

 	<?endif?>

		</td>
		<td align="right">
	&nbsp;<input  type="image" src="/bitrix/templates/avangard/images/search_go.jpg" type="submit" name="set_filter"
	 value="Ôèëüòð" />
		</td>
	</tr>
	</table>
</br>


  <?
  $nnn=0;
foreach($arResult["arrProp"]["12"]["VALUE_LIST"] as $ID => $cItem):
$iname="arrFilter_pf[W_SEARCH_PARAM][".$nnn."]";
?>
<?
  	$checked = "";

   if($_GET["arrFilter_pf"]["W_SEARCH_PARAM"][$nnn]!=""){
   	$checked = "checked";
   }
?>


<input name="<?=$iname?>" type="checkbox" value="<?=$ID?>" <?echo($checked)?> />
<?echo($cItem."<br />");
$nnn++;
?>

<?endforeach;?>

<input type="hidden" name="f_pass" value="1" />
<input type="hidden" name="set_filter" value="Y" />
             </td>
             </tr>




        </table>
</form>

