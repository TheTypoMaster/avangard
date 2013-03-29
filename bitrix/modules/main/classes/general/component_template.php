<?
global $arBXAvailableTemplateEngines;
global $arBXRuntimeTemplateEngines;

$arBXAvailableTemplateEngines = array(
	"php" => array(
		"templateExt" => array("php"),
		"function" => ""
	)
);

$arBXRuntimeTemplateEngines = False;

class CBitrixComponentTemplate
{
	var $__name = "";
	var $__page = "";
	var $__engineID = "";

	var $__file = "";
	var $__fileAlt = "";
	var $__folder = "";
	var $__siteTemplate = "";
	var $__templateInTheme = False;

	var $__component = null;
	var $__component_epilog = false;

	var $__bInited = False;
	private $__view = array();

	function CBitrixComponentTemplate()
	{
		$this->__bInited = False;

		$this->__file = "";
		$this->__fileAlt = "";
		$this->__folder = "";
	}

	/***********  GET  ***************/
	function GetName()
	{
		if (!$this->__bInited)
			return null;

		return $this->__name;
	}

	function GetPageName()
	{
		if (!$this->__bInited)
			return null;

		return $this->__page;
	}

	function GetFile()
	{
		if (!$this->__bInited)
			return null;

		return $this->__file;
	}

	function GetFolder()
	{
		if (!$this->__bInited)
			return null;

		return $this->__folder;
	}

	function GetSiteTemplate()
	{
		if (!$this->__bInited)
			return null;

		return $this->__siteTemplate;
	}

	function IsInTheme()
	{
		if (!$this->__bInited)
			return null;

		return $this->__templateInTheme;
	}

	function &GetCachedData()
	{
		$arReturn = null;

		if (!$this->__bInited)
			return $arReturn;

		$arReturn = array();

		if($this->__folder <> '')
		{
			$fname = $_SERVER["DOCUMENT_ROOT"].$this->__folder."/style.css";
			if (file_exists($fname))
				$arReturn["additionalCSS"] = $this->__folder."/style.css";
		}

		return $arReturn;
	}

	/***********  INIT  ***************/
	function ApplyCachedData($arData)
	{
		global $APPLICATION;

		if ($arData && is_array($arData))
		{
			if (array_key_exists("additionalCSS", $arData) && StrLen($arData["additionalCSS"]) > 0)
			{
				$APPLICATION->SetAdditionalCSS($arData["additionalCSS"]);

				//Check if parent component exists and plug css it to it's "collection"
				if($this->__component && $this->__component->__parent)
					$this->__component->__parent->addChildCSS($this->__folder."/style.css");
			}
		}
	}

	function InitTemplateEngines($arTemplateEngines = array())
	{
		global $arBXAvailableTemplateEngines, $arBXRuntimeTemplateEngines;

		if (array_key_exists("arCustomTemplateEngines", $GLOBALS)
			&& is_array($GLOBALS["arCustomTemplateEngines"])
			&& count($GLOBALS["arCustomTemplateEngines"]) > 0)
		{
			$arBXAvailableTemplateEngines = $arBXAvailableTemplateEngines + $GLOBALS["arCustomTemplateEngines"];
		}

		if (is_array($arTemplateEngines) && count($arTemplateEngines) > 0)
			$arBXAvailableTemplateEngines = $arBXAvailableTemplateEngines + $arTemplateEngines;

		$arBXRuntimeTemplateEngines = array();

		foreach ($arBXAvailableTemplateEngines as $engineID => $engineValue)
			foreach ($engineValue["templateExt"] as $ext)
				$arBXRuntimeTemplateEngines[$ext] = $engineID;
	}

	function Init(&$component, $siteTemplate = false, $customTemplatePath = "")
	{
		global $arBXAvailableTemplateEngines, $arBXRuntimeTemplateEngines;

		$this->__bInited = False;

		if ($siteTemplate === False)
			$this->__siteTemplate = SITE_TEMPLATE_ID;
		else
			$this->__siteTemplate = $siteTemplate;

		if (StrLen($this->__siteTemplate) <= 0)
			$this->__siteTemplate = ".default";

		$this->__file = "";
		$this->__fileAlt = "";
		$this->__folder = "";

		if (!$arBXRuntimeTemplateEngines)
			$this->InitTemplateEngines();

		if (!($component instanceof cbitrixcomponent))
			return False;

		$this->__component = &$component;

		$this->__name = $this->__component->GetTemplateName();
		if (StrLen($this->__name) <= 0)
			$this->__name = ".default";

		$this->__name = preg_replace("'[\\\/]+'", "/", $this->__name);
		$this->__name = Trim($this->__name, "/");

		if (!$this->CheckName($this->__name))
			$this->__name = ".default";

		$this->__page = $this->__component->GetTemplatePage();
		if (StrLen($this->__page) <= 0)
			$this->__page = "template";

		if (!$this->__SearchTemplate($customTemplatePath))
			return False;

		$this->__GetTemplateEngine();

		$this->__bInited = True;

		return True;
	}

	function CheckName($name)
	{
		return preg_match("#^([A-Za-z0-9_.-]+)(/[A-Za-z0-9_.-]+)?$#i", $name);
	}

	/***********  SEARCH  ***************/
	// Search file by its path and name without extention
	function __SearchTemplateFile($path, $fileName)
	{
		global $arBXAvailableTemplateEngines, $arBXRuntimeTemplateEngines;

		if (!$arBXRuntimeTemplateEngines)
			$this->InitTemplateEngines();

		$fname = $_SERVER["DOCUMENT_ROOT"].$path."/".$fileName.".php";
		if (file_exists($fname) && is_file($fname))
		{
			return $fileName.".php";
		}
		else
		{
			// Look at glob() function for PHP >= 4.3.0 !!!

			foreach ($arBXRuntimeTemplateEngines as $templateExt => $engineID)
			{
				if ($templateExt == "php")
					continue;

				if (file_exists($_SERVER["DOCUMENT_ROOT"].$path."/".$fileName.".".$templateExt)
					&& is_file($_SERVER["DOCUMENT_ROOT"].$path."/".$fileName.".".$templateExt))
				{
					return $fileName.".".$templateExt;
				}
			}
		}

		return False;
	}

	function __SearchTemplate($customTemplatePath = "")
	{
		$this->__file = "";
		$this->__fileAlt = "";
		$this->__folder = "";

		$arFolders = array();
		$relativePath = $this->__component->GetRelativePath();

		$parentComponent = & $this->__component->GetParent();
		if ($parentComponent)
		{
			$parentRelativePath = $parentComponent->GetRelativePath();
			$parentTemplate = & $parentComponent->GetTemplate();
			$parentTemplateName = $parentTemplate->GetName();

			$arFolders[] = BX_PERSONAL_ROOT."/templates/".$this->__siteTemplate."/components".$parentRelativePath."/".$parentTemplateName.$relativePath;
			$arFolders[] = BX_PERSONAL_ROOT."/templates/.default/components".$parentRelativePath."/".$parentTemplateName.$relativePath;
			$arFolders[] = "/bitrix/components".$parentRelativePath."/templates/".$parentTemplateName.$relativePath;
		}
		$arFolders[] = BX_PERSONAL_ROOT."/templates/".$this->__siteTemplate."/components".$relativePath;
		$arFolders[] = BX_PERSONAL_ROOT."/templates/.default/components".$relativePath;
		$arFolders[] = "/bitrix/components".$relativePath."/templates";

		if (strlen($customTemplatePath) > 0 && $templatePageFile = $this->__SearchTemplateFile($customTemplatePath, $this->__page))
		{
			$this->__fileAlt = $customTemplatePath."/".$templatePageFile;

			for ($i = 0, $cnt = count($arFolders); $i < $cnt; $i++)
			{
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$arFolders[$i]."/".$this->__name)
					&& is_dir($_SERVER["DOCUMENT_ROOT"].$arFolders[$i]."/".$this->__name))
				{
					$this->__file = $arFolders[$i]."/".$this->__name."/".$templatePageFile;
					$this->__folder = $arFolders[$i]."/".$this->__name;
				}

				if (StrLen($this->__file) > 0)
				{
					if ($i == 0 || $i == 3)
						$this->__siteTemplate = $this->__siteTemplate;
					elseif ($i == 1 || $i == 4)
						$this->__siteTemplate = ".default";
					else
						$this->__siteTemplate = "";

					if ($parentComponent && $i < 3)
						$this->__templateInTheme = True;
					else
						$this->__templateInTheme = False;

					break;
				}
			}
			return (strlen($this->__file) > 0);
		}

		foreach ($arFolders as $folder)
		{
			$fname = $folder."/".$this->__name;
			if (file_exists($_SERVER["DOCUMENT_ROOT"].$fname))
			{
				if (is_dir($_SERVER["DOCUMENT_ROOT"].$fname))
				{
					if ($templatePageFile = $this->__SearchTemplateFile($fname, $this->__page))
					{
						$this->__file = $fname."/".$templatePageFile;
						$this->__folder = $fname;
					}
				}
				elseif (is_file($_SERVER["DOCUMENT_ROOT"].$fname))
				{
					$this->__file = $fname;
					if (StrPos($this->__name, "/") !== False)
						$this->__folder = $folder."/".SubStr($this->__name, 0, bxstrrpos($this->__name, "/"));
				}
			}
			else
			{
				if ($templatePageFile = $this->__SearchTemplateFile($folder, $this->__name))
					$this->__file = $folder."/".$templatePageFile;
			}

			if (StrLen($this->__file) > 0)
			{
				if ($i == 0 || $i == 3)
					$this->__siteTemplate = $this->__siteTemplate;
				elseif ($i == 1 || $i == 4)
					$this->__siteTemplate = ".default";
				else
					$this->__siteTemplate = "";

				if ($parentComponent && $i < 3)
					$this->__templateInTheme = True;
				else
					$this->__templateInTheme = False;

				break;
			}
		}

		return (StrLen($this->__file) > 0);
	}

	/***********  INCLUDE  ***************/
	function __IncludePHPTemplate(&$arResult, &$arParams, $parentTemplateFolder = "")
	{
		global $APPLICATION, $USER, $DB;

		if (!$this->__bInited)
			return False;

		$templateName = $this->__name;
		$templateFile = $this->__file;
		$templateFolder = $this->__folder;

		$componentPath = $this->__component->GetPath();

		$component = & $this->__component;

		if (strlen($this->__fileAlt) > 0)
		{
			include($_SERVER["DOCUMENT_ROOT"].$this->__fileAlt);
			return;
		}

		$templateData = false;

		include($_SERVER["DOCUMENT_ROOT"].$this->__file);

		$component_epilog = $this->__folder."/component_epilog.php";
		if(file_exists($_SERVER["DOCUMENT_ROOT"].$component_epilog))
		{
			//These will be available with extract then component will
			//execute epilog without template
			$component->SetTemplateEpilog(array(
				"epilogFile" => $component_epilog,
				"templateName" => $this->__name,
				"templateFile" => $this->__file,
				"templateFolder" => $this->__folder,
				"templateData" => $templateData,
			));
		}
	}

	function IncludeTemplate(&$arResult)
	{
		global $arBXAvailableTemplateEngines;

		if (!$this->__bInited)
			return False;

		$arParams = $this->__component->arParams;

		if($this->__folder <> '')
		{
			$arLangMessages = $this->IncludeLangFile();
			$this->__IncludeMutatorFile($arResult, $arParams);
			$this->__IncludeCSSFile();
			$this->__IncludeJSFile();
		}

		$parentTemplateFolder = "";
		$parentComponent = $this->__component->GetParent();
		if ($parentComponent)
		{
			$parentTemplate = $parentComponent->GetTemplate();
			$parentTemplateFolder = $parentTemplate->GetFolder();
		}

		if (StrLen($arBXAvailableTemplateEngines[$this->__engineID]["function"]) > 0
			&& function_exists($arBXAvailableTemplateEngines[$this->__engineID]["function"]))
		{
			$result = call_user_func(
				$arBXAvailableTemplateEngines[$this->__engineID]["function"],
				$this->__file,
				$arResult,
				$arParams,
				$arLangMessages,
				$this->__folder,
				$parentTemplateFolder,
				$this
			);
		}
		else
		{
			$result = $this->__IncludePHPTemplate($arResult, $arParams, $parentTemplateFolder);
		}

		return $result;
	}

	function __IncludeLangFile($path)
	{
		static $messCache = array();
		if (!isset($messCache[$path]))
			$messCache[$path] = __IncludeLang($path, true);

		global $MESS;
		$MESS = $messCache[$path] + $MESS;

		return $messCache[$path];
	}

	function IncludeLangFile($relativePath = "", $lang = false)
	{
		$arLangMessages = array();

		if($this->__folder <> '')
		{
			$absPath = $_SERVER["DOCUMENT_ROOT"].$this->__folder."/lang/";

			if ($lang === false)
				$lang = LANGUAGE_ID;

			if ($relativePath == "")
				$relativePath = bx_basename($this->__file);

			if ($lang != "en" && $lang != "ru")
				$arLangMessages = $this->__IncludeLangFile($absPath.LangSubst($lang)."/".$relativePath);

			$arLangMessages = $this->__IncludeLangFile($absPath.$lang."/".$relativePath) + $arLangMessages;
		}

		return $arLangMessages;
	}

	function __IncludeMutatorFile(&$arResult, &$arParams)
	{
		global $APPLICATION, $USER, $DB;

		if($this->__folder <> '')
		{
			if (file_exists($_SERVER["DOCUMENT_ROOT"].$this->__folder."/result_modifier.php"))
				include($_SERVER["DOCUMENT_ROOT"].$this->__folder."/result_modifier.php");
		}
	}

	function __IncludeCSSFile()
	{
		global $APPLICATION;

		if($this->__folder <> '' && file_exists($_SERVER["DOCUMENT_ROOT"].$this->__folder."/style.css"))
		{
			$APPLICATION->SetAdditionalCSS($this->__folder."/style.css");

			//Check if parent component exists and plug css it to it's "collection"
			if($this->__component && $this->__component->__parent)
				$this->__component->__parent->addChildCSS($this->__folder."/style.css");
		}
	}

	function __IncludeJSFile()
	{
		global $APPLICATION;
		if($this->__folder <> '')
		{
			$fname = $_SERVER["DOCUMENT_ROOT"].$this->__folder."/script.js";
			if (file_exists($fname))
			{
				if($GLOBALS['APPLICATION']->IsJSOptimized() && ($GLOBALS['APPLICATION']->bShowHeadString || $GLOBALS['APPLICATION']->bShowHeadScript))
					$APPLICATION->AddHeadScript($this->__folder."/script.js");
				else
					echo "<script src=\"".$this->__folder."/script.js".'?'.filemtime($fname)."\" type=\"text/javascript\"></script>";
			}
		}
	}

	/***********  UTIL  ***************/
	function __GetTemplateExtension($templateName)
	{
		$templateName = trim($templateName, ". \r\n\t");
		$arTemplateName = explode(".", $templateName);
		return strtolower($arTemplateName[count($arTemplateName) - 1]);
	}

	function __GetTemplateEngine()
	{
		global $arBXRuntimeTemplateEngines;

		if (!$arBXRuntimeTemplateEngines)
			$this->InitTemplateEngines();

		$templateExt = $this->__GetTemplateExtension($this->__file);

		if (array_key_exists($templateExt, $arBXRuntimeTemplateEngines))
			$this->__engineID = $arBXRuntimeTemplateEngines[$templateExt];
		else
			$this->__engineID = "php";
	}

	function SetViewTarget($target, $pos = 500)
	{
		$this->EndViewTarget();
		$view = &$this->__view;

		if(!isset($view[$target]))
			$view[$target] = array();
		$view[$target][] = array(false, $pos);

		ob_start();
	}

	function EndViewTarget()
	{
		$view = &$this->__view;
		if(!empty($view))
		{
			//Get the key to last started view target
			end($view);
			$target_key = key($view);

			//Get the key to last added "sub target"
			//in most cases there will be only one
			end($view[$target_key]);
			$sub_target_key = key($view[$target_key]);

			$sub_target = &$view[$target_key][$sub_target_key];
			if($sub_target[0] === false)
			{
				$sub_target[0] = ob_get_contents();
				$GLOBALS["APPLICATION"]->AddViewContent($target_key, $sub_target[0], $sub_target[1]);
				$this->__component->addViewTarget($target_key, $sub_target[0], $sub_target[1]);
				ob_end_clean();
			}
		}
	}

/**** EDIA AREA ICONS ************/
/*
inside template.php:

$this->AddEditAction(
	'USER'.$arUser['ID'], // entry id. prefix like 'USER' needed only in case when template has two or more lists of differrent editable entities

	$arUser['EDIT_LINK'], // edit link, should be set in a component. will be open in js popup.

	GetMessage('INTR_ISP_EDIT_USER'), // button caption

	array( // additional params
		'WINDOW' => array("width"=>780, "height"=>500), // popup params
		'ICON' => 'bx-context-toolbar-edit-icon' // icon css
		'SRC' => '/bitrix/images/myicon.gif' // icon image
	)
);

icon css is set to "edit" icon by default. button caption too.

$this->GetEditAreaId with the same id MUST be used for marking entry contaner or row, like this:
<tr id="<?=$this->GetEditAreaId('USER'.$arUser['ID']);?>">
*/
	function GetEditAreaId($entryId)
	{
		return $this->__component->GetEditAreaId($entryId);
	}

	function AddEditAction($entryId, $editLink, $editTitle = false, $arParams = array())
	{
		$this->__component->addEditButton(array('AddEditAction', $entryId, $editLink, $editTitle, $arParams));
	}

	/*
$arParams['CONFIRM'] = false - disable confirm;
$arParams['CONFIRM'] = 'Text' - confirm with custom text;
no $arParams['CONFIRM'] at all - confirm with default text
*/
	function AddDeleteAction($entryId, $deleteLink, $deleteTitle = false, $arParams = array())
	{
		$this->__component->addEditButton(array('AddDeleteAction', $entryId, $deleteLink, $deleteTitle, $arParams));
	}
}
?>