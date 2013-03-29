<?
IncludeModuleLangFile(__FILE__);

class CCloudStorageBucket extends CAllCloudStorageBucket
{
	protected $_ID;
	protected $arBucket;
	protected $service;
	protected static $arBuckets;

	function __construct($ID)
	{
		$this->_ID = intval($ID);
	}

	function getBucketArray()
	{
		return $this->arBucket;
	}

	function getService()
	{
		return $this->service;
	}

	function __get($name)
	{
		if(!$this->arBucket)
		{
			self::_init();
			$this->arBucket = self::$arBuckets[$this->_ID];
		}

		if($this->arBucket && array_key_exists($name, $this->arBucket))
			return $this->arBucket[$name];
		else
			return null;
	}

	function Init()
	{
		if(is_object($this->service))
		{
			return true;
		}
		else
		{
			if($this->SERVICE_ID)
				$this->service = CCloudStorage::GetServiceByID($this->SERVICE_ID);
			return is_object($this->service);
		}
	}

	function CheckSettings(&$arSettings)
	{
		return $this->service->CheckSettings($this->arBucket, $arSettings);
	}

	function CreateBucket()
	{
		return $this->service->CreateBucket($this->arBucket);
	}

	function GetFileSRC($arFile)
	{
		if(is_array($arFile) && isset($arFile["URN"]))
			return $this->service->GetFileSRC($this->arBucket, $arFile["URN"]);
		else
			return preg_replace("'(?<!:)/+'s", "/", $this->service->GetFileSRC($this->arBucket, $arFile));
	}

	function FileExists($filePath)
	{
		return $this->service->FileExists($this->arBucket, $filePath);
	}

	function DownloadToFile($arFile, $filePath)
	{
		return $this->service->DownloadToFile($this->arBucket, $arFile, $filePath);
	}

	function SaveFile($filePath, $arFile)
	{
		return $this->service->SaveFile($this->arBucket, $filePath, $arFile);
	}

	function DeleteFile($filePath)
	{
		return $this->service->DeleteFile($this->arBucket, $filePath);
	}

	function FileCopy($arFile, $filePath, $bRecursive = false)
	{
		return $this->service->FileCopy($this->arBucket, $arFile, $filePath, $bRecursive);
	}

	function ListFiles($filePath = "/", $bRecursive = false)
	{
		return $this->service->ListFiles($this->arBucket, $filePath, $bRecursive);
	}

	function GetFileSize($filePath)
	{
		$DIR_NAME = substr($filePath, 0, strrpos($filePath, "/") + 1);
		$FILE_NAME = substr($filePath, strlen($DIR_NAME));

		$arListing = $this->service->ListFiles($this->arBucket, $DIR_NAME, false);
		if(is_array($arListing))
		{
			foreach($arListing["file"] as $i => $name)
				if($name === $FILE_NAME)
					return $arListing["file_size"][$i];
		}
		return 0;
	}

	static function GetAllBuckets()
	{
		self::_init();
		return self::$arBuckets;
	}

	function Add($arFields)
	{
		global $DB, $APPLICATION, $CACHE_MANAGER;
		$strError = '';
		$this->_ID = 0;

		if(!$this->CheckFields($arFields, 0))
			return false;

		$arFields["FILE_COUNT"] = 0;
		if(is_array($arFields["FILE_RULES"]))
			$arFields["FILE_RULES"] = serialize($arFields["FILE_RULES"]);
		else
			$arFields["FILE_RULES"] = false;

		$this->arBucket = $arFields;
		if($this->Init())
		{

			if(!$this->CheckSettings($arFields["SETTINGS"]))
				return false;
			$this->arBucket["SETTINGS"] = $arFields["SETTINGS"];

			if($this->CreateBucket())
			{
				$arFields["SETTINGS"] = serialize($arFields["SETTINGS"]);
				$this->_ID = $DB->Add("b_clouds_file_bucket", $arFields);
				$this->arBucket = null;
				if(CACHED_b_clouds_file_bucket !== false)
					$CACHE_MANAGER->CleanDir("b_clouds_file_bucket");
				return $this->_ID;
			}
			else
			{
				$e = $APPLICATION->GetException();
				if(is_object($e))
					$strError = GetMessage("CLO_STORAGE_CLOUD_ADD_ERROR", array("#error_msg#" => $e->GetString()));
				else
					$strError = GetMessage("CLO_STORAGE_CLOUD_ADD_ERROR", array("#error_msg#" => 'CSB42343'));
			}
		}
		else
		{
			$strError = GetMessage("CLO_STORAGE_CLOUD_ADD_ERROR", array("#error_msg#" => GetMessage("CLO_STORAGE_UNKNOWN_SERVICE")));
		}

		$APPLICATION->ResetException();
		$e = new CApplicationException($strError);
		$APPLICATION->ThrowException($e);
		return false;
	}

	function Delete()
	{
		global $DB, $APPLICATION, $CACHE_MANAGER;
		$strError = '';

		if($this->Init())
		{
			if($this->service->IsEmptyBucket($this->arBucket))
			{
				if($this->service->DeleteBucket($this->arBucket))
				{
					$res = $DB->Query("DELETE FROM b_clouds_file_bucket WHERE ID = ".$this->_ID);
					if(CACHED_b_clouds_file_bucket !== false)
						$CACHE_MANAGER->CleanDir("b_clouds_file_bucket");
					if(is_object($res))
					{
						$this->arBucket = null;
						$this->_ID = 0;
						return true;
					}
					else
					{
						$strError = GetMessage("CLO_STORAGE_DB_DELETE_ERROR");
					}
				}
				else
				{
					$e = $APPLICATION->GetException();
					$strError = GetMessage("CLO_STORAGE_CLOUD_DELETE_ERROR", array("#error_msg#" => is_object($e)? $e->GetString(): ''));
				}
			}
			else
			{
				$e = $APPLICATION->GetException();
				if(is_object($e))
					$strError = GetMessage("CLO_STORAGE_CLOUD_DELETE_ERROR", array("#error_msg#" => $e->GetString()));
				else
					$strError = GetMessage("CLO_STORAGE_CLOUD_BUCKET_NOT_EMPTY");
			}
		}
		else
		{
			$strError = GetMessage("CLO_STORAGE_CLOUD_DELETE_ERROR", array("#error_msg#" => GetMessage("CLO_STORAGE_UNKNOWN_SERVICE")));
		}

		$APPLICATION->ResetException();
		$e = new CApplicationException($strError);
		$APPLICATION->ThrowException($e);
		return false;
	}

	function Update($arFields)
	{
		global $DB, $CACHE_MANAGER;

		if($this->_ID <= 0)
			return false;

		$this->service = CCloudStorage::GetServiceByID($this->SERVICE_ID);
		if(!is_object($this->service))
			return false;

		unset($arFields["FILE_COUNT"]);
		unset($arFields["SERVICE_ID"]);
		unset($arFields["LOCATION"]);
		unset($arFields["BUCKET"]);

		if(!$this->CheckFields($arFields, $this->_ID))
			return false;

		if(array_key_exists("FILE_RULES", $arFields))
		{
			if(is_array($arFields["FILE_RULES"]))
				$arFields["FILE_RULES"] = serialize($arFields["FILE_RULES"]);
			else
				$arFields["FILE_RULES"] = false;
		}

		if(array_key_exists("SETTINGS", $arFields))
		{
			if(!$this->CheckSettings($arFields["SETTINGS"]))
				return false;
			$arFields["SETTINGS"] = serialize($arFields["SETTINGS"]);
		}

		$strUpdate = $DB->PrepareUpdate("b_clouds_file_bucket", $arFields);
		if(strlen($strUpdate) > 0)
		{
			$strSql = "
				UPDATE b_clouds_file_bucket SET
				".$strUpdate."
				WHERE ID = ".$this->_ID."
			";
			if(!$DB->Query($strSql))
				return false;
		}

		if(CACHED_b_clouds_file_bucket !== false)
			$CACHE_MANAGER->CleanDir("b_clouds_file_bucket");

		return $this->_ID;
	}

	function CheckFields(&$arFields, $ID)
	{
		global $APPLICATION;
		$aMsg = array();

		if(array_key_exists("ACTIVE", $arFields))
			$arFields["ACTIVE"] = $arFields["ACTIVE"] === "N"? "N": "Y";

		if(array_key_exists("READ_ONLY", $arFields))
			$arFields["READ_ONLY"] = $arFields["READ_ONLY"] === "Y"? "Y": "N";

		$arServices = CCloudStorage::GetServiceList();
		if(isset($arFields["SERVICE_ID"]))
		{
			if(!array_key_exists($arFields["SERVICE_ID"], $arServices))
				$aMsg[] = array("id" => "SERVICE_ID", "text" => GetMessage("CLO_STORAGE_WRONG_SERVICE"));
		}

		if(isset($arFields["BUCKET"]))
		{
			$arFields["BUCKET"] = trim($arFields["BUCKET"]);

			$bBadLength = false;
			if(strpos($arFields["BUCKET"], ".") !== false)
			{
				$arName = explode(".", $arFields["BUCKET"]);
				$bBadLength = false;
				foreach($arName as $str)
					if(strlen($str) < 2 || strlen($str) > 63)
						$bBadLength = true;
			}

			if(strlen($arFields["BUCKET"]) <= 0)
				$aMsg[] = array("id" => "BUCKET", "text" => GetMessage("CLO_STORAGE_EMPTY_BUCKET"));
			if(preg_match("/[^a-z0-9-.]/", $arFields["BUCKET"]))
				$aMsg[] = array("id" => "BUCKET", "text" => GetMessage("CLO_STORAGE_BAD_BUCKET_NAME"));
			if(strlen($arFields["BUCKET"]) < 2 || strlen($arFields["BUCKET"]) > 63)
				$aMsg[] = array("id" => "BUCKET", "text" => GetMessage("CLO_STORAGE_WRONG_BUCKET_NAME_LENGTH"));
			if($bBadLength)
				$aMsg[] = array("id" => "BUCKET", "text" => GetMessage("CLO_STORAGE_WRONG_BUCKET_NAME_LENGTH2"));
			if(!preg_match("/^[a-z0-9].*[a-z0-9]\$/", $arFields["BUCKET"]))
				$aMsg[] = array("id" => "BUCKET", "text" => GetMessage("CLO_STORAGE_BAD_BUCKET_NAME2"));
			if(preg_match("/(-\\.|\\.-)/", $arFields["BUCKET"]))
				$aMsg[] = array("id" => "BUCKET", "text" => GetMessage("CLO_STORAGE_BAD_BUCKET_NAME3"));

			if(strlen($arFields["BUCKET"]) > 0)
			{
				$rsBucket = CCloudStorageBucket::GetList(array(), array(
					"=SERVICE_ID" => $arFields["SERVICE_ID"],
					"=BUCKET" => $arFields["BUCKET"],
				));
				$arBucket = $rsBucket->Fetch();
				if(is_array($arBucket) && $arBucket["ID"] != $ID)
					$aMsg[] = array("id" => "BUCKET", "text" => GetMessage("CLO_STORAGE_BUCKET_ALREADY_EXISTS"));
			}
		}

		if(!empty($aMsg))
		{
			$e = new CAdminException($aMsg);
			$APPLICATION->ThrowException($e);
			return false;
		}
		return true;
	}

	static function GetList($arOrder=false, $arFilter=false, $arSelect=false)
	{
		global $DB;

		if(!is_array($arSelect))
			$arSelect = array();
		if(count($arSelect) < 1)
			$arSelect = array(
				"ID",
				"ACTIVE",
				"READ_ONLY",
				"SORT",
				"SERVICE_ID",
				"LOCATION",
				"BUCKET",
				"SETTINGS",
				"CNAME",
				"PREFIX",
				"FILE_COUNT",
				"FILE_SIZE",
				"LAST_FILE_ID",
				"FILE_RULES",
			);

		if(!is_array($arOrder))
			$arOrder = array();

		$arQueryOrder = array();
		foreach($arOrder as $strColumn => $strDirection)
		{
			$strColumn = strtoupper($strColumn);
			$strDirection = strtoupper($strDirection)=="ASC"? "ASC": "DESC";
			switch($strColumn)
			{
				case "ID":
				case "SORT":
					$arSelect[] = $strColumn;
					$arQueryOrder[$strColumn] = $strColumn." ".$strDirection;
					break;
			}
		}

		$arQuerySelect = array();
		foreach($arSelect as $strColumn)
		{
			$strColumn = strtoupper($strColumn);
			switch($strColumn)
			{
				case "ID":
				case "ACTIVE":
				case "READ_ONLY":
				case "SORT":
				case "SERVICE_ID":
				case "LOCATION":
				case "BUCKET":
				case "SETTINGS":
				case "CNAME":
				case "PREFIX":
				case "FILE_COUNT":
				case "FILE_SIZE":
				case "LAST_FILE_ID":
				case "FILE_RULES":
					$arQuerySelect[$strColumn] = "s.".$strColumn;
					break;
			}
		}
		if(count($arQuerySelect) < 1)
			$arQuerySelect = array("ID"=>"s.ID");

		$obQueryWhere = new CSQLWhere;
		$arFields = array(
			"ID" => array(
				"TABLE_ALIAS" => "s",
				"FIELD_NAME" => "s.ID",
				"FIELD_TYPE" => "int",
				"MULTIPLE" => false,
				"JOIN" => false,
			),
			"SERVICE_ID" => array(
				"TABLE_ALIAS" => "s",
				"FIELD_NAME" => "s.SERVICE_ID",
				"FIELD_TYPE" => "string",
				"MULTIPLE" => false,
				"JOIN" => false,
			),
			"BUCKET" => array(
				"TABLE_ALIAS" => "s",
				"FIELD_NAME" => "s.BUCKET",
				"FIELD_TYPE" => "string",
				"MULTIPLE" => false,
				"JOIN" => false,
			),
		);
		$obQueryWhere->SetFields($arFields);

		if(!is_array($arFilter))
			$arFilter = array();
		$strQueryWhere = $obQueryWhere->GetQuery($arFilter);

		$bDistinct = $obQueryWhere->bDistinctReqired;

		$strSql = "
			SELECT ".($bDistinct? "DISTINCT": "")."
			".implode(", ", $arQuerySelect)."
			FROM
				b_clouds_file_bucket s
			".$obQueryWhere->GetJoins()."
		";

		if($strQueryWhere)
		{
			$strSql .= "
				WHERE
				".$strQueryWhere."
			";
		}

		if(count($arQueryOrder) > 0)
		{
			$strSql .= "
				ORDER BY
				".implode(", ", $arQueryOrder)."
			";
		}

		return $DB->Query($strSql);
	}

	function _init()
	{
		global $DB, $CACHE_MANAGER;

		if(!self::$arBuckets)
		{
			$cache_id = "cloud_buckets_v2";
			if(
				CACHED_b_clouds_file_bucket !== false
				&& $CACHE_MANAGER->Read(CACHED_b_clouds_file_bucket, $cache_id, "b_clouds_file_bucket")
			)
			{
				self::$arBuckets = $CACHE_MANAGER->Get($cache_id);
			}
			else
			{
				self::$arBuckets = array();

				$rs = $DB->Query("
					SELECT *
					FROM b_clouds_file_bucket
					ORDER BY SORT DESC, ID ASC
				");
				while($ar = $rs->Fetch())
				{
					if($ar["FILE_RULES"])
						$arRules = unserialize($ar["FILE_RULES"]);
					else
						$arRules = array();

					$ar["FILE_RULES_COMPILED"] = CCloudStorageBucket::CompileRules($arRules);

					if($ar["SETTINGS"])
						$arSettings = unserialize($ar["SETTINGS"]);
					else
						$arSettings = array();

					if(is_array($arSettings))
						$ar["SETTINGS"] = $arSettings;
					else
						$ar["SETTINGS"] = array();

					self::$arBuckets[intval($ar['ID'])] = $ar;
				}

				if(CACHED_b_clouds_file_bucket !== false)
					$CACHE_MANAGER->Set($cache_id, self::$arBuckets);
			}
		}
	}

	function CompileRules($arRules)
	{
		$arCompiled = array();
		if(is_array($arRules))
		{
			foreach($arRules as $rule)
			{
				if(is_array($rule))
				{
					$arCompiled[] = array(
						"MODULE_MASK" => isset($rule["MODULE"])? CCloudStorageBucket::CompileModuleRule($rule["MODULE"]): "",
						"EXTENTION_MASK" => isset($rule["EXTENSION"])? CCloudStorageBucket::CompileExtentionRule($rule["EXTENSION"]): "",
						"SIZE_ARRAY" => isset($rule["SIZE"])? CCloudStorageBucket::CompileSizeRule($rule["SIZE"]): "",
					);
				}
			}
		}
		return $arCompiled;
	}

	function CompileModuleRule($str)
	{
		$res = array();
		$ar = explode(",", $str);
		foreach($ar as $s)
		{
			$s = trim($s);
			if(strlen($s))
				$res[$s] = preg_quote($s, '/');
		}
		if(!empty($res))
			return "/^(".implode("|", $res).")\$/";
		else
			return "";
	}

	function CompileExtentionRule($str)
	{
		$res = array();
		$ar = explode(",", $str);
		foreach($ar as $s)
		{
			$s = trim($s);
			if(strlen($s))
				$res[$s] = preg_quote(".".$s, '/');
		}
		if(!empty($res))
			return "/(".implode("|", $res).")\$/i";
		else
			return "";
	}

	function CompileSizeRule($str)
	{
		$res = array();
		$ar = explode(",", $str);
		foreach($ar as $s)
		{
			$s = trim($s);
			if(strlen($s))
			{
				$arSize = explode("-", $s);
				if(count($arSize) == 1)
					$res[] = array(CCloudStorageBucket::ParseSize($arSize[0]), CCloudStorageBucket::ParseSize($arSize[0]));
				else
					$res[] = array(CCloudStorageBucket::ParseSize($arSize[0]), CCloudStorageBucket::ParseSize($arSize[1]));
			}
		}
		return $res;
	}

	function ParseSize($str)
	{
		static $scale = array(''=>1,'K'=>1024,'M'=>1048576,'G'=>1073741824);
		$str = strtoupper(trim($str));
		if(strlen($str) && preg_match("/([0-9.]+)(|K|M|G)\$/", $str, $match))
		{
			return $match[1]*$scale[$match[2]];
		}
		else
		{
			return false;
		}
	}

	function ConvertPOST($arPOST)
	{
		$arRules = array();

		if(isset($arPOST["MODULE"]) && is_array($arPOST["MODULE"]))
		{
			foreach($_POST["MODULE"] as $i => $MODULE)
			{
				if(!isset($arRules[intval($i)]))
					$arRules[intval($i)] = array("MODULE" => "", "EXTENSION" => "", "SIZE" => "");
				$arRules[intval($i)]["MODULE"] = $MODULE;
			}
		}

		if(isset($arPOST["EXTENSION"]) && is_array($arPOST["EXTENSION"]))
		{
			foreach($_POST["EXTENSION"] as $i => $EXTENSION)
			{
				if(!isset($arRules[intval($i)]))
					$arRules[intval($i)] = array("MODULE" => "", "EXTENSION" => "", "SIZE" => "");
				$arRules[intval($i)]["EXTENSION"] = $EXTENSION;
			}
		}

		if(isset($arPOST["SIZE"]) && is_array($arPOST["SIZE"]))
		{
			foreach($_POST["SIZE"] as $i => $SIZE)
			{
				if(!isset($arRules[intval($i)]))
					$arRules[intval($i)] = array("MODULE" => "", "EXTENSION" => "", "SIZE" => "");
				$arRules[intval($i)]["SIZE"] = $SIZE;
			}
		}

		return $arRules;
	}

	function setHeader($name, $value)
	{
		$this->service->setHeader($name, $value);
	}
}
?>