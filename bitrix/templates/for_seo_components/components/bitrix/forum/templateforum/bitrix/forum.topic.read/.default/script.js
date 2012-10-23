function SelectElements(oObj)
{
	if (typeof oObj != "object")
		return;

	var items = document.getElementsByName('message_id[]');
	if (typeof items == "object")
	{
		if (!items.length || (typeof(items.length) == 'undefined'))
			items = [items];
		
		for (i = 0; i < items.length; i++)
		{
			if (items[i].type && (items[i].type=="checkbox"))
			{
				items[i].checked = oObj.checked;
				items[i].onclick = function(){oObj.checked = false;}
			}
		}
	}
	return;
}

function ForumSendMessage(id, url, ajax_type)
{
	if (!((typeof(url) == "string") && (url.length > 0)))
		return false;

	ajax_type = (ajax_type == "Y" ? "Y" : "N");
	
	if (ajax_type == "N")
	{
		jsUtils.Redirect([], url);
		return false;
	}
	
	TID = CPHttpRequest.InitThread();
	
	CPHttpRequest.SetAction(TID,
		function(data, TID)
		{
			eval('result = ' + data + ';');
			if (typeof(result) == "object")
			{
				if (document.getElementById('message_post_' + result['id']))
				{
					document.getElementById('message_post_' + result['id']).innerHTML = result['post'];
					location.hash = 'message' + result['id'];
				}
			}
			FCloseWaitWindow('send_data');
			return;
		}
	);
	
	FShowWaitWindow('send_data');
	CPHttpRequest.Send(TID, url, {"AJAX_CALL" : "Y", "ACTION" : "EDIT", "INDEX" : id + Math.random()});
	return false;
}

function ForumPostMessage(form)
{
	if (typeof form != "object")
		return false;
	var bConvertor = document.createElement('INPUT');
	bConvertor.type = 'hidden';
	bConvertor.name = 'CONVERT_DATA';
	bConvertor.value = 'Y';
	form.appendChild(bConvertor);
	var iRand = document.createElement('INPUT');
	iRand.type = 'hidden';
	iRand.name = 'INDEX';
	iRand.value = Math.random();;
	form.appendChild(iRand);

	var TID = CPHttpRequest.InitThread();
	FShowWaitWindow('send_data');
	CPHttpRequest.MigrateFormToAjax(form, function(data){
		var result = false;
		eval('result=' + data + ';');
		if (typeof(result) == "object")
		{
			var search = [/\&quot\;/g,/\&lt\;/g,/\&gt\;/g,/\&amp\;quot\;/g,/\&amp\;lt\;/g,/\&amp\;gt\;/g, /\&amp\;amp\;/g];
			var replace = ['"','<','>','&quot;','&lt;','&gt;','&amp;'];
			for (var i=0; i<search.length; i++) 
				result['post'] = result['post'].replace(search[i], replace[i]);
			if (document.getElementById('message_post_' + result['id']))
			{
				document.getElementById('message_post_' + result['id']).innerHTML = result['post'];
				location.hash = 'message' + result['id'];
			}
		}
		FCloseWaitWindow('send_data');
	});
}