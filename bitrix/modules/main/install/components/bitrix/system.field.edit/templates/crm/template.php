<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CUtil::InitJSCore(array('ajax', 'popup'));

//$APPLICATION->SetAdditionalCSS('/bitrix/js/crm/css/crm.css');
?>
<link type="text/css" href="/bitrix/js/crm/css/crm.css" rel="stylesheet" />
<script type="text/javascript" src="/bitrix/js/crm/crm.js"></script>
<?
$fieldName = $arParams["arUserField"]["~FIELD_NAME"];
$formName = $arParams["form_name"];
?>
<div id="crm-<?=$fieldName?>-box">
	<div  class="crm-button-open">
		<a id="crm-<?=$fieldName?>-open" href="#open" onclick="obCrm['<?=$formName?>crm-<?=$fieldName?>-open'].Open()"><?=GetMessage('CRM_FF_CHOISE');?></a>
	</div>
</div>
<script type="text/javascript">
BX.ready(function() {
	setTimeout(function(){
		CRM.Set(BX('crm-<?=$fieldName?>-open'),
			'<?=CUtil::JSEscape($fieldName)?>', '',
			<?echo CUtil::PhpToJsObject($arResult['ELEMENT']);?>,
			<?=($arResult["PREFIX"]=='Y'? 'true': 'false')?>,
			<?=($arResult["MULTIPLE"]=='Y'? 'true': 'false')?>,
			<?echo CUtil::PhpToJsObject($arResult['ENTITY_TYPE']);?>,
			{
				'lead': '<?=CUtil::JSEscape(GetMessage('CRM_FF_LEAD'))?>',
				'contact': '<?=CUtil::JSEscape(GetMessage('CRM_FF_CONTACT'))?>',
				'company': '<?=CUtil::JSEscape(GetMessage('CRM_FF_COMPANY'))?>',
				'deal': '<?=CUtil::JSEscape(GetMessage('CRM_FF_DEAL'))?>',
				'ok': '<?=CUtil::JSEscape(GetMessage('CRM_FF_OK'))?>',
				'cancel': '<?=CUtil::JSEscape(GetMessage('CRM_FF_CANCEL'))?>',
				'close': '<?=CUtil::JSEscape(GetMessage('CRM_FF_CLOSE'))?>',
				'wait': '<?=CUtil::JSEscape(GetMessage('CRM_FF_SEARCH'))?>',
				'noresult': '<?=CUtil::JSEscape(GetMessage('CRM_FF_NO_RESULT'))?>',
				'add' : '<?=CUtil::JSEscape(GetMessage('CRM_FF_CHOISE'))?>',
				'edit' : '<?=CUtil::JSEscape(GetMessage('CRM_FF_CHANGE'))?>',
				'search' : '<?=CUtil::JSEscape(GetMessage('CRM_FF_SEARCH'))?>',
				'last' : '<?=CUtil::JSEscape(GetMessage('CRM_FF_LAST'))?>'
			});
	}, 100);
});
</script>