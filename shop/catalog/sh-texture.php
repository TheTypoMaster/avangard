<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Образец ткани");
?> 
<? 
if (!empty($_GET['tx']))
{
   $idtx = intval($_GET['tx']);
   $arSelect = Array("ID", "NAME", "DETAIL_PICTURE");
   $arFilter = Array("IBLOCK_ID"=>20, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "ID" => $idtx);
   $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
   $ob = $res->GetNextElement();
   $arFields = $ob->GetFields();
   $resFileTexture = CFile::GetByID($arFields[DETAIL_PICTURE]);
   $arFileTexture = $resFileTexture->Fetch();
   //echo 'Название ткани: '.$arFields[NAME].'<br>';
   //echo "<img src=\"/upload/".$arFileTexture[SUBDIR]."/".$arFileTexture[FILE_NAME]."\" />";
   //$name = $arFields[NAME];
   //$path = '/upload/'.$arFileTexture[SUBDIR].'/'.$arFileTexture[FILE_NAME];
echo  '/upload/'.$arFileTexture[SUBDIR].'/'.$arFileTexture[FILE_NAME];
}
else 
  {
    //$name = 'no';
    //$path = '';
  }
//echo '{"name":"'.$name.'","pic_path":"'.$path.'"}';
//exit;
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>