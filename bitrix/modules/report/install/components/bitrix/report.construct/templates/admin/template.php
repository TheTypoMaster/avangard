<?php

	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

	if (!empty($arResult['ERROR']))
	{
		echo $arResult['ERROR'];
		return false;
	}

	if (!empty($arResult['FORM_ERROR']))
	{
		?>
	<font color='red'><?=$arResult['FORM_ERROR']?></font><br/><br/>
	<?
	}

	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/js/report/css/report.css');
	$GLOBALS['APPLICATION']->AddHeadString('<script type="text/javascript" src="/bitrix/js/report/construct.js"></script>', true);

	CJSCore::Init(array('date', 'access'));

?>

<!-- Redefinition of some styles -->
<style type="text/css">
	#sale-report-construct-buttons-block { padding-top: 18px; }
	#report-filter-limit-checkbox { margin: 0px 0px 0px 0px; }
	.reports-filter-block { width: 800px; }
	.report-filter-calendar { margin-left: 4px; }
	.reports-forming-column { height: 45px; padding-top: 0px; }
	.reports-add-col-button-down,
	.reports-add-col-button-up,
	.reports-checkbox { vertical-align: middle; margin-top: 10px; }
	.reports-filter-item { height: 30px; }
	.reports-filter-butt-wrap { top: 3px; }
	.reports-filter-butt-wrap .reports-checkbox { margin-top: 0px; }
	.reports-filter-item-name { width: 160px; }
	.reports-limit-res-select-lable { vertical-align: middle; }
	.reports-sort-column { margin-bottom: 15px; }
	.reports-add-col-title { height: 16px; padding-top: 3px; }
	.reports-add-col-tit-prcnt,
	.reports-add-col-tit-edit,
	.reports-add-col-tit-prcnt-close,
	.reports-add-col-tit-remove { top: 4px; }
	.reports-add-col-title { height: 32px; }
	.reports-add-col-select.reports-add-col-select-calc { position: relative; top: -5px; }
	.reports-add-col-title { top: 10px; }
	.reports-add-col-tit-text { margin-right: 5px; }
	.reports-add-col-input input { position: relative; top: -5px; }
	.reports-add-col-inp-title { top: -17px; }
	.reports-add-col-select-prcnt, .reports-add-col-select-prcnt-by { position: relative; top: -5px; }
	.reports-limit-res-select-lable { margin-right: 5px; margin-left: 5px; }


	table.edit-table div {font-size:14px;}
	.reports-list-table th {
		font: 14px Arial,Helvetica,sans-serif;
	}

	.filter-field-date-combobox .filter-date-interval{display:none;}
	.filter-field-date-combobox span.filter-date-interval-hellip{display:none;}
	.filter-field-date-combobox .filter-date-interval-after{display:inline;}
	.filter-field-date-combobox .filter-date-interval-before{display:inline;}
	.filter-field-date-combobox .filter-date-interval-after.filter-date-interval-before{display:block;margin-top:0.5em;}
	.filter-field-date-combobox .filter-date-interval-after.filter-date-interval-before span.filter-date-interval-hellip{display:inline-block;margin:0;}
	.filter-field-date-combobox .filter-date-interval-to{display:none;}
	.filter-field-date-combobox .filter-date-interval-from{display:none;}
	.filter-field-date-combobox .filter-date-interval-after .filter-date-interval-to{display:inline;}
	.filter-field-date-combobox .filter-date-interval-before .filter-date-interval-from{display:inline;}
	.filter-field-date-combobox .filter-day-interval {display:none;}
	.filter-field-date-combobox .filter-day-interval-selected {display:inline;}
	.webform-content {
		padding: 7px 20px 15px 16px;
	}
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

	.adm-filter-box-sizing { width: auto; }
	.reports-title-label { padding-top: 20px; }
</style>

<script type="text/javascript">

	BX.message({'REPORT_DEFAULT_TITLE': '<?=CUtil::JSEscape(GetMessage('REPORT_DEFAULT_TITLE'))?>'});
	BX.message({'REPORT_ADD': '<?=CUtil::JSEscape(GetMessage('REPORT_ADD'))?>'});
	BX.message({'REPORT_CANCEL': '<?=CUtil::JSEscape(GetMessage('REPORT_CANCEL'))?>'});
	BX.message({'REPORT_PRCNT_VIEW_IS_NOT_AVAILABLE': '<?=CUtil::JSEscape(GetMessage('REPORT_PRCNT_VIEW_IS_NOT_AVAILABLE'))?>'});
	BX.message({'REPORT_PRCNT_BUTTON_TITLE': '<?=CUtil::JSEscape(GetMessage('REPORT_PRCNT_BUTTON_TITLE'))?>'});

</script>

<!-- The form is defined in a body of administrative page -->
<?php echo bitrix_sessid_post('csrf_token')?>

<div class="reports-constructor">

<div class="adm-filter-wrap">
	<?
		$_title = '';
		if (!empty($arResult['report']['TITLE'])) $_title = $arResult['report']['TITLE'];
	?>
	<div class="adm-input-wrap">
		<div class="reports-title-label"><?=GetMessage('REPORT_TITLE')?></div>

		<input style="padding-left: 5px; padding-right: 5px;" class="adm-input" type="text" id="reports-new-title" name="report_title" value="<?=htmlspecialcharsbx($_title)?>" />

		<div class="reports-title-label"><?=GetMessage('REPORT_DESCRIPTION')?></div>

		<div style="padding-left: 0px; padding-right: 10px;">
			<textarea rows="5" style="padding-left: 5px; padding-right: 5px; width: 100%;" name="report_description"><?=htmlspecialcharsbx($arResult['report']['DESCRIPTION'])?></textarea>
		</div>
	</div>



	<?php /*    Site option    */ ?>
	<? if (isset($arParams['F_SALE_SITE'])): ?>
	<div class="reports-title-label"><?=GetMessage('SALE_REPORT_SITE')?></div>

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
	<? endif; ?>



	<div class="reports-title-label"><?=GetMessage('REPORT_PERIOD')?></div>

	<?
	$_date_from = '';
	$_date_to = '';
	if ($arResult["preSettings"]["period"]['type'] == 'interval')
	{
		$_date_from = ConvertTimeStamp($arResult["preSettings"]["period"]['value'][0], 'SHORT');
		$_date_to = ConvertTimeStamp($arResult["preSettings"]["period"]['value'][1], 'SHORT');
	}
	else if ($arResult["preSettings"]["period"]['type'] == 'before')
	{
		$_date_from = ConvertTimeStamp($arResult["preSettings"]["period"]['value'], 'SHORT');
	}
	else if ($arResult["preSettings"]["period"]['type'] == 'after')
	{
		$_date_to = ConvertTimeStamp($arResult["preSettings"]["period"]['value'], 'SHORT');
	}
	?>
	<!-- stub -->
	<div style="display: none;">
		<select id="task-interval-filter">
			<option value="" selected></option>
		</select>
		<span class="filter-date-interval"></span>
		<span class="filter-day-interval">	</span>
	</div>

	<!-- period -->
	<div class="adm-filter-alignment adm-calendar-block">
		<div class="adm-filter-box-sizing">
			<!-- period select -->
			<span class="adm-select-wrap adm-calendar-period">
				<select onchange="OnReportIntervalChange(this)" name="F_DATE_TYPE"
						id="report-interval-filter" class="adm-select adm-calendar-period">
					<?php foreach($arResult['periodTypes'] as $key):?>
					<option value="<?=htmlspecialcharsbx($key)?>"<?=($key == $arResult["preSettings"]["period"]['type']) ? ' selected' : ''?>><?=GetMessage('REPORT_CALEND_'.strtoupper(htmlspecialcharsbx($key)))?></option>
					<?php endforeach;?>
				</select>
			</span>
			<!-- filter date from -->
			<div style="display: none;" class="adm-input-wrap adm-calendar-inp adm-calendar-first">
				<input type="text" value="<?=$_date_from?>" size="10" name="F_DATE_FROM"
					id="REPORT_INTERVAL_F_DATE_FROM" class="adm-input adm-calendar-from">
				<img onclick="BX.calendar({node:this, field:'REPORT_INTERVAL_F_DATE_FROM', form: '', bTime: false, bHideTime: false});"
							title="<?php echo GetMessage("TASKS_PICK_DATE")?>" class="adm-calendar-icon">
			</div>
			<!-- filter separator -->
			<span style="display: none;" class="adm-calendar-separate"></span>
			<!-- filter date to -->
			<div style="display: none;" class="adm-input-wrap adm-calendar-second">
				<input type="text" value="<?=$_date_to?>" size="10" name="F_DATE_TO"
					id="REPORT_INTERVAL_F_DATE_TO" class="adm-input adm-calendar-to">
				<img onclick="BX.calendar({node:this, field:'REPORT_INTERVAL_F_DATE_TO', form: '', bTime: false, bHideTime: false});"
					title="<?php echo GetMessage("TASKS_PICK_DATE")?>" class="adm-calendar-icon">
			</div>
			<!-- days field -->
			<div style="display: none;" class="adm-input-wrap filter-day-interval">
				<span class="<?php if ($arResult["preSettings"]["period"]['type'] == "days"): ?>filter-day-interval-selected<?php endif; ?>">
					<input type="text" size="5" class="filter-date-days"
						value="<?php echo $arResult["preSettings"]["period"]['type'] == "days" ? $arResult["preSettings"]["period"]['value'] : ""?>"
						name="F_DATE_DAYS"/>
				</span>
				<span> <?php echo GetMessage("REPORT_CALEND_REPORT_DAYS")?></span>
			</div>
		</div>
	</div>
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
			else if(select.value == "before") filterDateTo.style.display = 'inline-block';
			else if(select.value == "after") filterDateFrom.style.display = 'inline-block';
			else if(select.value == "days") filterDays.style.display = 'inline-block';
		}

		BX.ready(function() {
			OnReportIntervalChange(BX('report-interval-filter'));
		});
	</script>
</div>

<!-- select -->
<div class="reports-content-block-disabled-style" id='report_columns_list'>
	<span class="reports-content-block-title"><?=GetMessage('REPORT_COLUMNS')?></span>
	<div class="reports-add-columns-block" id="reports-add-columns-block">

		<div id="reports-forming-column-example" style="display: none">
			<input type="hidden" name="report_select_columns[%s][name]" />
			<span class="reports-add-col-checkbox">
				<input type="checkbox" class="reports-checkbox"/>
			</span><span
			class="reports-add-col-buttons-bl">
				<span class="reports-add-col-button-down"></span><span class="reports-add-col-button-up"></span>
			</span><span
			class="reports-add-col-title"><span
			class="reports-add-col-tit-text"></span><span
			class="reports-add-col-input"><input type="text" name="report_select_columns[%s][alias]"/><span
			class="reports-add-col-inp-title"><?=GetMessage('REPORT_NEW_COLUMN_TITLE')?></span></span><span
			class="reports-add-col-tit-prcnt" title="<?=GetMessage('REPORT_PRCNT_BUTTON_TITLE')?>"></span><span
			class="reports-add-col-tit-edit" title="<?=GetMessage('REPORT_CHANGE_COLUMN_TITLE')?>"></span><span
			class="reports-add-col-tit-remove" title="<?=GetMessage('REPORT_REMOVE_COLUMN')?>"></span></span>
		</div>

		<div class="reports-add-column" id="reports-add-column-block">
			<span class="reports-checkbox-arrow"></span>
			<span class="reports-checkbox-title"><?=GetMessage('REPORT_CALC_COLUMN')?></span><span
			class="reports-add-column-link reports-dashed" id="reports-add-select-column-button"><?=GetMessage('REPORT_ADD_SELECT_COLUMN')?></span>
		</div>
	</div>
	<div class="reports-sort-column">
		<span class="reports-content-block-title"><?=GetMessage('REPORT_SORT_BY_SELECT_COLUMN')?></span><select class="reports-sort-select" id="reports-sort-select" name="reports_sort_select"><option value="_">_</option></select>
		<select name="reports_sort_type_select" id="reports-sort-type-select" class="reports-sort-type-select"><option value="ASC"><?=GetMessage('REPORT_SORT_TYPE_ASC')?></option><option value="DESC"><?=GetMessage('REPORT_SORT_TYPE_DESC')?></option></select>
	</div>

	<script type="text/javascript">

		BX.ready(function() {

			GLOBAL_REPORT_SELECT_COLUMN_COUNT = 0;

		<? foreach ($arResult['preSettings']['select'] as $num => $selElem): ?>
				addSelectColumn(BX.findChild(
					BX('reports-add_col-popup-cont'),
					{tag:'input', attr:{type:'checkbox', name:'<?=CUtil::JSEscape($selElem['name'])?>'}}, true
				),
					'<?=strlen($selElem['aggr']) ? CUtil::JSEscape($selElem['aggr']) : ''?>',
					'<?=strlen($selElem['alias']) ? CUtil::JSEscape($selElem['alias']) : ''?>',
					<?=$num?>);
				<? endforeach; ?>

		<? foreach ($arResult['preSettings']['select'] as $num => $selElem): ?>
				<? if (strlen($selElem['prcnt'])): ?>
					setPrcntView(<?=$num?>, '<?=CUtil::JSEscape($selElem['prcnt'])?>');
					<? endif; ?>
				<? endforeach; ?>

		<? if (array_key_exists("sort", $arResult["preSettings"])): ?>
				// add default sort
				setSelectValue(BX('reports-sort-select'), '<?=CUtil::JSEscape($arResult["preSettings"]['sort'])?>');
				<? endif; ?>

		<? if (array_key_exists("sort_type", $arResult["preSettings"])): ?>
				// add default sort
				setSelectValue(BX('reports-sort-type-select'), '<?=CUtil::JSEscape($arResult["preSettings"]['sort_type'])?>');
				<? endif; ?>

			startSubFilterRestore();
		});

	</script>
</div>

<!-- filters -->
<div class="webform-additional-fields">
	<div class="reports-content-block-disabled-style">
		<span class="reports-content-block-title reports-title-filter"><?=GetMessage('REPORT_FILTER')?></span>

		<div class="reports-filter-block">

			<div class="reports-limit-results" id="reports-filter-base-andor-selector">
				<span class="reports-limit-res-select-lable"><?=GetMessage('REPORT_RESULT_LIMIT_BY')?></span><select filterId="0" class="reports-limit-res-select">
				<option value="AND"><?=GetMessage('REPORT_ANDOR_AND')?></option>
				<option value="OR"><?=GetMessage('REPORT_ANDOR_OR')?></option>
			</select><span class="reports-limit-res-select-lable" id="reports-filter-base-andor-selector-text-and"><?=GetMessage('REPORT_RESULT_LIMIT_CONDITIONS')?></span><span class="reports-limit-res-select-lable" id="reports-filter-base-andor-selector-text-or" style="display: none;"><?=GetMessage('REPORT_RESULT_LIMIT_CONDITION')?></span>
			</div>

			<div id="reports-filter-item-example" style="display: none;">
				<span class="reports-filter-item-name"><span class="reports-dashed"><?=GetMessage('REPORT_CHOOSE_FIELD')?></span></span><span class="reports-filter-butt-wrap"><span class="reports-filter-del-item"><i></i></span><span
				class="reports-filter-add-item"><i></i></span><span
				class="reports-filter-and-or"><span class="reports-filter-and-or-text"><?=GetMessage('REPORT_ANDOR')?></span><i></i></span><input type="checkbox" class="reports-checkbox" name="changeable" checked /></span>
			</div>

			<div id="reports-filter-andor-container-example" style="display: none" class="reports-filter-andor-container">
				<div class="reports-filter-andor-item">
					<select style="width: 80px;">
						<option value="AND"><?=GetMessage('REPORT_ANDOR_ALL')?></option>
						<option value="OR"><?=GetMessage('REPORT_ANDOR_ANY')?></option>
					</select>
					<span class="reports-limit-res-select-lable reports-limit-res-select-lable-and"><?=GetMessage('REPORT_ANDOR_ALL_LABEL')?></span><span class="reports-limit-res-select-lable reports-limit-res-select-lable-or" style="display: none;"><?=GetMessage('REPORT_ANDOR_ANY_LABEL')?></span><span class="reports-filter-butt-wrap"><span class="reports-filter-del-item"><i></i></span><span
					class="reports-filter-add-item"><i></i></span><span
					class="reports-filter-and-or"><span class="reports-filter-and-or-text"><?=GetMessage('REPORT_ANDOR')?></span><i></i></span><input type="checkbox" class="reports-checkbox" disabled/></span>
				</div>
			</div>

			<div class="reports-filter-andor-container" id="reports-filter-columns-container"></div>

			<div class="reports-filter-checkbox-title">
				<span class="reports-filter-checkbox-arrow"></span>
				<span class="reports-filter-checkbox-tit-text"><?=GetMessage('REPORT_CHANGE_FILTER_IN_VIEW')?></span>
			</div>
		</div>

		<script type="text/javascript">

			var GLOBAL_REPORT_FILTER_COUNT = 1;
			var GLOBAL_PRE_FILTERS = null;

			<? if (!empty($arResult["preSettings"]["filter"])): ?>
			var GLOBAL_PRE_FILTERS = <?=CUtil::PhpToJSObject($arResult["preSettings"]["filter"])?>;
				<? endif; ?>

			BX.ready(function() {
			<? if (!empty($arResult["preSettings"]["limit"])): ?>
					// add default limit
					BX('report-filter-limit-checkbox').checked = true;
					BX('report-filter-limit-input').disabled = false;
					BX('report-filter-limit-input').style.backgroundColor = '#ffffff';
					BX('report-filter-limit-input').value = "<?=$arResult["preSettings"]["limit"]?>";
					<? endif; ?>
			});

		</script>

		<div class="reports-filter-quan-item">
			<input type="checkbox" class="reports-checkbox" id="report-filter-limit-checkbox"/>
			<span class="reports-limit-res-select-lable"><label for="report-filter-limit-checkbox"><?=GetMessage('REPORT_RESULT_LIMIT')?></label></span>
			<input type="text" class="reports-filter-quan-inp" id="report-filter-limit-input" name="report_filter_limit" disabled/>
		</div>
	</div>
	<div class="webform-corners-bottom">
		<div class="webform-left-corner"></div>
		<div class="webform-right-corner"></div>
	</div>
</div>

<!-- preview -->
<div class="reports-preview-table-report" id="reports-preview-table-report">
	<span class="reports-prev-table-title"><?=GetMessage('REPORT_SCHEME_PREVIEW')?></span>

	<div class="reports-list">
		<div class="reports-list-left-corner"></div>
		<div class="reports-list-right-corner"></div>
		<table cellspacing="0" class="report-list-table">
			<tr>
				<th></th>
			</tr>
		</table>
	</div>
</div>

</div>

<!-- add select column popup -->
<div class="reports-add_col-popup-cont" id="reports-add_col-popup-cont" style="display:none;">
	<div class="reports-add_col-popup-title">
		<?=GetMessage('REPORT_POPUP_COLUMN_TITLE'.'_'.call_user_func(array($arParams['REPORT_HELPER_CLASS'], 'getOwnerId')))?>
	</div>
	<div class="popup-window-hr popup-window-buttons-hr"><i></i></div>
	<div class="reports-add_col-popup">
		<?=call_user_func(array($arParams['REPORT_HELPER_CLASS'], 'buildHTMLSelectTreePopup'), $arResult['fieldsTree'])?>
	</div>
</div>

<!-- choose filter column popup -->
<div class="reports-add_col-popup-cont reports-add_filcol-popup-cont" id="reports-add_filcol-popup-cont" style="display:none;">
	<div class="reports-add_col-popup-title">
		<?=GetMessage('REPORT_POPUP_FILTER_TITLE'.'_'.call_user_func(array($arParams['REPORT_HELPER_CLASS'], 'getOwnerId')))?>
	</div>
	<div class="popup-window-hr popup-window-buttons-hr"><i></i></div>
	<div class="reports-add_col-popup">
		<?=call_user_func(array($arParams['REPORT_HELPER_CLASS'], 'buildHTMLSelectTreePopup'), $arResult['fieldsTree'], true)?>
	</div>
</div>

<!-- percent view examples -->
<div id="report-select-prcnt-examples" style="display: none">
	<select class="reports-add-col-select-prcnt" style="margin-left: 4px; display: none;" disabled>
		<option value="self_column"><?=GetMessage('REPORT_PRCNT_BY_COLUMN')?></option>
		<option value="other_field"><?=GetMessage('REPORT_PRCNT_BY_FIELD')?></option>
	</select>
	<select	class="reports-add-col-select-prcnt-by" style="display: none;" disabled>
	</select>
</div>

<!-- select calc examples -->
<div id="report-select-calc-examples" style="display: none">
	<? foreach($arResult['calcVariations'] as $key => $values): ?>
	<select id="report-select-calc-<?=$key?>" disabled>
		<? foreach ($values as $v): ?>
		<option value="<?=htmlspecialcharsbx($v)?>"><?=GetMessage('REPORT_SELECT_CALC_VAR_'.htmlspecialcharsbx($v))?></option>
		<? endforeach; ?>
	</select>
	<? endforeach; ?>
</div>

<!-- filter compare examples -->
<div id="report-filter-compare-examples" style="display: none">
	<? foreach($arResult['compareVariations'] as $key => $values): ?>
	<select id="report-filter-compare-<?=htmlspecialcharsbx($key)?>" class="report-filter-compare-<?=htmlspecialcharsbx($key)?>">
		<? foreach ($values as $v): ?>
		<option value="<?=$v?>"><?=GetMessage('REPORT_FILTER_COMPARE_VAR_'.$v)?></option>
		<? endforeach; ?>
	</select>
	<? endforeach; ?>
</div>

<!-- filter value control examples -->
<div id="report-filter-value-control-examples" style="display: none">

	<span name="report-filter-value-control-integer">
		<input type="text" name="value" />
	</span>

	<span name="report-filter-value-control-float">
		<input type="text" name="value" />
	</span>

	<span name="report-filter-value-control-string">
		<input type="text" name="value" />
	</span>

	<span name="report-filter-value-control-boolean">
		<select class="report-filter-select" name="value">
			<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
			<option value="true"><?=GetMessage('REPORT_BOOLEAN_VALUE_TRUE')?></option>
			<option value="false"><?=GetMessage('REPORT_BOOLEAN_VALUE_FALSE')?></option>
		</select>
	</span>

	<span name="report-filter-value-control-datetime" class="report-filter-calendar">
		<input type="text" class="reports-filter-input" name="value" /><img alt="img" class="reports-filt-calen-img" src="/bitrix/js/main/core/images/calendar-icon.gif" />
	</span>

</div>
