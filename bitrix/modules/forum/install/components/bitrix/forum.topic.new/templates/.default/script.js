function ForumSendMessage(ajax_type)
{
	this.AJAX_TYPE = (ajax_type == "Y" ? "Y" : "N");
	this.oData = [];
	this.SendData = function(form)
	{
		if (typeof(form) != "object")
			form = document.forms["REPLIER"];
			
		if (typeof(form) != "object")
			return false;
			
		this.oData = [];
		if (this.InitData(form) && this.CheckData(form))
		{
			if (this.AJAX_TYPE == "Y")
			{
				aSend = this.oData;
				aSend["AJAX_CALL"] = "Y";
				TID = CPHttpRequest.InitThread();
				PShowWaitWindow('send_message');
				
				CPHttpRequest.SetAction(TID, function(data){ForumReplaceNoteError(data)});
				CPHttpRequest.Post(TID, form.action, aSend);
			}
			else
			{
				form.submit();
			}
			return true;
		}
		return false;
	}
	
	this.CheckData = function(form)
	{
		if (typeof(form) != "object")
			return false;
		MessageMax = 64000;
		aError = [];
		MessageLength = this.oData["POST_MESSAGE"].length;
	
		if (MessageLength < 2)
			aError.push(oText['no_message']);
	    else if ((MessageMax != 0) && (MessageLength > MessageMax))
   			aError.push(oText['max_len1'] + MessageMax + oText['max_len2'] + MessageLength);
   		
		if (form["AUTHOR_NAME"] && form["AUTHOR_NAME"].value && this.oData["AUTHOR_NAME"].length <= 0)
			aError.push(oText['no_author_name']);
		if (form["AUTHOR_EMAIL"] && form["AUTHOR_EMAIL"].value && this.oData["AUTHOR_EMAIL"].length <= 0)
			aError.push(oText['no_author_email']);
		if (form["TITLE"] && form["TITLE"].value && this.oData["TITLE"].length <= 0)
			aError.push(oText['no_title']);
		
		if (aError.length > 0)
		{
			alert(aError.join("\n"));
			return false;
		}
		return true;
	}
	
	this.InitData = function(form)
	{
		this.oData = {
			"AJAX_CALL" : "",
			"PAGE_NAME" : "",
			"FID" : "",
			"TID" : "",
			"MID" : "",
			"sessid" : "",
			"TITLE" : "",
			"DESCRIPTION" : "",
			"TAGS" : "",
			"MESSAGE_TYPE" : "",
			"MESSAGE_MODE" : "",
			"POST_MESSAGE" : "",
			"AUTHOR_NAME" : "", 
			"AUTHOR_EMAIL" : "",
			"USE_SMILES" : "",
			"ATTACH_IMG" : "",
			"captcha_word" : "",
			"captcha_code" : "",
			// User Info
			"TORUM_SUBSCRIBE" : "N",
			"FORUM_SUBSCRIBE" : "N",
			
			"EDIT_ADD_REASON" : "N",
			"EDITOR_NAME" : "",
			"EDITOR_MAIL" : "",
			"EDIT_REASON" : "",
		};
		try
		{
			this.oData["AJAX_CALL"] = (form["AJAX_CALL"].value == "Y" ? "Y" : "N");
			this.oData["PAGE_NAME"] = (form["PAGE_NAME"].length > 0 ? form["PAGE_NAME"] : "topic_new");
			this.oData["FID"] = (form["FID"].value > 0 ? form["FID"].value : 0);
			this.oData["TID"] = (form["TID"].value > 0 ? form["TID"].value : 0);
			this.oData["MID"] = (form["MID"].value > 0 ? form["MID"].value : 0);
			this.oData["sessid"] = form["sessid"].value;
			this.oData["MESSAGE_TYPE"] = form["MESSAGE_TYPE"].value;
			this.oData["MESSAGE_MODE"] = form["MESSAGE_MODE"].value;
			this.oData["POST_MESSAGE"] = form["POST_MESSAGE"].value;
			
			this.oData["USE_SMILES"] = (form["USE_SMILES"].checked ? "Y" : "N");
			
			if (form["AUTHOR_NAME"] && form["AUTHOR_NAME"].value)
				this.oData["AUTHOR_NAME"] = form["AUTHOR_NAME"].value;
			if (form["AUTHOR_EMAIL"] && form["AUTHOR_EMAIL"].value)
				this.oData["AUTHOR_EMAIL"] = form["AUTHOR_EMAIL"].value;
			// Topic info
			if (form["TITLE"] && form["TITLE"].value)
				this.oData["TITLE"] = form["TITLE"].value;
			if (form["DESCRIPTION"] && form["DESCRIPTION"].value)
				this.oData["DESCRIPTION"] = form["DESCRIPTION"].value;
			if (form["TAGS"] && form["TAGS"].value)
				this.oData["TAGS"] = form["TAGS"].value;
			
			// User Info
			if (form["TOPIC_SUBSCRIBE"] && !form["TOPIC_SUBSCRIBE"].disabled)
				this.oData["TOPIC_SUBSCRIBE"] = form["TOPIC_SUBSCRIBE"].value;
			if (form["FORUM_SUBSCRIBE"] && !form["FORUM_SUBSCRIBE"].disabled)
				this.oData["FORUM_SUBSCRIBE"] = form["FORUM_SUBSCRIBE"].value;
			
			if (form["ATTACH_IMG"] && form["ATTACH_IMG"].value && form["ATTACH_IMG"].value.length > 0)
				this.oData["ATTACH_IMG"] = form["ATTACH_IMG"].value;
			if (form["ATTACH_IMG_del"] && form["ATTACH_IMG_del"].checked)
				this.oData["ATTACH_IMG_del"] = "Y";
			
				
			if (form["captcha_word"] && form["captcha_word"].value && form["captcha_word"].value.length > 0)
				this.oData["captcha_word"] = form["captcha_word"].value;
			if (form["captcha_code"] && form["captcha_code"].value && form["captcha_code"].value.length > 0)
				this.oData["captcha_code"] = form["captcha_code"].value;
				
			if (form["EDIT_ADD_REASON"] && form["EDIT_ADD_REASON"].value)
				this.oData["EDIT_ADD_REASON"] = (form["EDIT_ADD_REASON"].checked ? "Y" : "N");
			if (form["EDITOR_NAME"] && form["EDITOR_NAME"].value && form["EDITOR_NAME"].value.length > 0)
				this.oData["EDITOR_NAME"] = form["EDITOR_NAME"].value;
			if (form["EDITOR_MAIL"] && form["EDITOR_MAIL"].value && form["EDITOR_MAIL"].value.length > 0)
				this.oData["EDITOR_MAIL"] = form["EDITOR_MAIL"].value;
			if (form["EDIT_REASON"] && form["EDIT_REASON"].value && form["EDIT_REASON"].value.length > 0)
				this.oData["EDIT_REASON"] = form["EDIT_REASON"].value;

			return true;
		}
		catch(e)
		{
			return false;
		}
	}
}

/* */
function ForumFormSend(form, ajax_type)
{
	if (typeof form != "object")
		return false;
	
	if (typeof forumMessage != "object")
		forumMessage = new ForumSendMessage(ajax_type);
		
	forumMessage.SendData(form);
	
	return false;
}