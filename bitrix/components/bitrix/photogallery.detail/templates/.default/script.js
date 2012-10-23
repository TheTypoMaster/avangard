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
		div.onclick = function(e)
		{
			if (!jsUtils.IsIE)
			{
				e.preventDefault();
				e.stopPropagation();
			}
		}
	}
	div.innerHTML = '<div class="photo-popup-container"><div class="photo-popup-inner"><div class="photo-popup-data" id="photo_window_edit_data"></div></div></div>';
	return div;
}
function EditPhoto(url)
{
	if (!((typeof url == "string") && (url.length > 0)))
		return true;

	var div = CreateWindowEdit();
	PhotoMenu.PopupShow(div, 
		false, 
		false, 
		false, 
		{ 'BeforeHide' : function() { try { BX.closeWait('photo_window_edit'); } catch (e) {}
} }
	);
	
	BX.showWait('photo_window_edit');
	BX.ajax.get(
		url, 
		{"AJAX_CALL" : "Y"}, 
		function(data)
		{
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
	    		BX.closeWait('photo_window_edit'); 
	    	} catch (e) {}
 
	    	div.innerHTML = data;
		});
/*	(new BX.CDialog({
			'content_url': url + (url.indexOf('?') !== -1 ? "&" : "?") + "ajax_window=Y", 
			'content_post': {},
			'buttons': [BX.CDialog.btnSave, BX.CDialog.btnCancel],
			'height': 400,
			'width': 600
		})).Show(); */
	return false;
}
function CheckForm(form)
{
	if (typeof form != "object")
		return false;
	oData = {"AJAX_CALL" : "Y"};
	for (var ii = 0; ii < form.elements.length; ii++)
	{
		if (form.elements[ii] && form.elements[ii].name)
		{
			if (form.elements[ii].type && form.elements[ii].type == "checkbox")
			{
				if (form.elements[ii].checked == true)
					oData[form.elements[ii].name] = form.elements[ii].value;
			}
			else
			{
				oData[form.elements[ii].name] = form.elements[ii].value;
			}
		}
	}
	
	BX.ajax.post(
		form.action, 
		oData, 
		function(data)
		{
			result = {};
	    	try	{
	    		BX.closeWait('photo_window_edit');
	    	} catch (e) {}
			
			try
			{
				eval("result = " + data + ";");
				if (result['url'] && result['url'].length > 0)
					BX.reload(result['url']);
				else
				{
					if (document.getElementById("photo_title"))
						document.getElementById("photo_title").innerHTML = result['TITLE'];
					if (document.getElementById("photo_date"))
						document.getElementById("photo_date").innerHTML = result['DATE'];
					if (document.getElementById("photo_tags"))
					{
						if (!result['TAGS'] || result['TAGS'].length <= 0)
						{
							document.getElementById("photo_tags").innerHTML = '';
							document.getElementById("photo_tags").parentNode.style.display = 'none'; 
						}
						else
						{
							document.getElementById("photo_tags").innerHTML = result['TAGS'];
							document.getElementById("photo_tags").parentNode.style.display = 'block'; 
						}
					}
					if (document.getElementById("photo_description"))
						document.getElementById("photo_description").innerHTML = result['DESCRIPTION'];
				}
				PhotoMenu.PopupHide('photo_window_edit');
			}
			catch(e)
			{
				if (document.getElementById('photo_window_edit'))
					document.getElementById('photo_window_edit').innerHTML = data;
			}
		});
	BX.showWait('photo_window_edit');
	
	return false;
}

function CancelSubmit()
{
	PhotoMenu.PopupHide('photo_window_edit');
	return false;
}

function ShowOriginal(src, title)
{
	var SrcWidth = screen.availWidth;
	var SrcHeight = screen.availHeight;
	var sizer = false;
	var text = '';
	if (!title)
		title = "";
	if (document.all)
	{
		 sizer = window.open("","","height=SrcHeight,width=SrcWidth,top=0,left=0,scrollbars=yes,fullscreen=yes");
	}
	else
	{
		sizer = window.open('',src,'width=SrcWidth,height=SrcHeight,menubar=no,status=no,location=no,scrollbars=yes,fullscreen=yes,directories=no,resizable=yes');
	}
	text += '<html><head>';
	text += '\n<script language="JavaScript" type="text/javascript">';
	text += '\nfunction SetBackGround(div)';
	text += '\n{';
	text += '\n		if (!div){return false;}';
	text += '\n		document.body.style.backgroundColor = div.style.backgroundColor;';
	text += '\n}';
	text += '\n</script>';
	text += '\n</head>';
	text += '\n<title>';
	text += ('\n' + title);
	text += '\n</title>';
	text += '\n<body bgcolor="#999999">';
	text += '\n';
	text += '\n<table width="100%" height="96%" border=0 cellpadding=0 cellspacing=0>';
	text += '\n<tr><td align=right>';
	text += '\n<table align=center cellpadding=0 cellspacing=2 border=0>';
	text += '\n<tr><td><div style="width:18px; height:18px; background-color:#FFFFFF;" onmouseover="SetBackGround(this);"></div></td></tr>';
	text += '\n<tr><td><div style="width:18px; height:18px; background-color:#E5E5E5;" onmouseover="SetBackGround(this);"></div></td></tr>';
	text += '\n<tr><td><div style="width:18px; height:18px; background-color:#CCCCCC;" onmouseover="SetBackGround(this);"></div></td></tr>';
	text += '\n<tr><td><div style="width:18px; height:18px; background-color:#B3B3B3;" onmouseover="SetBackGround(this);"></div></td></tr>';
	text += '\n<tr><td><div style="width:18px; height:18px; background-color:#999999;" onmouseover="SetBackGround(this);"></div></td></tr>';
	text += '\n<tr><td><div style="width:18px; height:18px; background-color:#808080;" onmouseover="SetBackGround(this);"></div></td></tr>';
	text += '\n<tr><td><div style="width:18px; height:18px; background-color:#666666;" onmouseover="SetBackGround(this);"></div></td></tr>';
	text += '\n<tr><td><div style="width:18px; height:18px; background-color:#4D4D4D;" onmouseover="SetBackGround(this);"></div></td></tr>';
	text += '\n<tr><td><div style="width:18px; height:18px; background-color:#333333;" onmouseover="SetBackGround(this);"></div></td></tr>';
	text += '\n<tr><td><div style="width:18px; height:18px; background-color:#1A1A1A;" onmouseover="SetBackGround(this);"></div></td></tr>';
	text += '\n<tr><td><div style="width:18px; height:18px; background-color:#000000;" onmouseover="SetBackGround(this);"></div></td></tr>';
	text += '\n<tr><td><div style="width:18px; height:18px;"></div></td>';
	text += '\n</table></td>';
	text += '\n<td align=center><img alt="" border=0 src="' + src + '" onClick="window.close();" style="cursor:pointer; cursor:hand;" /></td></tr>';
	text += '\n</table></body></html>';
	sizer.document.write(text);


	return true;
}
bPhotoUtilsLoad = true;