<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("������� �������");
?> 
<p> 
  <table width="80%" border="0" cellspacing="5" cellpadding="0"> 
    <tbody>
      <tr> <td colspan="2"> 
          <div align="center"><strong>���������� ����������� �� www.avangard.biz! 
              <br />
            '; $message .= '</strong></div>
        </td> </tr>
     
      <tr> <td><strong>�������� ������:</strong></td> <td>&nbsp;</td> </tr>
     
      <tr> <td>��������:</td> <td>'; $message .= &quot;$name&quot;; $message .='
          <br />
        ' ; $message .= &quot;$f_type&quot;; $message .= '</td> </tr>
     
      <tr> <td> ������:</td> <td><a href=";

$message .= &quot;$url&quot;;
$message .= ">'; $message .= &quot;$url&quot;; $message .= '</a> </td> </tr>
     
      <tr> <td colspan="2">
          <div align="left">
            <br />
           <img src="'.$_SERVER[" document_root"].$pic_path.'"="" style="width:150px" /> 
            <br \="" />
          
            <br \="" />
           '; $message .= &quot;$ptext&quot;; $message .='
            <br />
          
            <br />
          '; $message .= &quot;$dtext&quot;; $message .=' </div>
         </td> </tr>
     
      <tr> <td> ���������:</td> <td>'; $message .= &quot;$collection&quot;; $message .= ' </td> </tr>
     
      <tr> <td>������������:</td> <td> '; $message .= &quot;$complect&quot;; $message .= '</td> </tr>
     
      <tr> <td>����������� �����������:</td> <td> '; $message .= &quot;$descr&quot;; $message .= '</td> </tr>
     
      <tr> <td><strong>����������� ��������������:</strong></td> <td>&nbsp;</td> </tr>
     
      <tr> <td>�����:</td> <td>'; $message .= &quot;$length&quot;; $message .= '</td> </tr>
     
      <tr> <td>������:</td> <td>'; $message .= &quot;$width&quot;; $message .= '</td> </tr>
     
      <tr> <td>������:</td> <td>'; $message .= &quot;$height &quot; ; $message .= '</td> </tr>
     
      <tr> <td>�������� �����:</td> <td>'; $message .= &quot;$places&quot;; $message .= '</td> </tr>
     
      <tr> <td>�������� �������������:</td> <td>'; $message .= &quot; $transformation &quot;; $message .= '</td> </tr>
     </tbody>
  </table>
 </p>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>