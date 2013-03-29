<?
    header('Content-type: text/html; charset=UTF-8');
	$summ = $_GET['SUMMA']*1.05;
	$typecredit = $_GET['TYPECREDIT'];

?>
<html>
<?


if(!empty($_POST['sub_form'])) {
	$submit = true;
	$errors = array();


	if(!isset($_POST['Name']) || strlen($_POST['Name']) <= 0) {
		$errors[] = "Не заполнено поле ФИО";
	}


	if(!isset($_POST['Phone_code']) || strlen($_POST['Phone_code']) <= 0) {
		$errors[] = "Не заполнено поле Код номера для связи";
	}
	else {
		//проверка корректности
		if(preg_match("|^[-0-9]{3}$|i",$_POST['Phone_code'])) {
		}
		else {
			$errors[] = "поле Код номера для связи заполнено некорректно";
		}
	}
	if(!isset($_POST['Phone_number']) || strlen($_POST['Phone_number']) <= 0) {
		$errors[] = "Не заполнено поле Номер для связи";
	}
	else {
		//проверка корректности
		if(preg_match("|^[-0-9]{7}$|i",$_POST['Phone_number'])) {
		}
		else {
			$errors[] = "поле Номер для связи заполнено некорректно";
		}
	}

	if(!isset($_POST['approve']) || strlen($_POST['approve']) <= 0) {
		$errors[] = "Вы должны согласиться с условиями Соглашения";
	}
	
	
	if(empty($errors)) {	

        $N_MERCHANTID = "366830";

        $post_data = "";
		
        foreach($_POST as $k=>$v):
			if(is_array($v)):
				foreach($v as $n=>$m):
					$post_data.= $k."[]=".$m."&";
				endforeach;
			else:
				$post_data.= $k."=".$v."&";
			endif;
		endforeach;  

        foreach($_GET as $k=>$v):
			if(is_array($v)):
				foreach($v as $n=>$m):
					$post_data.= $k."[]=".$m."&";
				endforeach;
			else:
				$post_data.= $k."=".$v."&";
			endif;
		endforeach;     
		
        
        
		$post_data .="MERCHANTID=".$N_MERCHANTID;
		
        $ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,"http://retail-credit.su/receiver/postReceiver.aspx");
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_TIMEOUT,60);
		curl_setopt($ch, CURLOPT_HEADER,0);  // DO NOT RETURN HTTP HEADERS 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  // RETURN THE CONTENTS OF THE CALL
		$res = curl_exec($ch);	 

	 
	}
}
	
?>
<div class="order">
<?
if(empty($errors) && isset($submit)) 
{   
		echo "<center><h4 class='title-head'>Ваша заявка успешно отправлена</h4></center>";
		die();
    	return;
}
if(!empty($errors) && isset($submit)) 
{?>
	<div class="errors" style="color:red;">
		<?foreach($errors as $err):?>
				<?=$err."<br/>";?>
		<?endforeach;?>
	</div>
<?}?>
<form id="creditForm" action=""  method="POST">
    <table style="font-family: Arial, Helvetica, sans-serif; font-size: 14px">
        <tr>
            <td align="left" valign="top" style="border-width: thin; border-color: #C0C0C0; border-bottom-style: solid;">
                Фамилия Имя Отчество
            </td>
            <td align="left">
                <input type="text" Name="Name" style="font-family: Arial, Helvetica, sans-serif; width: 100%; font-size: 12px" value="<? if(isset($_POST['Name'])) echo $_POST['Name'];?>"/>
            </td>
        </tr>
        <tr>
            <td align="left" valign="top" style="border-width: thin; border-color: #C0C0C0; border-bottom-style: solid;">
                Контактный телефон
            </td>
            <td align="left">
                <table width="100%">
                        <tr>
                            <td align="center" valign="top">
                                <input name="Phone_code" size="4" maxlength="5" style="font-family: Arial, Helvetica, sans-serif; width: 100%; font-size: 12px" value="<? if(isset($_POST['Phone_code'])) echo $_POST['Phone_code'];?>"/>
                                <div>Код</div>
                            </td>
                            <td align="center" valign="top">
                                <input name="Phone_number" size="10" maxlength="9" style="font-family: Arial, Helvetica, sans-serif; width: 100%; font-size: 12px" value="<? if(isset($_POST['Phone_number'])) echo $_POST['Phone_number'];?>"/>
                                <div>
                                    Номер<br />
                                    телефона&nbsp;</div>
                            </td>
                        </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border-width: thin; border-color: #C0C0C0; border-bottom-style: solid;">
                Срок
            </td>
            <td>
                <select Name="Term">
                    <option>6</option><option>10</option><option>12</option><option>24</option><option>30</option><option>36</option>
                </select>
            </td>
        </tr>
         <tr>
            <td align="left" valign="top" style="border-width: thin; border-color: #C0C0C0; border-bottom-style: solid;">
                Сумма, руб
            </td>
            <td align="left">
                <input type="text" Name="SummTovar" style="font-family: Arial, Helvetica, sans-serif; width: 100%; font-size: 12px" disabled="disabled" readonly="readonly" value="<? echo number_format($summ, 2, '.', '');?>"/>
            </td>
        </tr>
        <tr>
            <td style="border-width: thin; border-color: #C0C0C0; border-bottom-style: solid;">
                Первоначальный взнос
            </td>
            <td>
                <select Name="Start_percent">
                    <option>10</option><option>20</option><option>30</option>
                </select>
            </td>
        </tr>
        <tr>
            <td style="border-width: thin; border-color: #C0C0C0; border-bottom-style: solid;">
                Регион регистрации
            </td>
            <td align="left">
                <select Name="Region">
                    <option>Москва</option><option>Московская область</option><option>Санкт-Петербург</option><option>Ленинградская область</option>
                </select>
            </td>
        </tr>
        <tr>
            <td align="left" valign="top">
                Согласен с <a target="_blank" href="http://yes-credit.ru/agreement.htm">условиями Соглашения</a>
            </td>
            <td>
                <input type="checkbox" checked="checked" name="approve" />
            </td>
        </tr>
        <tr>
            <td align="right" valign="top" colspan="2">
			<?if ($typecredit==0) echo "Кредит <input name='Rass' type='radio' value='0' checked/>" ;?>
			<?if ($typecredit==1) echo "Рассрочка <input name='Rass' type='radio' value='1' checked/>" ;?>  
			<?if ($typecredit==2) echo "Кредит <input name='Rass' type='radio' value='0' checked/>Рассрочка <input name='Rass' type='radio' value='1'/>" ;?>  
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
			<input type="submit" name="sub_form" value=" " style="background:url(yescredit/images/yescreditsend.png) no-repeat; width:187px; height:31px; border:0;" >
            </td>
        </tr>
    </table>
    <p style="font-size: 10px; font-family: Arial;">
        В сумму включена единоразовая комиссия за оформление кредита &ndash; 5% от стоимости
        товара. В случае выбора рассрочки комиссия не взимается. БЕЗ СКРЫТЫХ КОМИССИЙ И ПЛАТЫ ЗА ВЫДАЧУ.
    </p>
    <ol style="font-size: 12px; font-family: Arial;">
        <li><i>Я выбрал товар и хочу приобрести его в кредит.</i><br />
            Заполните кредитную заявку и нажмите кнопку «Отправить заявку».</li>
        <li><i>Я заполнил и отправил кредитную заявку на сайте интернет-магазина. Что дальше?</i><br />
            В течение часа ваша заявка будет обработана, после чего с вами свяжется кредитный
            менеджер.<br />
            Вам нужно будет ответить на несколько стандартных вопросов и подтвердить свои паспортные
            данные.</li>
        <li><i>Кредитный менеджер позвонил мне.</i><br />
            После звонка ваша заявка будет рассматриваться банком.<br />
            Решение будет сообщено вам в течение 2-х часов.</li>
        <li><i>Мне позвонили и сообщили, что банк одобрил мою заявку. Что дальше?</i><br />
            Вам позвонит кредитный менеджер и договорится о времени приезда к вам для подписания
            платежных документов.<br />
            После подписания договора Вы ожидаете доставки вашей покупки и при получении оплачиваете
            первоначальный взнос.</li>
    </ol>
    <p style="font-size: 11px; font-family: Arial;">
        Заявки, принятые после 19:00 рабочего дня и в выходные, обрабатываются с 10:00 первого
        рабочего дня.
    </p>
	</form>
