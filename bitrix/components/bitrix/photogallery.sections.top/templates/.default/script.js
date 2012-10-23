function EditAlbum(url)
{
	if ((typeof url == "string") && (url.length > 0))
	{
		TID = CPHttpRequest.InitThread();
		CPHttpRequest.SetAction(TID, function(data){
			PCloseWaitMessage();
			var div = document.createElement("DIV");
			div.id = "photo_section_edit";
			div.style.visible = 'hidden';
			div.className = "photo-popup";
			div.style.position = 'absolute';
			div.innerHTML = data;
			var left = parseInt(document.body.scrollLeft + document.body.clientWidth/2 - div.offsetWidth/2);
			var top = parseInt(document.body.scrollTop + document.body.clientHeight/2 - div.offsetHeight/2);
			
			var scripts = div.getElementsByTagName('script');
		    for (var i = 0; i < scripts.length; i++)
		    {
		        var thisScript = scripts[i];   
		        var text;
		        if (thisScript.src) 
		        {
		            var newScript = document.createElement("script");
		            newScript.type = thisScript.type;       
		            newScript.language = thisScript.language;
		            newScript.src = thisScript.src;             
		            document.body.appendChild(newScript);   
		        } 
		        else if (text = (thisScript.text || thisScript.innerHTML)) 
		        {
		            var text = (""+text).replace(/^\s*<!\-\-/, '').replace(/\-\->\s*$/, '');
		            eval(text);
		        }
		    }
		    document.body.appendChild(div);
			PhotoMenu.PopupShow(div, {'top' : top, 'left' : left});
		});
		
		PShowWaitMessage();
		
		CPHttpRequest.Send(TID, url, {"AJAX_CALL" : "Y"});
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
			oData[form.elements[ii].name] = form.elements[ii].value;
		}
			
	}
	
	TID = CPHttpRequest.InitThread();
	CPHttpRequest.SetAction(TID, 
		function(data)
		{
			result = {};
			try
			{
				eval("result = " + data + ";");
				if (result['url'] && result['url'].length > 0)
					jsUtils.Redirect({}, result['url']);
				if (document.getElementById("photo_album_name_" + result['ID']))
					document.getElementById("photo_album_name_" + result['ID']).innerHTML = result['NAME'];
				if (document.getElementById("photo_album_date_" + result['ID']))
					document.getElementById("photo_album_date_" + result['ID']).innerHTML = result['DATE'];
				if (document.getElementById("photo_album_description_" + result['ID']))
					document.getElementById("photo_album_description_" + result['ID']).innerHTML = result['DESCRIPTION'];
				PhotoMenu.PopupHide('photo_section_edit');
			}
			catch(e)
			{
				if (document.getElementById('photo_section_edit'))
					document.getElementById('photo_section_edit').innerHTML = data;
			}
			PCloseWaitMessage();
		});
	
	PShowWaitMessage();
	CPHttpRequest.Post(TID, form.action, oData);
	return false;
}

function DropAlbum(url)
{
	if ((typeof url == "string") && (url.length > 0))
	{
		TID = CPHttpRequest.InitThread();
		CPHttpRequest.SetAction(TID, function(data){
			PCloseWaitMessage();
			result = {};
			try
			{
				eval("result = " + data + ";");
				if (result['ID'] && document.getElementById("photo_album_info_" + result['ID']))
					document.getElementById("photo_album_info_" + result['ID']).style.display = 'none';
			}
			catch(e)
			{
			}
		});
		
		PShowWaitMessage();
		
		CPHttpRequest.Send(TID, url, {"AJAX_CALL" : "Y"});
	}
	return false;
}
function CancelSubmit()
{
	PhotoMenu.PopupHide('photo_section_edit');
	return false;
}