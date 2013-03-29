<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2012 Bitrix
 */
namespace Bitrix\Main;

use Bitrix\Main\Context;
use Bitrix\Main\Localization\LanguageTable;

class AdminPage extends Page
{
	protected $languageId;

	public function __construct(Application $application)
	{
		parent::__construct();

		$this->setApplication($application);
		$this->initializeCulture();
	}

	protected function initializeCulture()
	{
		$culture = null;

		$language = $this->getCurrentLang();
		if($language !== null)
		{
			$culture = Context\Culture::wakeUp($language["CULTURE_ID"]);
			$lang = $language["LID"];
		}
		else
		{
			$lang = "en";
		}

		if($culture === null)
			$culture = new Context\Culture();

		$context = $this->getContext();
		$context->setCulture($culture);
		$context->setLanguage($lang);

		$this->setLanguage($lang);
	}

	public function setLanguage($lang)
	{
		$this->languageId = $lang;
	}

	public function getLanguage()
	{
		return $this->languageId;
	}

	protected function getCurrentLang()
	{
		$context = $this->getContext();
		$request = $context->getRequest();

		$defaultLang = $request["lang"];
		if($defaultLang == '')
			$defaultLang = \COption::getOptionString("main", "admin_lid");

		if($defaultLang <> '')
		{
			$langDb = LanguageTable::getById($defaultLang);
			if(($language = $langDb->fetch()))
				return $language;
		}

		return null;
	}
}
