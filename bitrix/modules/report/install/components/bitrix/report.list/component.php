<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$requiredModules = array('report');

foreach ($requiredModules as $requiredModule)
{
	if (!CModule::IncludeModule($requiredModule))
	{
		ShowError(GetMessage("F_NO_MODULE"));
		return 0;
	}
}

$ownerId = $arResult['OWNER_ID'] = call_user_func(array($arParams['REPORT_HELPER_CLASS'], 'getOwnerId'));

// auto create fresh default reports only if some reports alredy exist
$userReportVersion = CUserOptions::GetOption(
	'report', '~U_'.$ownerId,
	call_user_func(array($arParams['REPORT_HELPER_CLASS'], 'getFirstVersion'))
);

$sysReportVersion = call_user_func(array($arParams['REPORT_HELPER_CLASS'], 'getCurrentVersion'));

if ($sysReportVersion !== $userReportVersion  && CheckVersion($sysReportVersion, $userReportVersion))
{
	CUserOptions::SetOption('report', '~U_'.$ownerId, $sysReportVersion);

	if (CReport::GetCountInt($ownerId) > 0)
	{
		$dReports = call_user_func(array($arParams['REPORT_HELPER_CLASS'], 'getDefaultReports'));

		foreach ($dReports as  $moduleVer => $vReports)
		{
			if ($moduleVer !== $userReportVersion && CheckVersion($moduleVer, $userReportVersion))
			{
				// add fresh vReports
				CReport::addFreshDefaultReports($vReports, $ownerId);
			}
		}
	}
}


// create default reports by user request
if (!empty($_POST['CREATE_DEFAULT']))
{
	$dReports = call_user_func(array($arParams['REPORT_HELPER_CLASS'], 'getDefaultReports'));

	foreach ($dReports as $moduleVer => $vReports)
	{
		CReport::addFreshDefaultReports($vReports, $ownerId);
	}

	LocalRedirect($arParams['PATH_TO_REPORT_LIST']);
}

// main action
$arResult['list'] = array();

$result = Bitrix\Report\ReportTable::getList(array(
	'select' => array('ID', 'TITLE', 'DESCRIPTION','CREATED_DATE'),
	'filter' => array('=CREATED_BY' => $USER->GetID(), '=OWNER_ID' => $ownerId)
));

while ($row = $result->fetch())
{
	$arResult['list'][] = $row;
}

$this->IncludeComponentTemplate();

