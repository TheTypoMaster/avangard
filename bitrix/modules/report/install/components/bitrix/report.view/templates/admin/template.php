<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!empty($arResult['ERROR']))
{
	echo $arResult['ERROR'];
	return false;
}

// calendar
CJSCore::Init(array('date','access'));

$arPeriodTypes = array(
	"month" => GetMessage("TASKS_THIS_MONTH"),
	"month_ago" => GetMessage("TASKS_PREVIOUS_MONTH"),
	"week" => GetMessage("TASKS_THIS_WEEK"),
	"week_ago" => GetMessage("TASKS_PREVIOUS_WEEK"),
	"days" => GetMessage("TASKS_LAST_N_DAYS"),
	"after" => GetMessage("TASKS_AFTER"),
	"before" => GetMessage("TASKS_BEFORE"),
	"interval" => GetMessage("TASKS_DATE_INTERVAL"),
	"all" => GetMessage("TASKS_DATE_ALL")
);

// <editor-fold defaultstate="collapsed" desc="Area for the upper buttons">

$aMenu = array(
	array(
		"TEXT" => GetMessage("REPORT_RETURN_TO_LIST"),
		"LINK" => $arParams["PATH_TO_REPORT_LIST"],
		"ICON"=>"btn_list",
	),
	array(
		"TEXT" => GetMessage("REPORT_EXCEL_EXPORT"),
		"LINK" => $APPLICATION->GetCurPageParam("EXCEL=Y"),
	),
	array(
		"TEXT" => GetMessage("REPORT_EDIT"),
		"LINK" => $arParams["PATH_TO_REPORT_CONSTRUCT"].'&ID='.$arParams['REPORT_ID'],
		"ICON"=>"btn_edit"
	)
);
$context = new CAdminContextMenu($aMenu);
$context->Show();

// </editor-fold>
?>

<style type="text/css">
	.pagetitle-wrap {
		margin: 0px -3px 0px -1px;
		min-height: 30px;
		padding: 0px 0px 4px 4px;
		position: relative;
	}
	.pagetitle {
		color: rgb(85, 85, 85);
		font-size: 30px;
		margin: -2px 0px 0px;
		padding: 0px;
		font-weight: normal;
		text-shadow: 0px 1px 0px rgb(255, 255, 255);
	}
	.pagetitle-menu {
		right: 5px;
		top: 0px;
		position: absolute;
		z-index: 2;
	}
	.adm-filter-main-table { width: auto !important;}
	.adm-list-table-header {
		cursor: pointer;
	}
	.reports-total-column { cursor: default; }
	.adm-filter-item-left { white-space: nowrap; }
	.adm-filter-box-sizing { width: auto; min-width: 300px;}
	.adm-filter-content .adm-select-wrap { max-width: none; }
	.adm-workarea .adm-input-wrap .adm-input { min-width: 110px; }
</style>

<!-- filter form -->
<form id="report-rewrite-filter" action="<?=CComponentEngine::MakePathFromTemplate(
	$arParams["PATH_TO_REPORT_VIEW"],
	array('report_id' => $arParams['REPORT_ID'])
);?>" method="GET">

<input type="hidden" name="lang" value="<?=htmlspecialcharsbx(LANGUAGE_ID)?>" />
<input type="hidden" name="ID" value="<?=htmlspecialcharsbx($arParams['REPORT_ID'])?>" />
<input type="hidden" name="sort_id" value="<?=htmlspecialcharsbx($arResult['sort_id'])?>" />
<input type="hidden" name="sort_type" value="<?=htmlspecialcharsbx($arResult['sort_type'])?>" />

<?
// prepare info
$info = array();
foreach($arResult['changeableFilters'] as $chFilter)
{
	$field = isset($chFilter['field']) ? $chFilter['field'] : null;
	// Try to obtain qualified field name (e.g. 'COMPANY_BY.COMPANY_TYPE_BY.STATUS_ID')
	$name = isset($chFilter['name']) ? $chFilter['name'] : ($field ? $field->GetName() : '');
	$info[] = array(
		'TITLE' => $chFilter['title'],
		'COMPARE' => strtolower(GetMessage('REPORT_FILTER_COMPARE_VAR_'.$chFilter['compare'])),
		'NAME' =>$chFilter['formName'],
		'ID' => $chFilter['formId'],
		'VALUE' => $chFilter['value'],
		'FIELD_NAME' => $name,
		'FIELD_TYPE' => $chFilter['data_type']
	);
}
?>

<table class="adm-filter-main-table">
	<tbody>
	<tr>
		<td class="adm-filter-main-table-cell">
			<div id="filter-tabs" class="adm-filter-tabs-block">
				<span class="adm-filter-tab adm-filter-tab-active" style="cursor: default;"><?=GetMessage('REPORT_FILTER')?></span>
			</div>
		</td>
	</tr>
	<tr>
		<td class="adm-filter-main-table-cell">
			<div class="adm-filter-content">
				<div class="adm-filter-content-table-wrap">
					<!-- control examples -->
					<table cellspacing="0" id="adm-report-chfilter-examples" class="adm-filter-content-table" style="display: none;">
						<tbody>
						<!-- date example -->
						<tr class="chfilter-field-datetime adm-report-chfilter-control">
							<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
							<td class="adm-filter-item-center">
								<div class="adm-filter-alignment adm-calendar-block">
									<div class="adm-filter-box-sizing">
										<div class="adm-input-wrap adm-calendar-inp adm-calendar-first">
											<input type="text" value="%VALUE%" name="%NAME%" id="%ID%" class="adm-input adm-filter-from">
											<img title="<?php echo GetMessage("TASKS_PICK_DATE")?>" class="adm-calendar-icon"
												onclick="BX.calendar({node:this, field:'%ID%', form: '', bTime: false, bHideTime: false});">
										</div>
									</div>
								</div>
							</td>
							<td class="adm-filter-item-right"></td>
						</tr>
						<!-- string example -->
						<tr class="chfilter-field-string adm-report-chfilter-control">
							<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
							<td class="adm-filter-item-center">
								<div class="adm-filter-alignment">
									<div class="adm-filter-box-sizing">
										<div class="adm-input-wrap">
											<input type="text" value="%VALUE%" name="%NAME%" class="adm-input">
										</div>
									</div>
								</div>
							</td>
							<td class="adm-filter-item-right"></td>
						</tr>
						<!-- integer example -->
						<tr class="chfilter-field-integer adm-report-chfilter-control">
							<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
							<td class="adm-filter-item-center">
								<div class="adm-filter-alignment">
									<div class="adm-filter-box-sizing">
										<div class="adm-input-wrap">
											<input type="text" value="%VALUE%" name="%NAME%" class="adm-input">
										</div>
									</div>
								</div>
							</td>
							<td class="adm-filter-item-right"></td>
						</tr>
						<!-- float example -->
						<tr class="chfilter-field-float adm-report-chfilter-control">
							<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
							<td class="adm-filter-item-center">
								<div class="adm-filter-alignment">
									<div class="adm-filter-box-sizing">
										<div class="adm-input-wrap">
											<input type="text" value="%VALUE%" name="%NAME%" class="adm-input">
										</div>
									</div>
								</div>
							</td>
							<td class="adm-filter-item-right"></td>
						</tr>
						<!-- boolean example -->
						<tr class="chfilter-field-boolean adm-report-chfilter-control" callback="RTFilter_chooseBoolean">
							<td class="adm-filter-item-left">%TITLE% "%COMPARE%":</td>
							<td class="adm-filter-item-center">
								<div class="adm-filter-alignment">
									<div class="adm-filter-box-sizing">
									<span class="adm-select-wrap">
										<select class="adm-select" id="%ID%" name="%NAME%" caller="true">
											<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
											<option value="true"><?=GetMessage('REPORT_BOOLEAN_VALUE_TRUE')?></option>
											<option value="false"><?=GetMessage('REPORT_BOOLEAN_VALUE_FALSE')?></option>
										</select>
										<script type="text/javascript">
											function RTFilter_chooseBooleanCatch(value)
											{
												setSelectValue(RTFilter_chooseBoolean_LAST_CALLER, value);
											}
										</script>
									</span>
								</div>
								</div>
							</td>
							<td class="adm-filter-item-right"></td>
						</tr>
						</tbody>
					</table>

					<table cellspacing="0" class="adm-filter-content-table" style="display: table;">
						<tbody>

						<?php /*    Site option    */ ?>
						<? if (isset($arParams['F_SALE_SITE'])): ?>
						<tr>
							<td class="adm-filter-item-left"><?=GetMessage('SALE_REPORT_SITE').':'?></td>
							<td class="adm-filter-item-center">
								<div class="adm-filter-alignment">
									<div class="adm-filter-box-sizing">
										<?php
										$selected = $arParams['F_SALE_SITE'];
										$siteList = call_user_func(array($arParams['REPORT_HELPER_CLASS'], 'getSiteList'));
										?>
										<span class="adm-select-wrap">
											<select class="adm-select" id="sale-site-filter" name="F_SALE_SITE">
												<? foreach($siteList as $kLID => $vSiteName): ?>
												<option <?php
															if ($kLID==$selected) echo 'selected="1"';
															?>value="<?=htmlspecialcharsbx($kLID)?>"><?=htmlspecialcharsbx($vSiteName)?></option>
												<? endforeach; ?>
											</select>
										</span>
									</div>
								</div>
							</td>
							<td class="adm-filter-item-right"></td>
						</tr>
						<? endif; ?>

						<!-- period -->
						<tr>
							<td class="adm-filter-item-left"><?=GetMessage('REPORT_PERIOD').':'?></td>
							<td class="adm-filter-item-center">
								<div class="adm-filter-alignment adm-calendar-block">
									<div class="adm-filter-box-sizing">
										<span class="adm-select-wrap adm-calendar-period">
											<select onchange="OnReportIntervalChange(this)" name="F_DATE_TYPE"
													id="report-interval-filter" class="adm-select adm-calendar-period">
												<?php foreach ($arPeriodTypes as $key => $type): ?>
												<option value="<?php echo htmlspecialcharsbx($key)?>"<?=($key == $arResult['period']['type']) ? " selected" : ""?>><?php echo htmlspecialcharsbx($type)?></option>
												<?php endforeach;?>
											</select>
										</span>
										<!-- filter date from -->
										<div style="display: none;" class="adm-input-wrap adm-calendar-inp adm-calendar-first">
											<input type="text" value="<?=$arResult['form_date']['from']?>" name="F_DATE_FROM"
												id="REPORT_INTERVAL_F_DATE_FROM" class="adm-input adm-calendar-from">
											<img onclick="BX.calendar({node:this, field:'REPORT_INTERVAL_F_DATE_FROM', form: '', bTime: false, bHideTime: false});"
												title="<?php echo GetMessage("TASKS_PICK_DATE")?>" class="adm-calendar-icon">
										</div>
										<!-- filter separator -->
										<span style="display: none;" class="adm-calendar-separate"></span>
										<!-- filter date to -->
										<div style="display: none;" class="adm-input-wrap adm-calendar-second">
											<input type="text" value="<?=$arResult['form_date']['to']?>" name="F_DATE_TO"
												id="REPORT_INTERVAL_F_DATE_TO" class="adm-input adm-calendar-to">
											<img onclick="BX.calendar({node:this, field:'REPORT_INTERVAL_F_DATE_TO', form: '', bTime: false, bHideTime: false});"
												title="<?php echo GetMessage("TASKS_PICK_DATE")?>" class="adm-calendar-icon">
										</div>
										<!-- days field -->
										<div style="display: none;" class="adm-input-wrap filter-day-interval">
											<span class="<?php if ($arResult["FILTER"]["F_DATE_TYPE"] == "days"): ?>filter-day-interval-selected<?php endif; ?>">
												<input type="text" class="filter-date-days" value="<?=$arResult['form_date']['days']?>"
													name="F_DATE_DAYS"/>
											</span>
											<span><?php echo GetMessage("TASKS_REPORT_DAYS")?></span>
										</div>
									</div>
								</div>
							</td>
							<td class="adm-filter-item-right"></td>
						</tr>
						<script type="text/javascript">
							function OnReportIntervalChange(select)
							{
								var filterSelectContainer = BX.findParent(select);
								var filterDateFrom = BX.findNextSibling(filterSelectContainer, {'tag':'div', 'class': 'adm-calendar-first'});
								var filterDateSeparator = BX.findNextSibling(filterDateFrom, {'tag':'span', 'class': 'adm-calendar-separate'});
								var filterDateTo = BX.findNextSibling(filterDateSeparator, {'tag':'div', 'class': 'adm-calendar-second'});
								var filterDays = BX.findNextSibling(filterDateTo, {'tag':'div', 'class': 'filter-day-interval'});

								filterDateFrom.style.display = 'none';
								filterDateSeparator.style.display = 'none';
								filterDateTo.style.display = 'none';
								filterDays.style.display = 'none';

								if (select.value == "interval")
								{
									filterDateFrom.style.display = 'inline-block';
									filterDateSeparator.style.display = 'inline-block';
									filterDateTo.style.display = 'inline-block';
								}
								/*else if(select.value == "before") filterDateTo.style.display = 'inline-block';*/
								else if(select.value == "before") filterDateFrom.style.display = 'inline-block';
								/*else if(select.value == "after") filterDateFrom.style.display = 'inline-block';*/
								else if(select.value == "after") filterDateTo.style.display = 'inline-block';
								else if(select.value == "days") filterDays.style.display = 'inline-block';
							}

							BX.ready(function() {
								OnReportIntervalChange(BX('report-interval-filter'));
							});
						</script>

						<tr id="adm-report-filter-chfilter" style="display: none;"></tr>

						</tbody>
					</table>
				</div>
				<div id="tbl_sale_transact_filter_bottom_separator" class="adm-filter-bottom-separate" style="display: block;"></div>
				<div class="adm-filter-bottom">
					<span id="report-rewrite-filter-button" class="adm-btn-wrap"><input type="submit" value="<?=GetMessage('REPORT_FILTER_APPLY')?>" title="<?=GetMessage('REPORT_FILTER_APPLY')?>" name="set_filter" class="adm-btn"></span>
					<span id="report-reset-filter-button" class="adm-btn-wrap"><input type="submit" name="del_filter_company_search" value="<?=GetMessage('REPORT_FILTER_CANCEL')?>" title="<?=GetMessage('REPORT_FILTER_CANCEL')?>" name="del_filter" class="adm-btn"></span>
				</div>
			</div>
		</td>
	</tr>
	</tbody>
</table>
<script type="text/javascript">
	BX.ready(function(){
		BX.bind(BX('report-reset-filter-button'), 'click', function(){
			BX.submit(BX('report-reset-filter'));
		});
		BX.bind(BX('report-rewrite-filter-button'), 'click', function(){
			BX.submit(BX('report-rewrite-filter'));
		});
	});
	function setSelectValue(select, value)
	{
		var i;
		for (i=0; i<select.options.length; i++)
		{
			if (select.options[i].value == value)
			{
				select.selectedIndex = i;
				break;
			}
		}
	}
</script>


<!-- insert changeable filters -->
<script type="text/javascript">

	function replaceInAttributesAndTextElements(el, info) {
		var i, attr;
		while (el) {
			if (3 == el.nodeType)
			{
				if (el.nodeValue)
				{
					el.nodeValue = el.nodeValue.replace(/%((?!VALUE)[A-Z]+)%/gi,
						function(str, p1, offset, s)
						{
							var n = p1.toUpperCase();
							return typeof(info[n]) != 'undefined' ? BX.util.htmlspecialchars(info[n]) : str;
						}
					);
					el.nodeVaue = el.nodeValue.replace('%VALUE%', BX.util.htmlspecialchars(info.VALUE));
				}
			}
			else if (1 == el.nodeType)
			{
				for (i in el.attributes)
				{
					attr = el.attributes[i];
					if (attr)
					{
						if (attr.value)
						{
							attr.value = attr.value.replace(/%((?!VALUE)[A-Z]+)%/gi,
								function(str, p1, offset, s)
								{
									var n = p1.toUpperCase();
									return typeof(info[n]) != 'undefined' ? BX.util.htmlspecialchars(info[n]) : str;
								}
							);
							attr.value = attr.value.replace('%VALUE%', BX.util.htmlspecialchars(info.VALUE));
						}
					}
				}
			}
			replaceInAttributesAndTextElements(el.firstChild, info);
			el = el.nextSibling;
		};
	};

	BX.ready(function() {
		var info = <?=CUtil::PhpToJSObject($info)?>;
		for (var i in info)
		{
			// insert value control
			// search in `examples-custom` by name or type
			// then search in `examples` by type
			var cpControl = BX.clone(
					BX.findChild(
							BX('adm-report-chfilter-examples-custom'),
							{className:'chfilter-field-'+info[i].FIELD_NAME},
							true
					)
					||
					BX.findChild(
							BX('adm-report-chfilter-examples-custom'),
							{className:'chfilter-field-'+info[i].FIELD_TYPE},
							true
					)
					||
					BX.findChild(
							BX('adm-report-chfilter-examples'),
							{className:'chfilter-field-'+info[i].FIELD_TYPE},
							true
					)
					, true
			);
			//global replace %ID%, %NAME%, %TITLE% and etc.
			replaceInAttributesAndTextElements(cpControl, info[i]);
			if (cpControl.getAttribute('callback') != null)
			{
				// set last caller
				var callerName = cpControl.getAttribute('callback') + '_LAST_CALLER';
				var callerObj = BX.findChild(cpControl, {attr:'caller'}, true);
				window[callerName] = callerObj;

				// set value
				var cbFuncName = cpControl.getAttribute('callback') + 'Catch';
				window[cbFuncName](info[i].VALUE);
			}

			BX.findParent(BX('adm-report-filter-chfilter')).appendChild(cpControl);
		}
	});

</script>

</form>

<form id="report-reset-filter" action="<?=CComponentEngine::MakePathFromTemplate(
	$arParams["PATH_TO_REPORT_VIEW"],
	array('report_id' => $arParams['REPORT_ID'])
);?>" method="GET">
	<input type="hidden" name="ID" value="<?=htmlspecialcharsbx($arParams['REPORT_ID'])?>" />
	<input type="hidden" name="sort_id" value="<?=htmlspecialcharsbx($arResult['sort_id'])?>" />
	<input type="hidden" name="sort_type" value="<?=htmlspecialcharsbx($arResult['sort_type'])?>" />
</form>

<div style="padding-top: 18px;"></div>

<!-- result table -->
<table cellspacing="0" class="adm-list-table" id="report-result-table">
	<!-- head -->
	<thead>
	<tr class="adm-list-table-header">
		<? $i = 0; foreach($arResult['viewColumns'] as $colId => $col): ?>
		<?
		$i++;

		if ($i == 1)
		{
			$th_class = 'reports-first-column';
		}
		else if ($i == count($arResult['viewColumns']))
		{
			$th_class = 'reports-last-column';
		}
		else
		{
			$th_class = 'reports-head-cell';
		}

		// sorting
		//$defaultSort = 'DESC';
		$defaultSort = $col['defaultSort'];

		if ($colId == $arResult['sort_id'])
		{
			$th_class .= ' reports-selected-column';

			if($arResult['sort_type'] == 'ASC')
			{
				$th_class .= ' reports-head-cell-top';
			}
		}
		else
		{
			if ($defaultSort == 'ASC')
			{
				$th_class .= ' reports-head-cell-top';
			}
		}

		?>
		<td class="adm-list-table-header adm-list-table-cell adm-list-table-cell-sort <?php
			if ($colId == $arResult['sort_id'])
			{
				if($arResult['sort_type'] == 'ASC')
					echo 'adm-list-table-cell-sort-up';
				else
					echo 'adm-list-table-cell-sort-down';
			}
			?> <?=$th_class?>" colId="<?=$colId?>" defaultSort="<?=$defaultSort?>">
			<div class="adm-list-table-cell-inner reports-head-cell">
				<span class="reports-head-cell-title"><?=htmlspecialcharsbx($col['humanTitle'])?></span>
			</div>
		</td>
		<? endforeach; ?>
	</tr>
	</thead>

	<!-- data -->
	<tbody>
	<? foreach ($arResult['data'] as $row): ?>
	<tr class="adm-list-table-row">
		<? $i = 0; foreach($arResult['viewColumns'] as $col): ?>
		<?
		$i++;
		if ($i == 1)
		{
			$td_class = 'reports-first-column';
		}
		else if ($i == count($arResult['viewColumns']))
		{
			$td_class = 'reports-last-column';
		}
		else
		{
			$td_class = '';
		}

		if (CReport::isColumnPercentable($col))
		{
			$td_class .= ' align-right';
		}
		else $td_class .= ' align-left';

		$finalValue = $row[$col['resultName']];

		// add link
		if (!empty($col['href']) && !empty($row['__HREF_'.$col['resultName']]))
		{
			if (is_array($finalValue))
			{
				// grc
				foreach ($finalValue as $grcIndex => $v)
				{
					$finalValue[$grcIndex] = '<a href="'
						.$arResult['grcData'][$col['resultName']][$grcIndex]['__HREF_'.$col['resultName']]
						.'">'.$v.'</a>';
				}
			}
			elseif (strlen($row[$col['resultName']]))
			{
				$finalValue = '<a href="'.$row['__HREF_'.$col['resultName']].'">'.$row[$col['resultName']].'</a>';
			}
		}

		// magic glue
		if (is_array($finalValue))
		{
			$finalValue = join(' / ', $finalValue);
		}
		?>
		<td class="adm-list-table-cell <?=$td_class?>"><?=$finalValue?></td>
		<? endforeach; ?>
	</tr>
	<? endforeach; ?>

	<tr>
		<td colspan="<?=count($arResult['viewColumns'])?>" class="reports-pretotal-column">
			<?php echo $arResult["NAV_STRING"]?>
			<br /><br />
			<span style="font-size: 14px;"><?=GetMessage('REPORT_TOTAL')?></span>
		</td>
	</tr>

	<tr class="adm-list-table-header">
		<? $i = 0; foreach($arResult['viewColumns'] as $col): ?>
		<?
		$i++;
		if ($i == 1)
		{
			$td_class = 'reports-first-column';
		}
		else if ($i == count($arResult['viewColumns']))
		{
			$td_class = 'reports-last-column';
		}
		else
		{
			$td_class = '';
		}
		?>
		<td class="adm-list-table-cell <?=$td_class?> reports-total-column">
			<div class="adm-list-table-cell-inner reports-head-cell">
				<span class="reports-head-cell-title"><?=htmlspecialcharsbx($col['humanTitle'])?></span>
			</div>
		</td>
		<? endforeach; ?>
	</tr>

	<tr class="adm-list-table-row">
		<? $i = 0; foreach($arResult['viewColumns'] as $col): ?>
		<?
		$i++;
		if ($i == 1)
		{
			$td_class = 'reports-first-column';
		}
		else if ($i == count($arResult['viewColumns']))
		{
			$td_class = 'reports-last-column';
		}
		else
		{
			$td_class = '';
		}

		if (CReport::isColumnPercentable($col))
		{
			$td_class .= ' align-right';
		}
		else $td_class .= ' align-left';
		?>
		<td class="adm-list-table-cell <?=$td_class?>"><?=array_key_exists('TOTAL_'.$col['resultName'], $arResult['total']) ? $arResult['total']['TOTAL_'.$col['resultName']] : '&mdash;'?></td>
		<? endforeach; ?>
	</tr>
	</tbody>


</table>
<script type="text/javascript">
	BX.ready(function(){
		var rows = BX.findChildren(BX('report-result-table'), {tag:'td', class:'adm-list-table-header'}, true);
		for (i in rows)
		{
			var ds = rows[i].getAttribute('defaultSort');
			if (ds == '')
			{
				BX.addClass(rows[i], 'report-column-disabled-sort');
				BX.removeClass(rows[i], 'adm-list-table-cell-sort');
				continue;
			}

			BX.bind(rows[i], 'click', function(){
				var colId = this.getAttribute('colId');
				var sortType = '';

				var isCurrent = BX.hasClass(this, 'reports-selected-column');

				if (isCurrent)
				{
					var currentSortType = BX.hasClass(this, 'reports-head-cell-top') ? 'ASC' : 'DESC';
					sortType = currentSortType == 'ASC' ? 'DESC' : 'ASC';
				}
				else
				{
					sortType = this.getAttribute('defaultSort');
				}

				var idInp = BX.findChild(BX('report-rewrite-filter'), {attr:{name:'sort_id'}});
				var typeInp = BX.findChild(BX('report-rewrite-filter'), {attr:{name:'sort_type'}});

				idInp.value = colId;
				typeInp.value = sortType;

				BX.submit(BX('report-rewrite-filter'));
			});
		}
	});
</script>

<!-- currency label -->
<? if (isset($arParams['REPORT_CURRENCY_LABEL_TEXT'])): ?>
<div class="adm-info-message-wrap">
	<div class="adm-info-message">
		<?=$arParams['REPORT_CURRENCY_LABEL_TEXT']?>
	</div>
</div>
<? endif; ?>

<!-- weight units label -->
<? if (isset($arParams['REPORT_WEIGHT_UNITS_LABEL_TEXT'])): ?>
<div class="adm-info-message-wrap">
	<div class="adm-info-message">
		<?=$arParams['REPORT_WEIGHT_UNITS_LABEL_TEXT']?>
	</div>
</div>
<? endif; ?>

<!-- description -->
<? if (strlen($arResult['report']['DESCRIPTION'])): ?>
<div class="adm-info-message-wrap">
	<div class="adm-info-message">
		<?=htmlspecialcharsbx($arResult['report']['DESCRIPTION'])?>
	</div>
</div>
<? endif; ?>
