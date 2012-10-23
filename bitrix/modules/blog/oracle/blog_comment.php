<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/blog/general/blog_comment.php");

class CBlogComment extends CAllBlogComment
{
	/*************** ADD, UPDATE, DELETE *****************/
	function Add($arFields)
	{
		global $DB;

		$arFields1 = array();
		foreach ($arFields as $key => $value)
		{
			if (substr($key, 0, 1) == "=")
			{
				$arFields1[substr($key, 1)] = $value;
				unset($arFields[$key]);
			}
		}

		if (!CBlogComment::CheckFields("ADD", $arFields))
			return false;

		$arInsert = $DB->PrepareInsert("b_blog_comment", $arFields);

		foreach ($arFields1 as $key => $value)
		{
			if (strlen($arInsert[0]) > 0)
				$arInsert[0] .= ", ";
			$arInsert[0] .= $key;
			if (strlen($arInsert[1]) > 0)
				$arInsert[1] .= ", ";
			$arInsert[1] .= $value;
		}

		$ID = False;
		if (strlen($arInsert[0]) > 0)
		{
			$ID = IntVal($DB->NextID("SQ_B_BLOG_COMMENT"));

			$strSql =
				"INSERT INTO b_blog_comment(ID, ".$arInsert[0].") ".
				"VALUES(".$ID.", ".$arInsert[1].")";

			$arBinds = Array();
			if (is_set($arFields, "POST_TEXT"))
				$arBinds["POST_TEXT"] = $arFields["POST_TEXT"];
			$DB->QueryBind($strSql, $arBinds);
		}

		if ($ID)
		{
			$arComment = CBlogComment::GetByID($ID);
			CBlogPost::Update($arComment["POST_ID"], array("=NUM_COMMENTS" => "NUM_COMMENTS + 1"));
		}

		return $ID;
	}

	function Update($ID, $arFields)
	{
		global $DB;

		$ID = IntVal($ID);

		$arFields1 = array();
		foreach ($arFields as $key => $value)
		{
			if (substr($key, 0, 1) == "=")
			{
				$arFields1[substr($key, 1)] = $value;
				unset($arFields[$key]);
			}
		}

		if (!CBlogComment::CheckFields("UPDATE", $arFields, $ID))
			return false;

		$strUpdate = $DB->PrepareUpdate("b_blog_comment", $arFields);

		foreach ($arFields1 as $key => $value)
		{
			if (strlen($strUpdate) > 0)
				$strUpdate .= ", ";
			$strUpdate .= $key."=".$value." ";
		}

		if (strlen($strUpdate) > 0)
		{
			$strSql =
				"UPDATE b_blog_comment SET ".
				"	".$strUpdate." ".
				"WHERE ID = ".$ID." ";

			$arBinds = Array();
			if (is_set($arFields, "POST_TEXT"))
				$arBinds["POST_TEXT"] = $arFields["POST_TEXT"];
			$DB->QueryBind($strSql, $arBinds);

			unset($GLOBALS["BLOG_COMMENT_CACHE_".$ID]);

			return $ID;
		}

		return False;
	}

	//*************** SELECT *********************/
	function GetList($arOrder = Array("ID" => "DESC"), $arFilter = Array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;

		if (count($arSelectFields) <= 0)
			$arSelectFields = array("ID", "BLOG_ID", "POST_ID", "PARENT_ID", "AUTHOR_ID", "AUTHOR_NAME", "AUTHOR_EMAIL", "AUTHOR_IP", "AUTHOR_IP1", "TITLE", "POST_TEXT");

		// FIELDS -->
		$arFields = array(
				"ID" => array("FIELD" => "C.ID", "TYPE" => "int"),
				"BLOG_ID" => array("FIELD" => "C.BLOG_ID", "TYPE" => "int"),
				"POST_ID" => array("FIELD" => "C.POST_ID", "TYPE" => "int"),
				"PARENT_ID" => array("FIELD" => "C.PARENT_ID", "TYPE" => "int"),
				"AUTHOR_ID" => array("FIELD" => "C.AUTHOR_ID", "TYPE" => "int"),
				"AUTHOR_NAME" => array("FIELD" => "C.AUTHOR_NAME", "TYPE" => "string"),
				"AUTHOR_EMAIL" => array("FIELD" => "C.AUTHOR_EMAIL", "TYPE" => "string"),
				"AUTHOR_IP" => array("FIELD" => "C.AUTHOR_IP", "TYPE" => "string"),
				"AUTHOR_IP1" => array("FIELD" => "C.AUTHOR_IP1", "TYPE" => "string"),
				"TITLE" => array("FIELD" => "C.TITLE", "TYPE" => "string"),
				"POST_TEXT" => array("FIELD" => "C.POST_TEXT", "TYPE" => "string"),
				"DATE_CREATE" => array("FIELD" => "C.DATE_CREATE", "TYPE" => "datetime"),

				"USER_LOGIN" => array("FIELD" => "U.LOGIN", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (C.AUTHOR_ID = U.ID)"),
				"USER_NAME" => array("FIELD" => "U.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (C.AUTHOR_ID = U.ID)"),
				"USER_LAST_NAME" => array("FIELD" => "U.LAST_NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (C.AUTHOR_ID = U.ID)"),
				"USER_EMAIL" => array("FIELD" => "U.EMAIL", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (C.AUTHOR_ID = U.ID)"),
				"USER" => array("FIELD" => "U.LOGIN,U.NAME,U.LAST_NAME,U.EMAIL,U.ID", "WHERE_ONLY" => "Y", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (C.AUTHOR_ID = U.ID)"),
			);
		// <-- FIELDS

		$arSqls = CBlog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_blog_comment C ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			//echo "!1!=".htmlspecialchars($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return False;
		}

		$strSql =
			"SELECT ".$arSqls["SELECT"]." ".
			"FROM b_blog_comment C ".
			"	".$arSqls["FROM"]." ";
		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_blog_comment C ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			//echo "!2.1!=".htmlspecialchars($strSql_tmp)."<br>";

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if ($arRes = $dbRes->Fetch())
				$cnt = $arRes["CNT"];

			$dbRes = new CDBResult();

			//echo "!2.2!=".htmlspecialchars($strSql)."<br>";

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"]) > 0)
				$strSql = "SELECT * FROM (".$strSql.") WHERE ROWNUM<=".$arNavStartParams["nTopCount"];

			//echo "!3!=".htmlspecialchars($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}
}
?>