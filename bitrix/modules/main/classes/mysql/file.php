<?
class CFile extends CAllFile
{
	function Delete($ID)
	{
		global $DB;
		$ID = intval($ID);

		if($ID <= 0)
			return;

		$res = CFile::GetByID($ID);
		if($res = $res->Fetch())
		{
			$delete_size = 0;
			$upload_dir = COption::GetOptionString("main", "upload_dir", "upload");

			$dname = $_SERVER["DOCUMENT_ROOT"]."/".$upload_dir."/".$res["SUBDIR"];
			$fname = $dname."/".$res["FILE_NAME"];

			if(file_exists($fname))
				if(unlink($fname))
					$delete_size += $res["FILE_SIZE"];

			$delete_size += CFile::ResizeImageDelete($res);

			$DB->Query("DELETE FROM b_file WHERE ID = ".$ID);

			if(file_exists($dname))
				@rmdir($dname);

			CFile::CleanCache($ID);

			foreach(GetModuleEvents("main", "OnFileDelete", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array($res));

			/****************************** QUOTA ******************************/
			if($delete_size > 0 && COption::GetOptionInt("main", "disk_space") > 0)
				CDiskQuota::updateDiskQuota("file", $delete_size, "delete");
			/****************************** QUOTA ******************************/
		}
	}

	function DoDelete($ID)
	{
		CFile::Delete($ID);
	}
}
?>