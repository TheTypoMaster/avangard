<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-section-list">
<ul>
<?
$CURRENT_DEPTH=$arResult["SECTION"]["DEPTH_LEVEL"]+1;
foreach($arResult["SECTIONS"] as $arSection):
	if($CURRENT_DEPTH<$arSection["DEPTH_LEVEL"])
		echo "<ul>";
	elseif($CURRENT_DEPTH>$arSection["DEPTH_LEVEL"])
		echo "</ul>";
	$CURRENT_DEPTH = $arSection["DEPTH_LEVEL"];
?>
	<li><a href="<?=$arSection["SECTION_PAGE_URL"]?>"><?=$arSection["NAME"]?>&nbsp;(<?=$arSection["ELEMENT_CNT"]?>)</a></li>
<?endforeach?>
</ul>
</div>
