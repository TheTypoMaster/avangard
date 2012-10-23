<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$first = true;
foreach ($arResult["VALUE"] as $res):
	if (!$first):
		?><span class="fields separator"></span><?
	else:
		$first = false;
	endif;
?><span class="fields files"><?=CFile::ShowFile($res, 0, $arParams["FILE_MAX_WIDTH"], $arParams["FILE_MAX_HEIGHT"], $arParams["FILE_SHOW_POPUP"]=="Y");?></span><?
endforeach;