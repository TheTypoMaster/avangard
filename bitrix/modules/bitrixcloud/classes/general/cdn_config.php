<?
IncludeModuleLangFile(__FILE__);
class CBitrixCloudCDNConfig
{
	private static $instance = /*.(CBitrixCloudCDNConfig).*/ null;
	private $active = 0;
	private $expires = 0; //timestamp
	private $domain = "";
	private $sites = /*.(array[string]string).*/ array();
	private $quota = /*.(CBitrixCloudCDNQuota).*/ null;
	private $classes = /*.(CBitrixCloudCDNClasses).*/ null;
	private $server_groups = /*.(CBitrixCloudCDNServerGroups).*/ null;
	private $locations = /*.(CBitrixCloudCDNLocations).*/ null;
	/**
	 *
	 *
	 */
	private function __construct()
	{
	}
	/**
	 * Returns proxy class instance (singleton pattern)
	 *
	 * @return CBitrixCloudCDNConfig
	 *
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance))
			self::$instance = new CBitrixCloudCDNConfig;

		return self::$instance;
	}
	/**
	 *
	 * @return CBitrixCloudCDNConfig
	 *
	 */
	public function updateQuota() /*. throws Exception .*/
	{
		$web_service = new CBitrixCloudCDNWebService($this->domain);
		$obXML = $web_service->actionQuota();
		$node = $obXML->SelectNodes("/control/quota");
		if (is_object($node))
			$this->quota = CBitrixCloudCDNQuota::fromXMLNode($node);
		else
			throw new Exception(GetMessage("BCL_CDN_CONFIG_XML_PARSE", array(
				"#CODE#" => "6",
			)));

		$this->quota->saveOption(CBitrixCloudOption::getOption("cdn_config_quota"));
		return $this;
	}
	/**
	 * Loads and parses xml
	 *
	 * @return CBitrixCloudCDNConfig
	 *
	 */
	public function loadRemoteXML() /*. throws Exception .*/
	{
		//Get configuration from remote service
		$this->sites = CBitrixCloudOption::getOption("cdn_config_site")->getArrayValue();
		$this->domain = CBitrixCloudOption::getOption("cdn_config_domain")->getStringValue();
		$web_service = new CBitrixCloudCDNWebService($this->domain);
		$obXML = $web_service->actionGetConfig();
		//
		// Parse it
		//
		$node = $obXML->SelectNodes("/control");
		if (is_object($node))
		{
			$this->active = intval($node->getAttribute("active"));
			$this->expires = strtotime($node->getAttribute("expires"));
		}
		else
		{
			$this->active = 0;
			$this->expires = 0;
		}

		$node = $obXML->SelectNodes("/control/quota");
		if (is_object($node))
			$this->quota = CBitrixCloudCDNQuota::fromXMLNode($node);
		else
			throw new Exception(GetMessage("BCL_CDN_CONFIG_XML_PARSE", array(
				"#CODE#" => "2",
			)));

		$node = $obXML->SelectNodes("/control/classes");
		if (is_object($node))
			$this->classes = CBitrixCloudCDNClasses::fromXMLNode($node);
		else
			throw new Exception(GetMessage("BCL_CDN_CONFIG_XML_PARSE", array(
				"#CODE#" => "3",
			)));

		$node = $obXML->SelectNodes("/control/servergroups");
		if (is_object($node))
			$this->server_groups = CBitrixCloudCDNServerGroups::fromXMLNode($node);
		else
			throw new Exception(GetMessage("BCL_CDN_CONFIG_XML_PARSE", array(
				"#CODE#" => "4",
			)));

		$node = $obXML->SelectNodes("/control/locations");
		if (is_object($node))
			$this->locations = CBitrixCloudCDNLocations::fromXMLNode($node, $this);
		else
			throw new Exception(GetMessage("BCL_CDN_CONFIG_XML_PARSE", array(
				"#CODE#" => "5",
			)));

		return $this;
	}
	/**
	 * Checks if it is active in webservice
	 *
	 * @return bool
	 *
	 */
	public function isActive()
	{
		return ($this->active > 0);
	}
	/**
	 * Checks if it is time to update policy
	 *
	 * @return bool
	 *
	 */
	public function isExpired()
	{
		return ($this->expires < time());
	}
	/**
	 * Sets the time to update policy
	 *
	 * @param int $time
	 * @return void
	 *
	 */
	public function setExpired($time)
	{
		$this->expires = $time;
		CBitrixCloudOption::getOption("cdn_config_expire_time")->setStringValue((string)$this->expires);
	}
	/**
	 * Returns resources domain name
	 *
	 * @return string
	 *
	 */
	public function getDomain()
	{
		return $this->domain;
	}
	/**
	 * Sets resources domain name
	 *
	 * @param string $domain
	 * @return CBitrixCloudCDNConfig
	 *
	 */
	public function setDomain($domain)
	{
		$this->domain = $domain;
		return $this;
	}
	/**
	 * Returns array of sites
	 *
	 * @return array[string]string
	 *
	 */
	public function getSites()
	{
		return $this->sites;
	}
	/**
	 * Sets array of sites to enable CDN
	 *
	 * @param array[string]string $sites
	 * @return CBitrixCloudCDNConfig
	 *
	 */
	public function setSites($sites)
	{
		$this->sites = /*.(array[string]string).*/ array();
		if (is_array($sites))
		{
			foreach ($sites as $site_id)
				$this->sites[$site_id] = $site_id;
		}
		return $this;
	}
	/**
	 * Returns quota object
	 *
	 * @return CBitrixCloudCDNQuota
	 *
	 */
	public function getQuota()
	{
		return $this->quota;
	}
	/**
	 * Returns file class object by it's name
	 *
	 * @param string $class_name
	 * @return CBitrixCloudCDNClass
	 *
	 */
	public function getClassByName($class_name)
	{
		return $this->classes->getClass($class_name);
	}
	/**
	 * Returns server group object by it's name
	 *
	 * @param string $server_group_name
	 * @return CBitrixCloudCDNServerGroup
	 *
	 *
	 */
	public function getServerGroupByName($server_group_name)
	{
		return $this->server_groups->getGroup($server_group_name);
	}
	/**
	 * Returns configured locations
	 *
	 * @return CBitrixCloudCDNLocations
	 *
	 */
	public function getLocations()
	{
		return $this->locations;
	}
	/**
	 * Returns unique array of all prefixes across all locations
	 *
	 * @return array[int]string
	 *
	 */
	public function getLocationsPrefixes()
	{
		$arPrefixes = array();
		$location = /*.(CBitrixCloudCDNLocation).*/ null;
		foreach ($this->locations as $location)
		{
			$arPrefixes = array_merge($arPrefixes, $location->getPrefixes());
		}
		return array_unique($arPrefixes);
	}
	/**
	 * Returns unique array of all extensions across all locations
	 *
	 * @return array[int]string
	 *
	 */
	public function getLocationsExtensions()
	{
		$arExtensions = array();
		$location = /*.(CBitrixCloudCDNLocation).*/ null;
		foreach ($this->locations as $location)
		{
			foreach ($location->getClasses() as $file_class)
				$arExtensions = array_merge($arExtensions, $file_class->getExtensions());
		}
		return array_unique($arExtensions);
	}
	/**
	 * Saves configuration into CBitrixCloudOption
	 *
	 * @return CBitrixCloudCDNConfig
	 *
	 */
	public function saveToOptions()
	{
		CBitrixCloudOption::getOption("cdn_config_active")->setStringValue((string)$this->active);
		CBitrixCloudOption::getOption("cdn_config_expire_time")->setStringValue((string)$this->expires);
		CBitrixCloudOption::getOption("cdn_config_domain")->setStringValue($this->domain);
		CBitrixCloudOption::getOption("cdn_config_site")->setArrayValue($this->sites);
		$this->quota->saveOption(CBitrixCloudOption::getOption("cdn_config_quota"));
		$this->classes->saveOption(CBitrixCloudOption::getOption("cdn_class"));
		$this->server_groups->saveOption(CBitrixCloudOption::getOption("cdn_server_group"));
		$this->locations->saveOption(CBitrixCloudOption::getOption("cdn_location"));
		return $this;
	}
	/**
	 * Loads configuration from CBitrixCloudOption
	 *
	 * @return CBitrixCloudCDNConfig
	 *
	 */
	public function loadFromOptions()
	{
		$this->active = intval(CBitrixCloudOption::getOption("cdn_config_active")->getStringValue());
		$this->expires = intval(CBitrixCloudOption::getOption("cdn_config_expire_time")->getStringValue());
		$this->domain = CBitrixCloudOption::getOption("cdn_config_domain")->getStringValue();
		$this->sites = CBitrixCloudOption::getOption("cdn_config_site")->getArrayValue();
		$this->quota = CBitrixCloudCDNQuota::fromOption(CBitrixCloudOption::getOption("cdn_config_quota"));
		$this->classes = CBitrixCloudCDNClasses::fromOption(CBitrixCloudOption::getOption("cdn_class"));
		$this->server_groups = CBitrixCloudCDNServerGroups::fromOption(CBitrixCloudOption::getOption("cdn_server_group"));
		$this->locations = CBitrixCloudCDNLocations::fromOption(CBitrixCloudOption::getOption("cdn_location"), $this);
		return $this;
	}
	/**
	 * @return bool
	 *
	 */
	public function lock()
	{
		return CBitrixCloudOption::lock();
	}
	/**
	 * @return void
	 *
	 */
	public function unlock()
	{
		CBitrixCloudOption::unlock();
	}
}
?>