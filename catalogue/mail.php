<?
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