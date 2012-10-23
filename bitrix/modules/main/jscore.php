<?
$pathJS = '/bitrix/js/main/core';
$pathCSS = '/bitrix/js/main/core/css';
$pathLang = BX_ROOT.'/modules/main/lang/'.LANGUAGE_ID;

$arJSCoreConfig = array(
	'ajax' => array(
		'js' => $pathJS.'/core_ajax.js',
	),
	'admin' => array(
		'js' => $pathJS.'/core_admin.js',
		'css' => $pathCSS.'/core_admin.css',
		'lang' => $pathLang.'/js_core_admin.php',
		'rel' => array('ajax'),
	),
	'autosave' => array(
		'js' => $pathJS.'/core_autosave.js',
		'lang' => $pathLang.'/js_core_autosave.php',
		'rel' => array('ajax'),
	),
	'fx' => array(
		'js' => $pathJS.'/core_fx.js',
	),
	'popup' => array(
		'js' => $pathJS.'/core_popup.js',
		'css' => $pathCSS.'/core_popup.css',
	),
	'tags' => array(
		'js' => $pathJS.'/core_tags.js',
		'css' => $pathCSS.'/core_tags.css',
		'lang' => $pathLang.'/js_core_tags.php',
		'rel' => array('popup'),
	),
	'timer' => array(
		'js' => $pathJS.'/core_timer.js',
	),
	'tooltip' => array(
		'js' => $pathJS.'/core_tooltip.js',
		'css' => $pathCSS.'/core_tooltip.css',
		'rel' => array('ajax'),
	),
	'translit' => array(
		'js' => $pathJS.'/core_translit.js',
		'lang' => $pathLang.'/js_core_translit.php',
		'lang_additional' => array('BING_KEY' => COption::GetOptionString('main', 'translate_key_bing', '')),
	),
	'window' => array(
		'js' => $pathJS.'/core_window.js',
		'css' => $pathCSS.'/core_window.css',
		'rel' => array('ajax'),
	),
	'access' => array(
		'js' => $pathJS.'/core_access.js',
		'css' => $pathCSS.'/core_access.css',
		'rel' => array('popup', 'ajax', 'finder'),
		'lang' => $pathLang.'/js_core_access.php',
	),
	'finder' => array(
		'js' => $pathJS.'/core_finder.js',
		'css' => $pathCSS.'/core_finder.css',
		'rel' => array('popup', 'ajax'),
	),
	'date' => array(
		'js' => $pathJS.'/core_date.js',
		'lang' => $pathLang.'/date_format.php',
	),
	'ls' => array(
		'js' => $pathJS.'/core_ls.js',
		'rel' => array('json')
	),

	/* external libs */

	'jquery' => array(
		'js' => '/bitrix/js/main/jquery/jquery-1.7.min.js',
		'skip_core' => true,
	),
	'jquery_src' => array(
		'js' => '/bitrix/js/main/jquery/jquery-1.7.js',
		'skip_core' => true,
	),
	'json' => array(
		'js' => '/bitrix/js/main/json/json2.min.js',
		'skip_core' => true,
	),
	'json_src' => array(
		'js' => '/bitrix/js/main/json/json2.js',
		'skip_core' => true,
	),
);

foreach ($arJSCoreConfig as $ext => $arExt)
{
	CJSCore::RegisterExt($ext, $arExt);
}
?>