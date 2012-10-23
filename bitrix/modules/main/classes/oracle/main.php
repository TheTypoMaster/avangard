<?
require_once(substr(__FILE__, 0, strlen(__FILE__) - strlen("/classes/oracle/main.php"))."/bx_root.php");

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/main.php");

class CMain extends CAllMain
{
	function __GetConditionFName()
	{
		return "CONDITION";
	}

	function EpilogActions()
	{
		//отправим сообщения
		CEvent::CheckEvents();
		//подчистим файлы
		CMain::FileAction();
	}

	function FileAction()
	{
		global $DB, $DOCUMENT_ROOT;

		$DB->StartTransaction();
		$strSql = "
			SELECT
				*
			FROM
				b_file_action A
			FOR UPDATE NOWAIT
			";
		if (!($rs = $DB->Query($strSql, true)))
		{
			$DB->Commit();
			return;
		}
		$upload_dir = COption::GetOptionString("main", "upload_dir", "upload");
		while ($ar = $rs->Fetch())
		{
			if ($ar["FILE_ACTION"]=="DELETE")
			{
				$dir = $_SERVER["DOCUMENT_ROOT"]."/".$upload_dir."/".$ar["SUBDIR"]."/";
				$file = $dir.$ar["FILE_NAME"];
				if (file_exists($file))
				{
					@unlink($file);
					//if (COption::GetOptionString("main", "save_original_file_name", "N")=="Y")
					@rmdir($dir);
				}
			}
			$DB->Query("DELETE FROM b_file_action WHERE ID = '".$ar["ID"]."'");
		}
		$DB->Commit();
	}


	function GetLang($cur_dir=false, $cur_host=false)
	{
		global $DB, $lang, $DOCUMENT_ROOT, $MAIN_LANGS_CACHE, $MAIN_LANGS_ADMIN_CACHE;

		if($cur_dir===false)
			$cur_dir = $this->GetCurDir();
		if($cur_host===false)
			$cur_host = $_SERVER["HTTP_HOST"];

		if(substr($cur_dir, 0, strlen(BX_ROOT."/admin/")) == BX_ROOT."/admin/"
			|| substr($cur_dir, 0, strlen(BX_ROOT."/updates/")) == BX_ROOT."/updates/"
			|| (defined("ADMIN_SECTION") && ADMIN_SECTION==true)
			|| (defined("BX_PUBLIC_TOOLS") && BX_PUBLIC_TOOLS===true)
			) //если раздел администрирования
		{
			//путь по параметру
			if(strlen($lang)<=0)
				$lang = COption::GetOptionString("main", "admin_lid", "ru");

			$R = CLanguage::GetList($o, $b, Array("LID"=>$lang, "ACTIVE"=>$ACTIVE));
			if($res = $R->Fetch())
			{
				$MAIN_LANGS_ADMIN_CACHE[$res["LID"]]=$res;
				return $res;
			}

			//если переменная не задана - берем язык по умолчанию
			$strSql =
				"SELECT * ".
				"FROM b_language ".
				"WHERE ACTIVE='Y' ".
				"ORDER BY DEF DESC, SORT";

			$R = $DB->Query($strSql);
			if($res = $R->Fetch())
			{
				$MAIN_LANGS_ADMIN_CACHE[$res["LID"]]=$res;
				return $res;
			}

			//ну если вообще ничего не задано - вернем просто
			return array("en", "MM/DD/YYYY", "MM/DD/YYYY HH24:MI:SS");
		}
		else //все остальные папки
		{
			$arURL = parse_url("http://".$cur_host);
			if($arURL["scheme"]=="" && strlen($arURL["host"])>0)
				$CURR_DOMAIN = $arURL["host"];
			else
				$CURR_DOMAIN = $cur_host;

			if(strpos($CURR_DOMAIN, ':')>0)
				$CURR_DOMAIN = substr($CURR_DOMAIN, 0, strpos($CURR_DOMAIN, ':'));
			$CURR_DOMAIN = Trim($CURR_DOMAIN, "\t\r\n\0 .");

			//текущий язык определяем по пути
			if(CACHED_b_lang!==false && CACHED_b_lang_domain!==false)
			{
				global $CACHE_MANAGER;
				$strSql =
					"SELECT L.*, L.LID as ID, L.LID as SITE_ID ".
					"FROM b_lang L ".
					"WHERE L.ACTIVE='Y' ".
					"ORDER BY ".
					"	LENGTH(L.DIR) DESC, ".
					"	L.DOMAIN_LIMITED DESC, ".
					"	SORT ";
				if($CACHE_MANAGER->Read(CACHED_b_lang, "b_lang".md5($strSql), "b_lang"))
				{
					$arLang = $CACHE_MANAGER->Get("b_lang".md5($strSql));
				}
				else
				{
					$arLang = array();
					$R = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
					while($row = $R->Fetch())
						$arLang[]=$row;
					$CACHE_MANAGER->Set("b_lang".md5($strSql), $arLang);
				}
	
				$strSql =
					"SELECT  ".
					"LD.LID as LD_LID,LD.DOMAIN as LD_DOMAIN ".
					"FROM  ".
					"	 b_lang_domain LD  ".
					"ORDER BY ".
					"	LENGTH(LD.DOMAIN) DESC ";
				if($CACHE_MANAGER->Read(CACHED_b_lang_domain, "b_lang_domain2", "b_lang_domain"))
				{
					$arLangDomain = $CACHE_MANAGER->Get("b_lang_domain2");
				}
				else
				{
					$arLangDomain = array();
					$R = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
					while($row = $R->Fetch())
						$arLangDomain[$row["LD_LID"]][]=$row;
					$CACHE_MANAGER->Set("b_lang_domain2", $arLangDomain);
				}
	
				$arJoin = array();
				foreach($arLang as $row)
				{
					//LEFT JOIN
					$bLeft = true;
					//LEFT JOIN b_lang_domain LD ON L.LID=LD.LID
					if(array_key_exists($row["LID"], $arLangDomain))
						foreach($arLangDomain[$row["LID"]] as $dom)
							//AND '".$DB->ForSql($CURR_DOMAIN, 255)."' LIKE CONCAT('%', LD.DOMAIN)
							if(substr($CURR_DOMAIN, -strlen($dom["LD_DOMAIN"]))==$dom["LD_DOMAIN"])
							{
								$arJoin[] = $row+$dom;
								$bLeft = false;
							}
					if($bLeft)
						$arJoin[] = $row+array("LD_LID"=>"","LD_DOMAIN"=>"");
				}
				$A = array();
				foreach($arJoin as $row)
				{
					//WHERE ('".$DB->ForSql($cur_dir)."' LIKE CONCAT(L.DIR, '%') OR LD.LID IS NOT NULL)
					if($row["LD_LID"]!="" || substr($cur_dir, 0, strlen($row["DIR"]))==$row["DIR"])
						$A[]=$row;
				}
	
				$res=false;
				if($res===false)
					foreach($A as $row)
						if(
							(substr($cur_dir, 0, strlen($row["DIR"]))==$row["DIR"])
							&& (($row["DOMAIN_LIMITED"]=="Y" && $row["LD_LID"]!="")||$row["DOMAIN_LIMITED"]!="Y")
						)
						{
							$res=$row;
							break;
						}
				if($res===false)
					foreach($A as $row)
						if(
							strncmp($cur_dir, $row["DIR"], strlen($cur_dir))==0
						)
						{
							$res=$row;
							break;
						}
				if($res===false)
					foreach($A as $row)
						if(
							(($row["DOMAIN_LIMITED"]=="Y" && $row["LD_LID"]!="")||$row["DOMAIN_LIMITED"]!="Y")
						)
						{
							$res=$row;
							break;
						}
				if($res===false && count($A)>0)
					$res=$A[0];
			}
			else
			{
				$dec1 = "DECODE(L.DOMAIN_LIMITED, 'Y', DECODE(LD.LID, NULL, 'N', 'Y'))"; //'Y' - если сработало ограничение по доменам
				$dec2 = "DECODE(".$dec1.", 'Y', 'Y', DECODE(L.DOMAIN_LIMITED, 'Y', 'N', 'Y'))"; //'Y' - если или сработало ограничение по доменам или домены не ограничены
				$dec3 = "DECODE(".$dec2.", ".
							"'Y', DECODE(INSTR('".$DB->ForSql($cur_dir)."', L.DIR), 1, 3, 1), ".
							"DECODE(INSTR('".$DB->ForSql($cur_dir)."', L.DIR), 1, 2, 0)".
							") ";
				$strSql =
					"SELECT L.*, L.LID as ID, L.LID as SITE_ID ".
					"FROM b_lang L ".
					"	LEFT JOIN b_lang_domain LD ON L.LID=LD.LID AND '".$DB->ForSql($CURR_DOMAIN, 255)."' LIKE '%'||LD.DOMAIN ".
					"WHERE ('".$DB->ForSql($cur_dir)."' LIKE L.DIR||'%' OR LD.LID IS NOT NULL) ".
					"	AND L.ACTIVE='Y' ".
					"ORDER BY ".
					"	".$dec3." DESC, ".
					"	LENGTH(L.DIR) DESC, ".
					"	L.DOMAIN_LIMITED DESC, ".
					"	L.SORT, ".
					"	LENGTH(LD.DOMAIN) DESC ";
	
				//echo $strSql;
				$R = $DB->Query($strSql, false, "File: ".__FILE__." Line:".__LINE__);
				$res = $R->Fetch();
			}

			if($res /*&& file_exists($DOCUMENT_ROOT.BX_ROOT."/php_interface/".$res["LID"])*/)
			{
				$MAIN_LANGS_CACHE[$res["LID"]]=$res;
				return $res;
			}

			//если переменная не задана - берем язык по умолчанию
			$strSql =
				"SELECT L.*, L.LID as ID, L.LID as SITE_ID ".
				"FROM b_lang L ".
				"WHERE ACTIVE='Y' ".
				"ORDER BY DEF DESC, SORT";

			$R = $DB->Query($strSql);
			while($res = $R->Fetch())
			{
				//if(file_exists($DOCUMENT_ROOT.BX_ROOT."/php_interface/".$res["LID"]))
				//{
					$MAIN_LANGS_CACHE[$res["LID"]]=$res;
					return $res;
				//}
		   	}
		}

		return array("en", "MM/DD/YYYY", "MM/DD/YYYY HH24:MI:SS");
	}
}

class CFile extends CAllFile
{
	function Delete($ID)
	{
		if(IntVal($ID)<=0)
			return;
		global $DB;
		$res = $DB->Query("BEGIN DELFILE(".IntVal($ID).", NULL); END;");
	}

	function SaveFile($arFile, $strSavePath/*, $int_max_file_size=0*/)
	{
		global $DB;
		$strFileName = basename($arFile["name"]);	/* filename.gif */
		$strFileExt = strrchr($arFile["name"], ".");

		if(strlen($arFile["del"]) > 0 && strlen($strFileName) <= 0)
			return "NULL";

		if(is_set($arFile, "content") && !is_set($arFile, "size"))
			$arFile["size"] = strlen($arFile["content"]);

		if(COption::GetOptionString("main", "save_original_file_name", "N")=="Y")
		{
			if(COption::GetOptionString("main", "convert_original_file_name", "Y")=="Y")
			{
				$strFileName = preg_replace('/([^'.BX_VALID_FILENAME_SYMBOLS.'])/e', "chr(rand(97, 122))", $strFileName);
			}
			$dir_add = "";
			$i=0;
			while(true)
			{
				$dir_add = substr(md5(uniqid(mt_rand(), true)), 0, 3);
				if(!file_exists($_SERVER["DOCUMENT_ROOT"]."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$strSavePath."/".$dir_add."/".$strFileName))
					break;
				if($i>=25)
				{
					$dir_add = md5(uniqid(mt_rand(), true));
					break;
				}
				$i++;
			}
			if(substr($strSavePath, -1, 1) <> "/")
				$strSavePath .= "/".$dir_add;
			else
				$strSavePath .= $dir_add."/";
			$newName = $strFileName;
		}
		else
		{
			$newName = md5(uniqid(mt_rand(), true)).$strFileExt;
			if(substr($strSavePath, -1, 1) <> "/")
				$strSavePath .= "/".substr($newName, 0, 3);
			else
				$strSavePath .= substr($newName, 0, 3)."/";
		}


		if(strlen($arFile["name"]) <= 0 or intval($arFile["size"]) <= 0 or  strlen($arFile["type"]) <= 0)
		{
			if(is_set($arFile, "description") && intval($arFile["old_file"])>0)
			{
				$strSql =
					"UPDATE b_file SET ".
					"       DESCRIPTION='".$DB->ForSQL($arFile["description"], 255)."' ".
					"WHERE ID=".intval($arFile["old_file"]);

				$DB->Query($strSql);
				CFile::CleanCache($arFile["old_file"]);
			}
			return false;
		}

		$strDirName = $_SERVER["DOCUMENT_ROOT"]."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$strSavePath."/";

		CheckDirPath($strDirName);

		$strDbFileNameX = $strDirName.$newName;

		if(is_set($arFile, "content"))
		{
			$f = @fopen($strDbFileNameX, "ab");
			if(!$f)
				return false;
			if(!fwrite($f, $arFile["content"]))
				return false;
			fclose($f);
		}
		elseif(!@copy($arFile["tmp_name"], $strDbFileNameX))
			return false;

		@chmod($strDbFileNameX, BX_FILE_PERMISSIONS);
		$imgArray = @getimagesize($strDbFileNameX);

		if(is_array($imgArray))
		{
			$intWIDTH = $imgArray[0];
			$intHEIGHT = $imgArray[1];
		}
		else
		{
			$intWIDTH = 0;
			$intHEIGHT = 0;
		}

		if($arFile["type"]=="image/pjpeg")
			$arFile["type"]="image/jpeg";

		$NEW_IMAGE_ID = $DB->NextID("sq_b_file", $db_Conn);
		$strSql =
			"INSERT INTO b_file(ID, HEIGHT, WIDTH, FILE_SIZE, CONTENT_TYPE, SUBDIR, FILE_NAME, MODULE_ID, ORIGINAL_NAME, DESCRIPTION) ".
			"VALUES('".$NEW_IMAGE_ID."', '".$intHEIGHT."', '".$intWIDTH."', '".IntVal($arFile["size"])."', '".$DB->ForSql($arFile["type"], 255)."' , '".$DB->ForSql($strSavePath, 255)."', '".$DB->ForSQL($newName, 255)."', '".$arFile["MODULE_ID"]."', '".$DB->ForSql($strFileName, 255)."', '".$DB->ForSQL($arFile["description"], 255)."') ";

		$DB->Query($strSql);
		CFile::CleanCache($NEW_IMAGE_ID);
		return $NEW_IMAGE_ID;
	}
}

class CSite extends CAllSite
{
	function GetCurTemplate()
	{
		global $DB, $APPLICATION, $USER, $CACHE_MANAGER;
		if(CACHED_b_site_template===false)
		{
			$strSql = "
				SELECT
					".CMain::__GetConditionFName().",
					TEMPLATE
				FROM
					b_site_template
				WHERE
					SITE_ID='".SITE_ID."'
				ORDER BY
					DECODE(".CMain::__GetConditionFName().", NULL, 2, 1),
					SORT
				";
			$dbr = $DB->Query($strSql);
			while($ar = $dbr->Fetch())
			{
				$strCondition = trim($ar["CONDITION"]);
				if(strlen($strCondition)>0 && (!@eval("return ".$strCondition.";")))
					continue;
				if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/templates/".$ar["TEMPLATE"]) && is_dir($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/templates/".$ar["TEMPLATE"]))
					return $ar["TEMPLATE"];
			}
		}
		else
		{
			if($CACHE_MANAGER->Read(CACHED_b_site_template, "b_site_template"))
			{
				$arSiteTemplateBySite = $CACHE_MANAGER->Get("b_site_template");
			}
			else
			{
				$dbr = $DB->Query("
					SELECT
						".CMain::__GetConditionFName().",
						TEMPLATE,
						SITE_ID
					FROM
						b_site_template
					ORDER BY
						SITE_ID, DECODE(".CMain::__GetConditionFName().", NULL, 2, 1), SORT
				");
				while($ar = $dbr->Fetch())
					$arSiteTemplateBySite[$ar['SITE_ID']][]=$ar;
				$CACHE_MANAGER->Set("b_site_template", $arSiteTemplateBySite);
			}
			foreach($arSiteTemplateBySite[SITE_ID] as $ar)
			{
				$strCondition = trim($ar["CONDITION"]);
				if(strlen($strCondition)>0 && (!@eval("return ".$strCondition.";")))
					continue;
				if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/templates/".$ar["TEMPLATE"]) && is_dir($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/templates/".$ar["TEMPLATE"]))
					return $ar["TEMPLATE"];
			}
		}

		return ".default";
	}
}

class CFilterQuery extends CAllFilterQuery
{
	function BuildWhereClause($word)
	{
		$this->cnt++;
		//if($this->cnt>10) return "1=1";

		global $DB;
		if (isset($this->m_kav[$word]))
			$word = $this->m_kav[$word];

		$this->m_words[] = $word;

		$word = $DB->ForSql($word, 100);

		$n = count($this->m_fields);
		$ret = "";
		if ($n>1) $ret = "(";
		for ($i=0; $i<$n; $i++)
		{
			$field = $this->m_fields[$i];
			if ($this->clob=="Y")
			{
				if ($this->procent=="Y")
				{
					if ($this->clob_upper != "Y")
					{
						$ret.= "
							(DBMS_LOB.INSTR($field, '$word')>0 and $field is not null)
							";
					}
					else
					{
						$ret.= "
							(DBMS_LOB.INSTR($field, upper('$word'))>0 and $field is not null)
							";
					}
				}
				else
				{
					if ($this->clob_upper!="Y")
					{
						$ret.= "
							(DBMS_LOB.COMPARE($field, '$word')=0 and $field is not null)
							";
					}
					else
					{
						$ret.= "
							(DBMS_LOB.COMPARE($field, upper('$word'))=0 and $field is not null)
							";
					}
				}
			}
			else
			{
				if ($this->procent=="Y")
				{
					$ret.= "
						(upper($field) like upper('%$word%') and $field is not null)
						";
				}
				elseif (strpos($word, "%")!==false || strpos($word, "_")!==false)
				{
					$ret.= "
						(upper($field) like upper('$word') and $field is not null)
						";
				}
				else
				{
					$ret.= "
						(upper(to_char($field))=upper('$word') and $field is not null)
						";
				}
			}
			if ($i<>$n-1) $ret.= " OR ";
		}
		if ($n>1) $ret.= ")";
		return $ret;
	}
}

class CLang extends CSite
{
}
?>
