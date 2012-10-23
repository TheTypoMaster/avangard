<?
function send_mail($to, $thm, $html, $path, $from, $from_name)
 {
 $path=$_SERVER["DOCUMENT_ROOT"].$path;
   $fp = fopen($path,"r+");

   if (!$fp)

     {
       print "Файл $path не может быть прочитан";
       exit();
     }

   $file = fread($fp, filesize($path));

   fclose($fp);

   $boundary = "--".md5(uniqid(time())); // генерируем разделитель

   $headers = "MIME-Version: 1.0\n";
   $headers .="Content-Type: multipart/mixed; boundary=\"$boundary\"\n";
   $headers .="From: $from_name <$from>\n";

   $multipart .= "--$boundary\n";

   $kod = 'windows-1251'; // или $kod = 'koi8-r;';

   $multipart .= "Content-Type: text/html; charset=$kod\n";

   $multipart .= "Content-Transfer-Encoding: Quot-Printed\n\n";

   $multipart .= "$html\n\n";



   $message_part = "--$boundary\n";

   $message_part .= "Content-Type: application/octet-stream\n";

   $message_part .= "Content-Transfer-Encoding: base64\n";

   $message_part .= "Content-Disposition: attachment; filename = \"".$path."\"\n\n";

   $message_part .= chunk_split(base64_encode($file))."\n";

   $multipart .= $message_part."--$boundary--\n";



   if(!mail($to, $thm, $multipart, $headers))
      {
        echo "<font color='red'><b>К сожалению, письмо не отправлено. Попробуйте позже.</b></font>";
        return false;//exit();
      }
     return true;
 }

?>
<?

function CaptchaCheckCode($captcha_word, $captcha_sid)
{
    include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");

    $cpt = new CCaptcha();
    return $cpt->CheckCode($captcha_word, $captcha_sid);
}

?>
