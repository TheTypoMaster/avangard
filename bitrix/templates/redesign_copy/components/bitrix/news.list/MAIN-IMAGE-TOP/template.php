
<?

$arOutput = Array(	"DETAIL_PAGE_URL",
					"PROPERTY_TOP_PHOTO",
					"IBLOCK_SECTION_ID",
					"ID",
					"SECTION_ID"
					);

   $items = GetIBlockElementListEx("catalogue", "furniture", Array(),
              Array("RAND" => "RAND",),1, Array("!PROPERTY_TOP"=>false), $arOutput);

   while($arItem = $items->GetNext())
   {
      echo ShowImage($arItem["PROPERTY_TOP_PHOTO_VALUE"], 530, 250, "border='0' style='margin-top:3px;'",
			  "/catalogue/".$arItem["IBLOCK_SECTION_ID"]."/tov_".$arItem["ID"].".html");
   }

?>







