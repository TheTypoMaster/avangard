<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$arColors = Array(
	'FF0000', 'FFFF00', '00FF00', '00FFFF', '0000FF', 'FF00FF', 'FFFFFF', 'EBEBEB', 'E1E1E1', 'D7D7D7', 'CCCCCC', 'C2C2C2', 'B7B7B7', 'ACACAC', 'A0A0A0', '959595',
	'EE1D24', 'FFF100', '00A650', '00AEEF', '2F3192', 'ED008C', '898989', '7D7D7D', '707070', '626262', '555', '464646', '363636', '262626', '111', '000000',
	'F7977A', 'FBAD82', 'FDC68C', 'FFF799', 'C6DF9C', 'A4D49D', '81CA9D', '7BCDC9', '6CCFF7', '7CA6D8', '8293CA', '8881BE', 'A286BD', 'BC8CBF', 'F49BC1', 'F5999D',
	'F16C4D', 'F68E54', 'FBAF5A', 'FFF467', 'ACD372', '7DC473', '39B778', '16BCB4', '00BFF3', '438CCB', '5573B7', '5E5CA7', '855FA8', 'A763A9', 'EF6EA8', 'F16D7E',
	'EE1D24', 'F16522', 'F7941D', 'FFF100', '8FC63D', '37B44A', '00A650', '00A99E', '00AEEF', '0072BC', '0054A5', '2F3192', '652C91', '91278F', 'ED008C', 'EE105A',
	'9D0A0F', 'A1410D', 'A36209', 'ABA000', '588528', '197B30', '007236', '00736A', '0076A4', '004A80', '003370', '1D1363', '450E61', '62055F', '9E005C', '9D0039',
	'790000', '7B3000', '7C4900', '827A00', '3E6617', '045F20', '005824', '005951', '005B7E', '003562', '002056', '0C004B', '30004A', '4B0048', '7A0045', '7A0026'
);
?>
<div id="ColorPick" style="visibility:hidden;position:absolute;top:0;left:0 ">
<table cellspacing="0" cellpadding="1" border="0" bgcolor="#ABADB3">
<tr>
<td>
<table cellspacing="1" cellpadding="0" border="0" bgcolor="#FFFFFF">
<?
for($i=0;$i<112;$i++) 
{
	$t_curCOL="#".$arColors[$i];
	echo ($i%16==0 && $i >= 16) ? "</tr>" : "";
	echo ($i%16==0) ? "<tr>" : "";
	echo '<td bgcolor="'.$t_curCOL.'" onmousedown="alterfont(\''.$t_curCOL.'\', \'COLOR\')"><img src="/bitrix/images/1.gif" border="0" width="15" height="15"></td>';
}
?>
</tr>
</table>
</td>
</tr>
</table></div>

<div id="smilesPanel" style="visibility:hidden;position:absolute;top:0;left:0 ">
<table cellspacing="0" cellpadding="1" border="0" bgcolor="#ABADB3">
<tr>
<td>
<table cellspacing="0" cellpadding="5" border="0" bgcolor="#FFFFFF">
<?
$i = 0;
$cols = $arParams["SMILES_COLS"];
foreach($arResult["Smiles"] as $arSmiles)
{
	echo ($i%$cols==0 && $i >= $cols) ? "</tr>" : "";
	echo ($i%$cols==0) ? "<tr>" : "";

	?>
	<td onmousedown="emoticon('<?=$arSmiles["TYPE"]?>')" ><img src="/bitrix/images/blog/smile/<?=$arSmiles["IMAGE"]?>" width="<?=$arSmiles["IMAGE_WIDTH"]?>" height="<?=$arSmiles["IMAGE_HEIGHT"]?>" title="<?=$arSmiles["LANG_NAME"]?>"style="cursor:pointer"></td>
	<?
	$i++;
}
?>
</tr>
</table>
</td>
</tr>
</table></div>


<script language=JavaScript>
var text_enter_url = "<?echo GetMessage("BPC_TEXT_ENTER_URL");?>";
var text_enter_url_name = "<?echo GetMessage("BPC_TEXT_ENTER_URL_NAME");?>";
var text_enter_image = "<?echo GetMessage("BPC_TEXT_ENTER_IMAGE");?>";
var list_prompt = "<?echo GetMessage("BPC_LIST_PROMPT");?>";
var error_no_url = "<?echo GetMessage("BPC_ERROR_NO_URL");?>";
var error_no_title = "<?echo GetMessage("BPC_ERROR_NO_TITLE");?>";

function ShowImageUpload()
{
	win = window.open(null,null,'height=150,width=400');
	<?
		$L = explode("\n",$image_form);
		foreach($L as $line)
		{
			$line = CUtil::JSEscape($line);
			echo "win.document.write('".$line."');\n";
		}
	?>
	win.document.close();
}

var last_div = '';
function showComment(key, subject, error, comment, userName, userEmail)
{
	if(!imgLoaded)
	{
		if(comment)
		{
			comment = comment.replace(/\n/g, '\\n');
			comment = comment.replace(/'/g, "\\'");
			comment = comment.replace(/"/g, '\\"');
		}
		else
			comment = '';
		setTimeout("showComment('"+key+"', '"+subject+"', '"+error+"', '"+comment+"', '"+userName+"', '"+userEmail+"')", 500);
	}
	else
	{
		<?
		if($arResult["use_captcha"]===true)
		{
			?>
			var im = document.getElementById('captcha');
			document.getElementById('captcha_del').appendChild(im);
			<?
		}
		?>
		var cl = document.getElementById('form_c_del').cloneNode(true);
		var ld = document.getElementById('form_c_del');
		ld.parentNode.removeChild(ld);
		document.getElementById('form_comment_' + key).appendChild(cl);
		document.getElementById('form_c_del').style.display = "block";
		document.form_comment.parentId.value = key;
		document.form_comment.edit_id.value = '';
		document.form_comment.act.value = 'add';
		document.form_comment.post.value = '<?=GetMessage("B_B_MS_SEND")?>';

		document.form_comment.action = document.form_comment.action+"#"+key;

		<?
		if($arResult["use_captcha"]===true)
		{
			?>
			var im = document.getElementById('captcha');
			document.getElementById('div_captcha').appendChild(im);
			im.style.display = "block";
			<?
		}
		?>

		if(error == "Y")
		{
			if(comment.length > 0)
			{
				comment = comment.replace(/\/</gi, '<');
				comment = comment.replace(/\/>/gi, '>');
				document.form_comment.comment.value = comment;
			}
			if(userName.length > 0)
			{
				userName = userName.replace(/\/</gi, '<');
				userName = userName.replace(/\/>/gi, '>');
				document.form_comment.user_name.value = userName;
			}
			if(userEmail.length > 0)
			{
				userEmail = userEmail.replace(/\/</gi, '<');
				userEmail = userEmail.replace(/\/>/gi, '>');
				document.form_comment.user_email.value = userEmail;
			}
			if(subject.length>0 && document.form_comment.subject)
			{
				subject = subject.replace(/\/</gi, '<');
				subject = subject.replace(/\/>/gi, '>');
				document.form_comment.subject.value = subject;
			}
		}
		last_div = key;
	}
	//document.form_comment.comment.focus();
	return false;
}

function editComment(key, subject, comment)
{
	if(!imgLoaded)
	{
		comment = comment.replace(/\n/g, '\\n');
		comment = comment.replace(/'/g, "\\'");
		comment = comment.replace(/"/g, '\\"');
		setTimeout("editComment('"+key+"', '"+subject+"', '"+comment+"')", 500);
	}
	else
	{
		var cl = document.getElementById('form_c_del').cloneNode(true);
		var ld = document.getElementById('form_c_del');
		ld.parentNode.removeChild(ld);
		document.getElementById('form_comment_' + key).appendChild(cl);
		document.getElementById('form_c_del').style.display = "block";
		document.form_comment.edit_id.value = key;
		document.form_comment.act.value = 'edit';
		document.form_comment.post.value = '<?=GetMessage("B_B_MS_SAVE")?>';
		document.form_comment.action = document.form_comment.action+"#"+key;

		if(comment.length > 0)
		{
			comment = comment.replace(/\/</gi, '<');
			comment = comment.replace(/\/>/gi, '>');
			document.form_comment.comment.value = comment;
		}
		if(subject.length>0 && document.form_comment.subject)
		{
			subject = subject.replace(/\/</gi, '<');
			subject = subject.replace(/\/>/gi, '>');
			document.form_comment.subject.value = subject;
		}
		last_div = key;
	}
	
	return false;
}
</script>