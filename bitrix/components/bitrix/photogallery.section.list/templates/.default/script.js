function CreateWindowEdit()
{
	var div = document.getElementById("photo_window_edit");
	if (!div)
	{
		var div = document.body.appendChild(document.createElement("DIV"));
		div.id = "photo_window_edit";
		div.style.visible = 'hidden';
		div.className = "photo-popup";
		div.style.position = 'absolute';
	}
	div.innerHTML = '<div class="photo-popup-container"><div class="photo-popup-inner"><div class="photo-popup-data" id="photo_window_edit_data"></div></div></div>';
	return div;
}

function EditAlbum(url)
{
	if ((typeof url == "string") && (url.length > 0))
	{
		var div = CreateWindowEdit();
		PhotoMenu.PopupShow(div, 
			false, 
			false, 
			false, 
			{ 'BeforeHide' : function() { try { jsAjaxUtil.CloseLocalWaitWindow(TID, 'photo_window_edit'); } catch (e) {}
 } }
		);
		
		var TID = jsAjax.InitThread();
		jsAjaxUtil.ShowLocalWaitWindow(TID, 'photo_window_edit', false);
		jsAjax.AddAction(TID, function(data){
			var div = document.getElementById("photo_window_edit_data");
			if (!div) { return false; }
			
			div.innerHTML = data;
			var scripts = div.getElementsByTagName('script');
		    for (var i = 0; i < scripts.length; i++)
		    {
		        var thisScript = scripts[i];
		        var text;
		        var sSrc = thisScript.src.replace(/http\:\/\/[^\/]+\//gi, '');
		        if (thisScript.src && sSrc != 'bitrix/js/main/utils.js' && sSrc != 'bitrix/js/main/admin_tools.js' &&
		        	sSrc != '/bitrix/js/main/utils.js' && sSrc != '/bitrix/js/main/admin_tools.js') 
		        {
		            var newScript = document.createElement("script");
		            newScript.type = 'text/javascript';
		            newScript.src = thisScript.src;
		            document.body.appendChild(newScript);
		        }
		        else if (thisScript.text || thisScript.innerHTML) 
		        {
		        	text = (thisScript.text ? thisScript.text : thisScript.innerHTML);
					text = (""+text).replace(/^\s*<!\-\-/, '').replace(/\-\->\s*$/, '');
		            eval(text);
		        }
		    }
	    	data = data.replace(/\<script([^\>])*\>([^\<]*)\<\/script\>/gi, '');
	    	try	{
	    		jsAjaxUtil.CloseLocalWaitWindow(TID, 'photo_window_edit');
	    	} catch (e) {}
	    	div.innerHTML = data;
		});
		jsAjax.Send(TID, url, {"AJAX_CALL" : "Y"});
	}
	return false;
}

function CheckForm(form)
{
	if (typeof form != "object")
		return false;
	oData = {"AJAX_CALL" : "Y"};
	for (var ii in form.elements)
	{
		if (form.elements[ii] && form.elements[ii].name)
		{
			if (form.elements[ii].type && form.elements[ii].type.toLowerCase() == "checkbox")
			{
				if (form.elements[ii].checked == true)
					oData[form.elements[ii].name] = form.elements[ii].value;
			}
			else
				oData[form.elements[ii].name] = form.elements[ii].value;
		}
	}
	var TID = jsAjax.InitThread();
	jsAjax.AddAction(TID, function(data){
		result = {};
    	try	{
    		jsAjaxUtil.CloseLocalWaitWindow(TID, 'photo_window_edit');
    	} catch (e) {}
		
		try
		{
			eval("result = " + data + ";");
			if (result['url'] && result['url'].length > 0)
				jsUtils.Redirect({}, result['url']);
			var arrId = {"NAME" : "photo_album_name_", "DATE" : "photo_album_date_", "DESCRIPTION" : "photo_album_description_"};
			for (var ID in arrId)
			{
				if (document.getElementById(arrId[ID] + result['ID']))
					document.getElementById(arrId[ID] + result['ID']).innerHTML = result[ID];
			}
			var res = document.getElementById('photo_album_info_' + result['ID']);
			
			if (res)
			{
				if (result['PASSWORD'].length <= 0)
					res.className = res.className.replace("photo-album-password", "");
				else
					res.className += " photo-album-password ";
			}
			PhotoMenu.PopupHide('photo_window_edit');
		}
		catch(e)
		{
			if (document.getElementById('photo_window_edit_data'))
				document.getElementById('photo_window_edit_data').innerHTML = data;
		}
	});
	jsAjaxUtil.ShowLocalWaitWindow(TID, 'photo_window_edit', false);
	jsAjax.Post(TID, form.action, oData);
	return false;
}

function CheckFormEditIcon(form)
{
	if (typeof form != "object")
		return false;
	oData = {"AJAX_CALL" : "Y"};
	for (var ii in form.elements)
	{
		if (form.elements[ii] && form.elements[ii].name)
		{
			if (form.elements[ii].type && form.elements[ii].type.toLowerCase() == "checkbox")
			{
				if (form.elements[ii].checked == true)
					oData[form.elements[ii].name] = form.elements[ii].value;
			}
			else
				oData[form.elements[ii].name] = form.elements[ii].value;
		}
	}
	oData["photos"] = [];
	for (var ii = 0; ii < form.elements["photos[]"].length; ii++)
	{
		if (form.elements["photos[]"][ii].checked == true)
			oData["photos"].push(form.elements["photos[]"][ii].value);
	}
	var TID = jsAjax.InitThread();
	jsAjax.AddAction(TID, function(data){
		result = {};
    	try	{
    		jsAjaxUtil.CloseLocalWaitWindow(TID, 'photo_window_edit');
    	} catch (e) {}
    	var result = {};
		try
		{
			eval("result = " + data + ";");
		}
		catch(e)
		{
			var result = {};
		}
		if (parseInt(result["ID"]) > 0)
		{
			if (document.getElementById("photo_album_img_" + result['ID']))
				document.getElementById("photo_album_img_" + result['ID']).src = result['SRC'];
			else if (document.getElementById("photo_album_cover_" + result['ID']))
				document.getElementById("photo_album_cover_" + result['ID']).style.backgroundImage = "url('" + result['SRC'] + "')";
			PhotoMenu.PopupHide('photo_window_edit');
		}
		else
		{
			if (document.getElementById('photo_window_edit_data'))
				document.getElementById('photo_window_edit_data').innerHTML = data;
		}
	});

	jsAjaxUtil.ShowLocalWaitWindow(TID, 'photo_window_edit', false);
	jsAjax.Post(TID, form.action, oData);
	return false;
}

function CheckFormEditIconCancel()
{
	PhotoMenu.PopupHide('photo_window_edit');
	return false;
}

function CancelSubmit()
{
	PhotoMenu.PopupHide('photo_window_edit');
	return false;
}

function DropAlbum(url)
{
	if ((typeof url == "string") && (url.length > 0))
	{
		var TID = jsAjax.InitThread();
		jsAjax.AddAction(TID, function(data){
			CloseWaitWindow();
			result = {};
			try
			{
				eval("result = " + data + ";");
				if (result['ID'] && document.getElementById("photo_album_info_" + result['ID']))
					document.getElementById("photo_album_info_" + result['ID']).style.display = 'none';
			}
			catch(e){}
		});
		ShowWaitWindow();
		jsAjax.Send(TID, url, {"AJAX_CALL" : "Y"});
	}
	return false;
}
window.__photo_check_name_length_count = 0;
function __photo_check_name_length()
{
	var nodes = document.getElementsByTagName('a');
	var result = false; 
	for (var ii = 0; ii < nodes.length; ii++)
	{
		var node = nodes[ii];
		if (!node.id.match(/photo\_album\_name\_(\d+)/gi))
			continue;
		result = true; 	
		if (node.offsetHeight <= node.parentNode.offsetHeight)
			continue;
		var div = node.parentNode;
		var text = node.innerHTML.replace(/\<wbr\/\>/gi, '').replace(/\<wbr\>/gi, '').replace(/\&shy\;/gi, ''); 
		while (div.offsetHeight < node.offsetHeight || div.offsetWidth < node.offsetWidth)
		{
			if ((div.offsetHeight  < (node.offsetHeight / 2)) || (div.offsetWidth < (node.offsetWidth / 2)))
				text = text.substr(0, parseInt(text.length / 2));
			else
				text = text.substr(0, (text.length - 2));
			node.innerHTML = text;
		}
		node.innerHTML += '...';
		if (div.offsetHeight < node.offsetHeight || div.offsetWidth < node.offsetWidth)
			node.innerHTML = text.substr(0, (text.length - 3)) + '...';
	}
	if (!result) 
	{
		window.__photo_check_name_length_count++;
		if (window.__photo_check_name_length_count < 7)
			setTimeout(__photo_check_name_length, 250);
	}
}
setTimeout(__photo_check_name_length, 250);