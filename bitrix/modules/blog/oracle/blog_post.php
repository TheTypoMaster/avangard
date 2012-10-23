<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/blog/general/blog_post.php");

class CBlogPost extends CAllBlogPost
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

		if (!CBlogPost::CheckFields("ADD", $arFields))
			return false;

		$arInsert = $DB->PrepareInsert("b_blog_post", $arFields, "blog/".$arFields["URL"]);

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
			$ID = IntVal($DB->NextID("SQ_B_BLOG_POST"));

			$strSql =
				"INSERT INTO b_blog_post(ID, ".$arInsert[0].") ".
				"VALUES(".$ID.", ".$arInsert[1].")";

			$arBinds = Array();
			if (is_set($arFields, "DETAIL_TEXT"))
				$arBinds["DETAIL_TEXT"] = $arFields["DETAIL_TEXT"];
			$DB->QueryBind($strSql, $arBinds);
		}

		if ($ID)
		{
			$arPost = CBlogPost::GetByID($ID);
			CBlog::SetStat($arPost["BLOG_ID"]);

			if (is_set($arFields, "PERMS_POST"))
				CBlogPost::SetPostPerms($ID, $arFields["PERMS_POST"], BLOG_PERMS_POST);
			if (is_set($arFields, "PERMS_COMMENT"))
				CBlogPost::SetPostPerms($ID, $arFields["PERMS_COMMENT"], BLOG_PERMS_COMMENT);

			if (CModule::IncludeModule("search"))
			{
				if ($arPost["DATE_PUBLISHED"] == "Y"
					&& $arPost["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_PUBLISH
					&& CBlogUserGroup::GetGroupPerms(1, $arPost["BLOG_ID"], $ID, BLOG_PERMS_POST) >= BLOG_PERMS_READ)
				{
					$arBlog = CBlog::GetByID($arPost["BLOG_ID"]);
					$arGroup = CBlogGroup::GetByID($arBlog["GROUP_ID"]);

					$arPostSite = array(
						$arGroup["SITE_ID"] => CBlogPost::PreparePath(
								$arBlog["URL"],
								$arPost["ID"],
								$arGroup["SITE_ID"]
							)
					);

					$arSearchIndex = array(
						"SITE_ID" => $arPostSite,
						"LAST_MODIFIED" => $arPost["DATE_PUBLISH"],
						"PARAM1" => "POST",
						"PARAM2" => $arPost["BLOG_ID"],
						"PARAM3" => $arPost["ID"],
						"PERMISSIONS" => array(2),
						"TITLE" => $arPost["TITLE"],
						"BODY" => blogTextParser::killAllTags($arPost["DETAIL_TEXT"])
					);

					CSearch::Index("blog", "P".$ID, $arSearchIndex);
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

		if (!CBlogPost::CheckFields("UPDATE", $arFields, $ID))
			return false;

		$arOldPost = CBlogPost::GetByID($ID);
		$filePath = $arOldPost["URL"];
		if (isset($arFields["URL"]) && strlen($arFields["URL"]) > 0)
			$filePath = $arFields["URL"];

		$strUpdate = $DB->PrepareUpdate("b_blog_post", $arFields, "blog/".$filePath);

		foreach ($arFields1 as $key => $value)
		{
			if (strlen($strUpdate) > 0)
				$strUpdate .= ", ";
			$strUpdate .= $key."=".$value." ";
		}

		if (strlen($strUpdate) > 0)
		{
			$oldPostPerms = CBlogUserGroup::GetGroupPerms(1, $arOldPost["BLOG_ID"], $ID, BLOG_PERMS_POST);

			$strSql =
				"UPDATE b_blog_post SET ".
				"	".$strUpdate." ".
				"WHERE ID = ".$ID." ";

			$arBinds = Array();
			if (is_set($arFields, "DETAIL_TEXT"))
				$arBinds["DETAIL_TEXT"] = $arFields["DETAIL_TEXT"];
			$DB->QueryBind($strSql, $arBinds);

			unset($GLOBALS["BLOG_POST_CACHE_".$ID]);
		}
		else
		{
			$ID = False;
		}

		if ($ID)
		{
			$arNewPost = CBlogPost::GetByID($ID);
			CBlog::SetStat($arNewPost["BLOG_ID"]);
			if ($arNewPost["BLOG_ID"] != $arOldPost["BLOG_ID"])
				CBlog::SetStat($arOldPost["BLOG_ID"]);

			if (is_set($arFields, "PERMS_POST"))
				CBlogPost::SetPostPerms($ID, $arFields["PERMS_POST"], BLOG_PERMS_POST);
			if (is_set($arFields, "PERMS_COMMENT"))
				CBlogPost::SetPostPerms($ID, $arFields["PERMS_COMMENT"], BLOG_PERMS_COMMENT);

			if (CModule::IncludeModule("search"))
			{
				$newPostPerms = CBlogUserGroup::GetGroupPerms(1, $arNewPost["BLOG_ID"], $ID, BLOG_PERMS_POST);

				if (
					$arOldPost["DATE_PUBLISHED"] == "Y"
					&& $arOldPost["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_PUBLISH
					&& $oldPostPerms >= BLOG_PERMS_READ
					&& (
						$arNewPost["DATE_PUBLISHED"] != "Y"
						|| $arNewPost["PUBLISH_STATUS"] != BLOG_PUBLISH_STATUS_PUBLISH
						|| $newPostPerms < BLOG_PERMS_READ
						)
					)
				{
					CSearch::Index("blog", "P".$ID,
						array(
							"TITLE" => "",
							"BODY" => ""
						)
					);
				}
				elseif (
					$arNewPost["DATE_PUBLISHED"] == "Y"
					&& $arNewPost["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_PUBLISH
					&& $newPostPerms >= BLOG_PERMS_READ
					)
				{
					$arBlog = CBlog::GetByID($arNewPost["BLOG_ID"]);
					$arGroup = CBlogGroup::GetByID($arBlog["GROUP_ID"]);

					$arPostSite = array(
						$arGroup["SITE_ID"] => CBlogPost::PreparePath(
								$arBlog["URL"],
								$arNewPost["ID"],
								$arGroup["SITE_ID"]
							)
					);

					$arSearchIndex = array(
						"SITE_ID" => $arPostSite,
						"LAST_MODIFIED" => $arNewPost["DATE_PUBLISH"],
						"PARAM1" => "POST",
						"PARAM2" => $arNewPost["BLOG_ID"],
						"PARAM3" => $arNewPost["ID"],
						"PERMISSIONS" => array(2),
						"TITLE" => $arNewPost["TITLE"],
						"BODY" => blogTextParser::killAllTags($arNewPost["DETAIL_TEXT"])
					);

					CSearch::Index("blog", "P".$ID, $arSearchIndex, True);
				}
			}
		}

		return $ID;
	}

	//*************** SELECT *********************/
	function GetByID($ID)
	{
		global $DB;

		$ID = IntVal($ID);

		if (isset($GLOBALS["BLOG_POST_CACHE_".$ID]) && is_array($GLOBALS["BLOG_POST_CACHE_".$ID]) && is_set($GLOBALS["BLOG_POST_CACHE_".$ID], "ID"))
		{
			return $GLOBALS["BLOG_POST_CACHE_".$ID];
		}
		else
		{
			$strSql =
				"SELECT P.ID, P.TITLE, P.BLOG_ID, P.AUTHOR_ID, P.PREVIEW_TEXT, ".
				"	P.PREVIEW_TEXT_TYPE, P.DETAIL_TEXT, P.DETAIL_TEXT_TYPE, P.KEYWORDS, ".
				"	P.PUBLISH_STATUS, P.CATEGORY_ID, P.ATRIBUTE, P.ENABLE_TRACKBACK, ".
				"	P.ENABLE_COMMENTS, DECODE(SIGN(SYSDATE - P.DATE_PUBLISH), 1, 'Y', 'N') as DATE_PUBLISHED, ".
				"	P.ATTACH_IMG, P.NUM_COMMENTS, P.NUM_TRACKBACKS, ".
				"	".$DB->DateToCharFunction("P.DATE_CREATE", "FULL")." as DATE_CREATE, ".
				"	".$DB->DateToCharFunction("P.DATE_PUBLISH", "FULL")." as DATE_PUBLISH ".
				"FROM b_blog_post P ".
				"WHERE P.ID = ".$ID."";
			$dbResult = $DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arResult = $dbResult->Fetch())
			{
				$GLOBALS["BLOG_POST_CACHE_".$ID] = $arResult;
				return $arResult;
			}
		}

		return False;
	}

	function GetList($arOrder = Array("ID" => "DESC"), $arFilter = Array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;

		if (isset($arFilter["DATE_PUBLISH_DAY"]) && isset($arFilter["DATE_PUBLISH_MONTH"]) && isset($arFilter["DATE_PUBLISH_YEAR"]))
		{
			if (strlen($arFilter["DATE_PUBLISH_YEAR"]) == 2)
				$arFilter["DATE_PUBLISH_YEAR"] = "20".$arFilter["DATE_PUBLISH_YEAR"];
			$date1 = mktime(0, 0, 0, $arFilter["DATE_PUBLISH_MONTH"], $arFilter["DATE_PUBLISH_DAY"], $arFilter["DATE_PUBLISH_YEAR"]);
			$date2 = mktime(0, 0, 0, $arFilter["DATE_PUBLISH_MONTH"], $arFilter["DATE_PUBLISH_DAY"] + 1, $arFilter["DATE_PUBLISH_YEAR"]);
			$arFilter[">=DATE_PUBLISH"] = ConvertTimeStamp($date1, "SHORT", SITE_ID);
			$arFilter["<DATE_PUBLISH"] = ConvertTimeStamp($date2, "SHORT", SITE_ID);

			unset($arFilter["DATE_PUBLISH_DAY"]);
			unset($arFilter["DATE_PUBLISH_MONTH"]);
			unset($arFilter["DATE_PUBLISH_YEAR"]);
		}
		elseif (isset($arFilter["DATE_PUBLISH_MONTH"]) && isset($arFilter["DATE_PUBLISH_YEAR"]))
		{
			if (strlen($arFilter["DATE_PUBLISH_YEAR"]) == 2)
				$arFilter["DATE_PUBLISH_YEAR"] = "20".$arFilter["DATE_PUBLISH_YEAR"];
			$date1 = mktime(0, 0, 0, $arFilter["DATE_PUBLISH_MONTH"], 1, $arFilter["DATE_PUBLISH_YEAR"]);
			$date2 = mktime(0, 0, 0, $arFilter["DATE_PUBLISH_MONTH"] + 1, 1, $arFilter["DATE_PUBLISH_YEAR"]);
			$arFilter[">=DATE_PUBLISH"] = ConvertTimeStamp($date1, "SHORT", SITE_ID);
			$arFilter["<DATE_PUBLISH"] = ConvertTimeStamp($date2, "SHORT", SITE_ID);

			unset($arFilter["DATE_PUBLISH_MONTH"]);
			unset($arFilter["DATE_PUBLISH_YEAR"]);
		}
		elseif (isset($arFilter["DATE_PUBLISH_YEAR"]))
		{
			if (strlen($arFilter["DATE_PUBLISH_YEAR"]) == 2)
				$arFilter["DATE_PUBLISH_YEAR"] = "20".$arFilter["DATE_PUBLISH_YEAR"];
			$date1 = mktime(0, 0, 0, 1, 1, $arFilter["DATE_PUBLISH_YEAR"]);
			$date2 = mktime(0, 0, 0, 1, 1, $arFilter["DATE_PUBLISH_YEAR"] + 1);
			$arFilter[">=DATE_PUBLISH"] = ConvertTimeStamp($date1, "SHORT", SITE_ID);
			$arFilter["<DATE_PUBLISH"] = ConvertTimeStamp($date2, "SHORT", SITE_ID);

			unset($arFilter["DATE_PUBLISH_YEAR"]);
		}

		if (count($arSelectFields) <= 0)
			$arSelectFields = array("ID", "TITLE", "BLOG_ID", "AUTHOR_ID", "PREVIEW_TEXT", "PREVIEW_TEXT_TYPE", "DETAIL_TEXT", "DETAIL_TEXT_TYPE", "DATE_CREATE", "DATE_PUBLISH", "KEYWORDS", "PUBLISH_STATUS", "CATEGORY_ID", "ATRIBUTE", "ATTACH_IMG", "ENABLE_TRACKBACK", "ENABLE_COMMENTS");

		// FIELDS -->
		$arFields = array(
			"ID" => array("FIELD" => "P.ID", "TYPE" => "int"),
			"TITLE" => array("FIELD" => "P.TITLE", "TYPE" => "string"),
			"BLOG_ID" => array("FIELD" => "P.BLOG_ID", "TYPE" => "int"),
			"AUTHOR_ID" => array("FIELD" => "P.AUTHOR_ID", "TYPE" => "int"),
			"PREVIEW_TEXT" => array("FIELD" => "P.PREVIEW_TEXT", "TYPE" => "string"),
			"PREVIEW_TEXT_TYPE" => array("FIELD" => "P.PREVIEW_TEXT_TYPE", "TYPE" => "string"),
			"DETAIL_TEXT" => array("FIELD" => "P.DETAIL_TEXT", "TYPE" => "string"),
			"DETAIL_TEXT_TYPE" => array("FIELD" => "P.DETAIL_TEXT_TYPE", "TYPE" => "string"),
			"DATE_CREATE" => array("FIELD" => "P.DATE_CREATE", "TYPE" => "datetime"),
			"DATE_PUBLISH" => array("FIELD" => "P.DATE_PUBLISH", "TYPE" => "datetime"),
			"KEYWORDS" => array("FIELD" => "P.KEYWORDS", "TYPE" => "string"),
			"PUBLISH_STATUS" => array("FIELD" => "P.PUBLISH_STATUS", "TYPE" => "string"),
			"CATEGORY_ID" => array("FIELD" => "P.CATEGORY_ID", "TYPE" => "int"),
			"ATRIBUTE" => array("FIELD" => "P.ATRIBUTE", "TYPE" => "string"),
			"ATTACH_IMG" => array("FIELD" => "P.ATTACH_IMG", "TYPE" => "int"),
			"ENABLE_TRACKBACK" => array("FIELD" => "P.ENABLE_TRACKBACK", "TYPE" => "string"),
			"ENABLE_COMMENTS" => array("FIELD" => "P.ENABLE_COMMENTS", "TYPE" => "string"),
			"NUM_COMMENTS" => array("FIELD" => "P.NUM_COMMENTS", "TYPE" => "int"),
			"NUM_TRACKBACKS" => array("FIELD" => "P.NUM_TRACKBACKS", "TYPE" => "int"),

			"PERMS" => array(),

			"AUTHOR_LOGIN" => array("FIELD" => "U.LOGIN", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (P.AUTHOR_ID = U.ID)"),
			"AUTHOR_NAME" => array("FIELD" => "U.NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (P.AUTHOR_ID = U.ID)"),
			"AUTHOR_LAST_NAME" => array("FIELD" => "U.LAST_NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (P.AUTHOR_ID = U.ID)"),
			"AUTHOR_EMAIL" => array("FIELD" => "U.EMAIL", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (P.AUTHOR_ID = U.ID)"),
			"AUTHOR" => array("FIELD" => "U.LOGIN,U.NAME,U.LAST_NAME,U.EMAIL,U.ID", "WHERE_ONLY" => "Y", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (P.AUTHOR_ID = U.ID)"),

			"CATEGORY_NAME" => array("FIELD" => "PC.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_blog_category PC ON (P.BLOG_ID = PC.BLOG_ID AND P.CATEGORY_ID = PC.ID)"),

			"BLOG_USER_ALIAS" => array("FIELD" => "BU.ALIAS", "TYPE" => "string", "FROM" => "LEFT JOIN b_blog_user BU ON (P.AUTHOR_ID = BU.USER_ID)"),
			"BLOG_USER_AVATAR" => array("FIELD" => "BU.AVATAR", "TYPE" => "int", "FROM" => "LEFT JOIN b_blog_user BU ON (P.AUTHOR_ID = BU.USER_ID)"),
			
			"BLOG_URL" => array("FIELD" => "B.URL", "TYPE" => "string", "FROM" => "INNER JOIN b_blog B ON (P.BLOG_ID = B.ID)"),
			"BLOG_ACTIVE" => array("FIELD" => "B.ACTIVE", "TYPE" => "string", "FROM" => "INNER JOIN b_blog B ON (P.BLOG_ID = B.ID)"),
			"BLOG_GROUP_ID" => array("FIELD" => "B.GROUP_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_blog B ON (P.BLOG_ID = B.ID)"),
			"BLOG_GROUP_SITE_ID" => array("FIELD" => "BG.SITE_ID", "TYPE" => "string", "FROM" => "INNER JOIN b_blog_group BG ON (B.GROUP_ID = BG.ID)"),
		);
		
		if(isset($arFilter["GROUP_CHECK_PERMS"]))
		{
			if(is_array($arFilter["GROUP_CHECK_PERMS"]))
			{
				foreach($arFilter["GROUP_CHECK_PERMS"] as $val)
				{
					if(IntVal($val)>0)
					{
						$arFields["POST_PERM_".$val] = Array(
								"FIELD" => "BUGP".$val.".PERMS", 
								"TYPE" => "string", 
								"FROM" => "LEFT JOIN b_blog_user_group_perms BUGP".$val." 
											ON (P.BLOG_ID = BUGP".$val.".BLOG_ID 
												AND P.ID = BUGP".$val.".POST_ID 
												AND BUGP".$val.".USER_GROUP_ID = ".$val." 
												AND BUGP".$val.".PERMS_TYPE = 'P')"
							);
						$arSelectFields[] = "POST_PERM_".$val;
					}
				}
			}
			else
			{
				if(IntVal($arFilter["GROUP_CHECK_PERMS"])>0)
				{
					$arFields["POST_PERM_".$arFilter["GROUP_CHECK_PERMS"]] = Array(
							"FIELD" => "BUGP.PERMS", 
							"TYPE" => "string", 
							"FROM" => "LEFT JOIN b_blog_user_group_perms BUGP 
										ON (P.BLOG_ID = BUGP.BLOG_ID 
											AND P.ID = BUGP.POST_ID 
											AND BUGP.USER_GROUP_ID = ".$arFilter["GROUP_CHECK_PERMS"]." 
											AND BUGP.PERMS_TYPE = 'P')"
						);
					$arSelectFields[] = "POST_PERM_".$arFilter["GROUP_CHECK_PERMS"];
				}
			}
			unset($arFilter["GROUP_CHECK_PERMS"]);
		}

		// <-- FIELDS

		$blogModulePermissions = $GLOBALS["APPLICATION"]->GetGroupRight("blog");
		if ($blogModulePermissions < "W")
		{
			if(!CBlog::IsBlogOwner($arFilter["BLOG_ID"], $GLOBALS["USER"]->GetID()))
			{
				$arUserGroups = CBlogUser::GetUserGroups(($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : 0), $arFilter["BLOG_ID"], "Y", BLOG_BY_USER_ID);
				$strUserGroups = "0";
				for ($i = 0; $i < count($arUserGroups); $i++)
					$strUserGroups .= ",".IntVal($arUserGroups[$i]);

				$arFields["PERMS"] = array("FIELD" => "UGP.PERMS", "TYPE" => "string", "FROM" => "INNER JOIN b_blog_user_group_perms UGP ON (P.ID = UGP.POST_ID AND P.BLOG_ID = UGP.BLOG_ID AND UGP.USER_GROUP_ID IN (".$strUserGroups.") AND UGP.PERMS_TYPE = '".BLOG_PERMS_POST."')");
			}
			else
				$arFields["PERMS"] = array("FIELD" => "'W'", "TYPE" => "string");
		}
		else
		{
			$arFields["PERMS"] = array("FIELD" => "'W'", "TYPE" => "string");
		}

		$arSqls = CBlog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_blog_post P ".
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
			"FROM b_blog_post P ".
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
				"FROM b_blog_post P ".
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

	function GetListCalendar($blogID, $year = false, $month = false, $day = false)
	{
		global $DB;

		$blogID = IntVal($blogID);

		if ($year)
			if (strlen($year) == 2)
				$year = "20".$year;

		if ($year && $month && $day)
		{
			$date1 = mktime(0, 0, 0, $month, $day, $year);
			$date2 = mktime(0, 0, 0, $month, $day + 1, $year);
		}
		elseif ($month && $year)
		{
			$date1 = mktime(0, 0, 0, $month, 1, $year);
			$date2 = mktime(0, 0, 0, $month + 1, 1, $year);
		}
		elseif ($year)
		{
			$date1 = mktime(0, 0, 0, 1, 1, $year);
			$date2 = mktime(0, 0, 0, 1, 1, $year + 1);
		}
		$datePublishFrom = ConvertTimeStamp($date1, "SHORT", SITE_ID);
		$datePublishTo = ConvertTimeStamp($date2, "SHORT", SITE_ID);

		$arUserGroups = CBlogUser::GetUserGroups(($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : 0), $arFilter["BLOG_ID"], "Y", BLOG_BY_USER_ID);
		$strUserGroups = "0";
		for ($i = 0; $i < count($arUserGroups); $i++)
			$strUserGroups .= ",".IntVal($arUserGroups[$i]);

		$strFromPerms =
			"	LEFT JOIN b_blog_user_group_perms UGP ".
			"		ON (P.ID = UGP.POST_ID ".
			"			AND P.BLOG_ID = UGP.BLOG_ID ".
			"			AND UGP.USER_GROUP_ID IN (".$strUserGroups.") ".
			"			AND UGP.PERMS_TYPE = '".$DB->ForSql(BLOG_PERMS_POST)."') ";
		$strWherePerms = " AND (UGP.PERMS > 'D') ";

		$blogModulePermissions = $GLOBALS["APPLICATION"]->GetGroupRight("blog");
		if ($blogModulePermissions >= "W")
		{
			$strFromPerms = "";
			$strWherePerms = "";
		}

		$strSql = 
			"SELECT to_char(P.DATE_PUBLISH, 'YYYY-MM-DD') as DATE_PUBLISH1, COUNT(P.ID) as CNT ".
			"FROM b_blog_post P ".$strFromPerms." ".
			"WHERE P.BLOG_ID = ".$blogID." ".
			"	AND P.DATE_PUBLISH >= ".$DB->CharToDateFunction($DB->ForSql($datePublishFrom), "SHORT")." ".
			"	AND P.DATE_PUBLISH < ".$DB->CharToDateFunction($DB->ForSql($datePublishTo), "SHORT")." ".
			"	AND P.PUBLISH_STATUS = '".$DB->ForSql(BLOG_PUBLISH_STATUS_PUBLISH)."' ".
			"	".$strWherePerms." ".
			"GROUP BY to_char(P.DATE_PUBLISH, 'YYYY-MM-DD') ".
			"ORDER BY DATE_PUBLISH1 ";
		$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$arResult = array();
		while ($arRes = $dbRes->Fetch())
		{
			$arDate = explode("-", $arRes["DATE_PUBLISH1"]);
			$arResult[] = array(
				"YEAR" => $arDate[0],
				"MONTH" => $arDate[1],
				"DAY" => $arDate[2],
				"DATE" => ConvertTimeStamp(mktime(0, 0, 0, $arDate[1], $arDate[2], $arDate[0]), "SHORT", LANG)
			);
		}

		return $arResult;
	}
}
?>