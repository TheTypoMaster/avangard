<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?><span class="ilike-light"><span class="bx-ilike-button <?=($arResult['VOTE_AVAILABLE'] == 'Y'? '': 'bx-ilike-button-disable')?>" id="bx-ilike-button-<?=CUtil::JSEscape(htmlspecialchars($arResult['VOTE_ID']))?>"><span class="bx-ilike-right-wrap <?=($arResult['USER_HAS_VOTED'] == 'N'? '': 'bx-you-like')?>"><span class="bx-ilike-right"><?=htmlspecialcharsEx($arResult['TOTAL_VOTES'])?></span></span><span class="bx-ilike-left-wrap" <?=($arResult['VOTE_AVAILABLE'] == 'Y'? '': 'title="'.$arResult['ALLOW_VOTE']['ERROR_MSG'].'"')?>><?
	if($arResult['VOTE_AVAILABLE'] == 'Y'):
		?><a href="#like" class="bx-ilike-text"><?=($arResult['USER_HAS_VOTED'] == 'N'? CUtil::JSEscape(htmlspecialchars($arResult['RATING_TEXT_LIKE_Y'])): CUtil::JSEscape(htmlspecialchars($arResult['RATING_TEXT_LIKE_N'])))?></a><?
	endif;?></span></span><span class="bx-ilike-wrap-block" id="bx-ilike-popup-cont-<?=CUtil::JSEscape(htmlspecialchars($arResult['VOTE_ID']))?>" style="display:none;"><span class="bx-ilike-popup"><span class="bx-ilike-wait"></span></span></span></span>
<script type="text/javascript">
BX.ready(function() {	
	var f = function () {
	<?if ($arResult['AJAX_MODE'] == 'Y'):?>
		BX.loadCSS('/bitrix/components/bitrix/rating.vote/templates/like/popup.css');
		BX.loadCSS('/bitrix/components/bitrix/rating.vote/templates/like/style.css');
		setTimeout(function(){
	<?endif;?>
			RatingLike.Set(
				'<?=CUtil::JSEscape(htmlspecialchars($arResult['VOTE_ID']))?>', 
				'<?=CUtil::JSEscape(htmlspecialchars($arResult['ENTITY_TYPE_ID']))?>', 
				'<?=IntVal($arResult['ENTITY_ID'])?>', 
				'<?=CUtil::JSEscape(htmlspecialchars($arResult['VOTE_AVAILABLE']))?>',
				'<?=$USER->GetId()?>',
				{'LIKE_Y' : '<?=CUtil::JSEscape(htmlspecialchars($arResult['RATING_TEXT_LIKE_N']))?>', 'LIKE_N' : '<?=CUtil::JSEscape(htmlspecialchars($arResult['RATING_TEXT_LIKE_Y']))?>', 'LIKE_D' : '<?=CUtil::JSEscape(htmlspecialchars($arResult['RATING_TEXT_LIKE_D']))?>'},
				'light',
				'<?=CUtil::JSEscape(htmlspecialchars($arResult['PATH_TO_USER_PROFILE']))?>'
			);
	<?if ($arResult['AJAX_MODE'] == 'Y'):?>	
		}, 200);
	<?endif;?>
	}
	var q = function()	{	if (!window.RatingLike)	{setTimeout(q, 200);}	else {f();}	}
	if (!window.RatingLike && !window.bRatingLikeLoading)	{window.bRatingLikeLoading = true;	BX.loadScript('/bitrix/js/main/rating_like.js', f);} else {q();}
});
</script>