<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Выбор ткани");
$divan_id = !empty($_GET['id']) ? $_GET['id'] : '3099';
?> 
<div class="gray_td"> 
  <h1>3D МОДЕЛЬ. ПОДБОР ТКАНИ.</h1>
 </div>

<?
header('Location: http://rvmarket.ru/divan'.$divan_id.'.html');
?>
<br />
 
<br />


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>