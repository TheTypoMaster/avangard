<?
/*********************************************************************
						Caching
*********************************************************************/
class CPHPCacheFiles
{
	var $filename;
	var $folder;
	var $content;
	var $vars;
	var $TTL;
	var $uniq_str;
	var $initdir;
	var $bStarted = false;
	var $bInit = "NO";
	//cache stats
	var $written = false;
	var $read = false;

	function IsAvailable()
	{
		return true;
	}

	function clean($basedir, $initdir, $filename = false)
	{
		if(strlen($filename))
		{
			$fn = $_SERVER["DOCUMENT_ROOT"].$basedir.$initdir.$filename;

			//This checks for Zend Server CE in order to supress warnings
			if(function_exists('accelerator_reset'))
			{
				@chmod($fn, BX_FILE_PERMISSIONS);
				if(@unlink($fn))
				{
					bx_accelerator_reset();
					return true;
				}
			}
			else
			{
				if(file_exists($fn))
				{
					@chmod($fn, BX_FILE_PERMISSIONS);
					if(unlink($fn))
					{
						bx_accelerator_reset();
						return true;
					}
				}
			}
			return false;
		}
		else
		{
			global $DB, $APPLICATION;
			static $bAgentAdded = false;

			$source = "/".trim($basedir, "/")."/".trim($initdir, "/");
			$bDelayedDelete = false;

			if(file_exists($_SERVER["DOCUMENT_ROOT"].$source))
			{
				$target = $source.".~";
				for($i = 0; $i < 9; $i++) //try to get new directory name no more than ten times
				{
					$suffix = rand(0, 999999);
					if(!file_exists($_SERVER["DOCUMENT_ROOT"].$target.$suffix))
					{
						if(
							$DB->Query("INSERT INTO b_cache_tag (SITE_ID, CACHE_SALT, RELATIVE_PATH, TAG)
							VALUES ('*', '*', '".$DB->ForSQL($target.$suffix)."', '*')")
						)
						{
							if(@rename($_SERVER["DOCUMENT_ROOT"].$source, $_SERVER["DOCUMENT_ROOT"].$target.$suffix))
								$bDelayedDelete = true;
						}
						break;
					}
				}
			}

			if($bDelayedDelete)
			{
				if(!$bAgentAdded)
				{
					$bAgentAdded = true;
					$rsAgents = CAgent::GetList(array("ID"=>"DESC"), array("NAME" => "CPHPCacheFiles::DelayedDelete(%"));
					if(!$rsAgents->Fetch())
					{
						$res = CAgent::AddAgent(
							"CPHPCacheFiles::DelayedDelete();",
							"main", //module
							"Y", //period
							1 //interval
						);

						if(!$res)
							$APPLICATION->ResetException();
					}
				}
			}
			else
			{
				DeleteDirFilesEx($basedir.$initdir);
			}

			bx_accelerator_reset();
		}
	}

	function read(&$arAllVars, $basedir, $initdir, $filename, $TTL)
	{
		$fn = $_SERVER["DOCUMENT_ROOT"].$basedir.$initdir.$filename;

		if(!file_exists($fn))
			return false;

		if(is_array($arAllVars))
		{
			$INCLUDE_FROM_CACHE='Y';
			if(!@include($fn))
				return false;
		}
		else
		{
			$handle = fopen($fn, "rb");
			if(!$handle)
				return false;

			$datecreate = fread($handle, 2);
			if($datecreate == "BX")
			{
				$datecreate = fread($handle, 12);
				$dateexpire = fread($handle, 12);
			}
			else
			{
				$datecreate .= fread($handle, 10);
			}
		}

		/* We suppress warning here in order not to break
		the compression under Zend Server */
		$this->read = @filesize($fn);

		if(intval($datecreate) < (mktime() - $TTL))
			return false;

		if(is_array($arAllVars))
		{
			$arAllVars = unserialize($ser_content);
		}
		else
		{
			$arAllVars = fread($handle, filesize($fn));
			fclose($handle);
		}

		return true;
	}

	function write($arAllVars, $basedir, $initdir, $filename, $TTL)
	{

		$folder = $_SERVER["DOCUMENT_ROOT"].$basedir.$initdir;
		$fn = $folder.$filename;
		$tmp_fn = $folder.md5(mt_rand()).".tmp";

		if(!CheckDirPath($fn))
			return;

		if($handle = fopen($tmp_fn, "wb+"))
		{
			if(is_array($arAllVars))
			{
				$contents = "<?";
				$contents .= "\nif(\$INCLUDE_FROM_CACHE!='Y')return false;";
				$contents .= "\n\$datecreate = '".str_pad(mktime(), 12, "0", STR_PAD_LEFT)."';";
				$contents .= "\n\$dateexpire = '".str_pad(mktime() + IntVal($TTL), 12, "0", STR_PAD_LEFT)."';";
				$contents .= "\n\$ser_content = '".str_replace("'", "\'", str_replace("\\", "\\\\", serialize($arAllVars)))."';";
				$contents .= "\nreturn true;";
				$contents .= "\n?>";
			}
			else
			{
				$contents = "BX".str_pad(mktime(), 12, "0", STR_PAD_LEFT).str_pad(mktime() + IntVal($this->TTL), 12, "0", STR_PAD_LEFT);
				$contents .= $arAllVars;
			}

			$this->written = fwrite($handle, $contents);
			$len = function_exists('mb_strlen')? mb_strlen($contents, 'latin1'): strlen($contents);

			fclose($handle);

			//This checks for Zend Server CE in order to supress warnings
			if(function_exists('accelerator_reset'))
				@unlink($fn);
			elseif(file_exists($fn))
				unlink($fn);

			if($this->written === $len)
				rename($tmp_fn, $fn);

			//This checks for Zend Server CE in order to supress warnings
			if(function_exists('accelerator_reset'))
				@unlink($tmp_fn);
			elseif(file_exists($tmp_fn))
				unlink($tmp_fn);
		}
	}

	function IsCacheExpired($path)
	{
		if(!file_exists($path))
			return true;

		$dateexpire = 0;

		$INCLUDE_FROM_CACHE='Y';

		$dfile = fopen($path, "rb");
		$str_tmp = fread($dfile, 150);
		fclose($dfile);

		if(
			preg_match("/dateexpire\s*=\s*'([\d]+)'/im", $str_tmp, $arTmp)
			|| preg_match("/^BX\\d{12}(\\d{12})/", $str_tmp, $arTmp)
			|| preg_match("/^(\\d{12})/", $str_tmp, $arTmp)
		)
		{
			if(strlen($arTmp[1]) <= 0 || doubleval($arTmp[1]) < mktime())
				return true;
		}

		return false;
	}

	function DeleteOneDir()
	{
		global $DB;
		$bDeleteFromQueue = false;

		$rs = $DB->Query($DB->TopSql("SELECT * from b_cache_tag WHERE TAG='*'", 1));
		if($ar = $rs->Fetch())
		{
			$dir_name = $_SERVER["DOCUMENT_ROOT"].$ar["RELATIVE_PATH"];
			if(file_exists($dir_name))
			{
				$dh = opendir($dir_name);
				if(is_resource($dh))
				{
					$Counter = 0;
					while(($file = readdir($dh)) !== false)
					{
						if($file != "." && $file != "..")
						{
							DeleteDirFilesEx($ar["RELATIVE_PATH"]."/".$file);
							$Counter++;
							break;
						}
					}
					closedir($dh);

					if($Counter == 0)
					{
						rmdir($dir_name);
						$bDeleteFromQueue = true;
					}
				}
			}
			else
			{
				$bDeleteFromQueue = true;
			}

			if($bDeleteFromQueue)
			{
				$DB->Query("
					DELETE FROM b_cache_tag
					WHERE SITE_ID = '".$DB->ForSQL($ar["SITE_ID"])."'
					AND CACHE_SALT = '".$DB->ForSQL($ar["CACHE_SALT"])."'
					AND RELATIVE_PATH = '".$DB->ForSQL($ar["RELATIVE_PATH"])."'
				");
			}
		}
	}

	function DelayedDelete($count = 1, $level = 1)
	{
		global $DB;

		$etime = time()+2;
		for($i = 0; $i < $count; $i++)
		{
			CPHPCacheFiles::DeleteOneDir();
			if(time() > $etime)
				break;
		}

		//try to adjust cache cleanup speed to cache cleanups
		$rs = $DB->Query("SELECT * from b_cache_tag WHERE TAG='**'");
		if($ar = $rs->Fetch())
			$last_count = intval($ar["RELATIVE_PATH"]);
		else
			$last_count = 0;
		$bWasStatRecFound = is_array($ar);

		$rs = $DB->Query("SELECT count(1) CNT from b_cache_tag WHERE TAG='*'");
		if($ar = $rs->Fetch())
			$this_count = $ar["CNT"];
		else
			$this_count = 0;

		$delta = $this_count - $last_count;
		if($delta > 0)
			$count = intval($this_count/3600)+1; //Rest of the queue in an hour
		elseif($count < 1)
			$count = 1;

		if($bWasStatRecFound)
			$DB->Query("UPDATE b_cache_tag SET RELATIVE_PATH='".$this_count."' WHERE TAG='**'");
		else
			$DB->Query("INSERT INTO b_cache_tag (TAG, RELATIVE_PATH) VALUES ('**', '".$this_count."')");

		if($this_count > 0)
			return "CPHPCacheFiles::DelayedDelete(".$count.");";
		else
			return "";
	}
}
?>