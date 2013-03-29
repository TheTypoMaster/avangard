<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions < "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);

if(!CBXFeatures::IsFeatureEnabled('SaleReports'))
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	ShowError(GetMessage("SALE_FEATURE_NOT_ALLOW"));

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

// <editor-fold desc="--------- Server processing ---------">
ClearVars();
$errorMessage = '';
$errAdmMessage = null;
$fCriticalError = false;

// Using report module
if (!CModule::IncludeModule('report'))
{
	$errorMessage .= GetMessage("REPORT_MODULE_NOT_INSTALLED").'<br>';
	$fCriticalError = true;
}

// Using currency module
if (!CModule::IncludeModule('currency'))
{
	$errorMessage .= GetMessage("CURRENCY_MODULE_NOT_INSTALLED").'<br>';
	$fCriticalError = true;
}

// Using catalog module
if (!CModule::IncludeModule('catalog'))
{
	$errorMessage .= GetMessage("CATALOG_MODULE_NOT_INSTALLED").'<br>';
	$fCriticalError = true;
}

// Using iblock module
if (!CModule::IncludeModule('iblock'))
{
	$errorMessage .= GetMessage("IBLOCK_MODULE_NOT_INSTALLED").'<br>';
	$fCriticalError = true;
}

// If exists $ID parameter and it more than 0, then it is identifier of report that will be created.
$ID = (int)$_REQUEST['ID'];
if ($ID == 0)
{
	$errorMessage .= GetMessage("REPORT_VIEW_REP_ID_NOT_SET").'<br>';
	$fCriticalError = true;
}

CBaseSaleReportHelper::init();

//<editor-fold defaultstate='collapsed' desc="Forming parameters of component report.view">

$arParams = array(
	'PATH_TO_REPORT_LIST' => '/bitrix/admin/sale_report.php?lang='.LANG,
	'PATH_TO_REPORT_CONSTRUCT' => '/bitrix/admin/sale_report_construct.php?lang='.LANG,
	'PATH_TO_REPORT_VIEW' => '/bitrix/admin/sale_report_view.php?lang='.LANG,
	'REPORT_ID' => $ID,
	'REPORT_CURRENCY_LABEL_TEXT' => GetMessage('SALE_REPORT_VIEW_CURRENCY_LABEL_TITLE').': '.$reportCurrency['FULL_NAME'],
	'ROWS_PER_PAGE' => 50,
	'NAV_TEMPLATE' => 'arrows_adm'
);
//</editor-fold>

// <editor-fold defaultstate="collapsed" desc="POST action">
if ($_REQUEST['cancel'])
	LocalRedirect($arParams['PATH_TO_REPORT_LIST']);

$siteList = CBaseSaleReportHelper::getSiteList();
if (isset($_REQUEST['F_SALE_SITE']))
{
	$siteId = substr($_REQUEST['F_SALE_SITE'],0,2);
	if (array_key_exists($siteId, $siteList))
	{
		$siteCookieId = CBaseSaleReportHelper::getSiteCookieId();
		setcookie($siteCookieId, $siteId, time()+365*24*3600);
	}
	$arParams['F_SALE_SITE'] = $siteId;
	CBaseSaleReportHelper::setDefaultSiteId($siteId);
	unset($siteId);
}
else
{
	$siteCookieId = CBaseSaleReportHelper::getSiteCookieId();
	if (isset($_COOKIE[$siteCookieId]))
	{
		$siteId = substr($_COOKIE[$siteCookieId],0,2);
		if (array_key_exists($siteId, $siteList)) $arParams['F_SALE_SITE'] = $siteId;
		CBaseSaleReportHelper::setDefaultSiteId($siteId);
		unset($siteId);
	}
}

if ($_REQUEST['REPORT_AJAX'] === 'Y')
{
	$arResponse = array();
	if (is_array($_REQUEST['filterTypes']))
	{
		$result = CBaseSaleReportHelper::getAjaxResponse($_REQUEST['filterTypes']);
		if (is_array($result)) $arResponse = $result;
	}
	echo CUtil::PhpToJSObject($arResponse);
	exit;
}
// </editor-fold>


if (!isset($arParams['F_SALE_SITE']))
{
	$arParams['F_SALE_SITE'] = CBaseSaleReportHelper::getDefaultSiteId();
}

// Get site currency
$siteId = CBaseSaleReportHelper::getDefaultSiteId();
$reportCurrencyId = COption::GetOptionString('sale', 'default_currency', null, ($siteId !== null) ? $siteId : false);
$reportCurrency = CCurrencyLang::GetById($reportCurrencyId,LANG);
$reportWeightUnits = CBaseSaleReportHelper::getDefaultSiteWeightUnits();
$arParams['REPORT_CURRENCY_LABEL_TEXT'] = GetMessage('SALE_REPORT_VIEW_CURRENCY_LABEL_TITLE').': '.$reportCurrency['FULL_NAME'];
$arParams['REPORT_WEIGHT_UNITS_LABEL_TEXT'] = GetMessage('SALE_REPORT_VIEW_WEIGHT_UNITS_LABEL_TITLE').': '.$reportWeightUnits;

// Beforehand we get report parameters.
$arRepParams = array();
if (!($arRepParams = Bitrix\Report\ReportTable::getById($ID)->NavNext(false)))
{
	$errorMessage .= GetMessage("SALE_REPORT_VIEW_ERROR_GET_REP_PARAMS").'<br>';
	$fCriticalError = true;
}

if (!$fCriticalError)
{
	// get helper name
	$arParams['OWNER_ID'] = $arRepParams['OWNER_ID'];
	$arParams['REPORT_HELPER_CLASS'] = CBaseSaleReportHelper::getHelperByOwner($arRepParams['OWNER_ID']);

	// fill report title
	$arParams['TITLE'] = $arRepParams['TITLE'];
}
// </editor-fold>



// Page header
$rep_title = GetMessage("SALE_REPORT_VIEW_TITLE");
if (isset($arParams['TITLE']) && !empty($arParams['TITLE'])) $rep_title .= ' "'.$arParams['TITLE'].'"';
$APPLICATION->SetTitle($rep_title);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");



// <editor-fold desc="--------- Page output ---------">

if( $errorMessage )
{
	$errAdmMessage = new CAdminMessage(
		array(
			"DETAILS"=>$errorMessage,
			"TYPE"=>"ERROR",
			"MESSAGE"=>(GetMessage('SALE_REPORT_VIEW_ERROR_VIEW_REP')),
			"HTML"=>true
		)
	);
	echo $errAdmMessage->Show();
}

if (!$fCriticalError)
{
	// <editor-fold desc="------------ Form output ------------">
?>

<style type="text/css">
	a.webform-field-textbox-clear {
		background: url("/bitrix/js/main/core/images/controls-sprite.png") no-repeat scroll -23px 0px transparent;
		cursor: pointer;
		display: block;
		width: 7px;
		height: 7px;
		position: absolute;
		right: 10px;
		top: 10px;
	}
	table.report-table {
		width: 100%;
	}
</style>


<!--<div class="adm-detail-content-wrap">
	<div class="adm-detail-content">
		<div class="adm-detail-content-item-block">-->
			<table cellspacing="0" class="report-table" class="adm-list-table">
			<tbody><tr><td>

			<div>
				<input type="hidden" name="ID" value="<?=$ID?>" />
				<table class="report-table">
				<tr>
					<td>
						<?php
						$APPLICATION->IncludeComponent(
							'bitrix:report.view',
							'admin',
							array(
								'REPORT_ID' => $arParams['REPORT_ID'],
								'TITLE' => $arParams['TITLE'],
								'PATH_TO_REPORT_LIST' => $arParams['PATH_TO_REPORT_LIST'],
								'PATH_TO_REPORT_CONSTRUCT' => $arParams['PATH_TO_REPORT_CONSTRUCT'],
								'PATH_TO_REPORT_VIEW' => $arParams['PATH_TO_REPORT_VIEW'],
								'OWNER_ID' => $arParams['OWNER_ID'],
								'REPORT_HELPER_CLASS' => $arParams['REPORT_HELPER_CLASS'],
								'REPORT_CURRENCY_LABEL_TEXT' => $arParams['REPORT_CURRENCY_LABEL_TEXT'],
								'REPORT_WEIGHT_UNITS_LABEL_TEXT' => $arParams['REPORT_WEIGHT_UNITS_LABEL_TEXT'],
								'ROWS_PER_PAGE' => $arParams['ROWS_PER_PAGE'],
								'NAV_TEMPLATE' => $arParams['NAV_TEMPLATE'],
								'F_SALE_SITE' => $arParams['F_SALE_SITE']
							),
							null,
							array('HIDE_ICONS' => 'Y')
						);
						?>
					</td>
				</tr>

				<tr style="display: none;">
					<td>
						<!-- custom control examples -->
						<table cellspacing="0" id="adm-report-chfilter-examples-custom" class="adm-filter-content-table" style="display: none;">
							<tbody>
							<!-- site example -->
							<tr class="chfilter-field-LID adm-report-chfilter-control" callback="RTFilter_chooseBoolean">
								<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
								<td class="adm-filter-item-center">
									<div class="adm-filter-alignment">
										<div class="adm-filter-box-sizing">
										<span class="adm-select-wrap">
											<select class="adm-select" id="%ID%" name="%NAME%" caller="true">
												<!--<option value=""><?/*=GetMessage('REPORT_IGNORE_FILTER_VALUE')*/?></option>-->
												<? foreach(CBaseSaleReportHelper::getSiteList() as $kID => $vSiteName): ?>
												<option value="<?=htmlspecialcharsbx($kID)?>"><?=htmlspecialcharsbx($vSiteName)?></option>
												<? endforeach; ?>
											</select>
										</span>
									</div>
									</div>
								</td>
								<td class="adm-filter-item-right"></td>
							</tr>
							<!-- gender example -->
							<tr class="chfilter-field-PERSONAL_GENDER adm-report-chfilter-control" callback="RTFilter_chooseBoolean">
								<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
								<td class="adm-filter-item-center">
									<div class="adm-filter-alignment">
										<div class="adm-filter-box-sizing">
										<span class="adm-select-wrap">
											<select class="adm-select" id="%ID%" name="%NAME%" caller="true">
												<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
												<? foreach(CBaseSaleReportHelper::getGenders() as $kID => $vName): ?>
												<option value="<?=htmlspecialcharsbx($kID)?>"><?=htmlspecialcharsbx($vName)?></option>
												<? endforeach; ?>
											</select>
										</span>
									</div>
									</div>
								</td>
								<td class="adm-filter-item-right"></td>
							</tr>
							<!-- person type example -->
							<tr class="chfilter-field-PERSON_TYPE_ID adm-report-chfilter-control" callback="RTFilter_chooseBoolean">
								<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
								<td class="adm-filter-item-center">
									<div class="adm-filter-alignment">
										<div class="adm-filter-box-sizing">
										<span class="adm-select-wrap">
											<select class="adm-select sale-report-site-dependent" id="%ID%" name="%NAME%" caller="true" tid="PersonType">
												<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
												<? $siteId = CBaseSaleReportHelper::getDefaultSiteId(); ?>
												<? foreach(CBaseSaleReportHelper::getPersonTypes() as $kID => $v): ?>
												<? if ($v['LID'] === $siteId): ?>
													<option value="<?=htmlspecialcharsbx($kID)?>"><?=htmlspecialcharsbx($v['NAME'])?></option>
													<? endif; ?>
												<? endforeach; ?>
											</select>
										</span>
									</div>
									</div>
								</td>
								<td class="adm-filter-item-right"></td>
							</tr>
							<!-- person type example -->
							<tr class="chfilter-field-ORDER.PERSON_TYPE_ID adm-report-chfilter-control" callback="RTFilter_chooseBoolean">
								<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
								<td class="adm-filter-item-center">
									<div class="adm-filter-alignment">
										<div class="adm-filter-box-sizing">
										<span class="adm-select-wrap">
											<select class="adm-select sale-report-site-dependent" id="%ID%" name="%NAME%" caller="true" tid="PersonType">
												<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
												<? $siteId = CBaseSaleReportHelper::getDefaultSiteId(); ?>
												<? foreach(CBaseSaleReportHelper::getPersonTypes() as $kID => $v): ?>
												<? if ($v['LID'] === $siteId): ?>
													<option value="<?=htmlspecialcharsbx($kID)?>"><?=htmlspecialcharsbx($v['NAME'])?></option>
													<? endif; ?>
												<? endforeach; ?>
											</select>
										</span>
									</div>
									</div>
								</td>
								<td class="adm-filter-item-right"></td>
							</tr>
							<!-- person type example -->
							<tr class="chfilter-field-Bitrix\Sale\Order:BUYER.PERSON_TYPE_ID adm-report-chfilter-control" callback="RTFilter_chooseBoolean">
								<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
								<td class="adm-filter-item-center">
									<div class="adm-filter-alignment">
										<div class="adm-filter-box-sizing">
										<span class="adm-select-wrap">
											<select class="adm-select sale-report-site-dependent" id="%ID%" name="%NAME%" caller="true" tid="PersonType">
												<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
												<? $siteId = CBaseSaleReportHelper::getDefaultSiteId(); ?>
												<? foreach(CBaseSaleReportHelper::getPersonTypes() as $kID => $v): ?>
												<? if ($v['LID'] === $siteId): ?>
													<option value="<?=htmlspecialcharsbx($kID)?>"><?=htmlspecialcharsbx($v['NAME'])?></option>
													<? endif; ?>
												<? endforeach; ?>
											</select>
										</span>
									</div>
									</div>
								</td>
								<td class="adm-filter-item-right"></td>
							</tr>
							<!-- status example -->
							<tr class="chfilter-field-Bitrix\Sale\Status adm-report-chfilter-control" callback="RTFilter_chooseBoolean">
								<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
								<td class="adm-filter-item-center">
									<div class="adm-filter-alignment">
										<div class="adm-filter-box-sizing">
										<span class="adm-select-wrap">
											<select class="adm-select" id="%ID%" name="%NAME%" caller="true">
												<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
												<? foreach(CBaseSaleReportHelper::getStatusList() as $kID => $vStatusName): ?>
												<option value="<?=htmlspecialcharsbx($kID)?>"><?=htmlspecialcharsbx($vStatusName)?></option>
												<? endforeach; ?>
											</select>
										</span>
									</div>
									</div>
								</td>
								<td class="adm-filter-item-right"></td>
							</tr>
							<!-- pay system example -->
							<tr class="chfilter-field-Bitrix\Sale\PaySystem adm-report-chfilter-control" callback="RTFilter_chooseBoolean">
								<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
								<td class="adm-filter-item-center">
									<div class="adm-filter-alignment">
										<div class="adm-filter-box-sizing">
										<span class="adm-select-wrap">
											<select class="adm-select sale-report-site-dependent" id="%ID%" name="%NAME%" caller="true" tid="PaySystem">
												<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
												<? $siteId = CBaseSaleReportHelper::getDefaultSiteId(); ?>
												<? foreach(CBaseSaleReportHelper::getPaySystemList() as $kID => $val): ?>
													<? if ($val['site_id'] === '' || $val['site_id'] === $siteId): ?>
													<option value="<?=htmlspecialcharsbx($kID)?>"><?=htmlspecialcharsbx($val['value'])?></option>
													<? endif; ?>
												<? endforeach; ?>
											</select>
										</span>
									</div>
									</div>
								</td>
								<td class="adm-filter-item-right"></td>
							</tr>
							<!-- delivery example -->
							<tr class="chfilter-field-DELIVERY_ID adm-report-chfilter-control" callback="RTFilter_chooseBoolean">
								<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
								<td class="adm-filter-item-center">
									<div class="adm-filter-alignment">
										<div class="adm-filter-box-sizing">
										<span class="adm-select-wrap">
											<select class="adm-select sale-report-site-dependent" id="%ID%" name="%NAME%" caller="true" tid="Delivery">
												<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
												<? $siteId = CBaseSaleReportHelper::getDefaultSiteId(); ?>
												<? foreach(CBaseSaleReportHelper::getDeliveryList() as $kID => $val): ?>
												<? if ($val['site_id'] === '' || $val['site_id'] === $siteId): ?>
													<option value="<?=htmlspecialcharsbx($kID)?>"><?=htmlspecialcharsbx($val['value'])?></option>
													<? endif; ?>
												<? endforeach; ?>
											</select>
										</span>
									</div>
									</div>
								</td>
								<td class="adm-filter-item-right"></td>
							</tr>
							<!-- delivery example -->
							<tr class="chfilter-field-ORDER.DELIVERY_ID adm-report-chfilter-control" callback="RTFilter_chooseBoolean">
								<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
								<td class="adm-filter-item-center">
									<div class="adm-filter-alignment">
										<div class="adm-filter-box-sizing">
										<span class="adm-select-wrap">
											<select class="adm-select sale-report-site-dependent" id="%ID%" name="%NAME%" caller="true" tid="Delivery">
												<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
												<? $siteId = CBaseSaleReportHelper::getDefaultSiteId(); ?>
												<? foreach(CBaseSaleReportHelper::getDeliveryList() as $kID => $val): ?>
												<? if ($val['site_id'] === '' || $val['site_id'] === $siteId): ?>
													<option value="<?=htmlspecialcharsbx($kID)?>"><?=htmlspecialcharsbx($val['value'])?></option>
													<? endif; ?>
												<? endforeach; ?>
											</select>
										</span>
									</div>
									</div>
								</td>
								<td class="adm-filter-item-right"></td>
							</tr>
							<!-- delivery example -->
							<tr class="chfilter-field-Bitrix\Sale\Order:BUYER.DELIVERY_ID adm-report-chfilter-control" callback="RTFilter_chooseBoolean">
								<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
								<td class="adm-filter-item-center">
									<div class="adm-filter-alignment">
										<div class="adm-filter-box-sizing">
										<span class="adm-select-wrap">
											<select class="adm-select sale-report-site-dependent" id="%ID%" name="%NAME%" caller="true" tid="Delivery">
												<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
												<? $siteId = CBaseSaleReportHelper::getDefaultSiteId(); ?>
												<? foreach(CBaseSaleReportHelper::getDeliveryList() as $kID => $val): ?>
												<? if ($val['site_id'] === '' || $val['site_id'] === $siteId): ?>
													<option value="<?=htmlspecialcharsbx($kID)?>"><?=htmlspecialcharsbx($val['value'])?></option>
													<? endif; ?>
												<? endforeach; ?>
											</select>
										</span>
									</div>
									</div>
								</td>
								<td class="adm-filter-item-right"></td>
							</tr>
							<!-- Loading lists of filters dependent on a site when the filter of a site is changed -->
							<script type="text/javascript">
								BX.ready(
									function ()
									{
										var siteSelect = BX('sale-site-filter');
										if (siteSelect)
										{
											BX.bind(siteSelect, 'change', onChangeSiteFilter);
										}
									}
								);
								function onChangeSiteFilter()
								{
									var filters, siteSelect, filterContainer;
									var filterType, arFilterTypes = [];
									var url, i;

									siteSelect = BX('sale-site-filter');
									filterContainer = BX('report-rewrite-filter');
									if (siteSelect && siteSelect.value && filterContainer)
									{
										filters = BX.findChildren(filterContainer, {class: 'sale-report-site-dependent'}, true);
										for(i in filters)
										{
											if (filters[i].tagName == 'SELECT')
											{
												filterType = filters[i].getAttribute('tid');
												if (filterType) arFilterTypes[arFilterTypes.length] = filterType;
												filters[i].value = '';
											}
										}
										if (arFilterTypes.length > 0)
										{
											BX.showWait();
											url = '/bitrix/admin/sale_report_view.php?ID=<?=$ID?>&<?=bitrix_sessid_get()?>'+
												'&REPORT_AJAX=Y&F_SALE_SITE='+siteSelect.value;
											BX.ajax.post(url, {'filterTypes': arFilterTypes}, fProcessAjaxResult );
										}
									}
								}
								function fProcessAjaxResult(res)
								{
									BX.closeWait();
									var i, filters, filterType, filterContainer;
									filterContainer = BX('report-rewrite-filter');
									if (filterContainer && res)
									{
										res = eval('('+res+')');
										filters = BX.findChildren(filterContainer, {class: 'sale-report-site-dependent'}, true);
										for(i in filters)
										{
											if (filters[i].tagName == 'SELECT')
											{
												filterType = filters[i].getAttribute('tid');
												if (filterType)
												{
													fRewriteSelectFromArray(filters[i], res[filterType], '');
												}
												filters[i].value = '';
											}
										}
									}
								}
								function fRewriteSelectFromArray(select, data, selectedValue)
								{
									var opt, el, i;
									var setSelected = false;
									if (select)
									{
										while (opt = select.lastChild) select.removeChild(opt);
										for (i in data)
										{
											el = BX.create('option');
											el.value = data[i]['value'];
											el.innerHTML = data[i]['text'];
											try
											{
												// for IE earlier than version 8
												select.add(el,select.options[null]);
											}
											catch (e)
											{
												select.add(el,null);
											}
											if (!setSelected && selectedValue == data[i]['value'])
											{
												el.selected = true;
												setSelected = true;
												select.selectedIndex = i;
											}
										}
									}
								}
							</script>
							<!-- Group example -->
							<tr class="chfilter-field-Bitrix\Main\Group adm-report-chfilter-control" callback="RTFilter_chooseGroup">
								<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
								<td class="adm-filter-item-center">
									<div class="adm-filter-alignment">
										<div class="adm-filter-box-sizing">
											<div class="adm-input-wrap">
												<input id="%ID%" type="text" value="%VALUE%" name="%NAME%" class="adm-input" caller="true" />
												<input type="hidden" name="%NAME%" value="" />
												<a class="webform-field-textbox-clear"></a>
											</div>
											</div>
									</div>
								</td>
								<td class="adm-filter-item-right"></td>
							</tr>
							<!-- User example -->
							<tr class="chfilter-field-Bitrix\Main\User adm-report-chfilter-control" callback="RTFilter_chooseBUYER">
								<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
								<td class="adm-filter-item-center">
									<div class="adm-filter-alignment">
										<div class="adm-filter-box-sizing">
											<div class="adm-input-wrap">
												<input id="%ID%" type="text" value="%VALUE%" name="%NAME%" class="adm-input" caller="true" />
												<input type="hidden" name="%NAME%" value="" />
												<a class="webform-field-textbox-clear"></a>
											</div>
										</div>
									</div>
								</td>
								<td class="adm-filter-item-right"></td>
							</tr>
							<!-- SaleSection example -->
							<tr class="chfilter-field-Bitrix\Sale\Section adm-report-chfilter-control" callback="RTFilter_chooseBoolean">
								<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
								<td class="adm-filter-item-center">
									<div class="adm-filter-alignment">
										<div class="adm-filter-box-sizing">
										<span class="adm-select-wrap">
											<select class="adm-select sale-report-site-dependent" id="%ID%" name="%NAME%" caller="true" tid="Section">
												<?php echo CBaseSaleReportHelper::getSectionsSelectHTMLOptions(); ?>
											</select>
										</span>
									</div>
									</div>
								</td>
								<td class="adm-filter-item-right"></td>
							</tr>
							<!-- IBlockSection example -->
							<tr class="chfilter-field-Bitrix\Iblock\Section adm-report-chfilter-control" callback="RTFilter_chooseBoolean">
								<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
								<td class="adm-filter-item-center">
									<div class="adm-filter-alignment">
										<div class="adm-filter-box-sizing">
										<span class="adm-select-wrap">
											<select class="adm-select sale-report-site-dependent" id="%ID%" name="%NAME%" caller="true" tid="Section">
												<?php echo CBaseSaleReportHelper::getSectionsSelectHTMLOptions(); ?>
											</select>
										</span>
									</div>
									</div>
								</td>
								<td class="adm-filter-item-right"></td>
							</tr>
							<script type="text/javascript">
								// BUYER, User controls
								BX.ready(
									function ()
									{
										var i = 0, temp = [], controls = [];
										temp[i++] = BX.findChildren(BX('report-rewrite-filter'), { className:'chfilter-field-BUYER' }, true);
										temp[i++] = BX.findChildren(BX('report-rewrite-filter'), { className:'chfilter-field-Bitrix\\Main\\User' }, true);
										for (i in temp) if (temp[i]) controls = controls.concat(temp[i]);
										if (controls)
										{
											for (i in controls)
											{
												var inp = BX.findChild(controls[i], {tag:'input', attr:{type:'text'}}, true);
												var x = BX.findNextSibling(inp, {tag:'a'});
												BX.bind(inp, 'click', RTFilter_chooseBUYER);
												BX.bind(inp, 'blur', RTFilter_chooseBUYERCatchFix);
												BX.bind(x, 'click', RTFilter_chooseBUYERClear);
											}
										}
									}
								);

								function RTFilter_chooseBUYER(control)
								{
									var elem = null;
									if (this.parentNode)
									{
										elem = this;
									}
									else
									{
										elem = BX.findChild(control, {tag:'input', attr: {type:'text'}}, true);
									}

									BX.Access.Init();
									BX.Access.SetSelected(null);
									BX.Access.ShowForm({callback: RTFilter_chooseBUYERCatch_fromBXAccess});

									RTFilter_chooseBUYER_LAST_CALLER = elem;
								}

								function RTFilter_chooseBUYERCatch_fromBXAccess(arSelected)
								{
									if (arSelected.user)
									{
										var user = null;
										for (var i in arSelected.user) { user = arSelected.user[i]; break; }
										if (user)
										{
											user.id = user.id.substr(1);
											RTFilter_chooseBUYERCatch(user);
										}
									}
								}

								function RTFilter_chooseBUYERCatch(user)
								{
									var inp = RTFilter_chooseBUYER_LAST_CALLER;
									var hid = BX.findNextSibling(inp, {tag:'input',attr:{type:'hidden'}});
									var x = BX.findNextSibling(inp, {tag:'a'});

									hid.value = user.id;

									if (parseInt(user.id) > 0)
									{
										inp.value = user.name;
										x.style.display = 'inline';
									}
									else
									{
										inp.value = '';
										x.style.display = 'none';
									}
									BX.Access.closeWait();
								}

								function RTFilter_chooseBUYERCatchFix()
								{
									var inp = RTFilter_chooseBUYER_LAST_CALLER;
									var hid = BX.findNextSibling(inp, {tag:'input',attr:{type:'hidden'}});

									if (inp.value.length < 1 && parseInt(hid.value) > 0)
									{
										var fobj = window['O_Single_' + inp.id];
										inp.value = fobj.arSelected[hid.value].name;
									}
								}

								function RTFilter_chooseBUYERClear(e)
								{
									RTFilter_chooseBUYER_LAST_CALLER = BX.findChild(this.parentNode, {tag:'input',attr:{type:'text'}});

									BX.PreventDefault(e);
									RTFilter_chooseBUYERCatch({id:''});
								}
							</script>
							<script type="text/javascript">
								// Group controls
								BX.ready(
									function ()
									{
										var controls = BX.findChildren(BX('report-rewrite-filter'), {className:'chfilter-field-Bitrix\\Main\\Group'}, true);
										if (controls != null)
										{
											for (i in controls)
											{
												var inp = BX.findChild(controls[i], {tag:'input', attr:{type:'text'}}, true);
												var x = BX.findNextSibling(inp, {tag:'a'});
												BX.bind(inp, 'click', RTFilter_chooseGroup);
												BX.bind(inp, 'blur', RTFilter_chooseGroupCatchFix);
												BX.bind(x, 'click', RTFilter_chooseGroupClear);
											}
										}
									}
								);

								function RTFilter_chooseGroup(control)
								{
									var elem = null;
									if (this.parentNode)
									{
										elem = this;
									}
									else
									{
										elem = BX.findChild(control, {tag:'input', attr: {type:'text'}}, true);
									}

									BX.Access.Init();
									BX.Access.SetSelected(null);
									BX.Access.ShowForm({callback: RTFilter_chooseGroupCatch_fromBXAccess});

									RTFilter_chooseGroup_LAST_CALLER = elem;
								}

								function RTFilter_chooseGroupCatch_fromBXAccess(arSelected)
								{
									if (arSelected.group)
									{
										var group = null;
										for (var i in arSelected.group) { group = arSelected.group[i]; break; }
										if (group)
										{
											group.id = group.id.substr(1);
											RTFilter_chooseGroupCatch(group);
										}
										else RTFilter_chooseGroupCatch({id:''});
									}
								}

								function RTFilter_chooseGroupCatch(group)
								{
									var inp = RTFilter_chooseGroup_LAST_CALLER;
									var hid = BX.findNextSibling(inp, {tag:'input',attr:{type:'hidden'}});
									var x = BX.findNextSibling(inp, {tag:'a'});

									hid.value = group.id;

									if (parseInt(group.id) > 0)
									{
										inp.value = group.name;
										x.style.display = 'inline';
									}
									else
									{
										inp.value = '';
										x.style.display = 'none';
									}
									BX.Access.closeWait();
								}

								function RTFilter_chooseGroupCatchFix()
								{
									var inp = RTFilter_chooseGroup_LAST_CALLER;
									var hid = BX.findNextSibling(inp, {tag:'input',attr:{type:'hidden'}});

									if (inp.value.length < 1 && parseInt(hid.value) > 0)
									{
										var fobj = window['O_Single_' + inp.id];
										inp.value = fobj.arSelected[hid.value].name;
									}
								}

								function RTFilter_chooseGroupClear(e)
								{
									RTFilter_chooseGroup_LAST_CALLER = BX.findChild(this.parentNode, {tag:'input',attr:{type:'text'}});

									BX.PreventDefault(e);
									RTFilter_chooseGroupCatch({id:''});
								}
							</script>
							</tbody>
						</table>
					</td>
				</tr>
				</table>
			</div>

			</td></tr></tbody>
			</table>
<!--		</div>
	</div>
	<div class="adm-detail-content-btns adm-detail-content-btns-empty"></div>
</div>-->


<?
	// </editor-fold>
}// if (!$fCriticalError)

// </editor-fold>



require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>