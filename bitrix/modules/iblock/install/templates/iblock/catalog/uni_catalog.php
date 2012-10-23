<?
//IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("iblock")):
//*********************************************************

$IBLOCK_TYPE_ID = Trim($IBLOCK_TYPE_ID);		// ID of informational block type
$LIST_PAGE_TEMPLATE = Trim($LIST_PAGE_TEMPLATE);
$CACHE_TIME = IntVal($CACHE_TIME);
$bDisplayPanel = ($DISPLAY_PANEL == "Y") ? True : False;

$LIST_PAGE_TEMPLATE_DEF = "catalog.php?BID=#IBLOCK_ID#";

function UNI_CATALOG_MakeRealPath($template, $ar)
{
	return
		str_replace("//", "/",
			str_replace("#LANG#", $ar["LANG_DIR"],
				str_replace("#SITE_DIR#", SITE_DIR,
					str_replace("#SERVER_NAME#", SITE_SERVER_NAME,
						str_replace("#IBLOCK_ID#", $ar["ID"], $template)
					)
				)
			)
		);
}

$cache = new CPHPCache;
$cache_id = "iblock_uni_catalog_".$IBLOCK_TYPE_ID."_".$LIST_PAGE_TEMPLATE."_".SITE_ID;

if ($bDisplayPanel)
	CIBlock::ShowPanel(0, 0, 0, $IBLOCK_TYPE_ID);

if ($CACHE_TIME>0 && $cache->InitCache($CACHE_TIME, $cache_id, "/".SITE_ID."/catalog/uni_catalog.php/"))
{
	$cache->Output();
}
else
{
	if ($CACHE_TIME>0)
		$cache->StartDataCache($CACHE_TIME, $cache_id, "/".SITE_ID."/catalog/uni_catalog.php/");


	$dbIBlocks = CIBlock::GetList(
			array("SORT" => "ASC", "NAME" => "ASC"),
			array("type" => $IBLOCK_TYPE_ID, "LID" => SITE_ID, "ACTIVE"=>"Y")
		);
	$dbIBlock = new CIBlockResult($dbIBlocks);

	if ($arIBlock = $dbIBlock->Fetch())
	{
		?>
		<table border="0" cellpadding="5" cellspacing="0" width="100%">
			<?
			do
			{
				$LIST_PAGE_TEMPLATE_tmp = $LIST_PAGE_TEMPLATE;
				if (strlen($LIST_PAGE_TEMPLATE_tmp)<=0)
					$LIST_PAGE_TEMPLATE_tmp = $arIBlock["LIST_PAGE_URL"];
				if (strlen($LIST_PAGE_TEMPLATE_tmp)<=0)
					$LIST_PAGE_TEMPLATE_tmp = $LIST_PAGE_TEMPLATE_DEF;
				$LIST_PAGE_TEMPLATE_tmp = UNI_CATALOG_MakeRealPath($LIST_PAGE_TEMPLATE_tmp, $arIBlock);
				?>
				<tr>
					<td valign="top">
						<font class="text">
							<?echo ShowImage($arIBlock["PICTURE"], 100, 100, "border='0' align='left' alt='".$arIBlock["NAME"]."'", $LIST_PAGE_TEMPLATE_tmp);?>
							<b><a href="<?= $LIST_PAGE_TEMPLATE_tmp ?>"> <?= $arIBlock["NAME"]?></a></b><br><br>
							<?= $arIBlock["DESCRIPTION"];?>
						</font>
					</td>
				</tr>
				<tr><td valign="top"><hr></td></tr>
				<?
			}
			while ($arIBlock = $dbIBlock->Fetch())
			?>
		</table>
		<?
	}

	if ($CACHE_TIME>0)
		$cache->EndDataCache(array());
}

//*********************************************************
endif;
?>