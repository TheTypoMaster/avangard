<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult["QUESTION"])):?>

<div class="learn-test-tabs"><?=GetMessage("LEARNING_QUESTION_TITLE");?>&nbsp;

<?if ($arResult["TEST"]["PASSAGE_TYPE"] == 2 && $arResult["NAV"]["PREV_NOANSWER"] != $arResult["NAV"]["PREV_QUESTION"] && $arResult["NAV"]["PREV_NOANSWER"]):?>

	<a class="previous" href="<?=$arResult["QBAR"][$arResult["NAV"]["PREV_NOANSWER"]]["URL"]?>" title="<?=GetMessage("LEARNING_QBAR_PREVIOUS_NOANSWER_TITLE")?>">&lsaquo;&lsaquo;</a>
	<a class="first" href="<?=$arResult["QBAR"][$arResult["NAV"]["PREV_QUESTION"]]["URL"]?>" title="<?=GetMessage("LEARNING_QBAR_PREVIOUS_TITLE")?>">&lsaquo;</a>

<?elseif ($arResult["NAV"]["PREV_QUESTION"]):?>
	<a class="previous" href="<?=$arResult["QBAR"][$arResult["NAV"]["PREV_QUESTION"]]["URL"]?>" title="<?=GetMessage("LEARNING_QBAR_PREVIOUS_TITLE")?>">&lsaquo;</a>
<?endif?>


<?while($arResult["NAV"]["START_PAGE"] <= $arResult["NAV"]["END_PAGE"]):?>

	<?if ($arResult["NAV"]["START_PAGE"] == $arResult["NAV"]["PAGE_NUMBER"]):?>
		<a class="selected" title="<?=GetMessage("LEARNING_QBAR_CURRENT_TITLE")?>">&nbsp;<?=$arResult["NAV"]["START_PAGE"]?>&nbsp;</a>
	<?elseif ($arResult["QBAR"][$arResult["NAV"]["START_PAGE"]]["ANSWERED"] == "Y"):?>

		<?if ($arResult["TEST"]["PASSAGE_TYPE"] == 2):?>
			<a href="<?=$arResult["QBAR"][$arResult["NAV"]["START_PAGE"]]["URL"]?>" class="answered" title="<?=GetMessage("LEARNING_QBAR_ANSWERED_TITLE")?>">&nbsp;<?=$arResult["NAV"]["START_PAGE"]?>&nbsp;</a>
		<?else:?>
			<a class="disabled" title="<?=GetMessage("LEARNING_QBAR_ANSWERED_TITLE")?>">&nbsp;<?=$arResult["NAV"]["START_PAGE"]?>&nbsp;</a>
		<?endif?>

	<?else:?>

		<?if ($arResult["TEST"]["PASSAGE_TYPE"] == 0):?>
		<a title="<?=GetMessage("LEARNING_QBAR_NOANSWERED_TITLE")?>">&nbsp;<?=$arResult["NAV"]["START_PAGE"]?>&nbsp;</a>
		<?else:?>
		<a title="<?=GetMessage("LEARNING_QBAR_NOANSWERED_TITLE")?>" href="<?=$arResult["QBAR"][$arResult["NAV"]["START_PAGE"]]["URL"]?>">&nbsp;<?=$arResult["NAV"]["START_PAGE"]?>&nbsp;</a>
		<?endif?>

	<?endif;?>

<?
$arResult["NAV"]["START_PAGE"]++;
endwhile;
?>

<?if ($arResult["TEST"]["PASSAGE_TYPE"] == 2 && $arResult["NAV"]["NEXT_NOANSWER"] != $arResult["NAV"]["NEXT_QUESTION"] && $arResult["NAV"]["NEXT_NOANSWER"]):?>

	<a class="last" href="<?=$arResult["QBAR"][$arResult["NAV"]["NEXT_QUESTION"]]["URL"]?>" title="<?=GetMessage("LEARNING_QBAR_NEXT_TITLE")?>">&rsaquo;</a>
	<a class="next" href="<?=$arResult["QBAR"][$arResult["NAV"]["NEXT_NOANSWER"]]["URL"]?>" title="<?=GetMessage("LEARNING_QBAR_NEXT_NOANSWER_TITLE")?>">&rsaquo;&rsaquo;</a>

<?elseif ($arResult["NAV"]["NEXT_QUESTION"]):?>
	<a class="next" href="<?=$arResult["QBAR"][$arResult["NAV"]["NEXT_QUESTION"]]["URL"]?>" title="<?=GetMessage("LEARNING_QBAR_NEXT_TITLE")?>">&rsaquo;</a>
<?endif?>

<?if ($arResult["TEST"]["TIME_LIMIT"]>0 && $arParams["SHOW_TIME_LIMIT"] == "Y"):?>
	<div id="learn-test-timer" title="<?=GetMessage("LEARNING_TEST_TIME_LIMIT");?>"><?=$arResult["SECONDS_TO_END_STRING"]?></div>
	<script type="text/javascript">
		var clockID = null; clockID = setTimeout("UpdateClock(<?=$arResult["SECONDS_TO_END"]?>)", 950);
	</script>
<?endif?>

</div>



<div class="learn-question-cloud">
	<div class="learn-question-number"><?=GetMessage("LEARNING_QUESTION_TITLE")?><br />
		<?=$arResult["NAV"]["PAGE_NUMBER"]?> <?=GetMessage("LEARNING_QUESTION_OF");?> <?=$arResult["NAV"]["PAGE_COUNT"]?>
	</div>
	<div class="learn-question-name"><?=$arResult["QUESTION"]["NAME"]?>
		<?if (strlen($arResult["QUESTION"]["DESCRIPTION"]) > 0):?>
			<br /><br /><?=$arResult["QUESTION"]["DESCRIPTION"]?>
		<?endif?>
	
		<?if ($arResult["QUESTION"]["FILE"] !== false):?>
			<br /><br /><img src="<?=$arResult["QUESTION"]["FILE"]["SRC"]?>" width="<?=$arResult["QUESTION"]["FILE"]["WIDTH"]?>" height="<?=$arResult["QUESTION"]["FILE"]["HEIGHT"]?>" />
		<?endif?>
	</div>
</div>

<br /><b><?=GetMessage("LEARNING_CHOOSE_ANSWER")?>:</b>

<form name="learn_test_answer" action="<?=$arResult["ACTION_PAGE"]?>" method="post">
<input type="hidden" name="TEST_RESULT" value="<?=$arResult["QBAR"][$arResult["NAV"]["PAGE_NUMBER"]]["ID"]?>">
<input type="hidden" name="<?=$arParams["PAGE_NUMBER_VARIABLE"]?>" value="<?=($arResult["NAV"]["PAGE_NUMBER"] + 1)?>">
<input type="hidden" name="back_page" value="<?=$arResult["REDIRECT_PAGE"]?>" />

<?foreach($arResult["QUESTION"]["ANSWERS"] as $arAnswer):?>

	<?if ($arResult["QUESTION"]["QUESTION_TYPE"] == "M"):?>
		<label><input type="checkbox" name="answer[]" value="<?=$arAnswer["ID"]?>" <?if (in_array($arAnswer["ID"], $arResult["QBAR"][$arResult["NAV"]["PAGE_NUMBER"]]["RESPONSE"])):?>checked <?endif?>/>&nbsp;<?=$arAnswer["ANSWER"]?></label><br />
	<?else:?>
		<label><input type="radio" name="answer" value="<?=$arAnswer["ID"]?>" <?if (in_array($arAnswer["ID"], $arResult["QBAR"][$arResult["NAV"]["PAGE_NUMBER"]]["RESPONSE"])):?>checked <?endif?>/>&nbsp;<?=$arAnswer["ANSWER"]?></label><br />
	<?endif?>

<?endforeach?>

	<br />

	<?if ($arResult["TEST"]["PASSAGE_TYPE"] > 0 && $arResult["NAV"]["PREV_QUESTION"]):?>
		<input type="submit" name="previous" onClick="javascript:window.location='<?=$arResult["QBAR"][$arResult["NAV"]["PREV_QUESTION"]]["URL"]?>'; return false;" value="<?=GetMessage("LEARNING_BTN_PREVIOUS")?>" />
	<?endif?>

	<input type="submit" name="next" value="<?=GetMessage("LEARNING_BTN_NEXT")?>"<?if ($arResult["TEST"]["PASSAGE_TYPE"] == 0):?> OnClick="return checkForEmpty('<?=GetMessage("LEARNING_NO_RESPONSE_CONFIRM")?>');"<?endif?>>
	&nbsp;&nbsp;&nbsp;
	<input type="submit" name="finish" value="<?=GetMessage("LEARNING_BTN_FINISH")?>" onClick="return confirm('<?=GetMessage("LEARNING_BTN_CONFIRM_FINISH")?>')">
	<input type="hidden" name="ANSWERED" value="Y">

</form>

<?elseif ($arResult["TEST_FINISHED"] === true):?>

	<?ShowError($arResult["ERROR_MESSAGE"]);?>
	<?ShowNote(GetMessage("LEARNING_COMPLETED"));?>

	<a href="<?=$arResult["GRADEBOOK_URL"]?>"><?=GetMessage("LEARNING_PROFILE")?></a>

<?elseif (strlen($arResult["ERROR_MESSAGE"]) > 0):?>

	<?ShowError($arResult["ERROR_MESSAGE"]);?>
	<br />
	<form name="learn_test_start" method="post" action="<?=$arResult["ACTION_PAGE"]?>">
	<input type="hidden" name="back_page" value="<?=$arResult["REDIRECT_PAGE"]?>" />
	<input type="submit" name="next" value="<?=GetMessage("LEARNING_BTN_CONTINUE")?>">
	</form>

<?else:?>

	<?=GetMessage("LEARNING_TEST_NAME")?>: <?=$arResult["TEST"]["NAME"];?><br />
	<?if (strlen($arResult["TEST"]["DESCRIPTION"]) > 0):?>
		<?=$arResult["TEST"]["DESCRIPTION"]?><br />
	<?endif?>
	
	<?if ($arResult["TEST"]["ATTEMPT_LIMIT"] > 0):?>
		<?=GetMessage("LEARNING_TEST_ATTEMPT_LIMIT")?>: <?=$arResult["TEST"]["ATTEMPT_LIMIT"]?>
	<?else:?>
		<?=GetMessage("LEARNING_TEST_ATTEMPT_LIMIT")?>: <?=GetMessage("LEARNING_TEST_ATTEMPT_UNLIMITED")?>
	<?endif?>
	<br />

	<?if ($arResult["TEST"]["TIME_LIMIT"] > 0):?>
		<?=GetMessage("LEARNING_TEST_TIME_LIMIT")?>: <?=$arResult["TEST"]["TIME_LIMIT"]?> <?=GetMessage("LEARNING_TEST_TIME_LIMIT_MIN")?>
	<?else:?>
		<?=GetMessage("LEARNING_TEST_TIME_LIMIT")?>: <?=GetMessage("LEARNING_TEST_TIME_LIMIT_UNLIMITED")?>
	<?endif?>
	<br />

	<?=GetMessage("LEARNING_PASSAGE_TYPE")?>:
	<?if ($arResult["TEST"]["PASSAGE_TYPE"] == 2):?>
		<?=GetMessage("LEARNING_PASSAGE_FOLLOW_EDIT")?>
	<?elseif ($arResult["TEST"]["PASSAGE_TYPE"] == 1):?>
		<?=GetMessage("LEARNING_PASSAGE_FOLLOW_NO_EDIT")?>
	<?else:?>
		<?=GetMessage("LEARNING_PASSAGE_NO_FOLLOW_NO_EDIT")?>
	<?endif?>
	<br />

	<br />
	<form name="learn_test_start" method="post" action="<?=$arResult["ACTION_PAGE"]?>">
	<input type="hidden" name="back_page" value="<?=$arResult["REDIRECT_PAGE"]?>" />
	<input type="submit" name="next" value="<?=GetMessage("LEARNING_BTN_START")?>">
	</form>

<?endif?>

