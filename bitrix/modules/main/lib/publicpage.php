<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2012 Bitrix
 */
namespace Bitrix\Main;

use Bitrix\Main\Context;

class PublicPage extends Page
{
	/** @var \Bitrix\Main\Context\Site */
	protected $site;

	/** @var \Bitrix\Main\SiteTemplate */
	protected $siteTemplate;

	public function __construct()
	{
		parent::__construct();

		//$this->initializeSite();
		//$this->initializeCulture();
	}

	protected function initializeSite()
	{
		$context = $this->application->getContext();
		$request = $context->getRequest();

		$currentDirectory = $request->getRequestedPageDirectory();

		$currentHost = "";
		$currentHostScheme = "";
		if ($request instanceof IHttpRequest)
		{
			/** @var $request \Bitrix\Main\HttpRequest */
			$currentHost = $request->getHttpHost();
			$currentHostScheme = $request->isHttps() ? "https://" : "http://";
		}

		$url = new Web\Uri($currentHostScheme.$currentHost, Web\UriType::ABSOLUTE);

		$currentDomain = $url->parse(Web\UriPart::HOST);
		$currentDomain = trim($currentDomain, "\t\r\n\0 .");

		$connection = Application::getDbConnection();
		$helper = $connection->getSqlHelper();

		$sql = "
			SELECT L.*, L.LID as ID, L.LID as SITE_ID
			FROM b_lang L
				LEFT JOIN b_lang_domain LD ON L.LID=LD.LID AND '".$helper->forSql($currentDomain, 255)."' LIKE CONCAT('%', LD.DOMAIN)
			WHERE ('".$helper->forSql($currentDirectory)."' LIKE CONCAT(L.DIR, '%') OR LD.LID IS NOT NULL)
				AND L.ACTIVE='Y'
			ORDER BY
				IF((L.DOMAIN_LIMITED='Y' AND LD.LID IS NOT NULL) OR L.DOMAIN_LIMITED<>'Y',
					IF('".$helper->forSql($currentDomain)."' LIKE CONCAT(L.DIR, '%'), 3, 1),
					IF('".$helper->forSql($currentDirectory)."' LIKE CONCAT(L.DIR, '%'), 2, 0)
				) DESC,
				LENGTH(L.DIR) DESC,
				L.DOMAIN_LIMITED DESC,
				SORT,
				LENGTH(LD.DOMAIN) DESC
		";

		//get site by path and domain
		$siteList = $connection->query($sql);
		$site = $siteList->fetch();

		//get site by default sorting
		if($site === false)
		{
			$sql = "
				SELECT L.*, L.LID as ID, L.LID as SITE_ID
				FROM b_lang L
				WHERE L.ACTIVE='Y'
				ORDER BY L.DEF DESC, L.SORT
			";
			$siteList = $connection->query($sql);
			$site = $siteList->fetch();
		}

		if($site !== false)
		{
			$culture = Context\Culture::wakeUp($site["CULTURE_ID"]);
			if($culture === null)
				$culture = new Context\Culture();

			$this->site = new Context\Site($site);
			$this->site->setCulture($culture);
		}
		else
		{
			throw new SystemException("Site not found.");
		}
	}

	protected function initializeCulture()
	{
		$context = $this->getContext();
		$context->setCulture($this->site->getCulture());
		$context->setLanguage($this->site->getLanguage());
	}

	public function getSite()
	{
		if (is_null($this->site))
			$this->initializeSite();

		return $this->site;
	}
}
