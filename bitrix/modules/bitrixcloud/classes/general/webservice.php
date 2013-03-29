<?
IncludeModuleLangFile(__FILE__);
abstract class CBitrixCloudWebService
{
	/**
	 * Returns URL to update policy
	 *
	 * @param array[string]string $arParams
	 * @return string
	 *
	 */
	protected abstract function getActionURL($arParams = /*.(array[string]string).*/ array());
	/**
	 * Returns action response XML
	 *
	 * @param string $action
	 * @return CDataXML
	 *
	 */
	protected function action($action) /*. throws CBitrixCloudException .*/
	{
		global $APPLICATION;
		$url = $this->getActionURL(array(
			"action" => $action,
		));
		$server = new CHTTP;
		$strXML = $server->Get($url);
		if ($strXML === false)
		{
			$e = $APPLICATION->GetException();
			if (is_object($e))
				throw new CBitrixCloudException($e->GetString(), "");
			else
				throw new CBitrixCloudException(GetMessage("BCL_CDN_WS_SERVER", array(
					"#STATUS#" => "-1",
				)), "");
		}
		if ($server->status != 200)
		{
			throw new CBitrixCloudException(GetMessage("BCL_CDN_WS_SERVER", array(
				"#STATUS#" => (string)$server->status,
			)), "");
		}
		$obXML = new CDataXML;
		if (!$obXML->LoadString($strXML))
		{
			throw new CBitrixCloudException(GetMessage("BCL_CDN_WS_XML_PARSE", array(
				"#CODE#" => "1",
			)), "");
		}
		$node = $obXML->SelectNodes("/error/code");
		if (is_object($node))
		{
			$error_code = $node->textContent();
			$message_id = "BCL_CDN_WS_".$error_code;
			/*
			GetMessage("BCL_CDN_WS_LICENSE_EXPIRE");
			GetMessage("BCL_CDN_WS_LICENSE_NOT_FOUND");
			GetMessage("BCL_CDN_WS_QUOTA_EXCEEDED");
			*/
			if (HasMessage($message_id))
				throw new CBitrixCloudException(GetMessage($message_id), $error_code);
			else
				throw new CBitrixCloudException(GetMessage("BCL_CDN_WS_SERVER", array(
					"#STATUS#" => $error_code,
				)), $error_code);
		}
		return $obXML;
	}
}
?>