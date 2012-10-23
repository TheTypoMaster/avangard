<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/search/classes/general/search.php");
IncludeModuleLangFile(__FILE__);

class CSearch extends CAllSearch
{
	function MakeSQL($query, $strSqlWhere, $strSort, $bIncSites, $bStem)
	{
		global $DB, $USER;
		if($bIncSites && $bStem)
			return "
			SELECT
				sc.ID
				,sc.MODULE_ID
				,sc.ITEM_ID
				,sc.LID
				,sc.TITLE
				,sc.BODY
				,sc.SEARCHABLE_CONTENT
				,sc.PARAM1
				,sc.PARAM2
				,sc.UPD
				,sc.DATE_FROM
				,sc.DATE_TO
				,L.DIR
				,L.SERVER_NAME
				,sc.URL as URL
				,st.TF as RANK
				,scsite.URL as SITE_URL
				,scsite.SITE_ID
				,".$DB->DateToCharFunction("sc.DATE_CHANGE")." as FULL_DATE_CHANGE
				,".$DB->DateToCharFunction("sc.DATE_CHANGE", "SHORT")." as DATE_CHANGE
			FROM b_search_content sc
				INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID
				INNER JOIN b_lang L ON scsite.SITE_ID=L.LID
				,(select search_content_id
				".(count($this->Query->m_stemmed_words)>1?",sum(st0.TF/sf0.FREQ) as TF ":",sum(st0.TF) as TF ")."
				from
				".(count($this->Query->m_stemmed_words)>1?"b_search_content_stem st0, b_search_content_freq sf0":"b_search_content_stem st0")."
				where st0.language_id='".$this->Query->m_lang."'
				".(count($this->Query->m_stemmed_words)>1?"and st0.stem=sf0.stem and sf0.language_id=st0.language_id":"")."
				and st0.stem in ('".implode("' ,'", $this->Query->m_stemmed_words)."')
				group by st0.search_content_id) st
			WHERE
				$query
				AND st.search_content_id = sc.id
				".($USER->IsAdmin()?"": "AND EXISTS(
					SELECT * FROM b_search_content_group scg
					WHERE sc.ID=scg.SEARCH_CONTENT_ID
					AND scg.GROUP_ID IN (".$USER->GetGroups().")) ")."
				".$strSqlWhere."
			".$strSort;
		elseif($bIncSites && !$bStem)
			return "
			SELECT
				sc.ID
				,sc.MODULE_ID
				,sc.ITEM_ID
				,sc.LID
				,sc.TITLE
				,sc.BODY
				,sc.SEARCHABLE_CONTENT
				,sc.PARAM1
				,sc.PARAM2
				,sc.UPD
				,sc.DATE_FROM
				,sc.DATE_TO
				,L.DIR
				,L.SERVER_NAME
				,sc.URL as URL
				,1 as RANK
				,scsite.URL as SITE_URL
				,scsite.SITE_ID
				,".$DB->DateToCharFunction("sc.DATE_CHANGE")." as FULL_DATE_CHANGE
				,".$DB->DateToCharFunction("sc.DATE_CHANGE", "SHORT")." as DATE_CHANGE
			FROM b_search_content sc
				INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID
				INNER JOIN b_lang L ON scsite.SITE_ID=L.LID
			WHERE
				$query
				".($USER->IsAdmin()?"": "AND EXISTS(
					SELECT * FROM b_search_content_group scg
					WHERE sc.ID=scg.SEARCH_CONTENT_ID
					AND scg.GROUP_ID IN (".$USER->GetGroups().")) ")."
				".$strSqlWhere."
			".$strSort;
		if(!$bIncSites && $bStem)
			return "
			SELECT
				sc.ID
				,sc.MODULE_ID
				,sc.ITEM_ID
				,sc.LID
				,sc.TITLE
				,sc.BODY
				,sc.SEARCHABLE_CONTENT
				,sc.PARAM1
				,sc.PARAM2
				,sc.UPD
				,sc.DATE_FROM
				,sc.DATE_TO
				,L.DIR
				,L.SERVER_NAME
				,sc.URL as URL
				,st.TF as RANK
				,".$DB->DateToCharFunction("sc.DATE_CHANGE")." as FULL_DATE_CHANGE
				,".$DB->DateToCharFunction("sc.DATE_CHANGE", "SHORT")." as DATE_CHANGE
			FROM b_search_content sc
				INNER JOIN b_lang L ON sc.LID=L.LID
				,(select search_content_id
				".(count($this->Query->m_stemmed_words)>1?",sum(st0.TF/sf0.FREQ) as TF ":",sum(st0.TF) as TF ")."
				from
				".(count($this->Query->m_stemmed_words)>1?"b_search_content_stem st0, b_search_content_freq sf0":"b_search_content_stem st0")."
				where st0.language_id='".$this->Query->m_lang."'
				".(count($this->Query->m_stemmed_words)>1?"and st0.stem=sf0.stem and sf0.language_id=st0.language_id":"")."
				and st0.stem in ('".implode("' ,'", $this->Query->m_stemmed_words)."')
				group by st0.search_content_id) st
			WHERE
				$query
				AND st.search_content_id = sc.id
				".($USER->IsAdmin()?"": "AND EXISTS(
					SELECT * FROM b_search_content_group scg
					WHERE sc.ID=scg.SEARCH_CONTENT_ID
					AND scg.GROUP_ID IN (".$USER->GetGroups().")) ")."
				".$strSqlWhere."
			".$strSort;
		if(!$bIncSites && !$bStem)
			return "
			SELECT
				sc.ID
				,sc.MODULE_ID
				,sc.ITEM_ID
				,sc.LID
				,sc.TITLE
				,sc.BODY
				,sc.SEARCHABLE_CONTENT
				,sc.PARAM1
				,sc.PARAM2
				,sc.UPD
				,sc.DATE_FROM
				,sc.DATE_TO
				,L.DIR
				,L.SERVER_NAME
				,sc.URL as URL
				,1 as RANK
				,".$DB->DateToCharFunction("sc.DATE_CHANGE")." as FULL_DATE_CHANGE
				,".$DB->DateToCharFunction("sc.DATE_CHANGE", "SHORT")." as DATE_CHANGE
			FROM b_search_content sc
				INNER JOIN b_lang L ON sc.LID=L.LID
			WHERE
				$query
				".($USER->IsAdmin()?"": "AND EXISTS(
					SELECT * FROM b_search_content_group scg
					WHERE sc.ID=scg.SEARCH_CONTENT_ID
					AND scg.GROUP_ID IN (".$USER->GetGroups().")) ")."
				".$strSqlWhere."
			".$strSort;
	}

	function ReindexLock()
	{
		global $DB;
		$DB->Query("LOCK TABLE b_search_content IN SHARE MODE", false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	function DeleteOld($SESS_ID, $MODULE_ID="", $SITE_ID="")
	{
		global $DB;
		$strFilter = "";
		$strJoin = "";
		if($MODULE_ID!="")
			$strFilter.=" AND MODULE_ID='".$DB->ForSql($MODULE_ID)."' ";
		if($SITE_ID!="")
		{
			$strFilter.=" AND scsite.SITE_ID='".$DB->ForSql($SITE_ID)."' ";
			$strJoin.=" INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID ";
		}
		$DB->Query("
			DELETE FROM b_search_content_group
			WHERE SEARCH_CONTENT_ID IN (
				SELECT ID
				FROM b_search_content sc
				".$strJoin."
				WHERE (UPD<>'".$DB->ForSql($SESS_ID)."' OR UPD IS NULL)
				".$strFilter."
			)", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$DB->Query("
			DELETE FROM b_search_content_stem
			WHERE SEARCH_CONTENT_ID IN (
				SELECT ID
				FROM b_search_content sc
				".$strJoin."
				WHERE (UPD<>'".$DB->ForSql($SESS_ID)."' OR UPD IS NULL)
				".$strFilter."
			)", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$DB->Query("
			DELETE FROM b_search_content_site
			WHERE SEARCH_CONTENT_ID IN (
				SELECT ID
				FROM b_search_content sc
				".$strJoin."
				WHERE (UPD<>'".$DB->ForSql($SESS_ID)."' OR UPD IS NULL)
				".$strFilter."
			)", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$DB->Query("
			DELETE FROM b_search_content sc
			WHERE (UPD<>'".$DB->ForSql($SESS_ID)."' OR UPD IS NULL)
			".($MODULE_ID!=""?" AND MODULE_ID='".$MODULE_ID."'":"")."
			AND NOT EXISTS (
				SELECT *
				FROM b_search_content_site scsite
				WHERE scsite.SEARCH_CONTENT_ID = sc.ID
			)", false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	function DeleteForReindex($MODULE_ID)
	{
		global $DB;
		$MODULE_ID = $DB->ForSql($MODULE_ID);
		$DB->Query("DELETE FROM b_search_content_group WHERE SEARCH_CONTENT_ID IN (SELECT ID FROM b_search_content WHERE MODULE_ID='".$MODULE_ID."')", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$DB->Query("DELETE FROM b_search_content_site WHERE SEARCH_CONTENT_ID IN (SELECT ID FROM b_search_content WHERE MODULE_ID='".$MODULE_ID."')", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$DB->Query("DELETE FROM b_search_content_stem WHERE SEARCH_CONTENT_ID IN (SELECT ID FROM b_search_content WHERE MODULE_ID='".$MODULE_ID."')", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$DB->Query("DELETE FROM b_search_content WHERE MODULE_ID='".$MODULE_ID."'", false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	function OnLangDelete($lang)
	{
		global $DB;
		$lang = $DB->ForSql($lang);

		$strSql = "
			SELECT SEARCH_CONTENT_ID
			FROM b_search_content_site
			WHERE SITE_ID='".$lang."'
			GROUP BY SEARCH_CONTENT_ID
			HAVING COUNT(*)=1
		";

		$DB->Query("DELETE FROM b_search_content_group WHERE SEARCH_CONTENT_ID in (".$strSql.")", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$DB->Query("DELETE FROM b_search_content_stem WHERE SEARCH_CONTENT_ID in (".$strSql.")", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$DB->Query("DELETE FROM b_search_content_site WHERE SITE_ID='".$lang."'", false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$r = $DB->Query(
			"SELECT sc.ID, MIN(scsite.SITE_ID) as SITE_ID ".
			"FROM b_search_content sc, b_search_content_site scsite ".
			"WHERE sc.LID = '".$lang."' ".
			"	AND sc.ID = scsite.SEARCH_CONTENT_ID ".
			"	AND scsite.SITE_ID <> '".$lang."' ".
			"GROUP BY sc.ID "
			, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		while($arR = $r->Fetch())
			$DB->Query("UPDATE b_search_content SET LID = '".$arR["SITE_ID"]."' WHERE ID=".$arR["ID"], false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$DB->Query("DELETE FROM b_search_content WHERE LID='".$lang."'", false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	function ChangePermission($MODULE_ID, $arGroups, $ITEM_ID=false, $PARAM1=false, $PARAM2=false, $SITE_ID=false)
	{
		global $DB;

		$strSqlWhere = CSearch::__PrepareFilter(Array("MODULE_ID"=>$MODULE_ID, "ITEM_ID"=>$ITEM_ID, "PARAM1"=>$PARAM1, "PARAM2"=>$PARAM2, "SITE_ID"=>$SITE_ID), $bIncSites);
		if(strlen($strSqlWhere)>0)
			$strSqlWhere="AND ".$strSqlWhere;
		$strSql = "
			DELETE FROM b_search_content_group
			WHERE EXISTS
			(
				SELECT *
				FROM
					b_search_content sc
					".($bIncSites?" ,b_search_content_site scsite ":"")."
				WHERE b_search_content_group.SEARCH_CONTENT_ID = sc.ID
					".($bIncSites?" AND sc.ID=scsite.SEARCH_CONTENT_ID ":"")."
					".$strSqlWhere."
			)
		";

		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$strGroups = "0";
		for($i=0; $i<count($arGroups); $i++)
			if(IntVal($arGroups[$i])>0)
				$strGroups .= ",".IntVal($arGroups[$i]);

		$strSql = "
			INSERT INTO b_search_content_group(SEARCH_CONTENT_ID, GROUP_ID)
			SELECT DISTINCT sc.ID, g.ID
			FROM b_group g, b_search_content sc
			".($bIncSites?"	INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID ":"")."
			WHERE g.ID IN (".$strGroups.")
			".$strSqlWhere."
		";

		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	function DeleteIndex($MODULE_ID, $ITEM_ID=false, $PARAM1=false, $PARAM2=false, $SITE_ID=false)
	{
		global $DB;

		$strSqlWhere = CSearch::__PrepareFilter(Array("MODULE_ID"=>$MODULE_ID, "ITEM_ID"=>$ITEM_ID, "PARAM1"=>$PARAM1, "PARAM2"=>$PARAM2, "SITE_ID"=>$SITE_ID), $bIncSites);
		if(strlen($strSqlWhere)>0)
			$strSqlWhere="AND ".$strSqlWhere;
		$strSql = "
			DELETE FROM b_search_content_stem
			WHERE EXISTS
			(
				SELECT *
				FROM b_search_content sc
				".($bIncSites?"INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID ":"")."
				WHERE b_search_content_stem.SEARCH_CONTENT_ID = sc.ID
				".$strSqlWhere."
			)";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$strSql = "
			DELETE FROM b_search_content_group
			WHERE EXISTS
			(
				SELECT *
				FROM b_search_content sc
			".($bIncSites?"	INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID ":"")."
				WHERE b_search_content_group.SEARCH_CONTENT_ID = sc.ID
				".$strSqlWhere."
			)";

		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$strSql = "
			DELETE FROM b_search_content_site
			WHERE EXISTS
			(
				SELECT *
				FROM b_search_content sc
			".($bIncSites?"	INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID ":"")."
				WHERE b_search_content_site.SEARCH_CONTENT_ID = sc.ID
				".$strSqlWhere."
			)";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$strSql = "
			DELETE FROM b_search_content
			WHERE EXISTS
			(
				SELECT *
				FROM b_search_content sc
			".($bIncSites?"	INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID ":"")."
				WHERE b_search_content.ID = sc.ID
				".$strSqlWhere."
			)";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

	}

	function FormatDateString($strField)
	{
		return "TO_CHAR(".$strField.", 'DD.MM.YYYY HH24:MI:SS')";
	}

	function Update($ID, $arFields)
	{
		global $DB;

		if(is_set($arFields, "LAST_MODIFIED"))
			$arFields["DATE_CHANGE"] = $arFields["LAST_MODIFIED"];
		elseif(is_set($arFields, "DATE_CHANGE"))
			$arFields["DATE_CHANGE"] = $DB->FormatDate($arFields["DATE_CHANGE"], "DD.MM.YYYY HH.MI.SS", CLang::GetDateFormat());

		$strUpdate = $DB->PrepareUpdate("b_search_content", $arFields);
		if(strlen($strUpdate) > 0)
		{
			$arBinds=Array();
			if(is_set($arFields, "BODY"))
				$arBinds["BODY"] = $arFields["BODY"];
			if(is_set($arFields, "SEARCHABLE_CONTENT"))
				$arBinds["SEARCHABLE_CONTENT"] = $arFields["SEARCHABLE_CONTENT"];
			$DB->QueryBind("UPDATE b_search_content SET ".$strUpdate." WHERE ID=".intval($ID), $arBinds);
		}
	}

	function StemIndex($arLID, $ID, $sContent)
	{
		global $DB;
		$arLang=array();
		if(!is_array($arLID))
			$arLID = Array();
		foreach($arLID as $site=>$url)
		{
			if(!isset($GLOBALS["CACHE_SEARCH_SITE_LANGS"][$site]))
			{
				$db_site_tmp = CSite::GetByID($site);
				if ($ar_site_tmp = $db_site_tmp->Fetch())
					$GLOBALS["CACHE_SEARCH_SITE_LANGS"][$site] = array(
						"LANGUAGE_ID" => $ar_site_tmp["LANGUAGE_ID"],
						"CHARSET" => $ar_site_tmp["CHARSET"],
						"SERVER_NAME" => $ar_site_tmp["SERVER_NAME"]
					);
			}
			if(isset($GLOBALS["CACHE_SEARCH_SITE_LANGS"][$site]))
				$arLang[$GLOBALS["CACHE_SEARCH_SITE_LANGS"][$site]["LANGUAGE_ID"]]++;
		}
		foreach($arLang as $lang=>$value)
		{
			$arDoc = stemming($sContent, $lang);
			$docLength = 0;
			foreach($arDoc as $word => $count)
				$docLength += $count;
			if($docLength>0)
			{
				$doc = "";
				$logDocLength = log($docLength<20?20:$docLength);
				$strSqlPrefix = "
						insert into b_search_content_stem
						(SEARCH_CONTENT_ID, LANGUAGE_ID, STEM, TF)
						SELECT ".$ID.", '".$lang."', T.STEM, T.TF
						FROM table(cast(f_stem('
				";
				$maxValuesLen = 1024;
				$strSqlValues = "";
				foreach($arDoc as $word => $count)
				{
					$strSqlValues .= " ".$word.";".number_format(log($count+1)/$docLength, 4, ".", "");
					if(strlen($strSqlValues) > $maxValuesLen)
					{
						$DB->Query($strSqlPrefix.substr($strSqlValues, 1)."') as tt_stem)) t", false, "File: ".__FILE__."<br>Line: ".__LINE__);
						$strSqlValues = "";
					}
				}
				if(strlen($strSqlValues) > 0)
				{
					$DB->Query($strSqlPrefix.substr($strSqlValues, 1)."') as tt_stem)) t", false, "File: ".__FILE__."<br>Line: ".__LINE__);
					$strSqlValues = "";
				}
			}
		}
	}

	function ChangeIndex($MODULE_ID, $arFields, $ITEM_ID=false, $PARAM1=false, $PARAM2=false, $SITE_ID=false)
	{
		global $DB;

		if(is_set($arFields, "TITLE"))
			$arFields["TITLE"] = Trim($arFields["TITLE"]);

		if(is_set($arFields, "BODY"))
			$arFields["BODY"] = Trim($arFields["BODY"]);

		if(is_set($arFields) && is_array($arFields["PERMISSIONS"]))
			CSearch::ChangePermission($MODULE_ID, $arFields["PERMISSIONS"], $ITEM_ID, $PARAM1, $PARAM2, $SITE_ID);

		$strUpdate = $DB->PrepareUpdate("b_search_content", $arFields);
		if(strlen($strUpdate) > 0)
		{
			$strSqlWhere = CSearch::__PrepareFilter(Array("MODULE_ID"=>$MODULE_ID, "ITEM_ID"=>$ITEM_ID, "PARAM1"=>$PARAM1, "PARAM2"=>$PARAM2, "SITE_ID"=>$SITE_ID), $bIncSites);
			$strSql = "
				UPDATE b_search_content SET
				".$strUpdate."
				WHERE ID IN (
					SELECT sc.ID
					FROM b_search_content sc
					".($bIncSites? "INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID": "")."
					".(strlen($strSqlWhere)>0? "WHERE ".$strSqlWhere: "")."
				)
			";
	
			$arBinds=Array();
			if(is_set($arFields, "BODY"))
				$arBinds["BODY"] = $arFields["BODY"];
			if(is_set($arFields, "SEARCHABLE_CONTENT"))
				$arBinds["SEARCHABLE_CONTENT"] = $arFields["SEARCHABLE_CONTENT"];
			$DB->QueryBind($strSql, $arBinds);
		}
	}
}

class CSearchQuery extends CAllSearchQuery
{
	var $cnt = 0;
	function BuildWhereClause($word)
	{
		$this->cnt++;
		if($this->cnt>10)
			return "1=1";

		global $DB;
		$strStem = "";
		if (isset($this->m_kav[$word]))
			$word = $this->m_kav[$word];
		elseif(COption::GetOptionString("search", "use_stemming", "N")=="Y")
			$strStem = $word;

		$this->m_words[] =($strStem==""? $word: $strStem);
		$word = $DB->ForSql(ToUpper($word), 100);

		if(strlen($strStem)>0)
		{
			$this->m_stemmed_words[] = $strStem;
			$ret = " exists (
					select * from b_search_content_stem st
					where st.language_id='".$this->m_lang."'
					and st.stem = '".$strStem."'
					and st.search_content_id = sc.id)\n";
		}
		else
			$ret = "(DBMS_LOB.INSTR(".$this->m_fields[0].", '$word')>0)";

		return $ret;

	}
}
?>
