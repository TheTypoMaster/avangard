<?
/**************************************************************************
Component "Random Photo".

This component is intended for displaying random photo. Mainly used for the site Home page.
 
Sample of usage:
$APPLICATION->IncludeFile("iblock/photo/random.php", Array(
	"IBLOCK_TYPE"	=>	"photo",
	));

Parameters:
IBLOCK_TYPE - Information block type

***************************************************************************/

IncludeTemplateLangFile(__FILE__); //including of lannguage resource file 

// Show random elements
if (CModule::IncludeModule("iblock")):
	$iblocks = GetIBlockList($IBLOCK_TYPE);
	if ($arIBlock = $iblocks->GetNext()):
		$arSelect = array ("NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE", "DETAIL_PAGE_URL");
		$dbPhoto = GetIBlockElementList($arIBlock["ID"], false, Array("RAND"=>"ASC"), false, array(), $arSelect);
		if($arPhoto = $dbPhoto->GetNext()):
			?>
			<table width="100%" border="0" cellpadding="2" cellspacing="0">
			  <tr>
				<td>
					<?
					$image1 = intval($arPhoto["PREVIEW_PICTURE"])<=0 ? $arPhoto["DETAIL_PICTURE"] : $arPhoto["PREVIEW_PICTURE"];
					$image2 = intval($arPhoto["DETAIL_PICTURE"])<=0 ? $arPhoto["PREVIEW_PICTURE"] : $arPhoto["DETAIL_PICTURE"];	
					echo CFile::Show2Images($image1, $image2, 150, 150, "hspace='0' vspace='0' border='0' title='".$arPhoto["NAME"]."'", true);?><br><font class="smalltext"><a href="<?=$arPhoto["DETAIL_PAGE_URL"]?>"><?echo $arPhoto["NAME"]?></a></font>
				</td>
			  </tr>
			</table>
			<?
		endif;
	endif;
else:
	echo ShowError(GetMessage('IB_NOT_INSTALL'));
endif;
?>
