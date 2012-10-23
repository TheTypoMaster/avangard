<?
class CCloudUtil
{
	public static function URLEncode($str, $charset)
	{
		global $APPLICATION;
		$strEncodedURL = '';
		$arUrlComponents = preg_split("#(://|/|\\?|=|&)#", $str, -1, PREG_SPLIT_DELIM_CAPTURE);
		foreach($arUrlComponents as $i => $part_of_url)
		{
			if($i % 2)
				$strEncodedURL .= $part_of_url;
			else
				$strEncodedURL .= urlencode($APPLICATION->ConvertCharset($part_of_url, LANG_CHARSET, $charset));
		}
		return $strEncodedURL;
	}
}
?>