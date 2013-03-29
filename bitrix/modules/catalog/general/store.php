<?php
IncludeModuleLangFile(__FILE__);

class CAllCatalogStore
{
	protected function CheckFields($action, &$arFields)
	{
		if (is_set($arFields["ADDRESS"]) && strlen($arFields["ADDRESS"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("CS_EMPTY_ADDRESS"));
			$arFields["ADDRESS"] = ' ';
		}
		if (($action == 'ADD') &&
			((is_set($arFields, "IMAGE_ID") && strlen($arFields["IMAGE_ID"])<0)))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("CS_WRONG_IMG"));
			return false;
		}
		if (($action == 'ADD') &&
			((is_set($arFields, "LOCATION_ID") && intval($arFields["LOCATION_ID"])<=0)))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("CS_WRONG_LOC"));
			return false;
		}
		if (($action == 'UPDATE') && is_set($arFields, "ID"))
			unset($arFields["ID"]);

		if (($action == 'UPDATE') && strlen($arFields["IMAGE_ID"])<=0)
			unset($arFields["IMAGE_ID"]);

		return true;
	}

	static function Update($id, $arFields)
	{
		global $DB, $USER;
		$id = intval($id);
		$arFields1 = array();
		if(array_key_exists('USER_ID',$arFields))
			unset($arFields['USER_ID']);
		if(array_key_exists('DATE_CREATE',$arFields))
			unset($arFields['DATE_CREATE']);
		if(array_key_exists('DATE_MODIFY', $arFields))
			unset($arFields['DATE_MODIFY']);
		if(!(!isset($USER) || !(($USER instanceof CUser) && ('CUser' == get_class($USER)))))
		{
			if (!isset($arFields["MODIFIED_BY"]) || intval($arFields["MODIFIED_BY"]) <= 0)
				$arFields["MODIFIED_BY"] = intval(@$USER->GetID());
		}
		$arFields1['DATE_MODIFY'] = $DB->GetNowFunction();
		if($id <= 0 || !self::CheckFields('UPDATE',$arFields))
			return false;
		$strUpdate = $DB->PrepareUpdate("b_catalog_store", $arFields);

		if(!empty($strUpdate))
		{
			foreach($arFields1 as $key => $value)
			{
				if(strlen($strUpdate)>0)
					$strUpdate .= ", ";
				$strUpdate .= $key."=".$value." ";
			}

			$strSql = "UPDATE b_catalog_store SET ".$strUpdate." WHERE ID = ".$id." ";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $id;
	}

	static function Delete($id)
	{
		global $DB;
		$id = intval($id);
		if ($id > 0)
		{
			$DB->Query("DELETE FROM b_catalog_store_product WHERE STORE_ID = ".$id." ", true);
			$DB->Query("DELETE FROM b_catalog_store WHERE ID = ".$id." ", true);
			return true;
		}
		return false;
	}
}