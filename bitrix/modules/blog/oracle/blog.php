<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/blog/general/blog.php");

class CBlog extends CAllBlog
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

		if (!CBlog::CheckFields("ADD", $arFields))
			return false;

		$db_events = GetModuleEvents("blog", "OnBeforeBlogAdd");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEvent($arEvent, $arFields)===false)
				return false;

		$arInsert = $DB->PrepareInsert("b_blog", $arFields);

		foreach ($arFields1 as $key => $value)
		{
			if (strlen($arInsert[0]) > 0)
				$arInsert[0] .= ", ";
			$arInsert[0] .= $key;
			if (strlen($arInsert[1]) > 0)
				$arInsert[1] .= ", ";
			$arInsert[1] .= $value;
		}

		$ID = false;
		if (strlen($arInsert[0]) > 0)
		{
			$ID = IntVal($DB->NextID("SQ_B_BLOG"));

			$strSql =
				"INSERT INTO b_blog(ID, ".$arInsert[0].") ".
				"VALUES(".$ID.", ".$arInsert[1].")";

			$arBinds = Array();
			if (is_set($arFields, "DESCRIPTION"))
				$arBinds["DESCRIPTION"] = $arFields["DESCRIPTION"];
			$DB->QueryBind($strSql, $arBinds);

			if (is_set($arFields, "PERMS_POST"))
				CBlog::SetBlogPerms($ID, $arFields["PERMS_POST"], BLOG_PERMS_POST);
			if (is_set($arFields, "PERMS_COMMENT"))
				CBlog::SetBlogPerms($ID, $arFields["PERMS_COMMENT"], BLOG_PERMS_COMMENT);

			$events = GetModuleEvents("blog", "OnBlogAdd");
			while ($arEvent = $events->Fetch())
				ExecuteModuleEvent($arEvent, $ID, $arFields);
		}

		if ($ID)
		{
			$arBlog = CBlog::GetByID($ID);

			if (CModule::IncludeModule("search"))
			{
				if ($arBlog["ACTIVE"] == "Y")
				{
					$arGroup = CBlogGroup::GetByID($arBlog["GROUP_ID"]);

					$arPostSite = array(
						$arGroup["SITE_ID"] => CBlog::PreparePath(
								$arBlog["URL"],
								$arGroup["SITE_ID"]
							)
					);

					$arSearchIndex = array(
						"SITE_ID" => $arPostSite,
						"LAST_MODIFIED" => $arBlog["DATE_UPDATE"],
						"PARAM1" => "BLOG",
						"PARAM2" => $arBlog["OWNER_ID"],
						"PERMISSIONS" => array(2),
						"TITLE" => $arBlog["NAME"],
						"BODY" => blogTextParser::killAllTags($arBlog["DESCRIPTION"])
					);

					CSearch::Index("blog", "B".$ID, $arSearchIndex);
				}
			}
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

		if (!CBlog::CheckFields("UPDATE", $arFields, $ID))
			return false;

		$db_events = GetModuleEvents("blog", "OnBeforeBlogUpdate");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEvent($arEvent, $ID, $arFields)===false)
				return false;

		$arBlogOld = CBlog::GetByID($ID);

		$strUpdate = $DB->PrepareUpdate("b_blog", $arFields);

		foreach ($arFields1 as $key => $value)
		{
			if (strlen($strUpdate) > 0)
				$strUpdate .= ", ";
			$strUpdate .= $key."=".$value." ";
		}

		if (strlen($strUpdate) > 0)
		{
			$strSql =
				"UPDATE b_blog SET ".
				"	".$strUpdate." ".
				"WHERE ID = ".$ID." ";

			$arBinds = Array();
			if (is_set($arFields, "DESCRIPTION"))
				$arBinds["DESCRIPTION"] = $arFields["DESCRIPTION"];
			$DB->QueryBind($strSql, $arBinds);

			unset($GLOBALS["BLOG_CACHE_".$ID]);
			unset($GLOBALS["BLOG4OWNER_CACHE_".$arBlogOld["OWNER_ID"]]);

			$events = GetModuleEvents("blog", "OnBlogUpdate");
			while ($arEvent = $events->Fetch())
				ExecuteModuleEvent($arEvent, $ID, $arFields);

			if (is_set($arFields, "PERMS_POST"))
				CBlog::SetBlogPerms($ID, $arFields["PERMS_POST"], BLOG_PERMS_POST);
			if (is_set($arFields, "PERMS_COMMENT"))
				CBlog::SetBlogPerms($ID, $arFields["PERMS_COMMENT"], BLOG_PERMS_COMMENT);
		}
		else
		{
			$ID = False;
		}

		if ($ID)
		{
			$arBlog = CBlog::GetByID($ID);

			if (CModule::IncludeModule("search"))
			{
				if ($arBlogOld["ACTIVE"] == "Y" && $arBlog["ACTIVE"] != "Y")
				{
					CSearch::DeleteIndex("blog", false, "POST", $ID);
					CSearch::DeleteIndex("blog", "B".$ID);
				}
				elseif ($arBlog["ACTIVE"] == "Y")
				{
					$arGroup = CBlogGroup::GetByID($arBlog["GROUP_ID"]);

					$arPostSite = array(
						$arGroup["SITE_ID"] => CBlog::PreparePath(
								$arBlog["URL"],
								$arGroup["SITE_ID"]
							)
					);

					$arSearchIndex = array(
						"SITE_ID" => $arPostSite,
						"LAST_MODIFIED" => $arBlog["DATE_UPDATE"],
						"PARAM1" => "BLOG",
						"PARAM2" => $arBlog["OWNER_ID"],
						"PERMISSIONS" => array(2),
						"TITLE" => $arBlog["NAME"],
						"BODY" => blogTextParser::killAllTags($arBlog["DESCRIPTION"])
					);

					CSearch::Index("blog", "B".$ID, $arSearchIndex);
				}
			}
		}

		return $ID;
	}

	//*************** SELECT *********************/
	function GetList($arOrder = Array("ID" => "DESC"), $arFilter = Array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;

		if (count($arSelectFields) <= 0)
			$arSelectFields = array("ID", "NAME", "DESCRIPTION", "DATE_CREATE", "DATE_UPDATE", "ACTIVE", "OWNER_ID", "URL", "REAL_URL", "GROUP_ID", "ENABLE_COMMENTS", "ENABLE_IMG_VERIF", "ENABLE_RSS", "LAST_POST_ID", "LAST_POST_DATE", "EMAIL_NOTIFY");

		// FIELDS -->
		$arFields = array(
				"ID" => array("FIELD" => "B.ID", "TYPE" => "int"),
				"NAME" => array("FIELD" => "B.NAME", "TYPE" => "string"),
				"DESCRIPTION" => array("FIELD" => "B.DESCRIPTION", "TYPE" => "string"),
				"DATE_CREATE" => array("FIELD" => "B.DATE_CREATE", "TYPE" => "datetime"),
				"DATE_UPDATE" => array("FIELD" => "B.DATE_UPDATE", "TYPE" => "datetime"),
				"ACTIVE" => array("FIELD" => "B.ACTIVE", "TYPE" => "char"),
				"OWNER_ID" => array("FIELD" => "B.OWNER_ID", "TYPE" => "int"),
				"URL" => array("FIELD" => "B.URL", "TYPE" => "string"),
				"REAL_URL" => array("FIELD" => "B.REAL_URL", "TYPE" => "string"),
				"GROUP_ID" => array("FIELD" => "B.GROUP_ID", "TYPE" => "int"),
				"ENABLE_COMMENTS" => array("FIELD" => "B.ENABLE_COMMENTS", "TYPE" => "char"),
				"ENABLE_IMG_VERIF" => array("FIELD" => "B.ENABLE_IMG_VERIF", "TYPE" => "char"),
				"ENABLE_RSS" => array("FIELD" => "B.ENABLE_RSS", "TYPE" => "char"),
				"EMAIL_NOTIFY" => array("FIELD" => "B.EMAIL_NOTIFY", "TYPE" => "char"),
				"LAST_POST_ID" => array("FIELD" => "B.LAST_POST_ID", "TYPE" => "int"),
				"LAST_POST_DATE" => array("FIELD" => "B.LAST_POST_DATE", "TYPE" => "datetime"),
				"AUTO_GROUPS" => array("FIELD" => "B.AUTO_GROUPS", "TYPE" => "string"),

				"OWNER_LOGIN" => array("FIELD" => "U.LOGIN", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (B.OWNER_ID = U.ID)"),
				"OWNER_NAME" => array("FIELD" => "U.NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (B.OWNER_ID = U.ID)"),
				"OWNER_LAST_NAME" => array("FIELD" => "U.LAST_NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (B.OWNER_ID = U.ID)"),
				"OWNER_EMAIL" => array("FIELD" => "U.EMAIL", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (B.OWNER_ID = U.ID)"),
				"OWNER" => array("FIELD" => "U.LOGIN,U.NAME,U.LAST_NAME,U.EMAIL,U.ID", "WHERE_ONLY" => "Y", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (B.OWNER_ID = U.ID)"),

				"GROUP_NAME" => array("FIELD" => "G.NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_blog_group G ON (B.GROUP_ID = G.ID)"),
				"GROUP_SITE_ID" => array("FIELD" => "G.SITE_ID", "TYPE" => "string", "FROM" => "INNER JOIN b_blog_group G ON (B.GROUP_ID = G.ID)"),

				"BLOG_USER_ALIAS" => array("FIELD" => "BU.ALIAS", "TYPE" => "string", "FROM" => "LEFT JOIN b_blog_user BU ON (B.OWNER_ID = BU.USER_ID)"),
				"BLOG_USER_AVATAR" => array("FIELD" => "BU.AVATAR", "TYPE" => "int", "FROM" => "LEFT JOIN b_blog_user BU ON (B.OWNER_ID = BU.USER_ID)"),
			);
		// <-- FIELDS

		$arSqls = CBlog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_blog B ".
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
			"FROM b_blog B ".
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
				"FROM b_blog B ".
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