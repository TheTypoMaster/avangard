<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$tplShown = false;
$rsEvents = GetModuleEvents("main", "system.field.view.file");
while (($arEvent = $rsEvents->Fetch()) && (!$tplShown))
{
	$tplShown = ExecuteModuleEventEx($arEvent, array($arResult, $arParams));
}

if (!$tplShown)
{
	$first = true;
	foreach ($arResult["VALUE"] as $res):
		if (!$first):
			?><span class="bx-br-separator"><br /></span><?
		else:
			$first = false;
		endif;
	?><span class="fields files"><?
	$arFile = CFile::GetFileArray($res);
	if($arFile)
	{
		if(substr($arFile["CONTENT_TYPE"], 0, 6) == "image/")
			echo CFile::ShowImage($arFile, $arParams["FILE_MAX_WIDTH"], $arParams["FILE_MAX_HEIGHT"], "", "", ($arParams["FILE_SHOW_POPUP"]=="Y"));
		else
			echo '<a href="'.htmlspecialcharsbx($arFile["SRC"]).'">'.htmlspecialcharsbx($arFile["FILE_NAME"]).'</a> ('.CFile::FormatSize($arFile["FILE_SIZE"]).')';
	}

	?></span><?
	endforeach;
}
?>
