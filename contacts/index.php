<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?>
<B>Адрес:</B> 656002, Алтайский край, город Барнаул, улица  Цеховая, 58.
<br /><br />
<B>Директор:</B> Ароян Юрий Рашидович<br />
<B>Зам. директора:</B> Лисицын Александр Юрьевич<br />
<B>Гл. бухгалтер:</B> Шульдякова Наталья Анатольевна <br /><br />
<B>Тел. /факс:</B><br /> (3852) 24-05-03, 24-05-28, 61-74-16 (приемная)<br />
(3852) 61-05-03 (коммерческий отдел)<br />
(3852) 61-92-92 (начальник производства)  <br /><br />
<B>e-mail:</B> <a href="mailto:bfvi@inbox.ru">bfvi@inbox.ru</a>

<div style="clear: both"></div>

<?
  $mail_to = "esvserge@mail.ru";
  $ok2="yes";
  $mail_ok = $_POST['mail_ok'];
  if ($mail_ok=="ok")
    {

  $_POST['mail_name'] = htmlspecialchars(stripslashes($_POST['mail_name']));
  $_POST['mail_subject'] = htmlspecialchars(stripslashes($_POST['mail_subject']));
  $_POST['mail_tel'] = htmlspecialchars(stripslashes($_POST['mail_tel']));
  $_POST['mail_msg'] = htmlspecialchars(stripslashes($_POST['mail_msg']));

  $captcha_sid = $_POST['captcha_sid'];
  $captcha_word = $_POST['captcha_word'];

//  if(empty($_POST['mail_subject'])) echo"<font color='red'>Введите адрес получателя</font>";

// проверяем правильности заполнения с помощью регулярного выражения



extract($_POST, EXTR_SKIP);
$err="";
  if (!preg_match("/^[0-9a-z_]+@[0-9a-z_^\.]+\.[a-z]{2,3}$/i", $_POST['mail_subject'])||empty($_POST['mail_subject']))
    $err= "<font color='red' size='3pt'><br><b>Введите адрес в виде somebody@server.com</b></font><br />";

  if (!$APPLICATION->CaptchaCheckCode($captcha_word, $captcha_sid) && ($ID == 0))
      {
//       ShowMessage('Введен неправильный защитный код!');
       $err.="<br/><font color='red' size='3pt'><b>Введен неправильный защитный код!<br/> Попробуйте ввести код еще раз. <br/></b></font>";
       }
  if ($err!="") echo $err;
    else
     {


  $picture = "";

  // Если поле выбора вложения не пустое - закачиваем его на сервер

  if (!empty($_FILES['mail_file']['tmp_name']))

  {

    // Закачиваем файл

    $path = $_FILES['mail_file']['name'];

    if (copy($_FILES['mail_file']['tmp_name'], $path)) $picture = $path;

  }

  $mail_name = $_POST['mail_name'];
  $mail_subject = $_POST['mail_subject'];
  $mail_tel = $_POST['mail_tel'];
  $mail_msg = $_POST['mail_msg'];


  // Отправляем почтовое сообщение

  $message="<body>
  <br><b>ФИО:</b> ".$mail_name."
  <br><b>Телефон :</b> ".$mail_tel."
  <br><b>E-mail:</b> ".$mail_subject."
  <br><b>Текст сообщения:</b><br>".$mail_msg."</body>";
  $message2="<body>
  <br><b>ФИО:</b> ".$mail_name."
  <br><b>Телефон (с кодом города):</b> ".$mail_tel."
  <br><b>E-mail:</b> ".$mail_subject."
  <br><b>Текст сообщения:</b><br>".$mail_msg."
  <br><br>
  ВАШЕ СООБЩЕНИЕ ДОСТАВЛЕНО!
  </body>";


  $header="Content-type: text/html; charset=windows-1251";
  $header.="From: ";
  $header.="Subject: ";
  $header.="Content-type: text/html; charset=windows-1251";

  $ok=mail($mail_to, "www.grosslogistics.ru", $message, $header);
  $ok2=mail($mail_subject, "www.grosslogistics.ru", $message2, $header);



  if ($ok)
        {
?>
<p><font color="green">
<b>Ваше письмо отправлено!</b>
</font>
</p>

<?
$ok2="no";
}

  // Вспомогательная функция для отправки почтового сообщения с вложением



 }

}
if ($ok2=="yes")
{
?>



<br><br><br>
<p>Вы можете отправить нам сообщение, заполнив форму:</p>
<form name="cont" action="/contacts/index.php" method="POST" enctype="multipart/form-data">
<input type="hidden" name="mail_ok" value="ok">
<table border=0 cellpadding=5>
 <tr>
   <td>
    ФИО
    </td><td>
    <input type="text"  class="inputtext"  name="mail_name" value="<?=@$_POST['mail_name']?>">
   </td>
  </tr>
 <tr>
    <td>
    E-mail
    </td><td>
    <input type="text"  class="inputtext"  name="mail_subject" value="<?=@$_POST['mail_subject']?>">
    </td>
  </tr>
 <tr>
    <td>
    Телефон
    </td><td>
    <input type="text"  class="inputtext"  name="mail_tel" value="<?=@$_POST['mail_tel']?>"><br>
    </td>
  </tr>
 <tr>
    <td>
    Текст сообщения
    </td><td>
    <textarea name="mail_msg" cols="49" rows="5"  class="inputtextarea"><?=@$_POST['mail_msg']?></textarea>
    </td>
  </tr>


  <tr>
    <td>
        Защита от автозаполнения:
    </td>
    <td>
<?
$capCode = $GLOBALS["APPLICATION"]->CaptchaGetCode();
?>
<input type="hidden" name="captcha_sid" value="<?= htmlspecialchars($capCode) ?>">
<img align="left" src="/bitrix/tools/captcha.php?captcha_sid=<?= htmlspecialchars($capCode) ?>" width="180" height="40">

    </td>
</tr>
<tr>
    <td>

      Введите символы, <br />изображенные на картинке:

    </td>
    <td><input type="text" name="captcha_word" size="30" maxlength="50" value=""> </td>
</tr>



  <tr>
    <td>&nbsp;
    </td><td align="right">
<input type="reset" value="Очистить" class="but">
<input type="submit" value="Отправить" class="but">
    </td>
  </tr>


</table>
</form>

<?
}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>