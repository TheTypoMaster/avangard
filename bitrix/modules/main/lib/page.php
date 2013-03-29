<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2012 Bitrix
 */
namespace Bitrix\Main;

abstract class Page
{
	/** @var \Bitrix\Main\Application */
	protected $application;

	protected $title;

	public function __construct()
	{
	}

	public function setApplication(Application $application)
	{
		$this->application = $application;
	}

	protected function initializeCulture()
	{
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function startRequest()
	{
		ob_start();
	}

	public function render()
	{
		$text = ob_get_clean();
		/* TODO: предусмотреть механизм фильтров. Например, чтобы можно было что-то прописать перед тегом </body> */
		return /*$this->title."<br>".*/$text/*."<br>"*/;
	}

	/**
	 * @return Context
	 */
	public function getContext()
	{
		return $this->application->getContext();
	}

	/**
	 * @return HttpRequest
	 */
	public function getRequest()
	{
		$context = $this->application->getContext();
		return $context->getRequest();
	}

	/**
	 * @return HttpResponse
	 */
	public function getResponse()
	{
		$context = $this->application->getContext();
		return $context->getResponse();
	}
}
