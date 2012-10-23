<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<span id="rating-vote-<?=CUtil::JSEscape(htmlspecialchars($arResult['VOTE_ID']))?>" class="rating-vote <?=($arResult['VOTE_AVAILABLE'] == 'N' ? 'rating-vote-disabled' : '')?>" title="<?=($arResult['VOTE_AVAILABLE'] == 'N'? $arResult['ALLOW_VOTE']['ERROR_MSG'] : '')?>">
	<span id="rating-vote-<?=CUtil::JSEscape(htmlspecialchars($arResult['VOTE_ID']))?>-result" class="rating-vote-result rating-vote-result-<?=($arResult['TOTAL_VALUE'] < 0 ? 'minus' : 'plus')?>" title="<?=CUtil::JSEscape(htmlspecialchars($arResult['VOTE_TITLE']))?>"> <?=htmlspecialchars($arResult['TOTAL_VALUE'])?></span>
	<a id="rating-vote-<?=CUtil::JSEscape(htmlspecialchars($arResult['VOTE_ID']))?>-plus" class="rating-vote-plus <?=($arResult['VOTE_BUTTON'] == 'PLUS'? 'rating-vote-plus-active': '')?>" title="<?=$arResult['VOTE_AVAILABLE'] == 'N'? '' : ($arResult['VOTE_BUTTON'] == 'PLUS'? GetMessage("RATING_COMPONENT_CANCEL"): GetMessage("RATING_COMPONENT_PLUS"))?>"></a>&nbsp;<a id="rating-vote-<?=CUtil::JSEscape(htmlspecialchars($arResult['VOTE_ID']))?>-minus" class="rating-vote-minus <?=($arResult['VOTE_BUTTON'] == 'MINUS'? 'rating-vote-minus-active': '')?>"  title="<?=$arResult['VOTE_AVAILABLE'] == 'N'? '' : ($arResult['VOTE_BUTTON'] == 'MINUS'? GetMessage("RATING_COMPONENT_CANCEL"): GetMessage("RATING_COMPONENT_MINUS"))?>"></a>
</span>
<script type="text/javascript">
BX.ready(function(){
	var f = function () {
	<?if ($arResult['AJAX_MODE'] == 'Y'):?>
		BX.loadCSS('/bitrix/components/bitrix/rating.vote/templates/standart/style.css');
		setTimeout(function(){
	<?endif;?>
			Rating.Set(
				'<?=CUtil::JSEscape(htmlspecialchars($arResult['VOTE_ID']))?>',
				'<?=CUtil::JSEscape(htmlspecialchars($arResult['ENTITY_TYPE_ID']))?>',
				'<?=IntVal($arResult['ENTITY_ID'])?>',
				'<?=CUtil::JSEscape(htmlspecialchars($arResult['VOTE_AVAILABLE']))?>',
				'<?=$USER->GetId()?>',
				{'PLUS' : '<?=GetMessage("RATING_COMPONENT_PLUS")?>', 'MINUS' : '<?=GetMessage("RATING_COMPONENT_MINUS")?>', 'CANCEL' : '<?=GetMessage("RATING_COMPONENT_CANCEL")?>'},
				'standart',
				'<?=CUtil::JSEscape(htmlspecialchars($arResult['PATH_TO_USER_PROFILE']))?>'
			);
	<?if ($arResult['AJAX_MODE'] == 'Y'):?>
		}, 200);
	<?endif;?>
	}
	var q = function()	{	if (!window.Rating)	{setTimeout(q, 200);}	else {f();}	}
	if (!window.Rating && !window.bRatingsLoading)	{window.bRatingsLoading = true;	BX.loadScript('/bitrix/js/main/rating.js', f);} else {q();}
});
</script>