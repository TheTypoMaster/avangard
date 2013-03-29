<?php
namespace Bitrix\Main;

abstract class Response
{
	private $buffer;

	/**
	 * @var Context
	 */
	private $context;

	const SPREAD_SITES = 2;
	const SPREAD_DOMAIN = 4;

	public function __construct(Context $context)
	{
		$this->context = $context;
	}

	public function setStatus($status)
	{
		$httpStatus = \Bitrix\Main\Config\Configuration::getValue("http_status");

		$bCgi = (stristr(php_sapi_name(), "cgi") !== false);
		if($bCgi && (($httpStatus == null) || ($httpStatus == false)))
		{
			header("Status: ".$status);
		}
		else
		{
			$server = $this->context->getServer();
			header($server->get("SERVER_PROTOCOL")." ".$status);
		}
	}

	/*
	Устанавливает кук и при необходимости запоминает параметры установленного кука в массиве для дальнейшего распостранения по доменам

	$name			: имя кука (без префикса)
	$value			: значение переменной
	$time			: дата после которой кук истекает
	$folder			: каталог действия кука
	$domain			: домен действия кука
	$secure			: флаг secure для кука (1 - secure)
	$spread			: Y - распостранить кук на все сайты и их домены
	$name_prefix	: префикс для имени кука (если не задан, то берется из настроек главного модуля)
	*/
	public function setCookie($name, $value, $time=false, $folder="/", $domain=false, $secure=false, $spread=true, $name_prefix=false)
	{
		if($time === false)
			$time = time()+60*60*24*30*12; // 30 суток * 12 ~ 1 год
		if($name_prefix===false)
			$name = \Bitrix\Main\Config\Option::get("main", "cookie_name", "BITRIX_SM")."_".$name;
		else
			$name = $name_prefix."_".$name;

		if($domain === false)
			$domain = $this->getCookieDomain();

		if($spread === "Y" || $spread === true)
			$spread_mode = static::SPREAD_DOMAIN | static::SPREAD_SITES;
		elseif($spread >= 1)
			$spread_mode = $spread;
		else
			$spread_mode = static::SPREAD_DOMAIN;

		//current domain only
		if($spread_mode & static::SPREAD_DOMAIN)
			setcookie($name, $value, $time, $folder, $domain, $secure);

		//spread over sites
//		if($spread_mode & static::SPREAD_SITES)
//			$this->arrSPREAD_COOKIE[$name] = array("V" => $value, "T" => $time, "F" => $folder, "D" => $domain, "S" => $secure);
	}

	protected function getCookieDomain()
	{
		static $bCache = false;
		static $cache  = false;
		if ($bCache)
			return $cache;

		$context = Application::getInstance()->getContext();
		$server = $context->getServer();

		$cacheFlags = \Bitrix\Main\Config\Configuration::getValue("cache_flags");
		$cacheTtl = (isset($cacheFlags["site_domain"]) ? $cacheFlags["site_domain"] : 0);

		if ($cacheTtl === false)
		{
			$connection = Application::getDbConnection();
			$sqlHelper = $connection->getSqlHelper();

			$sql = "SELECT DOMAIN ".
				"FROM b_lang_domain ".
				"WHERE '".$sqlHelper->forSql('.'.$server->getHttpHost())."' like ".$sqlHelper->getConcatFunction("'%.'", "DOMAIN")." ".
				"ORDER BY ".$sqlHelper->getLengthFunction("DOMAIN")." ";
			$recordset = $connection->query($sql);
			if ($record = $recordset->fetch())
				$cache = $record['DOMAIN'];
		}
		else
		{
			$managedCache = Application::getInstance()->getManagedCache();

			if ($managedCache->read($cacheTtl, "b_lang_domain", "b_lang_domain"))
			{
				$arLangDomain = $managedCache->get("b_lang_domain");
			}
			else
			{
				$arLangDomain = array("DOMAIN" => array(), "LID" => array());

				$connection = Application::getDbConnection();
				$sqlHelper = $connection->getSqlHelper();

				$recordset = $connection->query(
					"SELECT * ".
					"FROM b_lang_domain ".
					"ORDER BY ".$sqlHelper->getLengthFunction("DOMAIN")
				);
				while ($record = $recordset->fetch())
				{
					$arLangDomain["DOMAIN"][] = $record;
					$arLangDomain["LID"][$record["LID"]][] = $record;
				}
				$managedCache->set("b_lang_domain", $arLangDomain);
			}

			foreach ($arLangDomain["DOMAIN"] as $domain)
			{
				if (strcasecmp(substr('.'.$server->getHttpHost(), -(strlen($domain['DOMAIN']) + 1)), ".".$domain['DOMAIN']) == 0)
				{
					$cache = $domain['DOMAIN'];
					break;
				}
			}
		}

		$bCache = true;
		return $cache;
	}

	public function clear()
	{

	}

	public function redirect($url)
	{

	}

	public function flush($text)
	{
		//$this->writeHeaders();
		$this->writeBody($text);
	}

	private function writeBody($text)
	{
		echo $text;
	}

}
