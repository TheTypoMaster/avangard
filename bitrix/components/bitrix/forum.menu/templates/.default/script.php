<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
var oText = {
	 'del_topics': '<?=GetMessage("JS_DEL_TOPICS")?>'
	,'del_topic': '<?=GetMessage("JS_DEL_TOPIC")?>'
	,'del_messages': '<?=GetMessage("JS_DEL_MESSAGES")?>'
	,'del_message': '<?=GetMessage("JS_DEL_MESSAGE")?>'
	,'no_data' : '<?=(($arResult["sSection"] == "LIST") ? GetMessage('JS_NO_TOPICS') : GetMessage('JS_NO_MESSAGES'));?>'
};