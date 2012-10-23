<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-sections-top">

<?if ($_GET[f_pass]!=1):?>
<?else:?>

<?
	$arEl = Array();

	foreach($arResult["SECTIONS"] as $arSection){

        foreach($arSection["ITEMS"] as $arElement){

 			$arEl[$arElement["ID"]] = $arElement;

		}

    }

    //echo("<pre>"); print_r($arEl); echo("</pre>");
        ?>

<a name="list">

			<table cellpadding="0" cellspacing="0" border="0">

			<?foreach($arEl as $arElement):?>

				<tr>
					<td valign="top">
			  <?   echo ShowImage($arElement["PREVIEW_PICTURE"]["SRC"], 156, 97, "border='0'",
			  "/catalog/divan".$arElement["ID"].".htm");?>

					</td>
					<td valign="top">

					<b><a href="/catalog/divan<?=$arElement["ID"]?>.htm"><?=$arElement["NAME"]?></a></b>

					<br />
					<div class="bottext"><small>
					<b><?=$arElement["PROPERTIES"]["F_TYPE"]["VALUE"]?></b><br />
					<b>Коллекция:</b><a href="/search/collection/index.php?collection[1]=<?=$arElement["PROPERTIES"]["COLLECTION"]["VALUE"]?>">

 <?
   $arResColl=GetIBlockElement($arElement["PROPERTIES"]["COLLECTION"]["VALUE"]);
   echo($arResColl["NAME"]);
   ?>
   </a>

					<br/>
<b>					Комплектность:</b>
					 <?

	 foreach($arCU as $compl):
     $arResCompl=GetIBlockElement($compl);
 	 echo($arResCompl["NAME"]."<br />");
     endforeach;
                          ?>

						<?foreach($arElement["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
							<b><?=$arProperty["NAME"]?>:&nbsp;</b><?
								if(is_array($arProperty["DISPLAY_VALUE"]))
									echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
								else
									echo $arProperty["DISPLAY_VALUE"];?><br /?
						<?endforeach?>

						</small><br /></div>
						<br />
						<?//=$arElement["PREVIEW_TEXT"]?>
					</td>
				</tr>
				<?endforeach;?>
			</table>


<script language="javascript" type="text/javascript">

	window.location.replace('#list');

</script>

<?endif;?>
</div>
