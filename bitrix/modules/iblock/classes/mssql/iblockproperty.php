<?
//IncludeModuleLangFile(__FILE__);
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/general/iblockproperty.php");

class CIBlockProperty extends CAllIBlockProperty
{
	function _Update($ID, $arFields)
	{	
		global $DB;
		$ID=intval($ID);
		$rsProperty = $this->GetByID($ID);
		$arProperty = $rsProperty->Fetch();
		if(!$arProperty)
		{
			$this->LAST_ERROR = GetMessage("IBLOCK_PROPERTY_NOT_FOUND",array("#ID#"=>$ID));
			return false;
		}
		if($arProperty["VERSION"]!=2)
		{
			return true;
		}
		if(is_set($arFields, "MULTIPLE") && $arFields["MULTIPLE"]!=$arProperty["MULTIPLE"])
		{//MULTIPLE changed
			if($arFields["MULTIPLE"]=="Y")
			{//MULTIPLE=Y
				$strSql = "
					INSERT INTO b_iblock_element_prop_m".$arProperty["IBLOCK_ID"]."
					(IBLOCK_ELEMENT_ID, IBLOCK_PROPERTY_ID, VALUE, VALUE_ENUM, VALUE_NUM, DESCRIPTION)
					SELECT
						IBLOCK_ELEMENT_ID
						,".$arProperty["ID"]."
						,PROPERTY_".$arProperty["ID"]."
						,".($arProperty["PROPERTY_TYPE"]=="S" || $arProperty["PROPERTY_TYPE"]=="s"?
							"0":
							"PROPERTY_".$arProperty["ID"])."
						,".($arProperty["PROPERTY_TYPE"]=="S" || $arProperty["PROPERTY_TYPE"]=="s"?
							"0":
							"PROPERTY_".$arProperty["ID"])."
						,DESCRIPTION_".$arProperty["ID"]."
					FROM
						b_iblock_element_prop_s".$arProperty["IBLOCK_ID"]."
					WHERE
						PROPERTY_".$arProperty["ID"]." is not null
				";
				if(!$DB->Query($strSql))
				{
					$this->LAST_ERROR = GetMessage("IBLOCK_PROPERTY_CHANGE_ERROR",array("#ID#"=>$ID,"#CODE#"=>"MS01"));
					return false;
				}
				$strSql = "
					ALTER TABLE b_iblock_element_prop_s".$arProperty["IBLOCK_ID"]."
					DROP COLUMN PROPERTY_".$arProperty["ID"]."
				";
				if(!$DB->Query($strSql))
				{
					$this->LAST_ERROR = GetMessage("IBLOCK_PROPERTY_CHANGE_ERROR",array("#ID#"=>$ID,"#CODE#"=>"MS02"));
					return false;
				}
				$strSql = "
					ALTER TABLE b_iblock_element_prop_s".$arProperty["IBLOCK_ID"]."
					ADD PROPERTY_".$arProperty["ID"]." text
				";
				if(!$DB->Query($strSql))
				{
					$this->LAST_ERROR = GetMessage("IBLOCK_PROPERTY_CHANGE_ERROR",array("#ID#"=>$ID,"#CODE#"=>"MS03"));
					return false;
				}
			}
			else
			{//MULTIPLE=N
				switch($arFields["PROPERTY_TYPE"])
				{
					case "S":
						$strType = "varchar(2000)";
						break;
					case "N":
						$strType = "numeric(18,4)";
						break;
					case "L":
					case "F":
					case "G":
					case "E":
						$strType = "int";
						break;
					default://s - small string
						$strType = "varchar(255)";
				}
				$strSql = "
					ALTER TABLE b_iblock_element_prop_s".$arProperty["IBLOCK_ID"]."
					DROP COLUMN PROPERTY_".$arProperty["ID"]."
				";
			 	if(!$DB->Query($strSql))
				{
					$this->LAST_ERROR = GetMessage("IBLOCK_PROPERTY_CHANGE_ERROR",array("#ID#"=>$ID,"#CODE#"=>"MS04"));
					return false;
				}
				$strSql = "
					ALTER TABLE b_iblock_element_prop_s".$arProperty["IBLOCK_ID"]."
					ADD PROPERTY_".$arProperty["ID"]." ".$strType."
				";
			 	if(!$DB->Query($strSql))
				{
					$this->LAST_ERROR = GetMessage("IBLOCK_PROPERTY_CHANGE_ERROR",array("#ID#"=>$ID,"#CODE#"=>"MS05"));
					return false;
				}
				switch($arFields["PROPERTY_TYPE"])
				{
					case "N":
						$strTrans = "EN.VALUE_NUM";
						break;
					case "L":
					case "F":
					case "G":
					case "E":
						$strTrans = "EN.VALUE_ENUM";
						break;
					case "s":
						$strTrans = "SUBSTR(EN.VALUE, 0 ,255)";
						break;
					default:
						$strTrans = "EN.VALUE";
				}
				$strSql = "
					UPDATE
						b_iblock_element_prop_s".$arProperty["IBLOCK_ID"]."
					SET
						PROPERTY_".$ID."=".$strTrans."
						,DESCRIPTION_".$ID." = EN.DESCRIPTION
					FROM
						b_iblock_element_prop_s".$arProperty["IBLOCK_ID"]." EL
						,b_iblock_element_prop_m".$arProperty["IBLOCK_ID"]." EN
					WHERE
						EN.IBLOCK_ELEMENT_ID = EL.IBLOCK_ELEMENT_ID
						AND EN.IBLOCK_PROPERTY_ID = ".$ID."
				";
				if(!$DB->Query($strSql))
				{
					$this->LAST_ERROR = GetMessage("IBLOCK_PROPERTY_CHANGE_ERROR",array("#ID#"=>$ID,"#CODE#"=>"MS06"));
					return false;
				}
				$strSql = "
					DELETE FROM
						b_iblock_element_prop_m".$arProperty["IBLOCK_ID"]."
					WHERE
						IBLOCK_PROPERTY_ID = ".$ID."
				";
				if(!$DB->Query($strSql))
				{
					$this->LAST_ERROR = GetMessage("IBLOCK_PROPERTY_CHANGE_ERROR",array("#ID#"=>$ID,"#CODE#"=>"MS07"));
					return false;
				}
			}
		}
		else
		{//MULTIPLE not changed
			if(is_set($arFields ,"PROPERTY_TYPE")
			&& $arFields["PROPERTY_TYPE"]!=$arProperty["PROPERTY_TYPE"]
			&& $arProperty["MULTIPLE"]=="N")
			{
				switch($arFields["PROPERTY_TYPE"])
				{
					case "S":
						$strType = "varchar(2000)";
						break;
					case "N":
						$strType = "numeric(18,4)";
						break;
					case "L":
					case "F":
					case "G":
					case "E":
						$strType = "int";
						break;
					default://s - small string
						$strType = "varchar(255)";
				}
				$strSql = "
					INSERT INTO b_iblock_element_prop_m".$arProperty["IBLOCK_ID"]."
					(IBLOCK_ELEMENT_ID, IBLOCK_PROPERTY_ID, VALUE, VALUE_ENUM, VALUE_NUM, DESCRIPTION)
					SELECT
						IBLOCK_ELEMENT_ID
						,".$arProperty["ID"]."
						,PROPERTY_".$arProperty["ID"]."
						,".($arProperty["PROPERTY_TYPE"]=="S" || $arProperty["PROPERTY_TYPE"]=="s"?
							"0":
							"PROPERTY_".$arProperty["ID"])."
						,".($arProperty["PROPERTY_TYPE"]=="S" || $arProperty["PROPERTY_TYPE"]=="s"?
							"0":
							"PROPERTY_".$arProperty["ID"])."
						,null
					FROM
						b_iblock_element_prop_s".$arProperty["IBLOCK_ID"]."
					WHERE
						PROPERTY_".$arProperty["ID"]." is not null
				";
				if(!$DB->Query($strSql))
				{
					$this->LAST_ERROR = GetMessage("IBLOCK_PROPERTY_CHANGE_ERROR",array("#ID#"=>$ID,"#CODE#"=>"MS08"));
					return false;
				}
				$strSql = "
					UPDATE b_iblock_element_prop_s".$arProperty["IBLOCK_ID"]."
					SET PROPERTY_".$arProperty["ID"]."=null
				";
			 	if(!$DB->Query($strSql))
				{
					$this->LAST_ERROR = GetMessage("IBLOCK_PROPERTY_CHANGE_ERROR",array("#ID#"=>$ID,"#CODE#"=>"MS09"));
					return false;
				}
				$strSql = "
					ALTER TABLE b_iblock_element_prop_s".$arProperty["IBLOCK_ID"]."
					DROP COLUMN PROPERTY_".$arProperty["ID"]."
				";
			 	if(!$DB->Query($strSql))
				{
					$this->LAST_ERROR = GetMessage("IBLOCK_PROPERTY_CHANGE_ERROR",array("#ID#"=>$ID,"#CODE#"=>"MS10"));
					return false;
				}
				$strSql = "
					ALTER TABLE b_iblock_element_prop_s".$arProperty["IBLOCK_ID"]."
					ADD PROPERTY_".$arProperty["ID"]." ".$strType."
				";
			 	if(!$DB->Query($strSql))
				{
					$this->LAST_ERROR = GetMessage("IBLOCK_PROPERTY_CHANGE_ERROR",array("#ID#"=>$ID,"#CODE#"=>"MS11"));
					return false;
				}
				switch($arFields["PROPERTY_TYPE"])
				{
					case "N":
						$strTrans = "EN.VALUE_NUM";
						break;
					case "L":
					case "F":
					case "G":
					case "E":
						$strTrans = "EN.VALUE_ENUM";
						break;
					case "s":
						$strTrans = "SUBSTR(EN.VALUE, 0 ,255)";
						break;
					default:
						$strTrans = "EN.VALUE";
				}
				$strSql = "
					UPDATE
						b_iblock_element_prop_s".$arProperty["IBLOCK_ID"]."
					SET
						PROPERTY_".$ID."=".$strTrans."
						,DESCRIPTION_".$ID." = EN.DESCRIPTION
					FROM
						b_iblock_element_prop_s".$arProperty["IBLOCK_ID"]." EL
						,b_iblock_element_prop_m".$arProperty["IBLOCK_ID"]." EN
					WHERE
						EN.IBLOCK_ELEMENT_ID = EL.IBLOCK_ELEMENT_ID
						AND EN.IBLOCK_PROPERTY_ID = ".$ID."
				";
				if(!$DB->Query($strSql))
				{
					$this->LAST_ERROR = GetMessage("IBLOCK_PROPERTY_CHANGE_ERROR",array("#ID#"=>$ID,"#CODE#"=>"MS12"));
					return false;
				}
				$strSql = "
					DELETE FROM
						b_iblock_element_prop_m".$arProperty["IBLOCK_ID"]."
					WHERE
						IBLOCK_PROPERTY_ID = ".$ID."
				";
				if(!$DB->Query($strSql))
				{
					$this->LAST_ERROR = GetMessage("IBLOCK_PROPERTY_CHANGE_ERROR",array("#ID#"=>$ID,"#CODE#"=>"MS13"));
					return false;
				}
			}
		}
		return true;
	}
	///////////////////////////////////////////////////////////////////
	//Функция выборки списка
	///////////////////////////////////////////////////////////////////
	function GetList($arOrder=Array(), $arFilter=Array())
	{
		global $DB, $USER;
		$strSqlSearch = "";
		foreach($arFilter as $key=>$val)
		{
			$val = $DB->ForSql($val);
			if(strncmp($key, "!", 1)==0)
			{
				$key = substr($key, 1);
				$bInvert = true;
			}
			else
				$bInvert = false;
			$key = strtoupper($key);

			switch($key)
			{
			case "ACTIVE":
			case "SEARCHABLE":
			case "FILTRABLE":
				if($val=="Y" || $val=="N")
					$strSqlSearch .= "AND BP.".$key."='".$val."'\n";
				break;
			case "CODE":
			case "NAME":
				$strSqlSearch .= "AND UPPER(BP.".$key.") LIKE UPPER('".$val."')\n";
				break;
			case "XML_ID":
			case "EXTERNAL_ID":
				$strSqlSearch .= "AND ".($bInvert?" BP.XML_ID IS NULL OR NOT ":"")."(BP.XML_ID LIKE '".$val."')\n";
				break;
			case "TMP_ID":
				$strSqlSearch .= "AND ".($bInvert?" BP.TMP_ID IS NULL OR NOT ":"")."(BP.TMP_ID LIKE '".$val."')\n";
				break;
			case "PROPERTY_TYPE":
			case "USER_TYPE":
				$strSqlSearch .= "AND BP.".$key."='".$val."'\n";
				break;
			case "ID":
			case "IBLOCK_ID":
			case "LINK_IBLOCK_ID":
			case "VERSION":
				$strSqlSearch .= "AND BP.".$key."=".IntVal($val)."\n";
				break;
			case "IBLOCK_CODE":
				$strSqlSearch .= "AND UPPER(B.CODE)=UPPER('".$val."')\n";
				break;
			}
		}

		if(!$USER->IsAdmin())
			$strSqlSearch .=
					"AND EXISTS(".
					"	SELECT 'x' ".
					"	FROM b_iblock_group IBG ".
					"	WHERE IBG.IBLOCK_ID=B.ID ".
					"		AND IBG.GROUP_ID IN (".$USER->GetGroups().") ".
					"		AND IBG.PERMISSION>='".(strlen($arFilter["MIN_PERMISSION"])==1?$arFilter["MIN_PERMISSION"]:"R")."'".
					"		AND (IBG.PERMISSION='X' OR B.ACTIVE='Y')".
					")\n";

		$strSql =
			"SELECT BP.* ".
			"FROM b_iblock_property BP, b_iblock B ".
			"WHERE BP.IBLOCK_ID=B.ID ".
				$strSqlSearch;

		$arSqlOrder = Array();
		foreach($arOrder as $by=>$order)
		{
			$by = strtolower($by);
			$order = strtolower($order);
			if ($order!="asc")
				$order = "desc";

			if ($by == "id")		$arSqlOrder[] = " BP.ID ".$order." ";
			elseif ($by == "block_id")	$arSqlOrder[] = " BP.IBLOCK_ID ".$order." ";
			elseif ($by == "name")		$arSqlOrder[] = " BP.NAME ".$order." ";
			elseif ($by == "active")	$arSqlOrder[] = " BP.ACTIVE ".$order." ";
			elseif ($by == "sort")		$arSqlOrder[] = " BP.SORT ".$order." ";
			elseif ($by == "filtrable")	$arSqlOrder[] = " BP.FILTRABLE ".$order." ";
			elseif ($by == "searchable")	$arSqlOrder[] = " BP.SEARCHABLE ".$order." ";
			else
			{
				$arSqlOrder[] = " BP.TIMESTAMP_X ".$order." ";
				$by = "timestamp_x";
			}
		}

		$strSqlOrder = "";
		DelDuplicateSort($arSqlOrder);
		for ($i=0; $i<count($arSqlOrder); $i++)
		{
			if($i==0)
				$strSqlOrder = " ORDER BY ";
			else
				$strSqlOrder .= ",";

			$strSqlOrder .= $arSqlOrder[$i];
		}
		$strSql .= $strSqlOrder;

		$res = new CIBlockPropertyResult($DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__));
		return $res;
	}

	function DropColumnSQL($strTable, $arColumns)
	{
		$res = array();
		foreach($arColumns as $strColumn)
			$res[]="ALTER TABLE ".$strTable." DROP COLUMN ".$strColumn;
		return $res;
	}

	function _Add($ID, $arFields)
	{
		global $DB;
		$err_mess = "FILE: ".__FILE__."<br>LINE: ";
		if($arFields["MULTIPLE"]=="Y")
			$strType = "text";
		else
		{
			switch($arFields["PROPERTY_TYPE"])
			{
				case "S":
					$strType = "varchar(2000)";
					break;
				case "N":
					$strType = "numeric(18,4)";
					break;
				case "L":
				case "F":
				case "G":
				case "E":
					$strType = "int";
					break;
				default://s - small string
					$strType = "varchar(255)";
			}
		}
		$strSql = "
			ALTER TABLE b_iblock_element_prop_s".$arFields["IBLOCK_ID"]."
			ADD PROPERTY_".$ID." ".$strType.", DESCRIPTION_".$ID." varchar(255)
		";
		$rs = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $rs;
	}
}

class CIBlockPropertyEnum extends CAllIBlockPropertyEnum
{
}
?>
