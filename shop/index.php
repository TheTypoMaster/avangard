<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "»нтернет магазин м€гкой мебели.  упить недорогой диван онлайн");
$APPLICATION->SetTitle("»нтернет магазин м€гкой мебели.  упить недорогой диван онлайн");
?> 

<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	".default",
	Array(
		"AREA_FILE_SHOW" => "page",
		"AREA_FILE_SUFFIX" => "top_main_text",
		"EDIT_TEMPLATE" => "page_inc.php"
	)
);?>
<p></p>
<?
### список разделов интернет-магазина
$res = CIBlockSection::GetList(
	Array("LEFT_MARGIN"=>"ASC"), 
	Array("IBLOCK_ID"=>31, "SECTION_ID"=>"false","ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y"), 
	true,
	Array("ID", "NAME", "PICTURE", "UF_COLLECTION")
);
$elements = array();  
while($arSection = $res->GetNext())
	$elements[] = $arSection;

### список коллекций сайта
$res_col = CIBlockElement::GetList(
	Array("SORT"=>"ASC"), 
	Array("IBLOCK_ID"=>9,"ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y"), 
	false,
	false,
	Array("ID", "NAME")
);
$collection = array();
$collection_count = 0;
while($arSection_col = $res_col->GetNext())
{
	$collection_count++;
	$collection[$collection_count] = $arSection_col;
	foreach($elements as $el)
		if ($el[UF_COLLECTION] == $arSection_col[ID])
			$collection[$collection_count]["el"][] = $el;
}
?> 
<table width="738" align="center">
<?
$kol = 0;
foreach($collection as $co)
{
	if ($co[el]) 
	{
		$rows_count = count($co[el]); 
		$kol++;
		if(($kol > 1) && ($kol <= $collection_count))
			echo '<tr><td colspan="5" class="gray_line_small"></td></tr>';
		?>
		<tr>
			<td colspan="5"><b><?=$co[NAME] ?></b></td>
		</tr>
		<?$i=0;
		foreach($co[el] as $cur_elem)
		{
			$i++;
			$rows_count--;
			if($i == 1)
				//echo '<tr class="divan_row last_row">';
				echo "<tr class='divan_row ".($rows_count<3 ? 'last_row' : '')."'>";
			$elem_picture = CFile::GetPath($cur_elem["PICTURE"]);
			?>
			<td class="catalog_td">			
				<a href="/shop/catalogue/?SECTION_ID=<?=$cur_elem['ID']?>"><img class="catalog_picture" src="<?=$elem_picture ?>" alt="<?=$cur_elem['NAME'] ?>"></a><br>
				<a class="catalog_name" href="/shop/catalogue/?SECTION_ID=<?=$cur_elem['ID']?>"><?=$cur_elem['NAME'] ?></a>
			</td>
			<?
			if($i < 3) echo '<td width="26"></td>'; 			 
			if($i == 3){
				$i = 0;
				echo "</tr>";
			}				
		}
	}
}
?>
</table>
<br />
<br /> 

 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>