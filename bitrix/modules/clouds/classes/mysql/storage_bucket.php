<?
class CAllCloudStorageBucket
{
	function IncFileCounter($file_size = 0)
	{
		global $DB;
		return $DB->Query("
			UPDATE b_clouds_file_bucket
			SET FILE_COUNT = FILE_COUNT + 1
			".($file_size > 0? ",FILE_SIZE = FILE_SIZE + ".roundDB($file_size): "")."
			WHERE ID = ".$this->_ID."
		");
	}

	function DecFileCounter($file_size = 0)
	{
		global $DB;
		$res = $DB->Query("
			UPDATE b_clouds_file_bucket
			SET FILE_COUNT = FILE_COUNT - 1
			".($file_size > 0? ",FILE_SIZE = if(FILE_SIZE - ".roundDB($file_size)." > 0, FILE_SIZE - ".roundDB($file_size).", 0)": "")."
			WHERE ID = ".$this->_ID." AND FILE_COUNT > 0
		");
		return $res;
	}
}
?>