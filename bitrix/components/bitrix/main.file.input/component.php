<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arParams['MAX_FILE_SIZE'] = intval($arParams['MAX_FILE_SIZE']);
$arParams['MODULE_ID'] = $arParams['MODULE_ID'] && IsModuleInstalled($arParams['MODULE_ID']) ? $arParams['MODULE_ID'] : false;
// ALLOW_UPLOAD = 'A'll files | 'I'mages | 'F'iles with selected extensions
// ALLOW_UPLOAD_EXT = comma-separated list of allowed file extensions (ALLOW_UPLOAD='F')

if (
	$arParams['ALLOW_UPLOAD'] != 'I' &&
	(
		$arParams['ALLOW_UPLOAD'] != 'F' || strlen($arParams['ALLOW_UPLOAD_EXT']) <= 0
	)
)
	$arParams['ALLOW_UPLOAD'] = 'A';

if ($_POST['mfi_mode'])
{
	$APPLICATION->RestartBuffer();
	while(ob_end_clean()); // hack!
	Header('Content-Type: text/html; charset='.LANG_CHARSET);

	$cid = trim($_REQUEST['cid']);
	if (!$cid || !check_bitrix_sessid())
		die();

	if ($_POST["mfi_mode"] == "upload")
	{
		$count = sizeof($_FILES["mfi_files"]["name"]);

		$mid = $arParams['MODULE_ID'];
		$max_file_size = $arParams['MAX_FILE_SIZE'];

		if (!$mid || !IsModuleInstalled($mid))
			$mid = 'main';

		for($i = 0; $i < $count; $i++)
		{
			$arFile = array(
				"name" => $_FILES["mfi_files"]["name"][$i],
				"size" => $_FILES["mfi_files"]["size"][$i],
				"tmp_name" => $_FILES["mfi_files"]["tmp_name"][$i],
				"type" => $_FILES["mfi_files"]["type"][$i],
				"MODULE_ID" => $mid
			);

			$res = '';
			if ($arParams["ALLOW_UPLOAD"] == "I"):
				$res = CFile::CheckImageFile($arFile, $max_file_size, 0, 0);
			elseif ($arParams["ALLOW_UPLOAD"] == "F"):
				$res = CFile::CheckFile($arFile, $max_file_size, false, $arParams["ALLOW_UPLOAD_EXT"]);
			else:
				$res = CFile::CheckFile($arFile, $max_file_size, false, false);
			endif;

			if ($res === '')
			{
				$fileID = CFile::SaveFile($arFile, $mid);
				$tmp = array(
					"fileName" => $_FILES["mfi_files"]["name"][$i],
					"fileID" => $fileID
				);
				if ($fileID)
				{
					if (!isset($_SESSION["MFI_UPLOADED_FILES_".$cid]))
					{
						$_SESSION["MFI_UPLOADED_FILES_".$cid] = array($fileID);
					}
					else
					{
						$_SESSION["MFI_UPLOADED_FILES_".$cid][] = $fileID;
					}
					$file = CFile::GetFileArray($fileID);
					if ($file)
					{
						$tmp["fileURL"] = $file["SRC"];
						$tmp["fileSize"] = CFile::FormatSize($file['FILE_SIZE']);
					}
				}
				$arResult[] = $tmp;
			}
		}

		$uid = intval($_POST["uniqueID"]);
?>
<script type="text/javascript">
top.FILE_UPLOADER_CALLBACK_<?=$uid?>(<?=CUtil::PhpToJsObject($arResult);?>, <?=$uid;?>);
</script>
<?
	}
	elseif ($_POST['mfi_mode'] == 'delete')
	{
		$fid = intval($_POST["fileID"]);
		if (isset($_SESSION["MFI_UPLOADED_FILES_".$cid]) && in_array($fid, $_SESSION["MFI_UPLOADED_FILES_".$cid]))
		{
			CFile::Delete($fid);
			$key = array_search(intval($fid), $_SESSION["MFI_UPLOADED_FILES_".$cid]);
			unset($_SESSION["MFI_UPLOADED_FILES_".$cid][$key]);
		}
	}

	die();
}

if ($arParams['SILENT'])
	return;

if (substr($arParams['INPUT_NAME'], 0, -2) == '[]')
	$arParams['INPUT_NAME'] = substr($arParams['INPUT_NAME'], -2);
if (substr($arParams['INPUT_NAME_UNSAVED'], 0, -2) == '[]')
	$arParams['INPUT_NAME_UNSAVED'] = substr($arParams['INPUT_NAME_UNSAVED'], -2);

$arParams['INPUT_NAME'] = preg_match('/^[a-zA-Z0-9_]+$/', $arParams['INPUT_NAME']) ? $arParams['INPUT_NAME'] : false;
$arParams['INPUT_NAME_UNSAVED'] = preg_match('/^[a-zA-Z0-9_]+$/', $arParams['INPUT_NAME_UNSAVED']) ? $arParams['INPUT_NAME_UNSAVED'] : '';
$arParams['CONTROL_ID'] = preg_match('/^[a-zA-Z0-9_]+$/', $arParams['CONTROL_ID']) ? $arParams['CONTROL_ID'] : randString();

$arParams['INPUT_CAPTION'] = $arParams['INPUT_CAPTION'] ? $arParams['INPUT_CAPTION'] : GetMessage('MFI_INPUT_CAPTION_DEFAULT');

if (!$arParams['INPUT_NAME'])
{
	showError(GetMessage('MFI_ERR_NO_INPUT_NAME'));
	return false;
}

$arResult['CONTROL_UID'] = md5(randString(15));

$_SESSION["MFI_UPLOADED_FILES_".$arResult['CONTROL_UID']] = array();
$arResult['FILES'] = array();
if (is_array($arParams['INPUT_VALUE']))
{
	$dbRes = CFile::GetList(array(), array("@ID" => implode(",", $arParams["INPUT_VALUE"])));
	while ($arFile = $dbRes->GetNext())
	{
		$arFile['URL'] = CHTTP::URN2URI(CFile::GetFileSRC($arFile));
		$arFile['FILE_SIZE_FORMATTED'] = CFile::FormatSize($arFile['FILE_SIZE']);
		$arResult['FILES'][$arFile['ID']] = $arFile;
		$_SESSION["MFI_UPLOADED_FILES_".$arResult['CONTROL_UID']][] = $arFile['ID'];
	}
}

CUtil::InitJSCore(array('ajax'));

$this->IncludeComponentTemplate();

return $arParams['CONTROL_ID'];
?>