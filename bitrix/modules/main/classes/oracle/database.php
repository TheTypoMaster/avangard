<?
require_once(substr(__FILE__, 0, strlen(__FILE__) - strlen("/classes/oracle/database.php"))."/bx_root.php");

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/database.php");

/********************************************************************
*	Классы для работы с Oracle
********************************************************************/
class CDatabase extends CAllDatabase
{
	var $DBName;
	var $DBHost;
	var $DBLogin;
	var $DBPassword;
	var $bConnected;
	var $transaction = OCI_COMMIT_ON_SUCCESS;
	var $column_cache = Array();
	var $version;
	var $cntQuery;
	var $timeQuery;

	function GetVersion()
	{
		if($this->version)
			return $this->version;

		$rs = $this->Query('SELECT BANNER as R FROM v$version', false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		if($ar = $rs->Fetch())
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

	//начинает транзакцию
	function StartTransaction()
	{
		$this->transaction=OCI_DEFAULT;
	}

	//Делает COMMIT
	function Commit()
	{
		$this->DoConnect();
		OCICommit($this->db_Conn);
		$this->transaction = OCI_COMMIT_ON_SUCCESS;
	}

	//Делает ROLLBACK
	function Rollback()
	{
		$this->DoConnect();
		OCIRollback($this->db_Conn);
		$this->transaction = OCI_COMMIT_ON_SUCCESS;
	}

	//возвращает очередное значение сиквенса $sequence
	function NextID($sequence)
	{
		if(!empty($sequence))
		{
			$strGetNewID = "SELECT ".$sequence.".NEXTVAL FROM DUAL";
			$db_newid_set = $this->Query($strGetNewID) or die("Query Error! (NextID)");
			$db_newid = $db_newid_set->Fetch();
			return $db_newid["NEXTVAL"];
		}
		else
			return false;
	}


	////////////////////////////////////////////////////////////////////////
	//Переопределенные функции  ////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////
	//Соединяется с базой данных
	function Connect($DBHost, $DBName, $DBLogin, $DBPassword)
	{
		$this->type="ORACLE";
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
			return true;
		$this->bConnected = true;

		if (!defined(DBPersistent)) define("DBPersistent",false);

		if (DBPersistent)
			$this->db_Conn = @OCIPLogon($this->DBLogin, $this->DBPassword, $this->DBName);
		else
			$this->db_Conn = @OCILogon($this->DBLogin, $this->DBPassword, $this->DBName);

		if(!$this->db_Conn)
		{
			$arError = OCIError();
			if (DBPersistent) $s = "OCIPLogon"; else $s = "OCILogon";
			$s .= " Error:".$arError["message"];
			if($this->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
				echo "<br><font color=#ff0000>".$s."('-', '-', '-')</font><br>";
			else
				SendError("Error! ".$s."('-', '-', '-')\n\n");

			return false;
		}

		$this->cntQuery = 0;
		$this->timeQuery = 0;
		$this->arQueryDebug = array();
		global $DB, $USER, $APPLICATION;
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/after_connect.php"))
			include($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/after_connect.php");

		return true;
	}

	function CurrentTimeFunction()
	{
		return "SYSDATE";
	}

	function CurrentDateFunction()
	{
		return "TRUNC(SYSDATE)";
	}

	function DateToCharFunction($strFieldName, $strType="FULL", $lang=false, $bSearchInSitesOnly=false)
	{
		static $CACHE=array();
		$id = $strType.",".$lang.",".$bSearchInSitesOnly;
		if(!array_key_exists($id,$CACHE))
			$CACHE[$id] = str_replace("HH", "HH24", CLang::GetDateFormat($strType, $lang, $bSearchInSitesOnly));
		return "TO_CHAR(".$strFieldName.", '".$CACHE[$id]."')";
	}

	function CharToDateFunction($strValue, $strType="FULL", $lang=false)
	{
		return "TO_DATE('".$this->FormatDate($strValue, CLang::GetDateFormat($strType, $lang), "D.M.Y H:I:S")."', 'DD.MM.YYYY HH24:MI:SS')";
	}

	//	1		если date1 > date2
	//	0		если date1 = date2
	//	-1		если date1 < date2
	function CompareDates($date1, $date2)
	{
		$s_date1 = $this->CharToDateFunction($date1);
		$s_date2 = $this->CharToDateFunction($date2);
		$strSql = "SELECT sign($s_date1 - $s_date2) as RES FROM dual";
		$z = $this->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		$zr = $z->Fetch();
		return $zr["RES"];
	}

	//Делает запрос к базе данных
	function Query($strSql, $bIgnoreErrors=false, $error_position="")
	{
		$this->DoConnect();
		global $prev_Query, $DOCUMENT_ROOT, $PHPSESSID;
		$prev_Query[]=$strSql;
		$this->db_Error="";

		if($this->DebugToFile || $this->ShowSqlStat)
		{
			list($usec, $sec) = explode(" ", microtime());
			$start_time = ((float)$usec + (float)$sec);
		}

		$result = @OCIParse($this->db_Conn, $strSql);

		if(!$result)
		{
			$error=OCIError($this->db_Conn);
			$this->db_Error=$error["message"];
			if(!$bIgnoreErrors)
			{
				if ($this->DebugToFile)
				{
					$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/oracle_debug.sql","ab+");
					fputs($fp,"SESSION: ".$PHPSESSID." ERROR: ".$this->db_Error."\n\n----------------------------------------------------\n\n");
					@fclose($fp);
				}
				if($this->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
				{
					echo $error_position."<br><font color=#ff0000>Oracle Query Error: ".htmlspecialchars($strSql)."</font>[".$error["message"]."]<br>";
				}
				else
				{
					$error_position = eregi_replace("<br>","\n",$error_position);
					SendError($error_position."\nOracle Query Error:\n".$strSql." \n [".$error["message"]."]\n---------------\n\n");
				}
				AddMessage2Log($error_position." Oracle Query Error: ".$strSql." [".$error["message"]."]", "main");
				die("Oracle Query Error");
			}
			return false;
		}

		if(!@OCIExecute($result, $this->transaction))
		{
			$error=OCIError($result);
			$this->db_Error=$error["message"];
			if(!$bIgnoreErrors)
			{
				if($this->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
				{
					echo $error_position."<br><font color=#ff0000>Oracle Query Error: ".htmlspecialchars($strSql)."</font>[".$error["message"]."]<br>";
				}
				else
				{
					$error_position = eregi_replace("<br>","\n",$error_position);
					SendError($error_position."\nOracle Query Error:\n".$strSql." \n [".$error["message"]."]\n---------------\n\n");
				}
				AddMessage2Log($error_position." Oracle Query Error: ".$strSql." [".$error["message"]."]", "main");
				die("Oracle Query Error!");
			}
			return false;
		}

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
				$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/oracle_debug.sql","ab+");
				$str = "TIME: ".$exec_time." SESSION: ".session_id()." \n";
				$str .= $strSql."\n\n";
				$str .= "----------------------------------------------------\n\n";
				fputs($fp, $str);
				@fclose($fp);
			}
		}

		return new CDBResult($result);
	}


	function QueryBindSelect($strSql, $arBinds, $bIgnoreErrors=false, $error_position="")
	{
		$this->DoConnect();
		global $prev_Query;
		$prev_Query[]=$strSql;
		$this->db_Error="";
		if ($this->DebugToFile || $this->ShowSqlStat)
		{
			list($usec, $sec) = explode(" ",microtime());
			$start_time = ((float)$usec + (float)$sec);
		}

		$result = @OCIParse($this->db_Conn, $strSql);

		if(!$result)
		{
			$error=OCIError($this->db_Conn);
			$this->db_Error=$error["message"];
			if(!$bIgnoreErrors)
			{
				if($this->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
					echo "<br><font color=#ff0000>".$error_position."\n"."Parse Error: ".htmlspecialchars($strSql)."</font>[".$error["message"]."]<br>";
				else
					SendError("Parse Error:\n".$error_position."\n".$strSql." \n [".$error["message"]."]\n---------------\n\n");

				AddMessage2Log("Parse Error: ".$error_position."\n".$strSql." [".$error["message"]."]", "main");
				die("Query Error!");
			}
			return false;
		}

		foreach($arBinds as $key=>$value)
			OCIBindByName($result, ":".$key, $arBinds[$key], -1);

		$this->cntQuery++;
		if(!@OCIExecute($result, OCI_DEFAULT))
		{
			$error=OCIError($result);
			$this->db_Error=$error["message"];
			if(!$bIgnoreErrors)
			{
				if($this->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
					echo "<br><font color=#ff0000>".$error_position."\n"."Query Error: ".htmlspecialchars($strSql)."</font>[".$error["message"]."]<br>";
				else
					SendError("Query Error:\n".$error_position."\n".$strSql." \n [".$error["message"]."]\n---------------\n\n");
				AddMessage2Log("Query Error: ".$error_position."\n".$strSql." [".$error["message"]."]", "main");
				die("Query Error!");
			}
			return false;
		}

		for($i=0; $i<count($good_keys); $i++)
		{
			$CLOB[$i]->save($arBinds[$good_keys[$i]]);
		}

		if($this->transaction == OCI_COMMIT_ON_SUCCESS)
			OCICommit($this->db_Conn);

		if ($this->DebugToFile || $this->ShowSqlStat)
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
				$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/oracle_debug.sql","ab+");
				fputs($fp,"TIME: ".$exec_time." SESSION: ".session_id()." \n".$strSql."\n\n----------------------------------------------------\n\n");
				@fclose($fp);
			}
		}

		return new CDBResult($result);
	}

	//Делает запрос к базе данных. Длинные данные в insert'ах биндит
	function QueryLong($strSql, $bIgnoreErrors = false)
	{
//		return $this->Query($strSql, $bIgnoreErrors);

		$strSql = trim($strSql);
		if (strlen($strSql)<=0) return;

		if (preg_match("/^\s*(insert\s+.+?)\((.+?)\)\s*values\s*\((.+)\)\s*;*\s*$/is", $strSql, $matches))
		{
			// Значит мы дергаем insert
			$iMaxStrLen = 1995;
			$tables = $matches[1];
			$fields = $matches[2];
			$values = $matches[3];

			$arFields = Split(",", $fields);
			for ($j=0; $j<count($arFields); $j++) $arFields[$j] = trim($arFields[$j]);

			$arValues = array();
			$iSqlLen = strlen($values);
			$bString = False;
			$bFunc = False;
			$ch = "";
			$string_start = "";

			for ($i=0; $i<$iSqlLen; $i++)
			{
				$ch = $values[$i];

				if ($bString)
				{
					while (true)
					{
						$i = strpos($values, $string_start, $i);

						if (!$i)
						{
							$arValues[] = $values;
							break 2;
						}
						elseif ($string_start == '`' || $values[$i-1] != '\\')
						{
							$string_start = '';
							$bString = False;
							break;
						}
						else
						{
							$j = 2;
							$escaped_backslash = False;
							while ($i-$j > 0 && $values[$i-$j] == '\\')
							{
								$escaped_backslash = !$escaped_backslash;
								$j++;
							}

							if ($escaped_backslash)
							{
								$string_start = '';
								$bString = False;
								break;
							}
							else
								$i++;
						}
					}	// end while
				} // end if (in string)
				elseif ($bFunc)
				{
					$i = strpos($values, ")", $i);

					if (!$i)
					{
						$arValues[] = $values;
						break;
					}
					else
					{
						$string_start = '';
						$bFunc = False;
					}
				} // end if (in string)
				elseif ($ch == ",") // We are not in a string, first check for delimiter...
				{
					$arValues[] = substr($values, 0, $i);

					$values = ltrim(substr($values, min($i + 1, $iSqlLen)));
					$iSqlLen = strlen($values);
					if ($iSqlLen)
						$i = -1;
					else
						break;
				}
				elseif (($ch == '"') || ($ch == '\'') || ($ch == '`')) // ... then check for start of a string,...
				{
					$bString = True;
					$string_start = $ch;
				}
				elseif ($ch == '(')
				{
					$bFunc = True;
				}
				else
				{
				}
			}

			if (strlen(trim($values))>0)
			{
				$arValues[] = $values;
			}

			if (count($arValues)!=count($arFields))
			{
				$this->db_Error = "Incorrect insert query (g5j27) ";
				return;
			}
			else
			{
				$newStrSql = $tables." (";
				for ($i=0; $i<count($arFields); $i++)
				{
					if ($i>0) $newStrSql .= ",";
					$newStrSql .= $arFields[$i];
				}
				$newStrSql .= ") VALUES (";
				$arBind = array();
				for ($i=0; $i<count($arValues); $i++)
				{
					if ($i>0) $newStrSql .= ",";
					if (strlen($arValues[$i])>$iMaxStrLen)
					{
						$newStrSql .= "EMPTY_CLOB()";
						$arValues[$i] = trim($arValues[$i]);
						$arValues[$i] = substr($arValues[$i], 1, strlen($arValues[$i])-2);
						$arBind[$arFields[$i]] = str_replace("\\\\","\\",str_replace("''","'",$arValues[$i]));
					}
					else
					{
						$newStrSql .= $arValues[$i];
					}
				}
				$newStrSql .= ")";

//echo "\$this->QueryBind($newStrSql, $arBind, $bIgnoreErrors);<br>";
				$rResult = $this->QueryBind($newStrSql, $arBind, $bIgnoreErrors);
			}
		}
		else
		{
			$rResult = $this->Query($strSql, $bIgnoreErrors);
		}
		return $rResult;
	}

	//Делает запрос к базе данных
	function QueryBind($strSql, $arBinds, $bIgnoreErrors=false, $error_position="")
	{
		$this->DoConnect();
		global $prev_Query;
		$prev_Query[]=$strSql;
		$this->db_Error="";
		if ($this->DebugToFile || $this->ShowSqlStat)
		{
			list($usec, $sec) = explode(" ",microtime());
			$start_time = ((float)$usec + (float)$sec);
		}

		$strBinds1="";$strBinds2="";

		$keys = array_keys($arBinds);
		$good_keys = Array();

		for($i=0; $i<count($keys); $i++)
		{
			if(strlen($arBinds[$keys[$i]])>0)
			{
				if($strBinds1=="")
				{
					$strBinds1 = " RETURNING ";
					$strBinds2 = " INTO ";
				}
				else
				{
					$strBinds1 .= ",";
					$strBinds2 .= ",";
				}

				$good_keys[] = $keys[$i];
				$strBinds1 .= $keys[$i];
				$strBinds2 .= ":".$keys[$i];
			}
		}

		$strSql .= $strBinds1.$strBinds2;
		$result = @OCIParse($this->db_Conn, $strSql);
		if(!$result)
		{
			$error=OCIError($this->db_Conn);
			$this->db_Error=$error["message"];
			if(!$bIgnoreErrors)
			{
				if($this->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
					echo "<br><font color=#ff0000>Parse Error: ".htmlspecialchars($strSql)."</font>[".$error["message"]."]<br>";
				else
					SendError("Parse Error:\n".$strSql." \n [".$error["message"]."]\n---------------\n\n");
				AddMessage2Log("Parse Error: ".$strSql." [".$error["message"]."]", "main");
				die("Query Error!");
			}
			return false;
		}

		for($i=0; $i<count($good_keys); $i++)
		{
			$CLOB[$i] = OCINewDescriptor($this->db_Conn, OCI_D_LOB);
			OCIBindByName($result, ":".$good_keys[$i], $CLOB[$i], -1, OCI_B_CLOB);
		}

		$this->cntQuery++;
		if(!@OCIExecute($result, OCI_DEFAULT))
		{
			$error=OCIError($result);
			$this->db_Error=$error["message"];
			if(!$bIgnoreErrors)
			{
				if($this->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
					echo "<br><font color=#ff0000>Query Error: ".htmlspecialchars($strSql)."</font>[".$error["message"]."]<br>";
				else
					SendError("Query Error:\n".$strSql." \n [".$error["message"]."]\n---------------\n\n");
				AddMessage2Log("Query Error: ".$strSql." [".$error["message"]."]", "main");
				die("Query Error!");
			}
			return false;
		}

		for($i=0; $i<count($good_keys); $i++)
		{
			$CLOB[$i]->save($arBinds[$good_keys[$i]]);
		}

		if($this->transaction == OCI_COMMIT_ON_SUCCESS)
			OCICommit($this->db_Conn);

		if ($this->DebugToFile || $this->ShowSqlStat)
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
				$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/oracle_debug.sql","ab+");
				fputs($fp,"TIME: ".$exec_time." SESSION: ".session_id()." \n".$strSql."\n\n----------------------------------------------------\n\n");
				@fclose($fp);
			}
		}

		return new CDBResult($result);
	}

	//Отсоединяется от БД
	function Disconnect()
	{
		if (!DBPersistent && $this->bConnected) @OCILogOff($this->db_Conn);
	}


	function FormatValue($value, $arColumnInfo)
	{
		//Необходимо сделать дополнительные проверки для всех типов
		switch($arColumnInfo["DATA_TYPE"])
		{
			case "NUMBER":
				if(IntVal($arColumnInfo["DATA_SCALE"])<=0)
					$value = IntVal($value);
				else
					$value = Round(DoubleVal($value), $arColumnInfo["DATA_SCALE"]);

				if($arColumnInfo["DATA_PRECISION"]>0 && strlen(IntVal($value)) > IntVal($arColumnInfo["DATA_PRECISION"])-IntVal($arColumnInfo["DATA_SCALE"]))
					$value = IntVal(str_repeat('9', $arColumnInfo["DATA_PRECISION"] - $arColumnInfo["DATA_SCALE"]));

				return $value;

			case "VARCHAR2": case "CHAR":
				return str_replace("'","''",substr($value, 0, $arColumnInfo["DATA_LENGTH"]));

			default:
				return str_replace("'", "''", $value);
		}
		return $value;
	}

	function ForSql($strValue, $iMaxLength=0)
	{
		if($iMaxLength<=0 || $iMaxLength>2000)
			$iMaxLength=2000;
		if($iMaxLength>0)
			return str_replace("'","''", substr($strValue, 0, $iMaxLength));

		return str_replace("'","''", $strValue);
	}

	function ForSqlLike($strValue, $iMaxLength=0)
	{
		if($iMaxLength>0)
			$strValue = substr($strValue, 0, $iMaxLength);

		return str_replace("'", "''", str_replace("\\", "\\\\\\\\", $strValue));
	}

	function PrepareFields($strTableName, $strIdent = "str_", $strSuffix = "", $strPrefix="")
	{
		if(is_set($this->column_cache, $strTableName))
			$arColumns = $this->column_cache[$strTableName];
		else
		{
			$arColumns = Array();

			$strSql =
					"SELECT COLUMN_NAME, DATA_TYPE, DATA_PRECISION, DATA_SCALE, DATA_LENGTH ".
					"FROM USER_TAB_COLUMNS ".
					"WHERE UPPER(TABLE_NAME) = UPPER('".$strTableName."') ";

			$db_columns = $this->Query($strSql);
			while($arColumnInfo = $db_columns->Fetch())
				$arColumns[] = $arColumnInfo;

			$this->column_cache[$strTableName] = $arColumns;
		}

		for($i=0; $i<count($arColumns); $i++)
		{
			$cr = $arColumns[$i];
			$strFieldName = $strPrefix.$cr["COLUMN_NAME"].$strSuffix;
			global $$strFieldName;
			$strVarName = $strIdent.$cr["COLUMN_NAME"];
			global $$strVarName, $str_AMOUNT;
			initvar($strFieldName);
			switch($cr["DATA_TYPE"])
			{
				case "NUMBER":
					if(IntVal($cr["DATA_SCALE"])<=0)
						$$strVarName = IntVal($$strFieldName);
					else
						$$strVarName = roundEx(DoubleVal($$strFieldName), $cr["DATA_SCALE"]);

					if($cr["DATA_PRECISION"]>0 && strlen(IntVal($$strFieldName))>IntVal($cr["DATA_PRECISION"])-IntVal($cr["DATA_SCALE"]))
						$$strVarName=IntVal(str_repeat('9',$cr["DATA_PRECISION"]-$cr["DATA_SCALE"]));
					break;
				case "VARCHAR2": case "CHAR":
					$tmp=$$strFieldName;
					$$strVarName=$this->ForSql($tmp, $cr["DATA_LENGTH"]);
					break;
				default:
					$$strVarName=$this->ForSql($$strFieldName);
					break;
			}
		}
	}

	function PrepareInsert($strTableName, $arFields, $strFileDir="", $lang=false)
	{
		$strInsert1 = "";
		$strInsert2 = "";

		if(is_set($this->column_cache, $strTableName))
			$arColumns = $this->column_cache[$strTableName];
		else
		{
			$arColumns = Array();

			$strSql =
					"SELECT COLUMN_NAME, DATA_TYPE, DATA_PRECISION, DATA_SCALE, DATA_LENGTH ".
					"FROM USER_TAB_COLUMNS ".
					"WHERE UPPER(TABLE_NAME) = UPPER('".$strTableName."') ";

			$db_columns = $this->Query($strSql);
			while($arColumnInfo = $db_columns->Fetch())
				$arColumns[] = $arColumnInfo;

			$this->column_cache[$strTableName] = $arColumns;
		}

		for($i=0; $i<count($arColumns); $i++)
		{
			$arColumnInfo = $arColumns[$i];
			$strColumnName = $arColumnInfo["COLUMN_NAME"];
			$value = $arFields[$strColumnName];
			if(isset($value))
			{
				//массив может быть при сохранении файлов, тогда мы пропускаем - файлы требуют индивидуальной обработки
				if(is_array($value))
				{
					if(strlen($value["name"])>0 || strlen($value["del"])>0 || strlen($value["description"])>0)
					{
						$res = CFile::SaveFile($value, $strFileDir);
						if($res!==false && strlen($strFileDir)>0)
						{
							$strInsert1 .= ", ".$strColumnName;
							$strInsert2 .= ",  ".$res;
						}
					}
				}
				elseif($value === false)
				{
					$strInsert1 .= ", ".$strColumnName;
					$strInsert2 .= ", NULL ";
				}
				else
				{
					$strInsert1 .= ", ".$strColumnName;
					if($arColumnInfo["DATA_TYPE"]=="DATE")
					{
						if(strlen($value)>0)
							$strInsert2 .= ", TO_DATE('".$this->FormatDate($value, CLang::GetDateFormat("FULL", $lang), "D.M.Y H:I:S")."', 'DD.MM.YYYY HH24:MI:SS')";
						else
							$strInsert2 .= ", NULL ";
					}
					elseif($arColumnInfo["DATA_TYPE"]=="CLOB")
					{
						if(strlen($value)>0)
							$strInsert2 .= ", EMPTY_CLOB() ";
						else
							$strInsert2 .= ", NULL ";
					}
					else
					{
						$value = $this->FormatValue($value, $arColumnInfo);
						$strInsert2 .= ", '".$value."'";
					}
				}
			}
			elseif(is_set($arFields, "~".$strColumnName))
			{
				$strInsert1 .= ", ".$strColumnName;
				$strInsert2 .= ", ".$arFields["~".$strColumnName];
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
		if(is_set($this->column_cache, $strTableName))
			$arColumns = $this->column_cache[$strTableName];
		else
		{
			$arColumns = Array();

			$strSql =
					"SELECT COLUMN_NAME, DATA_TYPE, DATA_PRECISION, DATA_SCALE, DATA_LENGTH ".
					"FROM USER_TAB_COLUMNS ".
					"WHERE UPPER(TABLE_NAME) = UPPER('".$strTableName."') ";

			$db_columns = $this->Query($strSql);
			while($arColumnInfo = $db_columns->Fetch())
				$arColumns[] = $arColumnInfo;

			$this->column_cache[$strTableName] = $arColumns;
		}

		for($i=0; $i<count($arColumns); $i++)
		{
			$arColumnInfo = $arColumns[$i];
			$strColumnName = $arColumnInfo["COLUMN_NAME"];
			$value = $arFields[$strColumnName];
			if(isset($value))
			{
				//массив может быть при сохранении файлов, тогда мы пропускаем - файлы требуют индивидуальной обработки
				if(is_array($value))
				{
					if(strlen($value["name"])>0 || strlen($value["del"])>0 || strlen($value["description"])>0)
					{
						$res = CFile::SaveFile($value, $strFileDir);
						if($res!==false && strlen($strFileDir)>0)
							$strUpdate .= ", ".$strColumnName." = ".$res;
					}
				}
				elseif($value === false)
				{
					$strUpdate .= ", ".$strColumnName." = NULL";
				}
				else
				{
					if($arColumnInfo["DATA_TYPE"]=="DATE")
					{
						if(strlen($value)>0)
							$strUpdate .= ", ".$strColumnName." = TO_DATE('".$this->FormatDate($value, CLang::GetDateFormat("FULL", $lang), "D.M.Y H:I:S")."', 'DD.MM.YYYY HH24:MI:SS')";
						else
							$strUpdate .= ", ".$strColumnName." = NULL";
					}
					elseif($arColumnInfo["DATA_TYPE"]=="CLOB")
					{
						if(strlen($value)>0)
						{
							$strUpdate .= ", ".$strColumnName." = EMPTY_CLOB()";
							$arBinds[]=$strColumnName;
						}
						else
							$strUpdate .= ", ".$strColumnName." = NULL";
					}
					else
					{
						$value = $this->FormatValue($value, $arColumnInfo);
						$strUpdate .= ", ".$strColumnName." = '".$value."'";
					}
				}
			}
			elseif(is_set($arFields, "~".$strColumnName))
			{
				$strUpdate .= ", ".$strColumnName." = ".$arFields["~".$strColumnName];
			}
		}

		if($strUpdate!="")
			$strUpdate = substr($strUpdate, 2);

		return $strUpdate;
	}

	function Insert($table, $arFields, $error_position="", $DEBUG=false, $EXIST_ID="", $ignore_errors=false)
	{
		if(is_array($arFields))
		{
			while (list($field,$value)=each($arFields))
			{
				$str1 .= $field.", ";
				if (strlen($value)<=0) $str2 .= "'".$value."', ";
				else $str2 .= $value.", ";
			}
			$str1 = TrimEx($str1,",");
			$str2 = TrimEx($str2,",");
			if (strlen($EXIST_ID)>0)
			{
				$ID = $this->ForSql($EXIST_ID);
			}
			else
			{
				$ID = $this->NextID("sq_".$table);
			}
			$strSql = "INSERT INTO ".$table."(ID, ".$str1.") VALUES ('".$ID."', ".$str2.")";
			if ($DEBUG) echo "<br>".$strSql."<br>";
			$this->Query($strSql, $ignore_errors, $error_position);
			return $ID;
		}
		else return false;
	}

	function Update($table, $arFields, $WHERE="", $error_position="", $DEBUG=false, $ignore_errors=false)
	{
		$rows = 0;
		if (is_array($arFields))
		{
			while (list($field,$value)=each($arFields))
			{
				if (strlen($value)<=0)
					$str .= $field." = '', ";
				else
					$str .= $field." = ".$value.", ";
			}
			$str = TrimEx($str,",");
			$strSql = "UPDATE ".$table." SET ".$str." ".$WHERE;
			if ($DEBUG) echo "<br>".$strSql."<br>";
			$q = $this->Query($strSql, $ignore_errors, $error_position);
			$rows = $q->AffectedRowsCount();
		}
		return $rows;
	}

	function Add($tablename, $arFields, $arCLOBFields = Array(), $strFileDir="")
	{
		global $DB;
		$arFields["ID"] = $DB->NextID("sq_".$tablename);
		$arInsert = $DB->PrepareInsert($tablename, $arFields, $strFileDir);

		$arBinds=Array();
		foreach($arCLOBFields as $name)
			if(is_set($arFields, $name))
				$arBinds[$name] = $arFields[$name];

		$strSql =
			"INSERT INTO ".$tablename."(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";

		if(count($arBinds)>0)
			$DB->QueryBind($strSql, $arBinds);
		else
			$DB->Query($strSql);

		return $arFields["ID"];
	}

	function InitTableVarsForEdit($tablename, $strIdentFrom = "str_", $strIdentTo="str_", $strSuffixFrom="", $bAlways=false)
	{
		$strSql = "SELECT COLUMN_NAME ".
				"FROM USER_TAB_COLUMNS ".
				"WHERE UPPER(TABLE_NAME) = UPPER('".$tablename."') ";

		if($db_result = $this->Query($strSql))
		{
			while($db_result_table_columns=$db_result->Fetch())
			{
				$varnameFrom=$strIdentFrom.$db_result_table_columns["COLUMN_NAME"].$strSuffixFrom;
				$varnameTo=$strIdentTo.$db_result_table_columns["COLUMN_NAME"];
				global $$varnameFrom, $$varnameTo;
				if((isset($$varnameFrom) || $bAlways))
				{
					if(is_array($$varnameFrom))
					{
						foreach($$varnameFrom as $k=>$v)
							$$varnameFrom[$k] = htmlspecialchars($v);
					}
					else
						$$varnameTo = htmlspecialchars($$varnameFrom);
				}
			}
		}
	}

	function &GetTableFieldsList($tablename)
	{
		$arRes = array();

		$strSql = "SELECT COLUMN_NAME ".
				"FROM USER_TAB_COLUMNS ".
				"WHERE UPPER(TABLE_NAME) = UPPER('".$tablename."') ";

		if ($db_result = $this->Query($strSql))
		{
			while ($db_result_table_columns = $db_result->Fetch())
			{
				$arRes[] = $db_result_table_columns["COLUMN_NAME"];
			}
		}

		return $arRes;
	}

	function Concat()
	{
		$str = "";
		$ar = func_get_args();
		if (is_array($ar)) $str .= implode(" || ", $ar);
		return $str;
	}

	function IsNull($expression, $result)
	{
		return "NVL(".$expression.", ".$result.")";
	}

	function Length($field)
	{
		return "length($field)";
	}

	function TableExists($tableName)
	{
		$tableName = preg_replace("/[^A-Za-z0-9%_]+/i", "", $tableName);
		$tableName = Trim($tableName);

		if (strlen($tableName) <= 0)
			return False;

		$dbResult = $this->Query(
				"SELECT TABLE_NAME ".
				"FROM USER_TABLES ".
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
	var $arClobs=Array();
	var $bLast = false;
	function CDBResult($res=NULL)
	{
		parent::CAllDBResult($res);

		if($this->result)
		{
			//echo "[".$this->result."]";
			$intNumFields = OCINumCols($this->result);
			for($i=1; $i<=$intNumFields; $i++)
			{
				if (OCIColumnType($this->result, $i)=="CLOB")
					$this->arClobs[] = OCIColumnName($this->result, $i);
			}
		}
	}

	//После запроса делает выборку значений полей в массив
	function Fetch()
	{
		if($this->bNavStart || $this->bFromArray)
		{
			if(!is_array($this->arResult))
				return false;
			if($tmp=current($this->arResult))
				next($this->arResult);
			return $tmp;
		}
		elseif($this->bLast)
		{
			return false;
		}
		else
		{
			$arr = Array();
			$v=@OCIFetchInto($this->result, $arr, OCI_ASSOC + OCI_RETURN_NULLS + OCI_RETURN_LOBS);
			if(!$v)
			{
				$error=OCIError($this->result);
				if(IntVal($error["code"])!=0)
				{
					global $DB, $prev_Query;
					$error_msg="Ошибка в fetch! [".$error["code"]."] ".$error["message"]."\n";
					$error_msg.="Предыдущие запросы: \n";
					for($i=0; $i<count($prev_Query); $i++)
						$error_msg.=$prev_Query[$i]."\n\n";
					if($DB->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
						echo "<br><font color=#ff0000>Fetch Error!</font>[".$error["message"]."<br>".$error_msg."]<br>";
					else
						SendError($error_msg);
				}
				$this->bLast = true;
				return false;
			}

			for($i=0; $i<count($this->arClobs); $i++)
				if(is_object($arr[$this->arClobs[$i]]))
					$arr[$this->arClobs[$i]] = $arr[$this->arClobs[$i]]->load();
			return $arr;
		}
	}

	function SelectedRowsCount()
	{
		if($this->nSelectedCount !== false)
			return $this->nSelectedCount;

		return OCIRowCount($this->result);
	}

	function AffectedRowsCount()
	{
		return OCIRowCount($this->result);
	}

	function AffectedRowsCountEx()
	{
		return @OCIRowCount($this->result);
	}

	function FieldsCount()
	{
		return OCINumCols($this->result);
	}

	function FieldName($iCol)
	{
		return OCIColumnName($this->result, $iCol+1);
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

		@ocisetprefetch($this->result, 100);

		while($num_rows<$NavFirstRecordShow && !$rsEnd)
		{
			if(OCIFetchInto($this->result, $db_result_array, OCI_ASSOC+OCI_RETURN_NULLS+OCI_RETURN_LOBS))
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
			if(OCIFetchInto($this->result, $db_result_array, OCI_ASSOC+OCI_RETURN_NULLS+OCI_RETURN_LOBS ))
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
			while(OCIFetch($this->result))
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

		if(!$this->NavShowAll)
		{
			$strSql = "SELECT * FROM (SELECT T.*, ROWNUM as ROW_NUM_TMP FROM (".$strSql.") T  WHERE ROWNUM<=".$NavLastRecordShow.") WHERE ROW_NUM_TMP>".$NavFirstRecordShow;
			//$strSql = "SELECT * FROM (".$strSql.") WHERE ROWNUM BETWEEN ".$NavFirstRecordShow."+1 AND ".$NavLastRecordShow;
		}

		global $DB;
		$res_tmp = $DB->Query($strSql);
		while($r = $res_tmp->Fetch())
			$temp_arrray[] = $r;

		$this->arResult = $temp_arrray;
		$this->nSelectedCount = $cnt;
		$this->bDescPageNumbering = $bDescPageNumbering;
		$this->bFromLimited=true;
	}
}
?>
