<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/database.php");

/********************************************************************
*	Класс для работы с MSSQL
********************************************************************/
class CDatabase extends CAllDatabase
{
	var $DBName;
	var $version;
	var $DBHost;
	var $DBLogin;
	var $DBPassword;
	var $bConnected;
	var $column_cache = Array();
	var $open_transaction = false;
	var $cntQuery;
	var $timeQuery;

	function GetVersion()
	{
		if($this->version)
			return $this->version;

		$rs = $this->Query("SELECT @@VERSION as R", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		if ($ar = $rs->Fetch())
		{
			$version = trim($ar["R"]);
			$this->XE = (strpos($version, "Express Edition")>0);
			preg_match("#[0-9]+\.[0-9]+\.[0-9]+#", $version, $arr);
			$version = $arr[0];
			$this->version = $version;
			return $version;
		}
		else
			return false;
	}

	function StartTransaction()
	{
		$this->DoConnect();
		odbc_autocommit($this->db_Conn, false);
		//$this->Query("begin transaction", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
	}

	function Commit()
	{
		$this->DoConnect();
		odbc_commit($this->db_Conn);
		//$this->Query("commit", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		odbc_autocommit($this->db_Conn, true);
	}

	function Rollback()
	{
		$this->DoConnect();
		odbc_rollback($this->db_Conn);
		//$this->Query("rollback", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		odbc_autocommit($this->db_Conn, true);
	}

	function Connect($DBHost, $DBName, $DBLogin, $DBPassword)
	{
		$this->type="MSSQL";
		$this->DBHost = $DBHost;
		$this->DBName = $DBName;
		$this->DBLogin = $DBLogin;
		$this->DBPassword = $DBPassword;
		$this->bConnected = false;
		if(defined("DELAY_DB_CONNECT") && DELAY_DB_CONNECT===true)
			return true;
		else
			return $this->DoConnect();
	}

	function DoConnect()
	{
		if($this->bConnected)
			return;
		$this->bConnected = true;

		if(!defined("DBPersistent"))
			define("DBPersistent", true);

		//$DSN = "DRIVER={SQL Server}; SERVER={".$DBHost."};UID={".$DBLogin."};PWD={".$DBPassword."}; DATABASE={".$DBName."}";
		$DSN = $this->DBHost;

		if(DBPersistent)
			$this->db_Conn = odbc_pconnect($DSN, $this->DBLogin, $this->DBPassword);
		else
			$this->db_Conn = odbc_connect($DSN, $this->DBLogin, $this->DBPassword);

		if(!$this->db_Conn)
		{
			$s = (DBPersistent? "odbc_pconnect":"odbc_connect");
			if($this->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
				echo "<br><font color=#ff0000>Error! ".$s."('-', '-', '-')</font><br>#".odbc_error()." ".odbc_errormsg()."<br>";
			else
				SendError("Error! ".$s."('-', '-', '-')\n#".odbc_error()." ".odbc_errormsg()."\n");
			return false;
		}
		odbc_autocommit($this->db_Conn, true);

		if($this->DBName <> "")
			$this->Query("USE ".$this->DBName);

		$this->cntQuery = 0;
		$this->timeQuery = 0;
		$this->arQueryDebug = array();
		global $DB, $USER, $APPLICATION;
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/after_connect.php"))
			include($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/after_connect.php");
		return true;
	}

	function Query($strSql, $bIgnoreErrors=false, $error_position="")
	{
		$this->DoConnect();
		$this->db_Error="";

		if($this->DebugToFile || $this->ShowSqlStat)
		{
			list($usec, $sec) = explode(" ", microtime());
			$start_time = ((float)$usec + (float)$sec);
		}

		$result = @odbc_exec($this->db_Conn, $strSql);

		if($this->DebugToFile || $this->ShowSqlStat)
		{
			list($usec, $sec) = explode(" ",microtime());
			$end_time = ((float)$usec + (float)$sec);
			$exec_time = round($end_time-$start_time, 10);
	
			if($this->ShowSqlStat)
			{
				$this->cntQuery++;
				$this->timeQuery+=$exec_time;
				$this->arQueryDebug[] = array(
					"QUERY"	=>$strSql,
					"TIME"	=>$exec_time,
					"TRACE"	=>(function_exists("debug_backtrace")? debug_backtrace():false),
				);
			}

			if($this->DebugToFile)
			{
				$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/mssql_debug.sql","ab+");
				$str = "TIME: ".$exec_time." SESSION: ".session_id()." \n";
				$str .= $strSql."\n\n";
				$str .= "----------------------------------------------------\n\n";
				fputs($fp, $str);
				@fclose($fp);
			}
		}

		if(!$result)
		{
			$this->db_Error = "#".odbc_error()." ".odbc_errormsg();
			if(!$bIgnoreErrors)
			{
				AddMessage2Log($error_position." MSSQL Query Error: ".$strSql." [".$this->db_Error."]", "main");
				if ($this->DebugToFile)
				{
					$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/mssql_debug.sql","ab+");
					fputs($fp,"SESSION: ".session_id()." ERROR: ".$this->db_Error."\n\n----------------------------------------------------\n\n");
					@fclose($fp);
				}

				if($this->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
				{
					echo $error_position."<br>MSSQL Query Error:<br><font color=#ff0000><pre>".htmlspecialchars($strSql)."</pre></font><br>".$this->db_Error."<br>";
				}
				else
				{
					$error_position = eregi_replace("<br>","\n",$error_position);
					SendError($error_position."\nMSSQL Query Error:\n".$strSql." \n [".$this->db_Error."]\n---------------\n\n");
				}

				if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/dbquery_error.php"))
				{
					include($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/dbquery_error.php");
				}
				elseif(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/dbquery_error.php"))
				{
					include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/dbquery_error.php");
				}
				else
				{
					die("MSSQL Query Error!");
				}
				die();

			}
			return false;
		}
		if(defined("BX_MSSQL_BINMODE") && BX_MSSQL_BINMODE == true)
		{
			@odbc_binmode($result, ODBC_BINMODE_PASSTHRU);
			@odbc_longreadlen($result, 9999999);
		}
		$res = new CDBResult($result);
		$res->num_rows_affected = intval(odbc_num_rows($result));
		return $res;
	}

	//Делает запрос к базе данных. Для MSSQL больше ничего ;-)
	function QueryLong($strSql, $bIgnoreErrors = false)
	{
		return $this->Query($strSql, $bIgnoreErrors);
	}

	function CurrentTimeFunction()
	{
		return "getdate()";
	}

	function CurrentDateFunction()
	{
		return "
			convert(
				datetime,
				cast(year(getdate()) as varchar(4)) + '-' +
				cast(month(getdate()) as varchar(2)) + '-' +
				cast(day(getdate()) as varchar(2)),
				120
				)
			";
	}

	function DateFormatToDB($format, $field)
	{
		$format = preg_replace("#([^(DD|MM|YYYY|HH|SS|MI)])#", "+'\\1'+", $format);
		$format = str_replace(Array("DD","MM", "YYYY", "HH", "SS", "MI"), Array("~DD","~MM", "~YYYY", "~HH", "~SS", "~MI"), $format);
		$format = str_replace("++", "+", $format);
		$format = str_replace("~YYYY", "\n\tCONVERT(varchar(4),DATEPART(yyyy, $field))", $format);

		$format = str_replace("~MM", "\n\tREPLICATE('0',2-LEN(DATEPART(mm, $field)))+CONVERT(varchar(2),DATEPART(mm, $field))", $format);
		$format = str_replace("~DD", "\n\tREPLICATE('0',2-LEN(DATEPART(dd, $field)))+CONVERT(varchar(2),DATEPART(dd, $field))", $format);
		$format = str_replace("~HH", "\n\tREPLICATE('0',2-LEN(DATEPART(hh, $field)))+CONVERT(varchar(2),DATEPART(hh, $field))", $format);
		$format = str_replace("~MI", "\n\tREPLICATE('0',2-LEN(DATEPART(mi, $field)))+CONVERT(varchar(2),DATEPART(mi, $field))", $format);
		$format = str_replace("~SS", "\n\tREPLICATE('0',2-LEN(DATEPART(ss, $field)))+CONVERT(varchar(2),DATEPART(ss, $field))", $format);
		$format = "case when isdate($field)=0 then null else $format \nend";
		return $format;
	}

	function DateToCharFunction($field, $format_type="FULL", $lang=false)
	{
		$format = CLang::GetDateFormat($format_type, $lang);
		return $this->DateFormatToDB($format, $field);
	}

	function CharToDateFunction($value, $format_type="FULL", $lang=false)
	{
		$value = trim($value);
		if (strlen($value)<=0) return "NULL";
		$value = CDatabase::FormatDate($value, CLang::GetDateFormat($format_type, $lang), "YYYY-MM-DD HH:MI:SS");
		return "convert (datetime, '".$this->ForSql($value)."', 120)";
	}

	//	1		если date1 > date2
	//	0		если date1 = date2
	//	-1		если date1 < date2
	function CompareDates($date1, $date2)
	{
		$s_date1 = $this->CharToDateFunction($date1);
		$s_date2 = $this->CharToDateFunction($date2);
		$strSql = "
			SELECT
				CASE
					when $s_date1 > $s_date2 then 1
					when $s_date1 = $s_date2 then 0
					when $s_date1 < $s_date2 then -1
					else 'x'
				END as RES
			";
		$z = $this->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		$zr = $z->Fetch();
		return $zr["RES"];
	}

	function LastID()
	{
		$rs = $this->Query("SELECT @@IDENTITY as ID", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		$ar = $rs->Fetch();
		return $ar["ID"];
	}

	//Отсоединяется от БД
	function Disconnect()
	{
		if (!DBPersistent && $this->bConnected) odbc_close($this->db_Conn);
	}

	function PrepareFields($table, $prefix = "str_", $suffix = "")
	{
		$arColumns = $this->GetTableFields($table);
		if (is_array($arColumns) && count($arColumns)>0)
		{
			foreach($arColumns as $arColumn)
			{
				$column = $arColumn["NAME"];
				$type = $arColumn["TYPE"];
				global $$column;
				$var = $prefix.$column.$suffix;
				global $$var;
				switch ($type)
				{
					case "int":
					case "tinyint":
					case "smallint":
					case "bigint":
						$$var = intval($$column);
						break;
					case "real":
					case "float":
						$$var = doubleval($$column);
						break;
					default:
						$$var = $this->ForSql($$column);
				}
			}
		}
	}


	function PrepareInsert($table, $arFields, $file_dir="", $lang=false)
	{
		$strInsert1 = "";
		$strInsert2 = "";

		$arColumns = $this->GetTableFields($table);

		if(is_array($arColumns) && count($arColumns)>0)
		{
			foreach($arColumns as $arColumn)
			{
				$column = $arColumn["NAME"];
				$type = $arColumn["TYPE"];
				$scale = intval($arColumn["NUMERIC_SCALE"]);
				$value = $arFields[$column];

				if (is_set($arFields, $column) && isset($value))
				{
					if (is_array($value))
					{
						if (strlen($value["name"])>0 || strlen($value["del"])>0 || strlen($value["description"])>0)
						{
							$res = CFile::SaveFile($value, $file_dir);
							if ($res!==false && strlen($file_dir)>0)
							{
								$strInsert1 .= ", ".$column;
								$strInsert2 .= ",  ".$res;
							}
						}
					}
					elseif($value === false)
					{
						$strInsert1 .= ", ".$column;
						$strInsert2 .= ",  "."NULL ";
					}
					else
					{
						$strInsert1 .= ", ".$column;
						if($type == "datetime" || $type == "timestamp")
						{
							$s = (strlen(trim($value))<=0) ? ", NULL " : ", ".$this->CharToDateFunction($value);
							$strInsert2 .= $s;
						}
						else
						{
							switch ($type)
							{
								case "int":
								case "tinyint":
								case "smallint":
								case "bigint":
									$strInsert2 .= ", '".intval($value)."'";
									break;
								case "decimal":
								case "numeric":
									$strInsert2 .= ", '".round(doubleval($value), $scale)."'";
									break;
								case "real":
								case "float":
									$strInsert2 .= ", '".doubleval($value)."'";
									break;
								case "image":
									$strInsert2 .= ", ".$value;
									break;
								default:
									$strInsert2 .= ", '".$this->ForSql($value, $arColumn['CHARACTER_MAXIMUM_LENGTH'])."'";
							}
						}
					}
				}
				elseif(is_set($arFields, "~".$column))
				{
					$strInsert1 .= ", ".$column;
					$strInsert2 .= ", ".$arFields["~".$column];
				}
			}
		}

		if($strInsert1!="")
		{
			$strInsert1 = substr($strInsert1, 2);
			$strInsert2 = substr($strInsert2, 2);
		}
		return array($strInsert1, $strInsert2);
	}

	function PrepareUpdate($strTableName, $arFields, $strFileDir="", $lang = false)
	{
		return $this->PrepareUpdateBind($strTableName, $arFields, $strFileDir, $lang, $arBinds);
	}

	function PrepareUpdateBind($strTableName, $arFields, $strFileDir, $lang, &$arBinds)
	{
		$arBinds = array();
		$strUpdate = "";
		$arColumns = $this->GetTableFields($strTableName);
		if (is_array($arColumns) && count($arColumns)>0)
		{
			foreach($arColumns as $arColumn)
			{
				$column = $arColumn["NAME"];
				$type = $arColumn["TYPE"];
				$scale = intval($arColumn["NUMERIC_SCALE"]);
				$value = $arFields[$column];

				if (is_set($arFields, $column) && isset($value))
				{
					if(is_array($value))
					{
						if(strlen($value["name"])>0 || strlen($value["del"])>0 || is_set($value, "description"))
						{
							$res = CFile::SaveFile($value, $strFileDir);
							if($res!==false && strlen($strFileDir)>0)
								$strUpdate .= ", ".$column." = ".$res;
						}
					}
					elseif($value === false)
					{
						$strUpdate .= ", ".$column." = NULL";
					}
					else
					{
						switch ($type)
						{
							case "int":
							case "tinyint":
							case "smallint":
							case "bigint":
								$value = intval($value);
								break;
							case "decimal":
							case "numeric":
								$strInsert2 .= ", '".round(doubleval($value), $scale)."'";
								break;
							case "real":
								$value = doubleval($value);
								break;
							case "datetime":
							case "timestamp":
								$value = (strlen(trim($value))<=0) ? "NULL" : $this->CharToDateFunction($value);
								break;
							case "image":
								$value = $value;
								break;
							default:
								$value = "'".$this->ForSql($value, $arColumn['CHARACTER_MAXIMUM_LENGTH'])."'";
						}
						$strUpdate .= ", ".$column." = ".$value;
					}
				}
				elseif(is_set($arFields, "~".$column))
				{
					$strUpdate .= ", ".$column." = ".$arFields["~".$column];
				}
			}
			if($strUpdate!="")
				$strUpdate = substr($strUpdate, 2);
		}
		return $strUpdate;
	}

	function Insert($table, $arFields, $error_position="", $DEBUG=false, $EXIST_ID="", $ignore_errors=false)
	{
		if(is_array($arFields))
		{
			while (list($field,$value)=each($arFields))
			{
				$str1 .= $field.", ";
				$str2 .= (strlen($value)<=0) ? "'".$value."', "  : $value.", ";
			}
			$str1 = TrimEx($str1,",");
			$str2 = TrimEx($str2,",");

			if (strlen($EXIST_ID)>0)
			{
				$this->Query("SET IDENTITY_INSERT ".$table." ON", $ignore_errors, $error_position);
				$strSql = "INSERT INTO ".$table."(ID,".$str1.") VALUES ('".$this->ForSql($EXIST_ID)."',".$str2.")";
			}
			else
			{
				$strSql = "INSERT INTO ".$table."(".$str1.") VALUES (".$str2.")";
			}

			if ($DEBUG) echo "<br>".$strSql."<br>";
			$this->Query($strSql, $ignore_errors, $error_position);

			if (strlen($EXIST_ID)>0)
			{
				$this->Query("SET IDENTITY_INSERT ".$table." OFF", $ignore_errors, $error_position);
				$ID = $EXIST_ID;
			}
			else
			{
				$ID = $this->LastID();
			}

			return $ID;
		}
		else return false;
	}

	function Update($table, $arFields, $WHERE="", $error_position="", $DEBUG=false, $ignore_errors=false)
	{
		$rows = 0;
		if(is_array($arFields))
		{
			while (list($field,$value)=each($arFields))
			{
				$str .= (strlen($value)<=0) ? $field." = '', " : $field." = ".$value.", ";
			}
			$str = TrimEx($str,",");
			$strSql = "UPDATE ".$table." SET ".$str." ".$WHERE;
			if ($DEBUG) echo "<br>".$strSql."<br>";
			$w = $this->Query($strSql, $ignore_errors, $error_position);
			$rows = $w->AffectedRowsCount();
			if ($DEBUG) echo "affected_rows = ".$rows."<br>";
		}
		return $rows;
	}

	function Add($tablename, $arFields, $arCLOBFields = Array(), $strFileDir="")
	{
		global $DB;
		$arInsert = $DB->PrepareInsert($tablename, $arFields, $strFileDir, false, $debug);
		$strSql = "INSERT INTO ".$tablename."(".$arInsert[0].") VALUES(".$arInsert[1].")";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $DB->LastID();
	}

	function ForSql($value, $max=0)
	{
		return str_replace("\x00", "", ($max>0) ? str_replace("'","''", substr($value, 0, $max)) : str_replace("'","''", $value));
	}

	function ForSqlLike($value, $max=0)
	{
		if ($max>0)
			$value = substr($value, 0, $max);

		return str_replace("\x00", "", str_replace("'", "\'", str_replace("\\", "\\\\\\\\", $value)));
	}

	function InitTableVarsForEdit($table, $prefix_from = "str_", $prefix_to="str_", $suffix_from="", $safe_for_html=false)
	{
		$arColumns = $this->GetTableFields($table);
		if (is_array($arColumns) && count($arColumns)>0)
		{
			foreach($arColumns as $arColumn)
			{
				$column = $arColumn["NAME"];
				$var_from = $prefix_from.$column.$suffix_from;
				$var_to = $prefix_to.$column;
				global $$var_from, $$var_to;
				if ((isset($$var_from) || $safe_for_html))
				{
					if (is_array($$var_from))
					{
						foreach($$var_from as $k=>$v)
							$$var_from[$k] = htmlspecialchars($v);
					}
					else
						$$var_to = htmlspecialchars($$var_from);
				}
			}
		}
	}

	function GetTableFields($table)
	{
		if (!is_set($this->column_cache, $table))
		{
			$strSql = "
				SELECT
					*,
					COLUMN_NAME as NAME,
					DATA_TYPE as TYPE
				FROM
					INFORMATION_SCHEMA.COLUMNS
				WHERE
					TABLE_NAME = '$table'
				";
			$rs = $this->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			while ($ar = $rs->Fetch()) $this->column_cache[$table][] = $ar;
		}
		return $this->column_cache[$table];
	}

	function &GetTableFieldsList($table)
	{
		$arResult = array();
		$arColumns = $this->GetTableFields($table);
		if (is_array($arColumns) && count($arColumns)>0)
		{
			foreach($arColumns as $arColumn) $arResult[] = $arColumn["NAME"];
		}
		return $arResult;
	}

	function Concat()
	{
		$str = "";
		$ar = func_get_args();
		if (is_array($ar)) $str .= implode(" + ", $ar);
		return $str;
	}

	function IsNull($expression, $result)
	{
		return "ISNULL(".$expression.", ".$result.")";
	}

	function Length($field)
	{
		return "len($field)";
	}

	function TableExists($tableName)
	{
		$tableName = preg_replace("/[^A-Za-z0-9%_]+/i", "", $tableName);
		$tableName = Trim($tableName);

		if (strlen($tableName) <= 0)
			return False;

		$dbResult = $this->Query(
				"SELECT TABLE_NAME ".
				"FROM INFORMATION_SCHEMA.TABLES ".
				"WHERE TABLE_NAME LIKE '".$this->ForSql($tableName)."'"
			);
		if ($arResult = $dbResult->Fetch())
			return True;
		else
			return False;
	}
}

class CDBResult extends CAllDBResult
{
	var $num_rows_affected=-1;

	function __fetch_array($rownumber=false, $result=false)
	{
		if ($result===false) $result = $this->result;

		if ($rownumber!==false)
		{
			if(!odbc_fetch_row($result, $rownumber))
				return false;
		}
		else
		{
			if(!odbc_fetch_row($result))
				return false;
		}
		static $counter=0;
		$row=array();
		$numfields=odbc_num_fields($result);
		for ($i=1; $i<=$numfields; ++$i)
		{
			$row[odbc_field_name($result, $i)] = odbc_result($result, $i);
		}
		return $row;
	}

	//После запроса делает выборку значений полей в массив
	function Fetch()
	{
		if($this->bNavStart || $this->bFromArray)
		{
			if (!is_array($this->arResult)) return false;
			if ($tmp=current($this->arResult)) next($this->arResult);
			return $tmp;
		}
		else
		{
			return $this->__fetch_array();
		}
	}

	function SelectedRowsCount()
	{
		if($this->nSelectedCount !== false)
			return $this->nSelectedCount;

		if($this->NavRecordCount !== false)
			return $this->NavRecordCount;

		return odbc_num_rows($this->result);
	}

	function AffectedRowsCount($DEBUG=false)
	{
		return $this->num_rows_affected;//intval(odbc_num_rows($this->result));
	}

	function AffectedRowsCountEx()
	{
		if (intval($this->SelectedRowsCount())>0)
			return 0;
		else
			return $this->AffectedRowsCount();
	}

	function FieldsCount()
	{
		return odbc_num_fields($this->result);
	}

	function FieldName($iCol)
	{
		return odbc_field_name($this->result, $iCol+1);
	}

	function DBNavStart()
	{
		if($this->bFetched===true)
			return;
		$this->bFetched = true;
		$this->NavPageNomer = ($this->PAGEN < 1?($_SESSION[$this->SESS_PAGEN] < 1?1:$_SESSION[$this->SESS_PAGEN]):$this->PAGEN);

		if($this->NavShowAll)
		{
			$NavFirstRecordShow = 0;
			$NavLastRecordShow = 100000;
		}
		else
		{
			$NavFirstRecordShow = $this->NavPageSize*($this->NavPageNomer-1);
			$NavLastRecordShow = $this->NavPageSize*$this->NavPageNomer;
		}

		$temp_arrray=array();
		$num_rows=0;
		$rsEnd=false;

		$cache_arrray=array();

		while($num_rows<$NavFirstRecordShow && !$rsEnd)
		{
			if($db_result_array = $this->__fetch_array())
			{
				$num_rows++;

				if(count($cache_arrray)==$NavPageSize)
					$cache_arrray=array();
				$cache_arrray[]=$db_result_array;
			}
			else
				$rsEnd=true;
		}

		if($rsEnd && count($cache_arrray)>0)
		{
			$this->NavPageNomer = floor($num_rows / $this->NavPageSize);
			if($num_rows % $this->NavPageSize > 0)
				$this->NavPageNomer++;

			$temp_arrray=$cache_arrray;
		}

		$bFirst=true;
		while($num_rows<$NavLastRecordShow && !$rsEnd)
		{
			if($db_result_array = $this->__fetch_array())
			{
				$num_rows++;
				$temp_arrray[]=$db_result_array;
			}
			else
			{
				$rsEnd=true;
				if($bFirst && count($cache_arrray)>0)
				{
					$this->NavPageNomer = floor($num_rows / $this->NavPageSize);
					if($num_rows % $this->NavPageSize > 0)
						$this->NavPageNomer++;

					$temp_arrray=$cache_arrray;
				}
			}
			$bFirst=false;
		}

		if(!$rsEnd)
			while($this->__fetch_array())
			{
				$num_rows++;
			}

		$this->arResult=$temp_arrray;

		$this->NavRecordCount = $num_rows;
		if($this->NavShowAll)
		{
			$this->NavPageSize = $this->NavRecordCount;
			$this->NavPageNomer = 1;
		}

		if($this->NavPageSize > 0)
			$this->NavPageCount = floor($this->NavRecordCount / $this->NavPageSize);
		else
			$this->NavPageCount = 0;

		if($this->NavRecordCount % $this->NavPageSize > 0)
			$this->NavPageCount++;
	}

	function NavQuery($strSql, $cnt, $arNavStartParams)
	{
		if(is_set($arNavStartParams, "SubstitutionFunction"))
		{
			$arNavStartParams["SubstitutionFunction"]($this, $strSql, $cnt, $arNavStartParams);
			return;
		}
		if(is_set($arNavStartParams, "bShowAll"))
			$bShowAll = $arNavStartParams["bShowAll"];
		else
			$bShowAll = true;

		if(is_set($arNavStartParams, "iNumPage"))
			$iNumPage = $arNavStartParams["iNumPage"];
		else
			$iNumPage = false;

		if(is_set($arNavStartParams, "bDescPageNumbering"))
			$bDescPageNumbering = $arNavStartParams["bDescPageNumbering"];
		else
			$bDescPageNumbering = false;

		$this->InitNavStartVars($arNavStartParams);
		$this->NavRecordCount = $cnt;

		if($this->NavShowAll)
			$this->NavPageSize = $this->NavRecordCount;

		//Определяем число страниц при указанном размере страниц. Счет начиная с 1
		$this->NavPageCount = ($this->NavPageSize>0 ? floor($this->NavRecordCount/$this->NavPageSize) : 0);
		if($bDescPageNumbering)
		{
			$makeweight = ($this->NavRecordCount % $this->NavPageSize);
			if($this->NavPageCount == 0 && $makeweight > 0)
				$this->NavPageCount = 1;

			//Номер страницы для отображения.
			//if($iNumPage===false)
			//	$this->PAGEN = $this->NavPageCount;
			$this->NavPageNomer =
				(
					$this->PAGEN < 1 || $this->PAGEN > $this->NavPageCount
					?
						($_SESSION[$this->SESS_PAGEN] < 1 || $_SESSION[$this->SESS_PAGEN] > $this->NavPageCount
						?
							$this->NavPageCount
						:
							$_SESSION[$this->SESS_PAGEN]
						)
					:
						$this->PAGEN
				);

			//Смещение от начала RecordSet
			$NavFirstRecordShow = 0;
			if($this->NavPageNomer != $this->NavPageCount)
				$NavFirstRecordShow += $makeweight;

			$NavFirstRecordShow += ($this->NavPageCount - $this->NavPageNomer) * $this->NavPageSize;
			$NavLastRecordShow = $makeweight + ($this->NavPageCount - $this->NavPageNomer + 1) * $this->NavPageSize;
		}
		else
		{
			if($this->NavRecordCount % $this->NavPageSize > 0)
				$this->NavPageCount++;

			//Номер страницы для отображения. Отсчет начинается с 1
			$this->NavPageNomer = ($this->PAGEN < 1 || $this->PAGEN > $this->NavPageCount? ($_SESSION[$this->SESS_PAGEN] < 1 || $_SESSION[$this->SESS_PAGEN] > $this->NavPageCount? 1:$_SESSION[$this->SESS_PAGEN]):$this->PAGEN);

			//Смещение от начала RecordSet
			$NavFirstRecordShow = $this->NavPageSize*($this->NavPageNomer-1);
			$NavLastRecordShow = $this->NavPageSize*$this->NavPageNomer;
		}

		global $DB;
		$res_tmp = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		if(!$this->NavShowAll)
		{
			for($i=$NavFirstRecordShow+1; $i<$NavLastRecordShow+1; $i++)
			{
				$temp_array[] = $res_tmp->__fetch_array($i);
			}
		}
		else
		{
			while($ar = $res_tmp->__fetch_array())
				$temp_array[] = $ar;
		}

		$this->arResult = $temp_array;
		$this->nSelectedCount = $cnt;
		$this->bDescPageNumbering = $bDescPageNumbering;
		$this->bFromLimited=true;
	}
}
?>
