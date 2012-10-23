<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2010 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
ini_set("track_errors", "1");
ini_set('display_errors', 1);
error_reporting(E_ALL &~E_NOTICE);
$message = null;

# NO AUTH TESTS
if ($_REQUEST['unique_id'])
{
	if ($_REQUEST['unique_id'] != checker_get_unique_id())
		die('<h1>Permission denied: UNIQUE ID ERROR</h1>');

	if ($_GET['socket_test']) 
	{
		echo "SUCCESS";
	} 
	elseif ($_GET['dbconn_test'])
	{
		ob_start();
		define('NOT_CHECK_PERMISSIONS', true);
		require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
		$buff = '';
		while(ob_get_level())
		{
			$buff .= ob_get_contents();
			ob_end_clean();
		}
		ob_end_clean();
		if (function_exists('mb_internal_encoding'))
			mb_internal_encoding('ISO-8859-1');
		echo $buff === '' ? 'SUCCESS' : 'Length: '.strlen($buff).' ('.$buff . ')';
	}
	elseif ($_GET['upload_test']) 
	{
		if (function_exists('mb_internal_encoding'))
			mb_internal_encoding('ISO-8859-1');
		
		$dir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp';
		if (!file_exists($dir))
			mkdir($dir);

		$binaryData = '';
		for($i=40;$i<240;$i++)
			$binaryData .= chr($i);
		if ($_REQUEST['big'])
			$binaryData = str_repeat($binaryData, 21000);

		if ($_REQUEST['raw'])
			$binaryData_received = file_get_contents('php://input');
		elseif (move_uploaded_file($tmp_name = $_FILES['test_file']['tmp_name'], $image = $dir.'/site_checker.bin'))
		{
			$binaryData_received = file_get_contents($image);
			unlink($image);
		}
		else
		{
			echo 'move_uploaded_file('.$tmp_name.','.$image.')=false'."\n";
			echo '$_FILES='."\n";
			print_r($_FILES);
			die();
		}

		if ($binaryData === $binaryData_received)
			echo "SUCCESS";
		else
			echo 'strlen($binaryData)='.strlen($binaryData).', strlen($binaryData_received)='.strlen($binaryData_received);
	} 
	elseif ($_GET['post_test']) 
	{
		$ok = true;
		for ($i=0;$i<201;$i++)
			$ok = $ok && ($_POST['i'.$i] == md5($i));

		echo $ok ? 'SUCCESS' : 'FAIL';
	} 
	elseif ($_GET['memory_test']) 
	{
		@ini_set("memory_limit", "512M");
		$max = intval($_GET['max']);
		if ($max) 
		{
			for($i=1;$i<=$max;$i++)
			       $a[] = str_repeat(chr($i),1024*1024); // 1 Mb

			echo "SUCCESS";
		}
	} 
	elseif ($_GET['auth_test']) 
	{
		$remote_user = $_SERVER["REMOTE_USER"] ? $_SERVER["REMOTE_USER"] : $_SERVER["REDIRECT_REMOTE_USER"];
		$strTmp = base64_decode(substr($remote_user,6));
		if ($strTmp)
			list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', $strTmp);
		if ($_SERVER['PHP_AUTH_USER']=='test_user' && $_SERVER['PHP_AUTH_PW']=='test_password') 
			echo('SUCCESS');
	} 
	elseif ($_GET['session_test']) 
	{
		session_start();
		echo $_SESSION['CHECKER_CHECK_SESSION'];
		$_SESSION['CHECKER_CHECK_SESSION'] = 'SUCCESS';
	} 
	elseif ($_GET['redirect_test'])
	{
		$GLOBALS['_SERVER'] = $GLOBALS['_REQUEST'];
		function IsHTTPS()
		{
			return ($_SERVER["SERVER_PORT"]==443 || strtolower($_SERVER["HTTPS"])=="on");
		}

		function SetStatus($status)
		{
			$bCgi = (stristr(php_sapi_name(), "cgi") !== false);
			$bFastCgi = ($bCgi && (array_key_exists('FCGI_ROLE', $_SERVER) || array_key_exists('FCGI_ROLE', $_ENV)));
			if($bCgi && !$bFastCgi)
				header("Status: ".$status);
			else
				header($_SERVER["SERVER_PROTOCOL"]." ".$status);
		}

		if ($_REQUEST['done'])
			echo 'SUCCESS';
		else
		{
			SetStatus("302 Found");
			$protocol = (IsHTTPS() ? "https" : "http");
			$host = $_SERVER['HTTP_HOST'];
			if($_SERVER['SERVER_PORT'] <> 80 && $_SERVER['SERVER_PORT'] <> 443 && $_SERVER['SERVER_PORT'] > 0 && strpos($_SERVER['HTTP_HOST'], ":") === false)
				$host .= ":".$_SERVER['SERVER_PORT'];
			$url = "?redirect_test=Y&done=Y&unique_id=".checker_get_unique_id();
			header("Request-URI: ".$protocol."://".$host.$url);
			header("Content-Location: ".$protocol."://".$host.$url);
			header("Location: ".$protocol."://".$host.$url);
			exit;
		}
	}
	die();
}
# END NO AUTH TESTS

define('DEBUG_FLAG', str_replace('\\','/',$_SERVER['DOCUMENT_ROOT'] . '/bitrix/site_checker_debug'));
if (file_exists(DEBUG_FLAG))
{
	define('NOT_CHECK_PERMISSIONS', true);
	define("BX_COMPRESSION_DISABLED", true);
	define("NO_KEEP_STATISTIC", true);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
define('FIX_LINK', ' <a href="javascript:show_popup(\'' . GetMessage('SC_FIX_DATABASE') . '\', \'?fix_mode=2\', \'' . GetMessage('SC_FIX_DATABASE_CONFIRM') . '\')">' . GetMessage('SC_FIX') . '</a>');

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/prolog.php");
define("HELP_FILE", "utilities/site_checker.php");
error_reporting(E_ALL &~E_NOTICE);

////////////////////////////////////////////////////////////////////////
//////////   PARAMS   //////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
define(SUPPORT_PAGE, LANGUAGE_ID == 'ru' ? 'http://www.1c-bitrix.ru/support/' : 'http://www.bitrixsoft.com/support/');

$Apache_vercheck_min = "1.3.0";
$Apache_vercheck_max = "";

$IIS_vercheck_min = "5.0.0";
$IIS_vercheck_max = "";

////////////////////////////////////////////////////////////////////////
//////////   END PARAMS   //////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
if ($USER->CanDoOperation('view_other_settings'))
{
	if (file_exists(DEBUG_FLAG))
		if (!unlink(DEBUG_FLAG))
			CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE"=>'Can\'t delete ' . DEBUG_FLAG));
}
elseif(!defined('NOT_CHECK_PERMISSIONS') || NOT_CHECK_PERMISSIONS !== true)
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("SC_TEST_CONFIG"), "ICON" => "site_check", "TITLE" => GetMessage("SC_SUBTITLE_REQUIED")),
	array("DIV" => "edit2", "TAB" => GetMessage("SC_TAB_2"), "ICON" => "site_check", "TITLE" => GetMessage("SC_SUBTITLE_DISK")),
//	array("DIV" => "edit3", "TAB" => GetMessage('SC_TEST_CONFIG'), "ICON" => "site_check", "TITLE" => GetMessage('SC_TEST_CONFIG')),
//	array("DIV" => "edit4", "TAB" => GetMessage("SC_TAB_4"), "ICON" => "site_check", "TITLE" => GetMessage("SC_SUBTITLE_SITE_MODULES")),
	array("DIV" => "edit5", "TAB" => GetMessage("SC_TAB_5"), "ICON" => "site_check", "TITLE" => GetMessage("SC_TIK_TITLE")),
);

if ($_POST['access_check'])
{
	if (defined('NOT_CHECK_PERMISSIONS') && NOT_CHECK_PERMISSIONS ===true || check_bitrix_sessid())
	{
		$ob = new CSearchFiles;
		$ob->TimeLimit = 10;

		if ($_REQUEST['break_point']) 
			$ob->SkipPath = $_REQUEST['break_point'];

		$check_type = $_REQUEST['check_type'];

		$sNextPath = '';
		if ($check_type == 'upload')
		{
			if (!file_exists($tmp = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT.'/tmp'))
				mkdir($tmp);
			$upload = $_SERVER['DOCUMENT_ROOT'].'/'.COption::GetOptionString('main', 'upload_dir', 'upload');

			if (0===strpos($_REQUEST['break_point'], $upload))
				$path = $upload;
			else
			{
				$path = $tmp;
				$sNextPath = $upload;
			}
		}
		elseif($check_type == 'kernel')
			$path = $_SERVER['DOCUMENT_ROOT'].BX_ROOT;
		elseif($check_type == 'personal')
			$path = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT;
		else
		{
			$path = $_SERVER['DOCUMENT_ROOT'];
			$check_type = 'full';
		}

		if ($ob->Search($path))
		{
			if ($ob->BreakPoint || $sNextPath)
			{
				if ($ob->BreakPoint)
					$sNextPath = $ob->BreakPoint;
				$cnt_total = intval($_REQUEST['cnt_total']) + $ob->FilesCount;
				?><form method=post id=postform>
					<input type=hidden name=access_check value="Y">
					<input type=hidden name=lang value="<?=LANGUAGE_ID?>">
					<?=bitrix_sessid_post();?>
					<input type=hidden name=cnt_total value="<?=$cnt_total?>">
					<input type=hidden name=check_type value="<?=$check_type?>">
					<input type=hidden name=break_point value="<?=htmlspecialchars($sNextPath)?>">
				</form>
				<?
				CAdminMessage::ShowMessage(array(
					'TYPE' => 'OK',
					'HTML' => true,
					'MESSAGE' => GetMessage('SC_TESTING'),
					'DETAILS' => str_replace(array('#NUM#','#PATH#'),array($cnt_total,$sNextPath),GetMessage('SC_FILES_CHECKED')),
					)
				);
				?>
				<script>
				if (parent.document.getElementById('access_submit').disabled)
					window.setTimeout("parent.ShowWaitWindow();document.getElementById('postform').submit()",500);
				</script><? 
			}
			else
			{
				if ($check_type == 'full')
					COption::SetOptionString('main', 'site_checker_access', 'Y');
				CAdminMessage::ShowMessage(Array("TYPE"=>"OK", "MESSAGE"=>GetMessage("SC_FILES_OK")));
				?><script>parent.access_check_start(0);</script><?
			}
		}
		else
		{
			COption::SetOptionString('main', 'site_checker_access', 'N');
			CAdminMessage::ShowMessage(array(
				'TYPE' => 'ERROR',
				'MESSAGE' => GetMessage("SC_FILES_FAIL"),
				'DETAILS' => implode("<br>\n",$ob->arFail),
				'HTML' => true
				)
			);
			?><script>parent.access_check_start(0);</script><?
		}
	}
	else
		echo '<h1>Permission denied: BITRIX SESSID ERROR</h1>';
	exit;
}
elseif($_REQUEST['test_start'])
{
	if (defined('NOT_CHECK_PERMISSIONS') && NOT_CHECK_PERMISSIONS ===true || check_bitrix_sessid())
	{
		$oTest = new CSiteCheckerTest($_REQUEST['step'],(int) $_REQUEST['fix_mode']);
		if ($_REQUEST['global_test_vars'] && ($d = base64_decode($_REQUEST['global_test_vars'])))
		{
			if (!CheckSerializedData($d))
				die('Error unserialize');
			$oTest->arTestVars = unserialize($d);
		}
		else
			$oTest->arTestVars = array();
		if ($oTest->fix_mode)
			$oTest->timeout = 3;

		$oTest->Start();
		if ($oTest->percent < 100)
		{
			$strNextRequest = '&step='.$oTest->step.'&global_test_vars='.base64_encode(serialize($oTest->arTestVars));
			$strFinalStatus = '';
		}
		else
		{
			$strNextRequest = '';
			$strFinalStatus = '100%';
		}
		echo '
			iPercent = '.$oTest->percent.';
			test_percent = '.$oTest->test_percent.';
			strCurrentTestFunc = "'.$oTest->last_function.'";
			strCurrentTestName = "'.CUtil::JSEscape($oTest->strCurrentTestName).'";
			strNextTestName = "'.CUtil::JSEscape($oTest->strNextTestName).'";
			strNextRequest = "'.CUtil::JSEscape($strNextRequest).'";
			strResult = "'.CUtil::JSEscape(str_replace(array("\r","\n"),"",$oTest->strResult)).'";
			strFinalStatus = "'.CUtil::JSEscape($strFinalStatus).'";
		';
	}
	else
		echo '<h1>Permission denied: BITRIX SESSID ERROR</h1>';
	exit;
}
elseif ($_REQUEST['read_log']) // after prolog to sent correct charset
{
	header('Content-type: text/plain; charset='.LANG_CHARSET);
	$oTest = new CSiteCheckerTest();
	echo file_get_contents($_SERVER['DOCUMENT_ROOT'].$oTest->LogFile);
	exit;
}
elseif ($_REQUEST['help_id'])
{
	echo '<div style="font-size:1.2em;padding:20px">';
	if ($h = GetMessage('SC_HELP_' . strtoupper($_REQUEST['help_id'])))
	{
		$h = str_replace('<code>','<div style="border:1px solid #CCC;margin:10;padding:10;font-family:Monospace;background-color:#FEFEFA">',$h);
		$h = str_replace('</code>','</div>',$h);
		echo nl2br($h);
	}
	else
		echo GetMessage('SC_HELP_NOTOPIC');
	echo '</div>';
	exit;
}
elseif ($fix_mode = intval($_REQUEST['fix_mode']))
{
	?>
	<table id="fix_table" width="100%" class="internal" style="padding:20;padding-bottom:0"></table>
	<div id="fix_status"></div>
	<script>
		var fix_mode = <?=$fix_mode?>;
		CHttpRequest.Action = fix_onload;
		CHttpRequest.Send('site_checker.php?fix_mode=' + fix_mode + '&test_start=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>');
	</script>
	<?
	exit;
}


$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);

$APPLICATION->SetTitle(GetMessage("SC_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

echo BeginNote();
echo GetMessage("SC_NOTE", array('#VAL#' => DEBUG_FLAG));
echo EndNote();
?>

<?
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<tr>
		<td colspan="2"><?=GetMessage("SC_SUBTITLE_REQUIED_DESC")?></td>
	</tr>
	<tr>
	<td colspan="2">
	<?
	$clean_test_table = '<table id="result_table" width="100%" class="internal"><tr class="heading"><td width="40%"><b>'.GetMessage("SC_TEST_NAME").'</b></td><td><b>'.GetMessage("SC_TEST_RES").'</b></td></tr></table>';

	$oTest = new CSiteCheckerTest(); 
	?>

	<style>
		tr.sc_hover td {
			background-color:#f1f3f9;
		}

		.sc_link {
			float:right;
			cursor:pointer;
			color:#637ad0;
			text-decoration:underline;
		}

		.sc_cell {
			vertical-align:top;
			padding:4 !important;
		}

		.test_status {
			text-align:center;
			font-weight:bold;
			color:#FFF;
			background-color:#b9cbdf;
			padding:2;
		}
	</style>
	<script>
		var bTestFinished = false;
		var bSubmit;

		function show_popup(title, link, confirm_text)
		{
			if (confirm_text && !confirm(confirm_text))
				return;

			d = new BX.CAdminDialog({
				'title': title,
				'content_url': '/bitrix/admin/site_checker.php' + link, 
				//   'content_post': this.JSParamsToPHP(arParams, 'PARAMS')+ '&' +
				//  this.JSParamsToPHP(arProp, 'PROP')+'&'+this.SESS,
				'draggable': true,
				'resizable': true,
				'buttons': [BX.CAdminDialog.btnClose]
			})

			d.Show();
		}

		function set_start(val)
		{
			document.getElementById('test_start').disabled = val ? 'disabled' : '';
			document.getElementById('test_stop').disabled = val ? '' : 'disabled';
			document.getElementById('progress').style.visibility = val ? 'visible' : 'hidden';

			if (val)
			{
				ShowWaitWindow();
				document.getElementById('result').innerHTML = '<?=$clean_test_table?>';
				document.getElementById('status').innerHTML = '<?=$oTest->strCurrentTestName?>';

				document.getElementById('percent').innerHTML = '0%';
				document.getElementById('indicator').style.width = '0%';

				CHttpRequest.Action = test_onload;
				CHttpRequest.Send('site_checker.php?test_start=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>');
			}
			else
				CloseWaitWindow();
		}

		function test_onload(result)
		{
			try
			{
				eval(result);
				
				if (document.getElementById('test_start').disabled) // Stop was not pressed
				{
					document.getElementById('percent').innerHTML = iPercent + '%';
					document.getElementById('indicator').style.width = iPercent + '%';
					document.getElementById('status').innerHTML = strNextTestName;

					if (strResult != '')
					{
						oTable = document.getElementById('result_table');
						oRow = oTable.insertRow(-1);
//						oRow.style.backgroundColor = '#FCC';

						oRow.onmouseover = function()
						{
							this.className = 'sc_hover';
						}

						oRow.onmouseout = function()
						{
							this.className = '';
						}

						oCell = oRow.insertCell(-1);
						oCell.className = 'sc_cell';
						oCell.innerHTML = strCurrentTestName;

						oCell = oRow.insertCell(-1);
						oCell.className = 'sc_cell';
						oCell.innerHTML = '<div class="sc_link"><?=GetMessage('SC_MORE')?></div>' + strResult;

						oDiv = oCell.firstChild;
						oDiv.id = strCurrentTestFunc;
						oDiv.title = strCurrentTestName;
						oDiv.onclick = function(){show_popup(this.title, '?help_id=' + this.id + '&lang=<?=LANGUAGE_ID?>')};
					}

					if (strNextRequest)
					<? if ($_GET['HTTP_HOST']) { ?>
						CHttpRequest.Send('site_checker.php?HTTP_HOST=<?=urlencode($_GET['HTTP_HOST'])?>&SERVER_PORT=<?=urlencode($_GET['SERVER_PORT'])?>&HTTPS=<?=urlencode($_GET['HTTPS'])?>&test_start=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>' + strNextRequest);
					<? } else { ?>
						CHttpRequest.Send('site_checker.php?HTTP_HOST=' + window.location.hostname + '&SERVER_PORT=' + window.location.port + '&HTTPS=' + (window.location.protocol == 'https:' ? 'on' : '') + '&test_start=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>' + strNextRequest);
					<? } ?>
					else // Finish
					{
						set_start(0);
						bTestFinished = true;
						if (bSubmit)
						{
							if (window.tabControl)
								tabControl.SelectTab('edit5');
							SubmitToSupport();	
						}
					}
				}
			}
			catch(e) 
			{
				CloseWaitWindow();
				document.getElementById('test_start').disabled = '';
				document.getElementById('result').innerHTML += result;
				alert('<?=GetMessage("SC_TEST_FAIL")?>');
			}
		}

		function fix_onload(result)
		{
			try
			{
				eval(result);
				
				fix_status = document.getElementById('fix_status');
				if (test_percent < 100)
					fix_status.innerHTML = '<table width="100%" class="internal" style="padding:20;padding-top:0"><tr><td width="40%">' + strNextTestName + '</td><td><div class="test_status" style="width:' + test_percent + '%">' + test_percent +  '%</div></table>';
				else
					fix_status.innerHTML = '';
				
				if (strResult != '')
				{
					oTable = document.getElementById('fix_table');
					oRow = oTable.insertRow(-1);

					oCell = oRow.insertCell(-1);
					oCell.className = 'sc_cell';
					oCell.style.width = '40%';
					oCell.innerHTML = strCurrentTestName;

					oCell = oRow.insertCell(-1);
					oCell.className = 'sc_cell';
					oCell.innerHTML = strResult;
				}

				if (strNextRequest)
					CHttpRequest.Send('site_checker.php?fix_mode=' + fix_mode + '&test_start=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>' + strNextRequest);
				else // Finish
					fix_status.innerHTML = '';
			}
			catch(e) 
			{
				if (o = document.getElementById('fix_status'))
				{
					o.innerHTML = result;
					alert('<?=GetMessage("SC_TEST_FAIL")?>');
				}	
			}
		}
	</script>

		<input type=button value="<?=GetMessage("SC_START_TEST_B")?>" id="test_start" onclick="set_start(1)">
		<input type=button value="<?=GetMessage("SC_STOP_TEST_B")?>" disabled id="test_stop" onclick="bSubmit=false;set_start(0)">
		<div id="progress" style="visibility:hidden;padding-top:4px;" width="100%">
			<div id="status" style="font-weight:bold;font-size:1.2em"></div>
			<table border="0" cellspacing="0" cellpadding="2" width="100%">
				<tr>
					<td height="20">
						<div style="border:1px solid #B9CBDF">
							<div id="indicator" style="height:20px; width:0%; background-color:#B9CBDF"></div>
						</div>
					</td>
					<td width=30>&nbsp;<span id="percent" style="font-size:1.4em">0%</span></td>
				</tr>
			</table>
		</div>
		<div id="result" style="padding-top:10px"></div>




	</td>
	</tr>
<?flush();

$tabControl->BeginNextTab();?>
	<tr>
		<td colspan="2"><?echo GetMessage("SC_SUBTITLE_DISK_DESC");?></td>
	</tr>
	<tr>
		<td colspan="2">
		<script>
		function onFrameLoad(ob)
		{
			CloseWaitWindow();
			if (ob.contentDocument)
				oDoc = ob.contentDocument;
			else
				oDoc = ob.contentWindow.document;

			document.getElementById('access_result').innerHTML = oDoc.body.innerHTML
		}

		function access_check_start(val)
		{
			document.getElementById('access_submit').disabled = val ? 'disabled' : '';
			document.getElementById('access_stop').disabled = val ? '' : 'disabled';

			if (val)
				ShowWaitWindow();
			else
				CloseWaitWindow();
		}
		</script>
			<? # CAdminMessage::ShowMessage(Array("MESSAGE"=>GetMessage("SC_CHECK_FILES_ATTENTION"), "TYPE"=>"ERROR","DETAILS"=>GetMessage("SC_CHECK_FILES_WARNING")));	?>
			<form method="POST" action="site_checker.php" target="access_frame" onsubmit="access_check_start(1)">
			<input type=hidden name=access_check value=Y>
			<input type=hidden name=lang value="<?=LANGUAGE_ID?>">
			<?=bitrix_sessid_post();?>
			<label><input type=radio name=check_type value=full checked> <?=GetMessage("SC_CHECK_FULL")?></label><br>
			<label><input type=radio name=check_type value=upload> <?=GetMessage("SC_CHECK_UPLOAD")?></label><br>
			<label><input type=radio name=check_type value=kernel> <?=GetMessage("SC_CHECK_KERNEL")?></label><br>
			<? if (BX_ROOT != BX_PERSONAL_ROOT): ?>
				<label><input type=radio name=check_type value=cache> <?=GetMessage("SC_CHECK_FOLDER")?> <b><?=BX_PERSONAL_ROOT?></b></label><br>
			<? endif; ?>
			<br>
			<input type=submit value="<?=GetMessage("SC_CHECK_B")?>" id="access_submit">
			<input type=button value="<?=GetMessage("SC_STOP_B")?>" disabled id="access_stop" onclick="access_check_start(0)">
			</form>
			<div width="100%" id="access_result"></div>
			<iframe name="access_frame" style="width:1px;height:1px;visibility:hidden" onload="onFrameLoad(this)"></iframe>
		</td>
	</tr>
<?
flush();

$tabControl->BeginNextTab();

if(!isset($strTicketError))
	$strTicketError = "";
?>
<tr><td colspan="2"><?
	if(isset($ticket_sent))
	{
		if(!empty($aMsg))
		{
			$e = new CAdminException($aMsg);
			$APPLICATION->ThrowException($e);
			if($e = $APPLICATION->GetException())
			{
				$message = new CAdminMessage(GetMessage("SC_ERROR"), $e);
				if($message)
					echo $message->Show();
			}
		}

		if(strlen($strTicketError)>0 && !$message)
			CAdminMessage::ShowMessage($strTicketError);
		elseif(!$message)
			CAdminMessage::ShowNote(str_replace("#EMAIL#", $ticket_email, GetMessage("SC_TIK_SEND_SUCCESS")));
	}
		?></td>
</tr>
<script>
	function SubmitToSupport()
	{
		frm = document.forms.fticket;

		if (frm.ticket_text.value == '')
		{
			alert('<?=GetMessage("SC_NOT_FILLED")?>');
			return false;
		}

//		frm.submit_button.disabled = 'disabled';

		if (!bTestFinished && frm.ticket_test.checked)
		{
			alert('<?=GetMessage("SC_TEST_WARN")?>');
//			if (window.tabControl)
//				tabControl.SelectTab('edit3');
			bSubmit = true; // submit after test 
			set_start(1);
		}
		else if(frm.ticket_test.checked)
		{
			CHttpRequest.Action = function (result)
			{
				document.forms.fticket.test_file_contents.value = result;
				frm.submit();
			}
			CHttpRequest.Send('?read_log=Y');
		}
		else
			frm.submit();
	}
</script>
<?
		?>
<form method="POST" action="<?=SUPPORT_PAGE?>" name="fticket">
<input type="hidden" name="send_ticket" value="Y">
<input type="hidden" name="license_key" value="<?=(LICENSE_KEY == "DEMO"? "DEMO" : md5("BITRIX".LICENSE_KEY."LICENCE"))?>">
<input type="hidden" name="test_file_name" value="<?=$oTest->LogFile;?>">
<input type="hidden" name="test_file_contents" value="">
<input type="hidden" name="ticket_title" value="<?=GetMessage('SC_RUS_L1').' '.htmlspecialchars($_SERVER['HTTP_HOST'])?>">
<input type="hidden" name="BX_UTF" value="<?=(defined('BX_UTF') && BX_UTF==true)?'Y':'N'?>">
<input type="hidden" name="tabControl_active_tab" value="edit5">
<tr>
	<td valign="top"><span class="required">*</span><?=GetMessage("SC_TIK_DESCR")?><br>
			<small><?=GetMessage("SC_TIK_DESCR_DESCR")?></small></td>
	<td valign="top"><textarea name="ticket_text" rows="6" cols="60"><?= htmlspecialchars($ticket_text)?></textarea></td>
</tr>
<tr>
	<td valign="top"><label for="ticket_test"><?=GetMessage("SC_TIK_ADD_TEST")?></label></td>
	<td valign="top"><input type="checkbox" id="ticket_test" name="ticket_test" value="Y" checked></td>
</tr>
<?if (strlen($_REQUEST["last_error_query"])>0):?>
	<tr>
		<td valign="top"><?=GetMessage("SC_TIK_LAST_ERROR")?></td>
		<td valign="top">
			<?=GetMessage("SC_TIK_LAST_ERROR_ADD")?>
			<input type="hidden" name="last_error_query" value="<?= htmlspecialchars($_REQUEST["last_error_query"])?>">
		</td>
	</tr>
<?endif;?>
<tr>
	<td></td>
	<td>
		<input type="button" name="submit_button" onclick="SubmitToSupport()" value="<?=GetMessage("SC_TIK_SEND_MESS")?>">
	</td>
</tr>
<tr>
	<td colspan=2>
	<?
	echo BeginNote();
	echo GetMessage("SC_SUPPORT_COMMENT").' <a href="'.SUPPORT_PAGE.'" target=_blank>'.SUPPORT_PAGE.'</a>';
	echo EndNote();
	?>
	</td>
</tr>
</form>
<?
//$tabControl->Buttons();
$tabControl -> End();
$tabControl->ShowWarnings("fticket", $message);

#echo BeginNote();
#echo GetMessage("SC_COMMENT");
#echo EndNote();
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");

////////////////////////////////////////////////////////////////////////
//////////   FUNCTIONS   ///////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
function CheckGetModuleInfo($path)
{
	include_once($path);

	$arr = explode("/", $path);
	$i = array_search("modules", $arr);
	$class_name = $arr[$i+1];

	return CModule::CreateModuleObject($class_name);
}

class CSearchFiles
{
	function CSearchFiles()
	{
		$this->StartTime = time();
		$this->arFail = array();
		$this->FilesCount = 0;
		$this->MaxFail = 9;
		$this->TimeLimit = 0;

		$this->SkipPath = '';
		$this->BreakPoint = '';

	}

	function Search($path) 
	{
		if (time() - $this->StartTime > $this->TimeLimit)
		{
			$this->BreakPoint = $path;
			return count($this->arFail) == 0;
		}

		if (count($this->arFail) > $this->MaxFail)
			return false;

		if ($this->SkipPath)
		{
			if (0!==strpos($this->SkipPath, dirname($path)))
				return;

			if ($this->SkipPath == $path)
				unset($this->SkipPath);
		}

		if (is_dir($path))
		{
			if (is_readable($path))
			{
				if (!is_writable($path))
					$this->arFail[] = $path;

				if ($dir = opendir($path))
				{
					while(false !== $item = readdir($dir))
					{
						if ($item == '.' || $item == '..')
							continue;

						$this->Search($path.'/'.$item);
						if ($this->BreakPoint)
							break;
					}
					closedir($dir);
				}
			}
			else
				$this->arFail[] = $path;
		}
		elseif (!$this->SkipPath)
		{
			$this->FilesCount++;
			if (!is_readable($path) || !is_writable($path))
				$this->arFail[] = $path;
		}
		return count($this->arFail) == 0;
	}
}


function IsHttpResponseSuccess($res, $strRequest, &$strHeaders = '', $bHideRequest = false)
{
	$strRes = GetHttpResponse($res, $strRequest, $strHeaders);
	if (trim($strRes) == 'SUCCESS')
		return true;
	else
	{
		echo "\n== Request ==\n".($bHideRequest ? substr($strRequest, 0, strpos($strRequest, "\r\n\r\n")) : $strRequest) . "\n== Response ==\n" . $strHeaders . "\n== Body ==\n" . $strRes . "\n==========";
		return false;
	}
}

function GetHttpResponse($res, $strRequest, &$strHeaders)
{
	fputs($res, $strRequest);

	$strHeaders = "";
	$bChunked = False;
	$Content_Length = false;
	while (!feof($res) && ($line = fgets($res, 4096)) && $line != "\r\n")
	{
		$strHeaders .= $line;
		if (preg_match("/Transfer-Encoding: +chunked/i", $line))
			$bChunked = True;

		if (preg_match("/Content-Length: +([0-9]+)/i", $line, $regs))
			$Content_Length = $regs[1];
				
	}

	$strRes = "";
	if ($bChunked)
	{
		$maxReadSize = 4096;

		$length = 0;
		$line = fgets($res, $maxReadSize);
		$line = StrToLower($line);

		$strChunkSize = "";
		$i = 0;
		while ($i < StrLen($line) && in_array($line[$i], array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f")))
		{
			$strChunkSize .= $line[$i];
			$i++;
		}

		$chunkSize = hexdec($strChunkSize);

		while ($chunkSize > 0)
		{
			$processedSize = 0;
			$readSize = (($chunkSize > $maxReadSize) ? $maxReadSize : $chunkSize);

			while ($readSize > 0 && $line = fread($res, $readSize))
			{
				$strRes .= $line;
				$processedSize += StrLen($line);
				$newSize = $chunkSize - $processedSize;
				$readSize = (($newSize > $maxReadSize) ? $maxReadSize : $newSize);
			}
			$length += $chunkSize;

			$line = FGets($res, $maxReadSize);

			$line = FGets($res, $maxReadSize);
			$line = StrToLower($line);

			$strChunkSize = "";
			$i = 0;
			while ($i < StrLen($line) && in_array($line[$i], array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f")))
			{
				$strChunkSize .= $line[$i];
				$i++;
			}

			$chunkSize = hexdec($strChunkSize);
		}
	}
	elseif ($Content_Length !== false)
	{
		if ($Content_Length > 0)
			$strRes = fread($res, $Content_Length);
	}
	else
	{
		while ($line = fread($res, 4096))
			$strRes .= $line;
	}

	fclose($res);
	return $strRes;
}

class CSiteCheckerTest
{
	function __construct($step = 0, $fix_mode = 0)
	{
		$this->step = intval($step);
		$this->test_percent = 0;
		$this->strError = '';
		$this->timeout = 10; // sec for one step
		$this->strResult = '';
		$this->fix_mode = intval($fix_mode);

		$this->host = $_REQUEST['HTTP_HOST'] ? $_REQUEST['HTTP_HOST'] : 'localhost';
		if (($ru = GetMessage('SC_CYR_SYMBOLS_RU_ONLY')) && preg_match('/['.$ru.']/'.BX_UTF_PCRE_MODIFIER, $this->host))
			$this->host = CBXPunycode::ToASCII($this->host, $err);
		$this->ssl = $_REQUEST['HTTPS'] == 'on';
		$this->port = $_REQUEST['SERVER_PORT'] ? $_REQUEST['SERVER_PORT'] : ($this->ssl ? 443 : 80);

		$this->arTest = array(
			array('OpenLog' => GetMessage('SC_T_LOG')),
			array('check_php_modules' =>GetMessage('SC_T_MODULES')),
			array('check_php_settings' =>GetMessage('SC_T_PHP')),
			array('check_server_vars' =>GetMessage('SC_T_SERVER')),
			array('check_mbstring' =>GetMessage('SC_T_MBSTRING')),
			array('check_backtrack_limit' =>GetMessage('SC_T_BACKTRACK_LIMIT')),
			array('check_sites' =>GetMessage('SC_T_SITES')),
			array('check_socket' => GetMessage('SC_T_SOCK')),
			array('check_dbconn' => GetMessage('SC_T_DBCONN')),
			array('check_upload' => GetMessage('SC_T_UPLOAD')),
			array('check_upload_big' => GetMessage('SC_T_UPLOAD_BIG')),
			array('check_upload_raw' => GetMessage('SC_T_UPLOAD_RAW')),
			array('check_post' => GetMessage('SC_T_POST')),
			array('check_mail' => GetMessage('SC_T_MAIL')),
			array('check_mail_big' => GetMessage('SC_T_MAIL_BIG')),
			array('check_mail_b_event' => GetMessage('SC_T_MAIL_B_EVENT')),
			array('check_localredirect' => GetMessage('SC_T_REDIRECT')),
			array('check_memory_limit' => GetMessage('SC_T_MEMORY')),
			array('check_session' => GetMessage('SC_T_SESS')),
			array('check_session_ua' => GetMessage('SC_T_SESS_UA')),
			array('check_cache' => GetMessage('SC_T_CACHE')),
			array('check_update' => GetMessage('SC_UPDATE_ACCESS')),
			array('check_http_auth' => GetMessage('SC_T_AUTH')),
			array('check_exec' => GetMessage('SC_T_EXEC')),
			array('check_suhosin' => GetMessage('SC_T_SUHOSIN')),
			array('check_security' => GetMessage('SC_T_SECURITY')),
			array('check_bx_crontab' => GetMessage('SC_T_BX_CRONTAB')),
			array('check_divider' => GetMessage('SC_T_DELIMITER')),
			array('check_precision' => GetMessage('SC_T_PRECISION')),
			array('check_clone' => GetMessage('SC_T_CLONE')),
			array('check_getimagesize' => GetMessage('SC_T_GETIMAGESIZE')),
		);

		$this->arTestDB = array(
			array('check_mysql_bug_version' => GetMessage('SC_T_MYSQL_VER')),
			array('check_mysql_time' => GetMessage('SC_T_TIME')),
			array('check_mysql_mode' => GetMessage('SC_T_SQL_MODE')),
			array('check_mysql_increment' => GetMessage('SC_T_AUTOINC')),
//			array('check_mysql_table_status' => GetMessage('SC_T_CHECK')),
			array('check_mysql_connection_charset' => GetMessage('SC_CONNECTION_CHARSET')),
			array('check_mysql_db_charset' => GetMessage('SC_DB_CHARSET')),
			array('check_mysql_table_charset' => GetMessage('SC_T_CHARSET')),
		);

		if ($this->fix_mode == 1)
			$this->arTest = array(
				array('check_mysql_table_status' => GetMessage('SC_T_CHECK')),
			);
		elseif ($this->fix_mode == 2)
			$this->arTest = array(
				array('check_mysql_connection_charset' => GetMessage('SC_CONNECTION_CHARSET')),
				array('check_mysql_db_charset' => GetMessage('SC_DB_CHARSET')),
				array('check_mysql_table_charset' => GetMessage('SC_T_CHARSET')),
			);
		elseif (strtolower($GLOBALS['DB']->type) == 'mysql')
			$this->arTest = array_merge($this->arTest, $this->arTestDB);
		#$this->arTest = array_merge(array(array('OpenLog'=>GetMessage('SC_T_LOG'))), $this->arTestDB);

		list($this->function, $this->strCurrentTestName) = each($this->arTest[$this->step]);
		$this->strNextTestName = $this->strCurrentTestName;

		include($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/license_key.php');
		$this->LogFile = BX_ROOT.'/site_checker_'.md5('SITE_CHECKER'.$LICENSE_KEY).'.log';
	}

	function Start()
	{
		$this->test_percent = 100; // by default

		ob_start();
		$this->result = call_user_func(array($this,$this->function));
		$this->strError = ob_get_clean();

		if (!$this->strResult)
			$this->Result($this->result);

		if ($this->function != 'OpenLog' && !$this->fix_mode)
		{
			// write to log
			if (@$this->OpenLog($continue = true))
			{
				$text = date('Y-M-d H:i:s') . ' ' . $this->strCurrentTestName . ' (' . $this->function . "): " . $this->LogResult . "\n";
				if ($this->test_percent < 100)
					$text .= $this->test_percent.'% done' . "\n";

				if ($this->strError)
					$text .= strip_tags($this->strError)."\n";

				$text .= strip_tags($this->strResult)."\n";
				if (function_exists('htmlspecialchars_decode'))
					$text = htmlspecialchars_decode($text);

				fwrite($this->LogResourse, $text);
			}
		}

		$this->last_function = $this->function;
		$this->percent = floor(($this->step + $this->test_percent / 100) / count($this->arTest) * 100);

		if ($this->test_percent >= 100) // test finished
		{
			if ($this->step + 1 < count($this->arTest))
			{
				$this->step++;
				$this->test_percent = 0;
				$this->arTestVars['last_value'] = '';
				list($this->function, $this->strNextTestName) = each($this->arTest[$this->step]);
			}
			else // finish
				COption::SetOptionString('main', 'site_checker_success', $this->arTestVars['site_checker_success']);
		}
		elseif ($this->result === true)
			$this->strResult = ''; // in case of temporary result on this step

		if ($this->result === false)
			$this->arTestVars['site_checker_success'] = 'N';
	}

	function Result($result, $text = '')
	{
		if ($result === true)
		{	
			$this->LogResult = 'Ok';
			$color = 'green';
		}
		elseif ($result === null)
		{
			$this->LogResult = 'Warning';
			$color = 'black';
		}
		else
		{
			$this->LogResult = 'Fail';
			$color = 'red';
		}

		$this->strResult = '<span style="color:'.$color.';font-weight:bold">'.($text ? $text : ($result ? GetMessage('SC_TEST_SUCCESS') : GetMessage('SC_ERROR'))).'</span>';
		return $result;
	}

	function OpenLog($continue = false)
	{
		if (!$this->LogFile)
			return false;

		$this->LogResourse = fopen($_SERVER['DOCUMENT_ROOT'].$this->LogFile, $continue ? 'ab' : 'wb');
		if ($continue)
			return $this->LogResourse;
		elseif ($this->LogResourse)
		{
			$this->arTestVars['site_checker_success'] = 'Y';
			return $this->Result(null, GetMessage('SC_LOG_OK').' <a href="?read_log=Y" target="_blank">'.GetMessage('SC_F_OPEN').'</a>');
		}
		else
			return $this->Result(false,GetMessage('SC_CHECK_FILES'));

	}

	function ConnectToHost($host = false, $port = false, $ssl = false)
	{
		if (!$host)
		{
			if ($this->arTestVars['check_socket_fail'])
				return $this->Result(null, GetMessage('SC_SOCK_NA'));

			$host = $this->host;
			$port = $this->port;
			$ssl = $this->ssl ? 'ssl://' : '';
		}
		echo "Connection to $ssl$host:$port";
		if (!$res = fsockopen($ssl.$host, $port, $errno, $errstr, 5))
			return $this->Result(false, "Socket error [$errno]: $errstr");
		
		return $res;
	}

	###### TESTS #######
	function check_mail($big = false)
	{
		$body = "Test message.\nDelete it.";
		if ($big)
		{
			$str = file_get_contents(__FILE__);
			if (!$str)
				return $this->Result(false, GetMessage('SC_CHECK_FILES'));

			$body = str_repeat($str, 10);
		}

		list($usec0, $sec0) = explode(" ", microtime());
		$val = mail("hosting_test@bitrixsoft.com", "Bitrix site checker".($big ? CEvent::GetMailEOL() . "\tmultiline subject" : ""), $body, ($big ? 'BCC: noreply@bitrixsoft.com'."\r\n" : ''));
		list($usec1, $sec1) = explode(" ", microtime());
		$time = round($sec1 + $usec1 - $sec0 - $usec0, 2);
		if ($val)
		{
			if ($time > 1)
				return $this->Result(false, GetMessage('SC_SENT').' '.$time.' '.GetMessage('SC_SEC'));
		}
		else
			return false;

		return true;
	}

	function check_mail_big()
	{
		return $this->check_mail(true);
	}

	function check_mail_b_event()
	{
		$res = $GLOBALS['DB']->Query("SELECT COUNT(1) AS A FROM b_event WHERE SUCCESS_EXEC = 'N'");
		$f = $res->Fetch();
		if ($f['A'] > 0)
			return $this->Result(false, GetMessage('SC_T_MAIL_B_EVENT_ERR').' '.$f['A']);
		return true;
	}

	function check_socket()
	{
		$strRequest = "GET ".$_SERVER['PHP_SELF']."?socket_test=Y&unique_id=".checker_get_unique_id()." HTTP/1.1\r\n";
		$strRequest.= "Host: ".$this->host."\r\n";
		$strRequest.= "\r\n";

		$retVal = false;
		if ($res = $this->ConnectToHost())
			$retVal = IsHttpResponseSuccess($res, $strRequest);

		if (!$retVal)
			$this->arTestVars['check_socket_fail'] = 1;
		return $retVal;
	}

	function check_dbconn()
	{
		$strRequest = "GET ".$_SERVER['PHP_SELF']."?dbconn_test=Y&unique_id=".checker_get_unique_id()." HTTP/1.1\r\n";
		$strRequest.= "Host: ".$this->host."\r\n";
		$strRequest.= "\r\n";

		$retVal = false;
		if ($res = $this->ConnectToHost())
			return IsHttpResponseSuccess($res, $strRequest);
		return $retVal;
	}

	function check_upload($big = false, $raw = false)
	{
		if (($sp = ini_get("upload_tmp_dir")))
		{	
			if (!file_exists($sp))
				return $this->Result(false,GetMessage('SC_NO_TMP_FOLDER').' <i>('.htmlspecialchars($sp).')</i>');
			elseif (!is_writable($sp))
				return $this->Result(false,GetMessage('SC_TMP_FOLDER_PERMS').' <i>('.htmlspecialchars($sp).')</i>'); 
		}

		$binaryData = '';
		for($i=40;$i<240;$i++)
			$binaryData .= chr($i);
		if ($big)
			$binaryData = str_repeat($binaryData, 21000);

		if ($raw)
			$POST = $binaryData;
		else
		{
			$boundary = '--------'.md5(checker_get_unique_id());

			$POST = "--$boundary\r\n";
			$POST.= 'Content-Disposition: form-data; name="test_file"; filename="site_checker.bin'."\r\n";
			$POST.= 'Content-Type: image/gif'."\r\n";
			$POST.= "\r\n";
			$POST.= $binaryData."\r\n";
			$POST.= "--$boundary\r\n";
		}

		$strRequest = "POST ".$_SERVER['PHP_SELF']."?upload_test=Y&unique_id=".checker_get_unique_id()."&big=".($big ? 1 : 0)."&raw=".($raw ? 1 : 0)." HTTP/1.1\r\n";
		$strRequest.= "Host: ".$this->host."\r\n";
		if (!$raw)
			$strRequest.= "Content-Type: multipart/form-data; boundary=$boundary\r\n";
		$strRequest.= "Content-Length: ".(function_exists('mb_strlen') ? mb_strlen($POST, 'ISO-8859-1') : strlen($POST))."\r\n";
		$strRequest.= "\r\n";
		$strRequest.= $POST;

		if ($res = $this->ConnectToHost())
			return IsHttpResponseSuccess($res, $strRequest, $strHeaders, true);
		return false;
	}

	function check_upload_big()
	{
		return $this->check_upload(true);
	}

	function check_upload_raw()
	{
		return $this->check_upload(false, true);
	}

	function check_post()
	{
		$POST = '';
		for($i=0;$i<201;$i++)
			$POST .= 'i'.$i.'='.md5($i).'&';

		$strRequest = "POST ".$_SERVER['PHP_SELF']."?post_test=Y&unique_id=".checker_get_unique_id()." HTTP/1.1\r\n";
		$strRequest.= "Host: ".$this->host."\r\n";
		$strRequest.= "Content-Length: ".(function_exists('mb_strlen') ? mb_strlen($POST, 'ISO-8859-1') : strlen($POST))."\r\n";
		$strRequest.= "Content-Type: application/x-www-form-urlencoded\r\n";

		$strRequest.= "\r\n";
		$strRequest.= $POST;

		if ($res = $this->ConnectToHost())
			return IsHttpResponseSuccess($res, $strRequest);
		return false;
	}

	function check_memory_limit()
	{
		$total_steps = 7;

		if (!$this->arTestVars['last_value'])
		{
			$last_success = 0;
			$max = 16;
			$step = 1;
		}
		else
		{
			if (!CheckSerializedData($this->arTestVars['last_value']))
				return false;
			list($last_success, $max, $step) = unserialize($this->arTestVars['last_value']);
		}

		$strRequest = "GET ".$_SERVER['PHP_SELF']."?memory_test=Y&unique_id=".checker_get_unique_id()."&max=".($max - 1)." HTTP/1.1\r\n";
		$strRequest.= "Host: ".$this->host."\r\n";
		$strRequest.= "\r\n";

		if (!$res = $this->ConnectToHost())
			return false;

		if (IsHttpResponseSuccess($res, $strRequest))
		{
			$last_success = $max;
			$max *= 2;
		}
		else
			$max = floor(($last_success + $max) / 2);

		if ($max < 16)
			return false;

		if ($step < $total_steps)
		{
			$this->test_percent = floor(100 / $total_steps * $step);
			$step++;
			$this->arTestVars['last_value'] = serialize(array($last_success, $max, $step));
			return true;
		}

		return $this->Result(intval($last_success) > 32, GetMessage('SC_NOT_LESS',array('#VAL#'=>$last_success)));
	}

	function check_session()
	{
		if (!$this->arTestVars['last_value'])
		{
			$_SESSION['CHECKER_CHECK_SESSION'] = 'SUCCESS';
			$this->test_percent = 50;
			$this->arTestVars['last_value'] = 'Y';
		}
		else
		{
			if ($_SESSION['CHECKER_CHECK_SESSION'] != 'SUCCESS')
				return false;
			unset($_SESSION['CHECKER_CHECK_SESSION']);
		}
		return true;
	}

	function check_session_ua()
	{
		$strRequest = "GET ".$_SERVER['PHP_SELF']."?session_test=Y&unique_id=".checker_get_unique_id()." HTTP/1.1\r\n";
		$strRequest.= "Host: ".$this->host."\r\n";

		if ($this->arTestVars['last_value']) // second step: put session id
			$strRequest.= "Cookie: ".$this->arTestVars['last_value']."\r\n";

		$strRequest.= "\r\n";

		if (!$res = $this->ConnectToHost())
			return false;


		if (!$this->arTestVars['last_value']) // first step: read session id
		{
			$strRes = GetHttpResponse($res, $strRequest, $strHeaders);
			if (!preg_match('#Set-Cookie: ('.session_name().'=[a-z0-9\-\_]+?);#i',$strHeaders,$regs))
			{
				echo "\n== Request ==\n".$strRequest . "\n== Response ==\n" . $strHeaders . "\n== Body ==\n" . $strRes . "\n==========";
				return false;
			}

			$this->arTestVars['last_value'] = $regs[1];
			$this->test_percent = 50;
			return true;
		}
		else
			return IsHttpResponseSuccess($res, $strRequest, $strHeaders);
	}

	function check_http_auth()
	{
		$strRequest = "GET ".$_SERVER['PHP_SELF']."?auth_test=Y&unique_id=".checker_get_unique_id()." HTTP/1.1\r\n";
		$strRequest.= "Host: ".$this->host."\r\n";
		$strRequest.= "Authorization: Basic dGVzdF91c2VyOnRlc3RfcGFzc3dvcmQ=\r\n";
		$strRequest.= "\r\n";

		if ($res = $this->ConnectToHost())
			return IsHttpResponseSuccess($res, $strRequest);
		return false;
	}

	function check_update()
	{
		$ServerIP = COption::GetOptionString("main", "update_site", "www.bitrixsoft.com");
		$ServerPort = 80;

		$proxyAddr = COption::GetOptionString("main", "update_site_proxy_addr", "");
		$proxyPort = COption::GetOptionString("main", "update_site_proxy_port", "");
		$proxyUserName = COption::GetOptionString("main", "update_site_proxy_user", "");
		$proxyPassword = COption::GetOptionString("main", "update_site_proxy_pass", "");

		$bUseProxy = !$this->arTestVars['last_value'] && strlen($proxyAddr) > 0 && strlen($proxyPort) > 0;

		if ($bUseProxy)
		{
			$proxyPort = IntVal($proxyPort);
			if ($proxyPort <= 0)
				$proxyPort = 80;

			$requestIP = $proxyAddr;
			$requestPort = $proxyPort;
		}
		else
		{
			$requestIP = $ServerIP;
			$requestPort = $ServerPort;
		}

		$strRequest = "";
		$page = "us_updater_list.php";
		if ($bUseProxy)
		{
			$strRequest .= "POST http://".$ServerIP."/bitrix/updates/".$page." HTTP/1.0\r\n";
			if (strlen($proxyUserName) > 0)
				$strRequest .= "Proxy-Authorization: Basic ".base64_encode($proxyUserName.":".$proxyPassword)."\r\n";
		}
		else
			$strRequest .= "POST /bitrix/updates/".$page." HTTP/1.0\r\n";

		$strRequest.= "User-Agent: BitrixSMUpdater\r\n";
		$strRequest.= "Accept: */*\r\n";
		$strRequest.= "Host: ".$ServerIP."\r\n";
		$strRequest.= "Accept-Language: en\r\n";
		$strRequest.= "Content-type: application/x-www-form-urlencoded\r\n";
		$strRequest.= "Content-length: 7\r\n\r\n";
		$strRequest.= "lang=en";
		$strRequest.= "\r\n";

		$res = fsockopen($requestIP, $requestPort, $errno, $errstr, 5);

		if (!$res)
		{
			if ($bUseProxy)
				return $this->Result(false, GetMessage('SC_NO_PROXY'). ' ('.$errstr.')');
			else
				return $this->Result(false, GetMessage('SC_UPDATE_ERROR').' ('.$errstr.')');
		}
		else
		{
			$strRes = GetHttpResponse($res, $strRequest, $strHeaders);

			$strRes = strtolower(strip_tags($strRes)); 
			if ($strRes == "license key is invalid" || $strRes == "license key is required")
				return true;
			else
			{
				echo "\n== Request ==\n".$strRequest . "\n== Response ==\n" . $strHeaders . "\n== Body ==\n" . $strRes . "\n==========";
				if ($bUseProxy)
					return $this->Result(false, GetMessage('SC_PROXY_ERR_RESP'));
				else
					return $this->Result(false, GetMessage('SC_UPDATE_ERR_RESP'));
			}
		}
	}

	function check_cache()
	{
		$dir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/cache";
		$file0 = $dir."/".md5(mt_rand());
		$file1 = $file0.".tmp";
		$file2 = $file0.".php";
		if (!file_exists($dir))
			mkdir($dir, BX_DIR_PERMISSIONS);

		return ($f = fopen($file1, 'wb')) && (fclose($f)) && (rename($file1,$file2)) && (unlink($file2));
	}

	function check_exec()
	{
		$path = BX_ROOT.'/site_check_exec.php';
#		if (file_exists($_SERVER['DOCUMENT_ROOT'].$path))
#			return $this->Result(false,GetMessage('SC_FILE_EXISTS').' '.$path);
		if (!($f = fopen($_SERVER['DOCUMENT_ROOT'].$path, 'wb')))
			return $this->Result(false,GetMessage('SC_CHECK_FILES'));

		chmod($_SERVER['DOCUMENT_ROOT'].$path, BX_FILE_PERMISSIONS);

		fwrite($f,'<'.'? echo "SUCCESS"; ?'.'>');
		fclose($f);

		$strRequest = "GET ".$path." HTTP/1.1\r\n";
		$strRequest.= "Host: ".$this->host."\r\n";
		$strRequest.= "\r\n";

		if ($res = $this->ConnectToHost())
			$retVal = IsHttpResponseSuccess($res, $strRequest);
		else
			$retVal = false;

		unlink($_SERVER['DOCUMENT_ROOT'].$path);

		return $retVal;
	}

	function check_suhosin()
	{
		if (in_array('suhosin',get_loaded_extensions()))
			return $this->Result(null,GetMessage('SC_WARN_SUHOSIN',array('#VAL#' => ini_get('suhosin.simulation') ? 1 : 0)));
		return true;
	}

	function check_security()
	{
		if (function_exists('apache_get_modules') && in_array('mod_security',apache_get_modules()))
			return $this->Result(null,GetMessage('SC_WARN_SECURITY'));
		return true;
	}
	
	function check_divider()
	{
		$locale_info = localeconv();
		$delimiter = $locale_info['decimal_point'];
		if ($delimiter != '.')
			return $this->Result(false,GetMessage('SC_DELIMITER_ERR',array('#VAL#' => $delimiter)));

		return true;
	}

	function check_precision()
	{
		return 1234567891 == (string) doubleval(1234567891);
	}

	function check_clone()
	{
		$x = new CDatabase;
		$x->b = 'FAIL';

		$y = $x;
		$y->b = 'SUCCESS';

		return $x->b == 'SUCCESS';
	}

	function check_getimagesize()
	{
		$file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/fileman/install/components/bitrix/player/mediaplayer/player.swf';
		if (!file_exists($file))
			return $this->Result(null, "File not found: ".$file);

		if (false === getimagesize($file))
			return $this->Result(null, GetMessage('SC_SWF_WARN'));
		return true;
	}

	function check_localredirect()
	{
		$strSERVER = '';
		foreach(array('SERVER_PORT', 'HTTPS', 'FCGI_ROLE', 'HTTP_HOST', 'SERVER_PROTOCOL') as $var)
			$strSERVER .= '&'.$var.'='.urlencode($_SERVER[$var]);

		if (!$this->arTestVars['last_value'])
		{
			$strRequest = "GET ".$_SERVER['PHP_SELF']."?redirect_test=Y&unique_id=".checker_get_unique_id().$strSERVER." HTTP/1.1\r\n";
			$strRequest.= "Host: ".$this->host."\r\n";
			$strRequest.= "\r\n";

			if (!$res = $this->ConnectToHost())
				return false;

			$strRes = GetHttpResponse($res, $strRequest, $strHeaders);

			if (preg_match('#Location: (.+)#', $strHeaders, $regs))
			{
				$url = trim($regs[1]);
				if (!$url)
				{
					echo "\n== Request ==\n".$strRequest . "\n== Response ==\n" . $strHeaders . "\n== Body ==\n" . $strRes . "\n==========";
					return false;
				}

				$this->arTestVars['last_value'] = $url;
				$this->test_percent = 50;

				return true;
			}
			
			echo "\n== Request ==\n".$strRequest . "\n== Response ==\n" . $strHeaders . "\n== Body ==\n" . $strRes . "\n==========";
			return false;
		}
		else
		{
			$url = $this->arTestVars['last_value'];
			if (!$url)
				return false;

			$ar = parse_url($url);

			$host = $ar['host'];
			$ssl = $ar['scheme'] == 'https' ? 'ssl://' : '';
			$port = intval($ar['port']) ? intval($ar['port']) : ($ssl ? 443 : 80);

			$strRequest = "GET ".$_SERVER['PHP_SELF']."?redirect_test=Y&unique_id=".checker_get_unique_id().$strSERVER."&done=Y HTTP/1.1\r\n";
			$strRequest.= "Host: ".$host."\r\n";
			$strRequest.= "\r\n";

			if ($res = $this->ConnectToHost($host, $port, $ssl))
				return IsHttpResponseSuccess($res, $strRequest);
			return false;
		}
	}

	function check_mbstring()
	{
		$retVal = true;
		$bUtf = false;

		$rs = CSite::GetList($by,$order,array('ACTIVE'=>'Y'));
		while($f = $rs->Fetch())
			if (strpos(strtolower($f['CHARSET']),'utf')!==false)
			{
				$bUtf = true;
				break;
			}

		$overload  = intval(ini_get('mbstring.func_overload'));
		$encoding = strtolower(ini_get('mbstring.internal_encoding'));

		if ($bUtf)
		{
			$text = GetMessage('SC_MB_UTF');

			$retVal = ($overload == 2) && ($encoding == 'utf8' || $encoding == 'utf-8');
			if (!$retVal)
				$text .= ', '.GetMessage('SC_MB_CUR_SETTINGS').'<br>mbstring.func_overload='.$overload.'<br>mbstring.internal_encoding='.$encoding.
				'<br>'.GetMessage('SC_MB_REQ_SETTINGS').'<br>mbstring.func_overload=2<br>mbstring.internal_encoding=utf-8';

			if (!defined('BX_UTF') || BX_UTF !== true)
			{
				$retVal = false;
				$text .= '<br>'.GetMessage('SC_BX_UTF');
				$this->arTestVars['check_mbstring_fail'] = true;
			}
		}
		else
		{
			$text = GetMessage('SC_MB_NOT_UTF');

			if ($overload == 2)
			{
				$mb_string_req = '<br>mbstring.internal_encoding=latin1';
				$retVal = false === strpos($encoding,'utf');
			}
			else
			{
				$mb_string_req = '<br>mbstring.func_overload=0';
				$retVal = $overload == 0;
			}
			if (!$retVal)
				$text .= ', '.GetMessage('SC_MB_CUR_SETTINGS').'<br>mbstring.func_overload='.$overload.'<br>mbstring.internal_encoding='.$encoding.
				'<br>'.GetMessage('SC_MB_REQ_SETTINGS').$mb_string_req;

			if (defined('BX_UTF'))
			{
				$retVal = false;
				$text .= '<br>'.GetMessage('SC_BX_UTF_DISABLE');
				$this->arTestVars['check_mbstring_fail'] = true;
			}
		}

		return $this->Result($retVal, ($retVal ? GetMessage('SC_TEST_SUCCESS').'. ':'').$text);
	}

	function check_sites()
	{
		$strError = '';
		$bUtf = $bChar = $bFound = false;
		$arDocRoot = array();

		$rs = CSite::GetList($by,$order,array('ACTIVE'=>'Y'));
		while($f = $rs->Fetch())
		{
			$arDocRoot[] = trim($f['DOC_ROOT']);
			$bFound = strpos(strtolower($f['CHARSET']),'utf')!==false;

			$bUtf = $bUtf || $bFound;
			$bChar = $bChar || !$bFound;
		}

		if (count($arDocRoot) == 1)
		{
			if ($root = $arDocRoot[0])
				$strError = GetMessage('SC_PATH_FAIL_SET').' <i>'.htmlspecialchars($root).'</i><br>';
		}
		else
		{
			foreach($arDocRoot as $root)
			{
				if ($root)
				{
					if (!is_readable($root.'/bitrix'))
						$strError .= GetMessage('SC_NO_ROOT_ACCESS').' <i>'.htmlspecialchars($root).'/bitrix</i><br>';
				}
			}
		}

		if ($bUtf && $bChar)
			$strError.= GetMessage("SC_SITE_CHARSET_FAIL");

		if ($strError)
			return $this->Result(false, $strError);

		return true;
	}

	function check_php_modules()
	{
		$arMods = array(
			'fsockopen' => GetMessage("SC_SOCKET_F"),
			'xml_parser_create' => GetMessage("SC_MOD_XML"),
			'preg_match' => GetMessage("SC_MOD_PERL_REG"),
			'imagettftext' => "Free Type Text",
			'gzcompress' => "Zlib",
			'imagecreatetruecolor' => GetMessage("SC_MOD_GD"),
			'imagecreatefromjpeg' => GetMessage("SC_MOD_GD_JPEG")
		);

		$strError = '';
		foreach($arMods as $func => $desc)
		{
			if (!function_exists($func))
				$strError .= $desc."<br>\n";
		}

		if (defined('BX_UTF') && BX_UTF === true && !function_exists('mb_substr'))
			$strError .= GetMessage("SC_MOD_MBSTRING") . "<br>\n";

		if (!in_array('ssl', stream_get_transports()))
			$strError .= GetMessage('ERR_NO_SSL');

		if ($strError)
			return $this->Result(false,GetMessage('ERR_NO_MODS') . "<br>\n" . $strError);
		return true;
	}

	function check_php_settings()
	{
		$strError = '';
		$PHP_vercheck_min = '5.0.0';
		if (version_compare($v = phpversion(), $PHP_vercheck_min, '<'))
			$strError = GetMessage('SC_VER_ERR', array('#CUR#' => $v, '#REQ#' => $PHP_vercheck_min))."<br>\n";

		$arRequiredParams = array(
			'safe_mode' => 0,
			'magic_quotes_sybase' => 0,
			'allow_call_time_pass_reference' => 1,
			'file_uploads' => 1,
			'session.cookie_httponly' => 0,
			'file_uploads' => 1,
			'wincache.chkinterval' => 0,
			'session.auto_start' => 0
		);

		foreach($arRequiredParams as $param => $val)
		{
			$cur = ini_get($param);
			if (strtolower($cur) == 'on')
				$cur = 1;
			elseif (strtolower($cur) == 'off')
				$cur = 0;

			if ($cur != $val)
				$strError .=  GetMessage('SC_ERR_PHP_PARAM', array('#PARAM#' => $param, '#CUR#' => $cur ? htmlspecialchars($cur) : 'off', '#REQ#' => $val ? 'on' : 'off'))."<br>\n"; 
		}
		if ($strError)
			return $this->Result(false, $strError);
		return true;
	}

	function check_backtrack_limit()
	{
		$param = 'pcre.backtrack_limit';
		$cur = ini_get($param);
		ini_set($param,$cur + 1);
		$new = ini_get($param);

		return $new == $cur + 1;
	}

	function check_server_vars()
	{
		$strError = '';
		$dir0 = realpath(str_replace('\\','/',dirname(__FILE__)));
		$dir1 = realpath(str_replace('\\','/',rtrim($_SERVER['DOCUMENT_ROOT'],'\\/').BX_ROOT).'/modules/main/admin');

		if ($dir0 != $dir1)
			$strError = GetMessage('SC_DOCROOT_FAIL',array('#DIR0#'=>$dir0, '#DIR1#'=>$dir1))."<br>\n";

		list($host, $port) = explode(':',$_SERVER['HTTP_HOST']);
		if ($host != 'localhost' && !preg_match('#^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$#',$host))
		{
			if (!preg_match('#^[a-z0-9\-\.]{2,192}\.(xn--)?[a-z0-9]{2,63}$#i', $host))
				$strError .= GetMessage("SC_TEST_DOMAIN_VALID", array('#VAL#' => htmlspecialchars($_SERVER['HTTP_HOST'])))."<br>\n";
		}
		if ($strError)
			return $this->Result(false, $strError);
		return true;
	}

	function check_bx_crontab()
	{
		return !defined('BX_CRONTAB');
	}

	##############################
	# MYSQL Tests follow
	##############################
	function check_mysql_bug_version()
	{
		$MySql_vercheck_min = "4.1.11";
		$MySql_vercheck_max = "";

		$Oracle_vercheck_min = "10.0";
		$Oracle_vercheck_max = "";

		$MSSQL_vercheck_min = "9.0";
		$MSSQL_vercheck_max = "";

		$ver = $GLOBALS['DB']->GetVersion();
		if (version_compare($ver,$MySql_vercheck_min,'<'))
			return $this->Result(false, GetMessage('SC_MYSQL_ERR_VER', array('#CUR#' => $ver, '#REQ#' => $MySql_vercheck_min)));

		if ($ver == '4.1.21' // sorting
			|| $ver == '5.1.34' // auto_increment
			|| $ver == '5.0.41' // search
			)
			return $this->Result(false,GetMessage('SC_DB_ERR').' '.$ver);

		return true;
	}

	function check_mysql_mode()
	{
		$res = $GLOBALS['DB']->Query('SHOW VARIABLES LIKE \'sql_mode\'');
		$f = $res->Fetch();

		if (strlen($f['Value']) > 0)
			return $this->Result(false,GetMessage('SC_DB_ERR_MODE').' '.$f['Value']);
		return true;
	}

	function check_mysql_time()
	{
		$s = time();
		while($s == time());
		$s++;
		$res = $GLOBALS['DB']->Query('SELECT NOW() AS A');
		$f = $res->Fetch();
		if (($diff = abs($s - strtotime($f['A']))) == 0)
			return true;
		return $this->Result(false, GetMessage('SC_TIME_DIFF', array('#VAL#' => $diff)));
	}

	function check_mysql_increment()
	{
		global $DB;
		$ID = array();
		$table = 'b_site_checker_test';
		$DB->Query('DROP TABLE IF EXISTS '.$table);
		$DB->Query('CREATE TABLE '.$table.' (ID int(18) NOT NULL auto_increment, TEST varchar(50) default NULL, PRIMARY KEY  (`ID`))');
		$DB->Query('INSERT INTO '.$table.'(TEST) VALUES("TEST")');
		$DB->Query('INSERT INTO '.$table.'(TEST) VALUES("TEST")');

		$res = $DB->Query('SELECT ID FROM '.$table);
		while($f = $res->Fetch())
			$ID[] = $f['ID'];
		$increment = $ID[1] - $ID[0];
		$DB->Query('DROP TABLE IF EXISTS '.$table);
		return $increment == 1;
	}

	function check_mysql_table_status()
	{
		global $DB;
		$time = time();

		$strError = '';
		$i = 0;
		$res = $DB->Query('SHOW TABLES');
		$cnt = $res->SelectedRowsCount();
		while($f = $res->Fetch())
		{
			$i++;
			list($k, $table) = each($f);

			if ($this->arTestVars['last_value'])
			{
				if ($this->arTestVars['last_value'] == $table)
					unset($this->arTestVars['last_value']);
				continue;
			}
			
//			if ($f0['Data_length'] > $warn_size)
//				$result.= $this->Result(null,GetMessage('SC_TABLE_SIZE_WARN',array('#TABLE#'=>$table,'#SIZE#'=>floor($f0['Data_length']/1024/1024))))."<br>\n";

			if (!$this->fix_mode)
				$res0 = $DB->Query('CHECK TABLE `' . $table . '`');
			else
				$res0 = $DB->Query('REPAIR TABLE `' . $table . '`');

			$f0 = $res0->Fetch();
			if ($f0['Msg_type'] == 'error' || $f0['Msg_type'] == 'warning')
				$strError .= GetMessage('SC_TABLE_ERR', array('#VAL#' => $table)) . ' ' . $f0['Msg_text'] . "\n<br>";

			if (time()-$time >= $this->timeout)
			{
				$this->arTestVars['last_value'] = $table;
				$this->test_percent = floor($i / $cnt * 100);
				return true;
			}
		}

		if (!$strError)
			return true;

		if (!$this->fix_mode)
		{
			$this->arTestVars['check_table_status_fail'] = true;
			echo $strError; // to log
			return $this->Result(false, GetMessage('SC_TABLES_NEED_REPAIR') . ' <a href="javascript:show_popup(\'' . GetMessage('SC_FIX_DATABASE') . '\', \'?fix_mode=1\')">' . GetMessage('SC_FIX') . '</a>');
		}
		
		return $this->Result(false, $strError);

	}

	function check_mysql_connection_charset()
	{
		global $DB;
		$strError = '';

		if ($this->arTestVars['check_mbstring_fail'])
			return $this->Result(null, GetMessage('SC_MBSTRING_NA'));

		foreach(array('character_set_connection','collation_connection') as $val)
		{
			$res = $DB->Query('SHOW VARIABLES LIKE "' . $val . '"');
			$f = $res->Fetch();
			$$val = $f['Value'];
		}

		$bAllIn1251 = true;
		$res1 = CSite::GetList($by,$order,array('ACTIVE'=>'Y'));
		while($f1 = $res1->Fetch())
			$bAllIn1251 = $bAllIn1251 && trim(strtolower($f1['CHARSET'])) == 'windows-1251';

		if (defined('BX_UTF') && BX_UTF === true)
		{
			if ($character_set_connection != 'utf8')
				$strError = GetMessage("SC_CONNECTION_CHARSET_WRONG", array('#VAL#' => 'utf8', '#VAL1#' => $character_set_connection)); 
			elseif ($collation_connection != 'utf8_unicode_ci')
				$strError = GetMessage("SC_CONNECTION_COLLATION_WRONG_UTF", array('#VAL#' => $collation_connection)); 
		}
		else
		{
			if ($bAllIn1251 && $character_set_connection != 'cp1251')
				$strError = GetMessage("SC_CONNECTION_CHARSET_WRONG", array('#VAL#' => 'cp1251', '#VAL1#' => $character_set_connection)); 
			elseif ($character_set_connection == 'utf8')
				$strError = GetMessage("SC_CONNECTION_CHARSET_WRONG_NOT_UTF", array('#VAL#' => $character_set_connection)); 
		}

		echo 'CHARSET='.$character_set_connection.', COLLATION='.$collation_connection;

		if (!$strError)
			return true;

		$this->arTestVars['check_connection_charset_fail'] = true;
		return $this->Result(false, $strError);
	}

	function check_mysql_db_charset()
	{
		global $DB;
		if ($this->arTestVars['check_mbstring_fail'])
			return $this->Result(null, GetMessage('SC_MBSTRING_NA'));
		elseif ($this->arTestVars['check_table_status_fail'])
			return $this->Result(null, GetMessage('SC_TABLES_NEED_REPAIR'));
		elseif ($this->arTestVars['check_connection_charset_fail'])
			return $this->Result(null, GetMessage('SC_CONNECTION_CHARSET_NA'));

		$strError = '';

		foreach(array('character_set_connection','collation_connection','character_set_database','collation_database') as $val)
		{
			$res = $DB->Query('SHOW VARIABLES LIKE "' . $val . '"');
			$f = $res->Fetch();
			$$val = $f['Value'];
		}

		if ($this->fix_mode)
		{
			if ($DB->Query($sql = 'ALTER DATABASE `' . $DB->DBName. '` DEFAULT CHARACTER SET ' . $character_set_connection . ' COLLATE ' . $collation_connection, true))
				$strError = '';
			else
				$strError .= $sql . ' [' . $DB->db_Error . ']'; 
		}
		else
		{
			if ($character_set_connection != $character_set_database)
				$strError = GetMessage('SC_DATABASE_CHARSET_DIFF', array('#VAL0#' => $character_set_connection, '#VAL1#' => $character_set_database)) . FIX_LINK;
			elseif ($collation_database != $collation_connection)
				$strError = GetMessage('SC_DATABASE_COLLATION_DIFF', array('#VAL0#' => $collation_connection, '#VAL1#' => $collation_database)) . FIX_LINK;
		}
		
		echo 'CHARSET='.$character_set_database.', COLLATION='.$collation_database;

		if (!$strError)
			return true;

		$this->arTestVars['db_charset_fail'] = true;
		return $this->Result(false, $strError);
	}

	function check_mysql_table_charset()
	{
		global $DB;
		$strError = '';

		if ($this->arTestVars['check_mbstring_fail'])
			return $this->Result(null, GetMessage('SC_MBSTRING_NA'));
		elseif ($this->arTestVars['check_table_status_fail'])
			return $this->Result(null, GetMessage('SC_TABLES_NEED_REPAIR'));
		elseif ($this->arTestVars['check_connection_charset_fail'])
			return $this->Result(null, GetMessage('SC_CONNECTION_CHARSET_NA'));
		elseif ($this->arTestVars['db_charset_fail'])
			return $this->Result(null, GetMessage('SC_TABLE_CHECK_NA'));

		$res = $DB->Query('SHOW VARIABLES LIKE "character_set_database"');
		$f = $res->Fetch();
		$charset = trim($f['Value']);

		$res = $DB->Query('SHOW VARIABLES LIKE "collation_database"');
		$f = $res->Fetch();
		$collation = trim($f['Value']);

		$time = time();
		$i = 0;
		$res = $DB->Query('SHOW TABLES LIKE "b_%"');
		$cnt = $res->SelectedRowsCount();

		$arExclusion = array(
			'b_search_content_stem' => 'STEM', 
			'b_search_content_freq' => 'STEM', 
			'b_search_stem' => 'STEM',
			'b_search_tags' => 'NAME'
		);
		while($f = $res->Fetch())
		{
			$i++;
			list($k, $table) = each($f);

			if ($this->arTestVars['last_value'])
			{
				if ($this->arTestVars['last_value'] == $table)
					unset($this->arTestVars['last_value']);
				continue;
			}
			
			$res0 = $DB->Query('SHOW CREATE TABLE `' . $table . '`');
			$f0 = $res0->Fetch();

			if (preg_match('/DEFAULT CHARSET=([^ ]+)/i', $f0['Create Table'], $regs))
			{
				$t_charset = $regs[1];
				if (preg_match('/COLLATE=([^ ]+)/i', $f0['Create Table'], $regs))
					$t_collation = $regs[1];
				else
				{
					$res0 = $DB->Query('SHOW CHARSET LIKE "' . $t_charset . '"');
					$f0 = $res0->Fetch();
					$t_collation = $f0['Default collation'];
				}
			}
			else
			{
				$res0 = $DB->Query('SHOW TABLE STATUS LIKE "' . $table . '"');
				$f0 = $res0->Fetch();
				$t_collation = $f0['Collation'];
				$t_charset = get_charset_by_collation($t_collation);
			}

			if ($charset != $t_charset)
			{
				// table charset differs
				if (!$this->fix_mode)
				{
					$strError .= GetMessage('SC_DB_MISC_CHARSET',array('#TABLE#' => $table,'#VAL1#' => $t_charset,'#VAL0#'=>$charset)) . "<br>\n";
					$this->arTestVars['iError']++;
				}
			}
			elseif ($t_collation != $collation)
			{	// table collation differs
				if (!$this->fix_mode)
				{
					$strError .= GetMessage('SC_COLLATE_WARN',array('#TABLE#'=>$table,'#VAL1#'=>$t_collation,'#VAL0#'=>$collation))."<br>\n";
					$this->arTestVars['iError']++;
					$this->arTestVars['iErrorAutoFix']++;
				}
				elseif (!$DB->Query($sql = 'ALTER TABLE `' . $table . '` COLLATE ' . $collation, true))
				{
					$strError .= $sql . ' [' . $DB->db_Error . ']'; 
					break;
				}
			}

			// fields check
			$arFix = array();
			$res0 = $DB->Query("SHOW FULL COLUMNS FROM `" . $table . "`");
			while($f0 = $res0->Fetch())
			{
				$f_collation = $f0['Collation'];
				if ($f_collation === NULL || $f_collation === "NULL")
					continue;

				$f_charset = get_charset_by_collation($f_collation);
				if ($charset != $f_charset)
				{
					// field charset differs
					if (!$this->fix_mode)
					{
						$strError .= GetMessage('SC_TABLE_CHARSET_WARN',array('#TABLE#' => $table, '#VAL0#' => $charset, '#VAL1#' => $f_charset, '#FIELD#' => $f0['Field'])) . "<br>\n";
						$this->arTestVars['iError']++;
					}
				}
				elseif ($collation != $f_collation)
				{
					if ($arExclusion[$table] && strtoupper($f0['Field']) == $arExclusion[$table])
						continue;

					// field collation differs
					if (!$this->fix_mode)
					{
						$strError .= GetMessage('SC_FIELDS_COLLATE_WARN',array('#TABLE#' => $table, '#VAL0#' => $collation, '#VAL1#' => $f_collation, '#FIELD#' => $f0['Field'])) . "<br>\n";
						$this->arTestVars['iError']++;
						$this->arTestVars['iErrorAutoFix']++;
					}
					else
						$arFix[] = ' MODIFY `' . $f0['Field'] . '` ' . $f0['Type'] . ' COLLATE ' . $collation . ($f0['Null'] == 'YES' ? ' NULL' : ' NOT NULL') . ($f0['Default'] === NULL ? ($f0['Null'] == 'YES' ? ' DEFAULT NULL ' : '') : ' DEFAULT "' . $DB->ForSQL($f0['Default']) . '" ');
				}
			}

			if ($this->fix_mode && count($arFix))
			{
				if (!$DB->Query($sql = 'ALTER TABLE `'.$table.'` '.implode(",\n", $arFix), true))
				{
					$strError .= $sql . ' [' . $DB->db_Error . ']'; 
					break;
				}
			}

			if (time()-$time >= $this->timeout)
			{
				$this->arTestVars['last_value'] = $table;
				$this->test_percent = floor($i / $cnt * 100);
				return true;
			}
		}

		if (!$strError)
			return true;

		if ($this->fix_mode)
			return $this->Result(false, $strError);
		else
		{
			echo $strError; // to log
			return $this->Result(false, GetMessage('SC_CHECK_TABLES_ERRORS', array('#VAL#' => intval($this->arTestVars['iError']), '#VAL1#' => intval($this->arTestVars['iErrorAutoFix']))) . ($this->arTestVars['iErrorAutoFix'] > 0 ? FIX_LINK : ''));
		}
		

	}
	###############
}

function checker_get_unique_id()
{
	@include($_SERVER['DOCUMENT_ROOT'].'/bitrix/license_key.php');
	if (!$LICENSE_KEY)
		$LICENSE_KEY = 'DEMO';
	return md5($_SERVER['DOCUMENT_ROOT'].filemtime(__FILE__).$LICENSE_KEY);
}

function get_charset_by_collation($collation)
{
	global $DB;
	static $CACHE;
	if (!$c = &$CACHE[$collation])
	{
		$res0 = $DB->Query('SHOW COLLATION LIKE "' . $collation . '"');
		$f0 = $res0->Fetch();
		$c = $f0['Charset'];
	}
	return $c;
}
////////////////////////////////////////////////////////////////////////
//////////   END FUNCTIONS   ///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
?>
