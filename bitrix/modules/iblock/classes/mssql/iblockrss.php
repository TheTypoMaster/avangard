<?
//IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/general/iblockrss.php");

class CIBlockRSS extends CAllIBlockRSS
{
	function GetCache($cacheKey)
	{
		global $DB;

		$strSql = "
			SELECT 
				CACHE, 
				case when CACHE_DATE>getdate() then 'Y' else 'N' end as VALID 
			FROM 
				b_iblock_cache 
			WHERE 
				CACHE_KEY = '".$DB->ForSql($cacheKey)."' 
			";
		//echo "<pre>$strSql</pre>";
		$db_res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		return $db_res->Fetch();
	}

	function Add($IBLOCK_ID, $NODE, $NODE_VALUE)
	{
		global $DB;
		$IBLOCK_ID = IntVal($IBLOCK_ID);
		$strSql = "
			INSERT INTO b_iblock_rss (
				IBLOCK_ID, 
				NODE, 
				NODE_VALUE
			) VALUES (
				$IBLOCK_ID, 
				'".$DB->ForSql($NODE, 50)."', 
				'".$DB->ForSql($NODE_VALUE, 255)."'
			)
			";
		//echo "<pre>$strSql</pre>";
		$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
	}

	function UpdateCache($cacheKey, $CACHE, $HOURS_CACHE, $bCACHED)
	{
		global $DB;

		$HOURS_CACHE = intval($HOURS_CACHE);
		if ($bCACHED)
		{
			$strSql = "
				UPDATE b_iblock_cache SET 
					CACHE = '".$DB->ForSql($CACHE)."', 
					CACHE_DATE = dateadd(hour, $HOURS_CACHE, getdate())
				WHERE 
					CACHE_KEY = '".$DB->ForSql($cacheKey)."' 
				";
			//echo "<pre>$strSql</pre>";
			$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		}
		else
		{
			$strSql = "
				INSERT INTO b_iblock_cache (
					CACHE_KEY, 
					CACHE, 
					CACHE_DATE
				) VALUES (
					'".$DB->ForSql($cacheKey)."', 
					'".$DB->ForSql($CACHE)."', 
					dateadd(hour, $HOURS_CACHE, getdate())
				) 
				";
			//echo "<pre>$strSql</pre>";
			$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		}
	}

	function GetRSSText($arIBLOCK, $LIMIT_NUM = false, $LIMIT_DAY = false, $yandex = false)
	{
		global $DB;

		$strRes = "";

		$serverName = "";

		if (isset($arIBLOCK["SERVER_NAME"]) && strlen($arIBLOCK["SERVER_NAME"]) > 0)
			$serverName = $arIBLOCK["SERVER_NAME"];

		if (strlen($serverName) <=0 && !isset($arIBLOCK["SERVER_NAME"]))
		{
			$dbSite = CSite::GetList(($b="sort"), ($o="asc"), array("LID" => $arIBLOCK["LID"]));
			if ($arSite = $dbSite->Fetch())
				$serverName = $arSite["SERVER_NAME"];
		}

		if (strlen($serverName) <=0)
		{
			if (defined("SITE_SERVER_NAME") && strlen(SITE_SERVER_NAME)>0)
				$serverName = SITE_SERVER_NAME;
			else
				$serverName = COption::GetOptionString("main", "server_name", "www.bitrixsoft.com");
		}

		$strRes .= "<channel>\n";
		$strRes .= "<title>".htmlspecialchars($arIBLOCK["NAME"])."</title>\n";
		$strRes .= "<link>http://".$serverName."</link>\n";
		$strRes .= "<description>".htmlspecialchars($arIBLOCK["DESCRIPTION"])."</description>\n";
		$strRes .= "<lastBuildDate>".date("r")."</lastBuildDate>\n";
		$strRes .= "<ttl>".$arIBLOCK["RSS_TTL"]."</ttl>\n";

		if (IntVal($arIBLOCK["PICTURE"])>0)
		{
			$db_img = CFile::GetByID($arIBLOCK["PICTURE"]);
			$db_img_arr = $db_img->Fetch();
			if ($db_img_arr)
			{
				$strRes .= "<image>\n";
				$strRes .= "<title>".htmlspecialchars($arIBLOCK["NAME"])."</title>\n";
				$strImage = "http://".$serverName."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$db_img_arr["SUBDIR"]."/".$db_img_arr["FILE_NAME"];
				$strRes .= "<url>".$strImage."</url>\n";
				$strRes .= "<link>http://".$serverName."</link>\n";
				$strRes .= "<width>".$db_img_arr["WIDTH"]."</width>\n";
				$strRes .= "<height>".$db_img_arr["HEIGHT"]."</height>\n";
				$strRes .= "</image>\n";
			}
		}

		$arNodes = array();
		$db_res = $DB->Query("SELECT NODE, NODE_VALUE FROM b_iblock_rss WHERE IBLOCK_ID = ".intval($arIBLOCK["ID"]), false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		while ($db_res_arr = $db_res->Fetch())
		{
			$arNodes[$db_res_arr["NODE"]] = $db_res_arr["NODE_VALUE"];
		}

		$strSql = "
			SELECT
				BE.*,
				".$DB->DateToCharFunction("BE.TIMESTAMP_X")." TIMESTAMP_X,
				BE.ACTIVE_FROM, ".$DB->DateToCharFunction("BE.ACTIVE_FROM", "FULL")." ACTIVE_FROM,
				".$DB->DateToCharFunction("BE.ACTIVE_TO", "FULL")." ACTIVE_TO, 
				L.DIR LANG_DIR,
				B.DETAIL_PAGE_URL,
				B.LIST_PAGE_URL, B.LID, L.SERVER_NAME
			FROM 
				b_iblock_element BE,
				b_iblock B,
				b_lang L
			WHERE
				BE.IBLOCK_ID = B.ID
			and BE.WF_STATUS_ID = 1
			and BE.WF_PARENT_ELEMENT_ID is null
			and B.LID = L.LID
			and BE.IBLOCK_ID = ".intval($arIBLOCK["ID"])."
			and (
					(BE.ACTIVE_TO >= ".$DB->GetNowFunction()." or BE.ACTIVE_TO IS NULL) and
					(BE.ACTIVE_FROM <= ".$DB->GetNowFunction()." or BE.ACTIVE_FROM IS NULL)
				)
			and BE.ACTIVE = 'Y'
			and exists(
					SELECT
						'x'
					FROM
						b_iblock_group IBG
					WHERE
						IBG.IBLOCK_ID=B.ID
						and IBG.GROUP_ID IN (2)
						and IBG.PERMISSION>='R'
						and (IBG.PERMISSION='X' or B.ACTIVE='Y')
					)
			";

		if ($LIMIT_DAY!==false)
		{
			$stmp = AddToTimeStamp(array("DD" => -intval($LIMIT_DAY)), time());
			$date = ConvertTimeStamp($stmp, "FULL");
			$strSql .= " and (BE.ACTIVE_FROM >= ".$DB->CharToDateFunction($date, "FULL")." or BE.ACTIVE_FROM IS NULL) ";
		}
		$strSql .= "ORDER BY BE.ACTIVE_FROM DESC, BE.SORT ASC ";

		//echo "<pre>$strSql</pre>";
		$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

		$items = new CIBlockResult($res->result);
		if ($LIMIT_NUM!==False && IntVal($LIMIT_NUM)>0)
			$items->NavStart($LIMIT_NUM);

		while ($arItem = $items->GetNext())
		{
			$props = CIBlockElement::GetProperty($arIBLOCK["ID"], $arItem["ID"], "sort", "asc", Array("ACTIVE"=>"Y", "NON_EMPTY"=>"Y"));
			$arProps = Array();
			while ($arProp = $props->Fetch())
			{
				if (strlen($arProp["CODE"])>0)
					$arProps[$arProp["CODE"]] = Array("NAME"=>htmlspecialchars($arProp["NAME"]), "VALUE"=>htmlspecialchars($arProp["VALUE"]));
				else
					$arProps[$arProp["ID"]] = Array("NAME"=>htmlspecialchars($arProp["NAME"]), "VALUE"=>htmlspecialchars($arProp["VALUE"]));
			}

			$arLinkProp = $arProps["DOC_LINK"];

			$strRes .= "<item>\n";
			if (strlen($arNodes["title"])>0)
			{
				$strRes .= "<title>".htmlspecialchars(CIBlockRSS::ExtractProperties($arNodes["title"], $arProps, $arItem))."</title>\n";
			}
			else
			{
				$strRes .= "<title>".htmlspecialchars($arItem["NAME"])."</title>\n";
			}
			if (strlen($arNodes["link"])>0)
			{
				$strRes .= "<link>".htmlspecialchars(CIBlockRSS::ExtractProperties($arNodes["link"], $arProps, $arItem))."</link>\n";
			}
			else
			{
				$strRes .= "<link>http://".$serverName.(($arLinkProp["VALUE"]) ? $arLinkProp["VALUE"] : htmlspecialchars($arItem["DETAIL_PAGE_URL"]))."</link>\n";
			}
			if (strlen($arNodes["description"])>0)
			{
				$strRes .= "<description>".htmlspecialchars(CIBlockRSS::ExtractProperties($arNodes["description"], $arProps, $arItem))."</description>\n";
			}
			else
			{
				$strRes .= "<description>".(($arItem["PREVIEW_TEXT"] || $yandex) ? htmlspecialchars($arItem["PREVIEW_TEXT"]) : htmlspecialchars($arItem["DETAIL_TEXT"]))."</description>\n";
			}
			if (strlen($arNodes["enclosure"])>0)
			{
				$strRes .= "<enclosure url=\"".htmlspecialchars(CIBlockRSS::ExtractProperties($arNodes["enclosure"], $arProps, $arItem))."\" length=\"".htmlspecialchars(CIBlockRSS::ExtractProperties($arNodes["enclosure_length"], $arProps, $arItem))."\" type=\"".htmlspecialchars(CIBlockRSS::ExtractProperties($arNodes["enclosure_type"], $arProps, $arItem))."\"/>\n";
			}
			else
			{
				if (IntVal($arItem["PREVIEW_PICTURE"])>0)
				{
					$db_img = CFile::GetByID($arItem["PREVIEW_PICTURE"]);
					$db_img_arr = $db_img->Fetch();
					if ($db_img_arr)
					{
						$strImage = "http://".$serverName."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$db_img_arr["SUBDIR"]."/".$db_img_arr["FILE_NAME"];
						$strRes .= "<enclosure url=\"".$strImage."\" length=\"".$db_img_arr["FILE_SIZE"]."\" type=\"".$db_img_arr["CONTENT_TYPE"]."\" width=\"".$db_img_arr["WIDTH"]."\" height=\"".$db_img_arr["HEIGHT"]."\"/>\n";
					}
				}
			}
			if (strlen($arNodes["category"])>0)
			{
				$strRes .= "<category>".htmlspecialchars(CIBlockRSS::ExtractProperties($arNodes["category"], $arProps, $arItem))."</category>\n";
			}
			else
			{
				$strPath = "";
				$nav = CIBlockSection::GetNavChain($arIBLOCK["ID"], $arItem["IBLOCK_SECTION_ID"]);
				while ($nav->ExtractFields("nav_"))
				{
					$strPath .= $GLOBALS["nav_NAME"]."/";
				}
				if (strlen($strPath)>0)
				{
					$strRes .= "<category>".htmlspecialchars($strPath)."</category>\n";
				}
			}
			if ($yandex)
			{
				$strRes .= "<yandex:full-text>".htmlspecialchars($arItem["DETAIL_TEXT"])."</yandex:full-text>\n";
			}
			if (strlen($arNodes["pubDate"])>0)
			{
				$strRes .= "<pubDate>".htmlspecialchars(CIBlockRSS::ExtractProperties($arNodes["pubDate"], $arProps, $arItem))."</pubDate>\n";
			}
			else
			{
				if (strlen($arItem["ACTIVE_FROM"])>0)
				{
					$strRes .= "<pubDate>".date("r", MkDateTime($DB->FormatDate($arItem["ACTIVE_FROM"], Clang::GetDateFormat("FULL"), "DD.MM.YYYY H:I:S"), "d.m.Y H:i:s"))."</pubDate>\n";
				}
				else
				{
					$strRes .= "<pubDate>".date("r")."</pubDate>\n";
				}
			}
			$strRes .= "</item>\n";
		}
		$strRes .= "</channel>\n";
		return $strRes;
	}


	// типа агент
	function PreGenerateRSS($IBLOCK_ID, $yandex = true)
	{
		global $DB;

		$IBLOCK_ID = intval($IBLOCK_ID);
		$strSql = "
			SELECT 
				B.*, 
				S.CHARSET, 
				S.SERVER_NAME, 
				".$DB->DateToCharFunction("B.TIMESTAMP_X")." as TIMESTAMP_X 
			FROM 
				b_iblock B 
			LEFT JOIN b_lang S ON S.LID=B.LID 
			WHERE 
				ID = $IBLOCK_ID
			and exists(
				SELECT 
					'x' 
				FROM 
					b_iblock_group IBG 
				WHERE 
					IBG.IBLOCK_ID=B.ID
				and IBG.GROUP_ID IN (2)
				and IBG.PERMISSION>='R'
				and (IBG.PERMISSION='X' or B.ACTIVE='Y') 
				)
			";
		//echo "<pre>$strSql</pre>";
		$dbr = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		$bAccessable = False;
		if (($arIBlock = $dbr->GetNext()) && ($arIBlock["RSS_FILE_ACTIVE"]=="Y" && !$yandex || $arIBlock["RSS_YANDEX_ACTIVE"]=="Y" && $yandex))
			$bAccessable = True;

		if (!$bAccessable) return "";

		$strRes = "";
		$strRes .= "<"."?xml version=\"1.0\" encoding=\"".$arIBlock["CHARSET"]."\"?".">\n";
		$strRes .= "<rss version=\"2.0\"";
//		$strRes .= "<rss version=\"2.0\" xmlns=\"http://backend.userland.com/rss2\"";
		if ($yandex) $strRes .= " xmlns:yandex=\"http://news.yandex.ru\"";
		$strRes .= ">\n";

		$limit_num = false;
		$limit_day = 2;
		if (!$yandex)
		{
			$limit_num = false;
			if (strlen($arIBlock["RSS_FILE_LIMIT"])>0)
				$limit_num = IntVal($arIBlock["RSS_FILE_LIMIT"]);

			$limit_day = false;
			if (strlen($arIBlock["RSS_FILE_DAYS"])>0)
				$limit_day = IntVal($arIBlock["RSS_FILE_DAYS"]);
		}
		$strRes .= CIBlockRSS::GetRSSText($arIBlock, $limit_num, $limit_day, $yandex);

		$strRes .= "</rss>\n";

		$rss_file = $_SERVER["DOCUMENT_ROOT"].COption::GetOptionString("iblock", "path2rss", "/upload/");
		if ($yandex)
			$rss_file .= "yandex_rss_".IntVal($arIBlock["ID"]).".xml";
		else
			$rss_file .= "iblock_rss_".IntVal($arIBlock["ID"]).".xml";
		$fp = fopen($rss_file, "w");
		fwrite($fp, $strRes);
		fclose($fp);

		global $pPERIOD;
		$pPERIOD = IntVal($arIBlock["RSS_TTL"])*60*60;
		return "CIBlockRSS::PreGenerateRSS(".$IBLOCK_ID.", ".($yandex?"true":"false").");";
	}

}
?>