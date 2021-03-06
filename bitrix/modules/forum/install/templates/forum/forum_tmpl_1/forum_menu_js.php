var jsUtils =
{
	addEvent: function(el, evname, func)
	{
		if(el.attachEvent) // IE
			el.attachEvent("on" + evname, func);
		else if(el.addEventListener) // Gecko / W3C
			el.addEventListener(evname, func, false);
		else
			el["on" + evname] = func;
	},

	removeEvent: function(el, evname, func)
	{
		if(el.detachEvent) // IE
			el.detachEvent("on" + evname, func);
		else if(el.removeEventListener) // Gecko / W3C
			el.removeEventListener(evname, func, false);
		else
			el["on" + evname] = null;
	},

	GetRealPos: function(el)
	{
		if(!el || !el.offsetParent)
			return false;
		var res=Array();
		res["left"] = el.offsetLeft;
		res["top"] = el.offsetTop;
		var objParent = el.offsetParent;
		while(objParent.tagName != "BODY")
		{
			res["left"] += objParent.offsetLeft;
			res["top"] += objParent.offsetTop;
			objParent = objParent.offsetParent;
		}
		res["right"]=res["left"] + el.offsetWidth;
		res["bottom"]=res["top"] + el.offsetHeight;

		return res;
	},

	FindChildObject: function(obj, tag_name, class_name)
	{
		if(!obj)
			return null;
		var tag = tag_name.toUpperCase();
		var cl = (class_name? class_name.toLowerCase() : null);
		var n = obj.childNodes.length;
		for(var j=0; j<n; j++)
		{
			var child = obj.childNodes[j];
			if(child.tagName && child.tagName.toUpperCase() == tag)
				if(!class_name || child.className.toLowerCase() == cl)
					return child;
		}
		return null;
	},

	FindNextSibling: function(obj, tag_name)
	{
		var o = obj;
		var tag = tag_name.toUpperCase();
		while(o.nextSibling)
		{
			var sibling = o.nextSibling;
			if(sibling.tagName && sibling.tagName.toUpperCase() == tag)
				return sibling;
			o = sibling;
		}
		return null;
	},

	FindPreviousSibling: function(obj, tag_name)
	{
		var o = obj;
		var tag = tag_name.toUpperCase();
		while(o.previousSibling)
		{
			var sibling = o.previousSibling;
			if(sibling.tagName && sibling.tagName.toUpperCase() == tag)
				return sibling;
			o = sibling;
		}
		return null;
	},

	FindParentObject: function(obj, tag_name)
	{
		var o = obj;
		var tag = tag_name.toUpperCase();
		while(o.parentNode)
		{
			var parent = o.parentNode;
			if(parent.tagName && parent.tagName.toUpperCase() == tag)
				return parent;
			o = parent;
		}
		return null;
	},

	IsIE: function()
	{
		return (document.attachEvent && !this.IsOpera());
	},

	IsOpera: function()
	{
		return (navigator.userAgent.toLowerCase().indexOf('opera') != -1);
	},

	ToggleDiv: function(div)
	{
		var style = document.getElementById(div).style;
		if(style.display!="none")
			style.display = "none";
		else
			style.display = "block";
		return (style.display != "none");
	},

	urlencode: function(s)
	{
		return escape(s).replace(new RegExp('\\+','g'), '%2B');
	},

	OpenWindow: function(url, width, height)
	{
		var w = screen.width, h = screen.height;
		if(this.IsOpera())
		{
			w = document.body.offsetWidth;
			h = document.body.offsetHeight;
		}
		window.open(url, '', 'status=no,scrollbars=yes,resizable=yes,width='+width+',height='+height+',top='+Math.floor((h - height)/2-14)+',left='+Math.floor((w - width)/2-5));
	},
	
	SetPageTitle: function(s)
	{
		document.title = phpVars.titlePrefix+s;
		var h1 = document.getElementsByTagName("H1");
		if(h1)
			h1[0].innerHTML = s;
	},

	LoadPageToDiv: function(url, div_id)
	{
		var div = document.getElementById(div_id);
		if(!div)
			return;
		CHttpRequest.Action = function(result)
		{
			CloseWaitWindow();
			div.innerHTML = result;
		}
		ShowWaitWindow();
		CHttpRequest.Send(url);
	},

	trim: function(s)
	{
		var r, re;
		re = /^[ \r\n]+/g;
		r = s.replace(re, "");
		re = /[ \r\n]+$/g;
		r = r.replace(re, "");
		return r;
	},
	
	Redirect: function(args, url)
	{
		var e = null, bShift = false;
		if(args.length > 0)
			e = args[0];
		if(!e)
			e = window.event;
		if(e) 
			bShift = e.shiftKey;

		if(bShift) 
			window.open(url); 
		else 
		{
			ShowWaitWindow();
			window.location=url;
		}
	},

	False: function(){return false;},

	AlignToPos: function(pos, w, h)
	{
		var x = pos["left"], y = pos["bottom"];

		var body = document.body;
		if((body.clientWidth + body.scrollLeft) - (pos["left"] + w) < 0)
		{
			if(pos["right"] - w >= 0 )
				x = pos["right"] - w;
			else
				x = body.scrollLeft;
		}

		if((body.clientHeight + body.scrollTop) - (pos["bottom"] + h) < 0)
		{
			if(pos["top"] - h >= 0)
				y = pos["top"] - h;
			else
				y = body.scrollTop;
		}
		
		return {'left':x, 'top':y};
	}
}

/************************************************/

function JCFloatDiv() 
{
	var _this = this;
	this.floatDiv = null;
	this.x = this.y = 0;

	this.Show = function(div, left, top, dxShadow)
	{
		var zIndex = parseInt(div.style.zIndex);
		if(zIndex <= 0 || isNaN(zIndex))
			zIndex = 100;
		div.style.zIndex = zIndex;
		div.style.left = left + "px";
		div.style.top = top + "px";

		if(jsUtils.IsIE())
		{
			var frame = document.createElement("IFRAME");
			frame.src = "javascript:void(0)";
			frame.id = div.id+"_frame";
			frame.style.position = 'absolute';
			frame.style.zIndex = zIndex-1;
			frame.style.width = div.offsetWidth + "px";
			frame.style.height = div.offsetHeight + "px";
			frame.style.left = div.style.left;
			frame.style.top = div.style.top;
			document.body.appendChild(frame);
		}

		/*shadow*/
		if(isNaN(dxShadow))
			dxShadow = 5;
		if(dxShadow > 0)
		{
			var img;
			if(jsUtils.IsIE())
			{
	 			img = document.createElement("DIV");
	 			img.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='/bitrix/themes/"+phpVars.ADMIN_THEME_ID+"/images/shadow.png',sizingMethod='scale')";
			}
			else
			{
	 			img = document.createElement("IMG");
				img.src = '/bitrix/themes/'+phpVars.ADMIN_THEME_ID+'/images/shadow.png';
			}
			img.id = div.id+'_shadow';
			img.style.position = 'absolute';
			img.style.zIndex = zIndex-2;
			img.style.width = div.offsetWidth+'px';
			img.style.height = div.offsetHeight+'px';
			img.style.left = parseInt(div.style.left)+dxShadow+'px';
			img.style.top = parseInt(div.style.top)+dxShadow+'px';
			document.body.appendChild(img);
		}
	}
		
	this.Close = function(div)
	{
		if(!div)
			return;
		var sh = document.getElementById(div.id+"_shadow");
		if(sh)
			sh.parentNode.removeChild(sh);

		var frame = document.getElementById(div.id+"_frame");
		if(frame)
			frame.parentNode.removeChild(frame);
	}
		
	this.Move = function(div, x, y, dxShadow)
	{
		if(!div)
			return;
			
		var left = parseInt(div.style.left)+x;
		var top = parseInt(div.style.top)+y;
		div.style.left = left+'px';
		div.style.top = top+'px';

		this.AdjustShadow(div, dxShadow);
	}
	
	this.AdjustShadow = function(div, dxShadow)
	{
		var sh = document.getElementById(div.id+"_shadow");
		if(sh)
		{
			if(isNaN(dxShadow))
				dxShadow = 5;

			sh.style.width = div.offsetWidth+'px';
			sh.style.height = div.offsetHeight+'px';
			sh.style.left = parseInt(div.style.left)+dxShadow+'px';
			sh.style.top = parseInt(div.style.top)+dxShadow+'px';
		}

		var frame = document.getElementById(div.id+"_frame");
		if(frame)
		{
			frame.style.width = div.offsetWidth + "px";
			frame.style.height = div.offsetHeight + "px";
			frame.style.left = div.style.left;
			frame.style.top = div.style.top;
		}
	}

	this.StartDrag = function(e, div)
	{
		if(!e)
			e = window.event;
		this.x = e.clientX + document.body.scrollLeft;
		this.y = e.clientY + document.body.scrollTop;
		this.floatDiv = div;

		jsUtils.addEvent(document, "mousemove", _this.MoveDrag);
		document.onmouseup = this.StopDrag;
		
		var b = document.body;
	    b.ondrag = jsUtils.False;
	    b.onselectstart = jsUtils.False;
	    b.style.MozUserSelect = _this.floatDiv.style.MozUserSelect = 'none';
	    b.style.cursor = 'move';
    }

	this.StopDrag = function(e)
	{
		jsUtils.removeEvent(document, "mousemove", _this.MoveDrag);
		document.onmouseup = null;
		this.floatDiv = null;

		var b = document.body;
		b.ondrag = null;
		b.onselectstart = null;
		b.style.MozUserSelect = _this.floatDiv.style.MozUserSelect = '';
	    b.style.cursor = '';
	}

	this.MoveDrag = function(e)
	{
		var x = e.clientX + document.body.scrollLeft;
		var y = e.clientY + document.body.scrollTop;
		if(_this.x == x && _this.y == y)
			return;
	
		_this.Move(_this.floatDiv, (x - _this.x), (y - _this.y));
		_this.x = x;
		_this.y = y;
	}
}
var jsFloatDiv = new JCFloatDiv();

/************************************************/

function JCSplitter(params)
{
	this.params = params;

	this.Highlight = function(on)
	{
		var control = document.getElementById(this.params.control);
		var div = document.getElementById(this.params.divShown);
		if(div.style.display!="none")
			control.className = this.params.classShown+(on? 'sel':'');
		else
			control.className = this.params.classHidden+(on? 'sel':'');
	}

	this.Toggle = function()
	{
		var visible = jsUtils.ToggleDiv(this.params.divShown);
		jsUtils.ToggleDiv(this.params.divHidden);
		this.Highlight(false);
		document.getElementById(this.params.control).title = (visible? this.params.messHide : this.params.messShow);
		return visible;
	}
}

/************************************************/

<!--function JCAdminMenu(sOpenedSections)
{
	var _this = this;
	this.sMenuSelected='';
	this.x = 0;
	this.divToResize = null;
	this.divToBound = null;
	this.toggle = false;
	this.oSections = {};
	this.request = new JCHttpRequest();
	
	var aSect = sOpenedSections.split(',');
	for(var i in aSect)
		this.oSections[aSect[i]] = true;

	this.verSplitter = new JCSplitter({
		control:'vdividercell',
		divShown:'menudiv', divHidden:'hiddenmenucontainer',
		messHide:phpVars.messHideMenu, messShow:phpVars.messShowMenu,
		classShown:'vdividerknob vdividerknobleft', classHidden:'vdividerknob vdividerknobright'
	});
	this.horSplitter = new JCSplitter({
		control:'hdividercell',
		divShown:'buttonscontainer', divHidden:'smbuttonscontainer',
		messHide:phpVars.messHideButtons, messShow:phpVars.messShowButtons,
		classShown:'hdividerknob hdividerknobup', classHidden:'hdividerknob hdividerknobdown'
	});

	this.verSplitterToggle = function()
	{
		var visible = this.verSplitter.Toggle();
		jsUserOptions.SaveOption('admin_menu', 'pos', 'ver', (visible? 'on':'off'));
	}

	this.horSplitterToggle = function()
	{
		var visible = this.horSplitter.Toggle();
		jsUserOptions.SaveOption('admin_menu', 'pos', 'hor', (visible? 'on':'off'));
	}
	
	this.ToggleMenu = function(menu_id, menu_text)
	{
		var div = document.getElementById(menu_id);
		if(div.style.display!="none")
			return;

		/*menu div*/
		if(this.sMenuSelected != "")
			document.getElementById(this.sMenuSelected).style.display = 'none';
		div.style.display = "block";

		/*button*/
		document.getElementById('menutitle').innerHTML = menu_text;

		document.getElementById('btn_'+this.sMenuSelected).className = 'button';
		document.getElementById('smbtn_'+this.sMenuSelected).className = 'smbutton';
		document.getElementById('btn_'+menu_id).className = 'button buttonsel';
		document.getElementById('smbtn_'+menu_id).className = 'smbutton smbuttonsel';

		this.sMenuSelected = menu_id;
	}

	this.StartDrag = function()
	{
		if(this.toggle)
			return;
		if(document.getElementById('menudiv').style.display == 'none')
			return;

		this.divToBound = document.getElementById("buttonscontainer");
		if(this.divToBound.style.display == 'none')
			this.divToBound = document.getElementById("smbuttonscontainer");
		this.divToResize = document.getElementById('menucontainer');
		this.x = this.divToResize.offsetWidth;

		jsUtils.addEvent(document, "mousemove", _this.ResizeMenu);
		document.onmouseup = this.StopDrag;
		
		var b = document.body;
	    b.ondrag = jsUtils.False;
	    b.onselectstart = jsUtils.False;
	    b.style.MozUserSelect = 'none';
	    b.style.cursor = 'e-resize';
    }
    
	this.StopDrag = function(e)
	{
		jsUtils.removeEvent(document, "mousemove", _this.ResizeMenu);
		document.onmouseup = null;

		var b = document.body;
		b.ondrag = null;
		b.onselectstart = null;
		b.style.MozUserSelect = '';
	    b.style.cursor = '';
	    
	    if(window.onresize)
	    	window.onresize();

		jsUserOptions.SaveOption('admin_menu', 'pos', 'width', parseInt(_this.divToResize.style.width));
	}

	this.ResizeMenu = function(e)
	{
		var x = e.clientX + document.body.scrollLeft;
		if(	_this.x == x)
			return;
	
		var div = _this.divToResize;
		var mnu = _this.divToBound;

		if(x < mnu.offsetWidth)
		{
			div.style.width = mnu.offsetWidth+'px';
			_this.x = x;
			return;
		}

		div.style.width = div.offsetWidth+(x - _this.x)+'px';
		_this.x = x;
	}

	this.ToggleSection = function(cell, div_id, level)
	{
		if(jsUtils.ToggleDiv(div_id))
		{
			if(level <= 2)
				this.oSections[div_id] = true;
			cell.className='sign signminus';
		}
		else
		{
			this.oSections[div_id] = false;
			cell.className='sign signplus';
		}
		
		if(level <= 2)
		{
			var sect='';
			for(var i in this.oSections)
				if(this.oSections[i] == true)
					sect += (sect != ''? ',':'')+i;
			jsUserOptions.SaveOption('admin_menu', 'pos', 'sections', sect);
		}
	}

	this.ToggleDynSection = function(cell, module_id, div_id, level)
	{
		function MenuText(text)
		{
			var s = '';
			for(var i=0; i<level; i++)
				s += '<td><div class="menuindent"></div></td>\n';
			return(
				'<div class="menuline">'+
				'<table cellspacing="0">'+
				'	<tr>'+s+
				'		<td class="menutext menutext-loading">'+text+'</td>'+
				'	</tr>'+
				'</table>'+
				'</div>');
		}

		var div = document.getElementById(div_id);
		if(div.innerHTML == '')
		{
			div.innerHTML = MenuText(phpVars.messMenuLoading);

			this.request.Action = function(result)
			{
				result = jsUtils.trim(result);
				div.innerHTML = (result != ''? result : MenuText(phpVars.messNoData));
			}
			this.request.Send('/bitrix/admin/get_menu.php?lang='+phpVars.LANGUAGE_ID+'&admin_mnu_module_id='+module_id+'&admin_mnu_menu_id='+div_id);
		}
		this.ToggleSection(cell, div_id, level);
	}
}

/************************************************/
-->
var jsToolBar =
{
	popup: null,

	OnMouseOver: function(div)
	{
		div.className += ' hover';
	},

	OnMouseOut: function(div)
	{
		div.className = div.className.replace(/\s*hover/ig, '')
	},

	ShowMenu: function(a, items, menu)
	{
		if(!menu)
			menu = this.popup;

		var div = a;
		if(menu.controlDiv == div)
			menu.PopupHide();
		else
		{
			menu.PopupHide();
			if(items)
				menu.BuildItems(items);

			div.className += ' pressed';
			var pos = jsUtils.GetRealPos(div);
			pos["bottom"]+=2;

			menu.controlDiv = div;
			menu.OnClose = function()
			{
				div.className = div.className.replace(/\s*pressed/ig, "");
			}
			menu.PopupShow(pos);
		}
	}
}

/************************************************/

function JCAdminFilter(filter_id, aRows)
{
	var _this = this;
	this.filter_id = filter_id;
	this.aRows = aRows;
	this.oVisRows = null;

	this.ToggleFilterRow = function(row_id, on, bSave)
	{
		var row = document.getElementById(row_id);
		var gutter = document.getElementById('gutter_'+row_id);
		var delimiter = document.getElementById(row_id+'_delim');
		if(!row)
			return;

		var short_id = row_id.substr((this.filter_id+'_row_').length);

		if(on != true && on != false)
			on = (row.style.display == 'none');

		if(on == true)
		{
			try{
				row.style.display = 'table-row';
				delimiter.style.display = 'table-row';
			}
			catch(e){
				row.style.display = 'block';
				delimiter.style.display = 'block';
			}
			gutter.className = 'gutter guttersel';
			this.oVisRows[short_id] = true;
		}
		else
		{
			row.style.display = 'none';
			delimiter.style.display = 'none';
			gutter.className = 'gutter';
			this.oVisRows[short_id] = false;
		}
		
		if(bSave != false)
			this.SaveRowsOption();
	}
	
	this.SaveRowsOption = function()
	{
		var sRows = '';
		for(var key in this.oVisRows)
			if(this.oVisRows[key] == true)
				sRows += (sRows != ''? ',':'')+key;
		jsUserOptions.SaveOption('filter', this.filter_id, 'rows', sRows);
	}

	this.ToggleAllFilterRows = function(on)
	{
		var tbl = document.getElementById(this.filter_id);
		if(!tbl)
			return;

		var n = tbl.rows.length;
		for(var i=0; i<n; i++)
		{
			var row = tbl.rows[i];
			if(row.id && row.cells[0].className != 'delimiter')
				this.ToggleFilterRow(row.id, on, false);
		}
		this.SaveRowsOption();
	}

	this.InitFilter = function(oVisRows)
	{
		this.oVisRows = oVisRows;
		
		var i;
		var tbl = document.getElementById(this.filter_id);
		if(!tbl)
			return;

		var n=tbl.rows.length;
		for(i=0; i<n; i++)
		{
			var row = tbl.rows[i];
			var td = row.insertCell(-1);
			td.className = 'filterless';
			if(i>0)
			{
				row.id = this.filter_id+'_row_'+this.aRows[i-1];
				if(this.oVisRows[this.aRows[i-1]] == true)
					document.getElementById('gutter_'+row.id).className = 'gutter guttersel';
				else
					row.style.display = 'none';

				td.innerHTML = '<a href="javascript:void(0)" onclick="this.blur();'+this.filter_id+'.ToggleFilterRow(\''+row.id+'\');" hidefocus="true" title="'+phpVars.messFilterLess+'" class="context-button icon" id="filterless" onMouseOver="jsToolBar.OnMouseOver(this);" onMouseOut="jsToolBar.OnMouseOut(this);"><div class="empty"></div></a>';
			}
		}

		for(i=0; i<n; i++)
		{
			var tr = tbl.insertRow(i*2+1);
			if(i > 0)
			{
				if(this.oVisRows[this.aRows[i-1]] != true)
					tr.style.display = 'none';
				tr.id = this.filter_id+'_row_'+this.aRows[i-1]+'_delim';
			}
			var td = tr.insertCell(-1);
			td.colSpan = 3;
			td.className = 'delimiter';
			td.innerHTML = '<div class="empty"></div>';
		}

		try{
			tbl.style.display = 'table';}
		catch(e){
			tbl.style.display = 'block';}

		this.DisplayVisibleRows();
		this.SetActive(this.CheckActive());
	}

	this.GetParameters = function()
	{
		var form = jsUtils.FindParentObject(document.getElementById(this.filter_id), "form");
		if(!form)
			return;

		var i, s = "";
		var n = form.elements.length;
		for(i=0; i<n; i++)
		{
			var el = form.elements[i];
			if(el.disabled)
				continue;
			var tr = jsUtils.FindParentObject(el, 'tr');
			if(tr && tr.style && tr.style.display == 'none')
				continue;

			var val = "";
			switch(el.type.toLowerCase())
			{
				case 'select-one':
				case 'text':
				case 'textarea':
				case 'hidden':
					val = el.value;
					break;
				case 'radio':
				case 'checkbox':
					if(el.checked)
						val = el.value;
					break;
				case 'select-multiple':
					var j;
					var l = el.options.length;
					for(j=0; j<l; j++)
						if(el.options[j].selected)
							s += '&' + el.name + '=' + jsUtils.urlencode(el.options[j].value);
					break;
				default:
					break;
			}
			if(val != "")
				s += '&' + el.name + '=' + jsUtils.urlencode(val);
		}
		return s;
	}

	this.ClearParameters = function()
	{
		var form = jsUtils.FindParentObject(document.getElementById(this.filter_id), "form");
		if(!form)
			return;

		var i;
		var n = form.elements.length;
		for(i=0; i<n; i++)
		{
			var el = form.elements[i];
			switch(el.type.toLowerCase())
			{
				case 'text':
				case 'textarea':
					el.value = '';
					break;
				case 'select-one':
					el.selectedIndex = 0;
					if(el.onchange)
						el.onchange();
					break;
				case 'select-multiple':
					var j;
					var l = el.options.length;
					for(j=0; j<l; j++)
						el.options[j].selected = false;
					break;
				default:
					break;
			}
		}
	}

	this.OnSet = function(table_id, url)
	{
		this.SetActive(this.CheckActive());
		window[table_id].GetAdminList(url+'set_filter=Y'+this.GetParameters());
	}

	this.OnClear = function(table_id, url)
	{
		this.ClearParameters();
		this.SetActive(false);
		window[table_id].GetAdminList(url+'del_filter=Y');
	}

	this.SetActive = function(on)
	{
		var div = document.getElementById(this.filter_id+'_active_lamp');
		div.className = (on? 'active':'inactive');
		div.title = (on? phpVars.messFilterActive:phpVars.messFilterInactive);
	}

	this.CheckActive = function()
	{
		var form = jsUtils.FindParentObject(document.getElementById(this.filter_id), "form");
		if(!form)
			return;

		var i;
		var n = form.elements.length;
		for(i=0; i<n; i++)
		{
			var el = form.elements[i];
			if(el.disabled)
				continue;
			var tr = jsUtils.FindParentObject(el, 'tr');
			if(tr && tr.style && tr.style.display == 'none')
				continue;

			switch(el.type.toLowerCase())
			{
				case 'select-one':
					if(el.options[0].value.length != 0 && (el.options[0].value.toUpperCase() != 'NOT_REF' || el.value.toUpperCase() == 'NOT_REF'))
						break;
				case 'text':
				case 'textarea':
					if(el.value.length > 0)
						return true;
					break;
				case 'checkbox':
					if(el.checked)
						return true;
					break;
				case 'select-multiple':
					var j;
					var l = el.options.length;
					for(j=0; j<l; j++)
						if(el.options[j].selected)
							return true;
					break;
				default:
					break;
			}
		}
		return false;
	}

	this.DisplayVisibleRows = function()
	{
		var form = jsUtils.FindParentObject(document.getElementById(this.filter_id), "form");
		if(!form)
			return;

		var i;
		var n = form.elements.length;
		for(i=0; i<n; i++)
		{
			var el = form.elements[i];
			if(el.disabled)
				continue;

			var bVisible = false;
			switch(el.type.toLowerCase())
			{
				case 'select-one':
					if(el.value.length>0 && (el.options[0].value.length == 0 || (el.options[0].value.toUpperCase() == 'NOT_REF' && el.value.toUpperCase() != 'NOT_REF')))
						bVisible = true;
					break;
				case 'text':
				case 'textarea':
					if(el.value.length>0)
						bVisible = true;
					break;
				case 'checkbox':
					if(el.checked)
						bVisible = true;
					break;
				case 'select-multiple':
					var j;
					var l = el.options.length;
					for(j=0; j<l; j++)
						if(el.options[j].selected)
						{
							bVisible = true;
							break;
						}
					break;
				default:
					break;
			}
			if(bVisible)
			{
				var tr = jsUtils.FindParentObject(el, 'tr');
				if(tr.id)
					this.ToggleFilterRow(tr.id, true);
			}
		}
	}
}

/************************************************/

function JCAdminList(table_id)
{
	var _this = this;
	this.table_id = table_id;

	this.InitTable = function()
	{
		var tbl = document.getElementById(this.table_id);
		if(!tbl || tbl.rows.length<1 || tbl.rows[0].cells.length<1)
			return;

		var i;
		var nCols = tbl.rows[0].cells.length;
		var sortedIndex = -1;

		/*head row mousover action*/
		for(i=0; i<nCols; i++)
		{
			var j;
			var cell_sort = tbl.rows[1].cells[i];
			var sort_table = jsUtils.FindChildObject(cell_sort, "table", "sorting");

			for(j=0; j<2; j++)
			{
				var cell = tbl.rows[j].cells[i];
				cell.onmouseover = function(){_this.HighlightGutter(this, true)};
				cell.onmouseout = function(){_this.HighlightGutter(this, false)};

				/*expand sorting table behaviour on parent cell*/
				if(sort_table)
				{
					cell.onclick = sort_table.onclick;
					cell.title = sort_table.title;
					cell.style.cursor = "pointer";

					if(j == 0)
					{
						var cl = sort_table.rows[0].cells[1].className.toLowerCase();
						if(cl == "sign up" || cl == "sign down")
						{
							cell.className += ' sorted';
							sortedIndex = i;
						}
					}
				}
			}
			if(sort_table)
				sort_table.onclick = null;
		}

		var n = tbl.rows.length;
		for(i=0; i<n; i++)
		{
			var row = tbl.rows[i];

			/*first and last columns style classes*/
			row.cells[0].className += ' left';
	 		row.cells[row.cells.length-1].className += ' right';

	 		if(row.className && row.className == 'footer')
	 			continue;

			/*sorted column*/
			if(sortedIndex != -1 && sortedIndex < row.cells.length)
				row.cells[sortedIndex].className += ' sorted';

			if(i>=2)
			{
				/*first column checkbox action*/
				var checkbox = row.cells[0].childNodes[0];
				if(checkbox && checkbox.tagName && checkbox.tagName.toUpperCase() == "INPUT" && checkbox.type.toUpperCase() == "CHECKBOX")
				{
					checkbox.onclick = function(){_this.SelectRow(this); _this.EnableActions()};
					jsUtils.addEvent(row, "click", _this.OnClickRow);
				}

				/*rows mousover action*/
				row.onmouseover = function(){_this.HighlightRow(this, true)};
				row.onmouseout = function(){_this.HighlightRow(this, false)};

				if(i%2 == 0)
					row.className += ' odd';
				else
					row.className += ' even';

				if(row.oncontextmenu)
				{
					jsUtils.addEvent(row, "contextmenu",
						function(e)
						{
							if(!e) e = window.event;
							if(!phpVars.opt_context_ctrl && e.ctrlKey || phpVars.opt_context_ctrl && !e.ctrlKey) 
								return;

							var targetElement;
							if(e.target) targetElement = e.target;
							else if(e.srcElement) targetElement = e.srcElement;

							while(targetElement && !targetElement.oncontextmenu)
								targetElement = jsUtils.FindParentObject(targetElement, "tr");

							var x = e.clientX + document.body.scrollLeft;
							var y = e.clientY + document.body.scrollTop;
							var pos = {};
							pos['left'] = pos['right'] = x;
							pos['top'] = pos['bottom'] = y;

							var menu = window[_this.table_id+"_menu"];
							menu.PopupHide();
							menu.BuildItems(targetElement.oncontextmenu());
							menu.PopupShow(pos);

							e.returnValue = false;
							if(e.preventDefault) e.preventDefault();
						}
					);
				}
			}
		}

		if(tbl.rows.length > 2)
		{
			tbl.rows[2].className += ' top';
			tbl.rows[tbl.rows.length-1].className += ' bottom';
		}
	}

	this.HighlightGutter = function(cell, on)
	{
		var table = cell.parentNode.parentNode.parentNode;
		var gutter = table.rows[0].cells[cell.cellIndex];
		if(on)
			gutter.className += ' over';
		else
			gutter.className = gutter.className.replace(/\s*over/i, '');
	}

	this.HighlightRow = function(row, on)
	{
		if(on)
			row.className += ' over';
		else
			row.className = row.className.replace(/\s*over/i, '');
	}

	this.SelectRow = function(checkbox)
	{
		var row = checkbox.parentNode.parentNode;
		var tbl = row.parentNode.parentNode;
		var span = document.getElementById(tbl.id+'_selected_span');
		var selCount = parseInt(span.innerHTML);

		if(checkbox.checked)
		{
			row.className += ' selected';
			selCount++;
		}
		else
		{
			row.className = row.className.replace(/\s*selected/ig, '');
			selCount--;
		}
		span.innerHTML = selCount;

		var checkAll = document.getElementById(tbl.id+'_check_all');
		if(selCount == tbl.rows.length-2)
			checkAll.checked = true;
		else
			checkAll.checked = false;
	}

	this.OnClickRow = function(e)
	{
		if(!e)
			var e = window.event;
		if(!e.ctrlKey)
			return;
		var obj = (e.target? e.target : (e.srcElement? e.srcElement : null));
		if(!obj)
			return;
		if(!obj.parentNode.cells)
			return;
		var checkbox = obj.parentNode.cells[0].childNodes[0];
		if(checkbox && checkbox.tagName && checkbox.tagName.toUpperCase() == "INPUT" && checkbox.type.toUpperCase() == "CHECKBOX" && !checkbox.disabled)
		{
			checkbox.checked = !checkbox.checked;
			_this.SelectRow(checkbox);
		}
		_this.EnableActions();
	}

	this.SelectAllRows = function(checkbox)
	{
		var tbl = checkbox.parentNode.parentNode.parentNode.parentNode;
		var bChecked = checkbox.checked;
		var i;
		var n = tbl.rows.length;
		for(i=2; i<n; i++)
		{
			var box = tbl.rows[i].cells[0].childNodes[0];
			if(box && box.tagName.toUpperCase() == 'INPUT' && box.type.toUpperCase() == "CHECKBOX")
			{
				if(box.checked != bChecked && !box.disabled)
				{
					box.checked = bChecked;
					this.SelectRow(box);
				}
			}
		}
		this.EnableActions();
	}

	this.EnableActions = function()
	{
		var form = document.forms['form_'+this.table_id];
		if(!form) return;

		var bEnabled = this.IsActionEnabled();
		var bEnabledEdit = this.IsActionEnabled('edit');

		if(form.apply) form.apply.disabled = !bEnabled;
		var b = document.getElementById('action_edit_button');
		if(b) b.className = 'context-button icon action-edit-button'+(bEnabledEdit? '':'-dis');
		b = document.getElementById('action_delete_button');
		if(b) b.className = 'context-button icon action-delete-button'+(bEnabled? '':'-dis');
	}

	this.IsActionEnabled = function(action)
	{
		var form = document.forms['form_'+this.table_id];
		if(!form) return;

		var bChecked = false;
		var span = document.getElementById(this.table_id+'_selected_span');
		if(span && parseInt(span.innerHTML)>0)
			bChecked = true;

		if(action == 'edit')
			return !(form.action_target && form.action_target.checked) && bChecked;
		else
			return (form.action_target && form.action_target.checked) || bChecked;
	}

	this.SetActiveResult = function(callback)
	{
		CHttpRequest.Action = function(result)
		{
			CloseWaitWindow();
			document.getElementById(_this.table_id+"_result_div").innerHTML = result;
			_this.InitTable();
			jsAdminChain.AddItems(_this.table_id+"_navchain_div");
			if(callback)
				callback();
		}
	}

	this.GetAdminList = function(url, callback)
	{
		ShowWaitWindow();

		var link = document.getElementById('navchain-link');
		if(link)
			link.href = url;

		if(url.indexOf('?')>=0)
			url += '&mode=list&table_id='+escape(_this.table_id);
		else
			url += '?mode=list&table_id='+escape(_this.table_id);

		_this.SetActiveResult(callback);
		CHttpRequest.Send(url);
	}

	this.Sort = function(url, bCheckCtrl, args)
	{
		if(bCheckCtrl == true)
		{
			var e = null, bControl = false;
			if(args.length > 0)
				e = args[0];
			if(!e)
				e = window.event;
			if(e) 
				bControl = e.ctrlKey;
			url += (bControl? 'desc':'asc');
		}
		this.GetAdminList(url);
	}

	this.PostAdminList = function(url)
	{
		if(url.indexOf('?')>=0)
			url += '&mode=frame&table_id='+escape(this.table_id);
		else
			url += '?mode=frame&table_id='+escape(this.table_id);

		var frm = document.getElementById('form_'+this.table_id);

		try{frm.action.act.parentNode.removeChild(frm.action);}catch(e){}

		frm.action = url;
		frm.onsubmit();
		frm.submit();
	}
	
	this.ShowSettings = function(url)
	{
		if(document.getElementById("settings_float_div"))
			return;

		CHttpRequest.Action = function(result)
		{
			CloseWaitWindow();

			var div = document.body.appendChild(document.createElement("DIV"));
			div.id = "settings_float_div";
			div.className = "settings-float-form";
			div.style.position = 'absolute';
			div.innerHTML = result;

			var left = parseInt(document.body.scrollLeft + document.body.clientWidth/2 - div.offsetWidth/2);
			var top = parseInt(document.body.scrollTop + document.body.clientHeight/2 - div.offsetHeight/2);
			jsFloatDiv.Show(div, left, top);

			jsUtils.addEvent(document, "keypress", _this.SettingsOnKeyPress);
		}
		ShowWaitWindow();
		CHttpRequest.Send(url);
	}
	
	this.CloseSettings =  function()
	{
		jsUtils.removeEvent(document, "keypress", _this.SettingsOnKeyPress);
		var div = document.getElementById("settings_float_div");
		jsFloatDiv.Close(div);
		div.parentNode.removeChild(div);
	}

	this.SettingsOnKeyPress = function(e)
	{
		if(!e) e = window.event
		if(!e) return;
		if(e.keyCode == 27)
			_this.CloseSettings();
	}

	this.SaveSettings =  function()
	{
		ShowWaitWindow();

		var sCols='', sBy='', sOrder='', sPageSize='';

		var oSelect = document.list_settings.selected_columns;
		var n = oSelect.length;
		for(var i=0; i<n; i++)
			sCols += (sCols != ''? ',':'')+oSelect[i].value;
		
		oSelect = document.list_settings.order_field;
		if(oSelect)
			sBy = oSelect[oSelect.selectedIndex].value;

		oSelect = document.list_settings.order_direction;
		if(oSelect)
			sOrder = oSelect[oSelect.selectedIndex].value;

		oSelect = document.list_settings.nav_page_size;
		sPageSize = oSelect[oSelect.selectedIndex].value;
		
		var bCommon = (document.list_settings.set_default && document.list_settings.set_default.checked);
		
		jsUserOptions.SaveOption('list', this.table_id, 'columns', sCols, bCommon);
		jsUserOptions.SaveOption('list', this.table_id, 'by', sBy, bCommon);
		jsUserOptions.SaveOption('list', this.table_id, 'order', sOrder, bCommon);
		jsUserOptions.SaveOption('list', this.table_id, 'page_size', sPageSize, bCommon);

		var url = window.location.href;
		jsUserOptions.SendData(function(){_this.GetAdminList(url, _this.CloseSettings);});
	}
	
	this.DeleteSettings = function(bCommon)
	{
		ShowWaitWindow();
		var url = window.location.href;
		jsUserOptions.DeleteOption('list', this.table_id, bCommon, function(){_this.GetAdminList(url, _this.CloseSettings);});
	}
}

/************************************************/

function WaitOnKeyPress(e)
{
	if(!e) e = window.event
	if(!e) return;
	if(e.keyCode == 27)
		CloseWaitWindow();
}

function ShowWaitWindow()
{
	CloseWaitWindow();

	var div = document.body.appendChild(document.createElement("DIV"));
	div.id = "wait_window_div";
	div.innerHTML = phpVars.messLoading;
	div.className = "waitwindow";
	div.style.left = document.body.scrollLeft + (document.body.clientWidth - div.offsetWidth) - 5 + "px";
	div.style.top = document.body.scrollTop + 5 + "px";

	if(jsUtils.IsIE())
	{
		var frame = document.createElement("IFRAME");
		frame.src = "javascript:void(0)";
		frame.id = "wait_window_frame";
		frame.className = "waitwindow";
		frame.style.width = div.offsetWidth + "px";
		frame.style.height = div.offsetHeight + "px";
		frame.style.left = div.style.left;
		frame.style.top = div.style.top;
		document.body.appendChild(frame);
	}
	jsUtils.addEvent(document, "keypress", WaitOnKeyPress);
}

function CloseWaitWindow()
{
	jsUtils.removeEvent(document, "keypress", WaitOnKeyPress);

	var frame = document.getElementById("wait_window_frame");
	if(frame)
		frame.parentNode.removeChild(frame);

	var div = document.getElementById("wait_window_div");
	if(div)
		div.parentNode.removeChild(div);
}

/************************************************/

function PopupMenu(id)
{
	var _this = this;
	this.menu_id = id;
	this.controlDiv = null;
	this.dxShadow = 5

	this.OnClose = null;

	this.Create = function(zIndex, dxShadow)
	{
		if(!isNaN(dxShadow))
			this.dxShadow = dxShadow;

		var div = document.createElement("DIV");
		div.id = this.menu_id;
		div.style.position = 'absolute';
		div.style.zIndex = zIndex;
		div.style.left = '-1000px';
		div.style.top = '-1000px';
		div.style.visibility = 'hidden';
		document.body.appendChild(div);
		
		div.innerHTML = 
			'<table cellpadding="0" cellspacing="0" border="0">'+
			'<tr><td class="popupmenu">'+
			'<table cellpadding="0" cellspacing="0" border="0" id="'+this.menu_id+'_items">'+
			'<tr><td></td></tr>'+
			'</table>'+
			'</td></tr>'+
			'</table>';
	}

	this.PopupShow = function(pos)
	{
		var div = document.getElementById(this.menu_id);
		if(!div)
			return;

		setTimeout(function(){jsUtils.addEvent(document, "click", _this.CheckClick)}, 10);
		jsUtils.addEvent(document, "keypress", _this.OnKeyPress);

		var w = div.offsetWidth;
		var h = div.offsetHeight;
		pos = jsUtils.AlignToPos(pos, w, h);

		div.style.width = w + 'px';
		div.style.visibility = 'visible';

		jsFloatDiv.Show(div, pos["left"], pos["top"], this.dxShadow);

	    div.ondrag = jsUtils.False;
	    div.onselectstart = jsUtils.False;
	    div.style.MozUserSelect = 'none';
	}

	this.PopupHide = function()
	{
		var div = document.getElementById(this.menu_id);
		if(div)
		{
			jsFloatDiv.Close(div);
			div.style.visibility = 'hidden';
		}

		if(this.OnClose)
			this.OnClose();

		this.controlDiv = null;
		jsUtils.removeEvent(document, "click", _this.CheckClick);
		jsUtils.removeEvent(document, "keypress", _this.OnKeyPress);
	}

	this.CheckClick = function(e)
	{
		var div = document.getElementById(_this.menu_id);
		if(!div)
			return;

		if(div.style.visibility != 'visible')
			return;

		var x = e.clientX + document.body.scrollLeft;
		var y = e.clientY + document.body.scrollTop;

		/*menu region*/
		var posLeft = parseInt(div.style.left);
		var posTop = parseInt(div.style.top);
		var posRight = posLeft + div.offsetWidth;
		var posBottom = posTop + div.offsetHeight;
		if(x >= posLeft && x <= posRight && y >= posTop && y <= posBottom)
			return;

		if(_this.controlDiv)
		{
			var pos = jsUtils.GetRealPos(_this.controlDiv);
			if(x >= pos['left'] && x <= pos['right'] && y >= pos['top'] && y <= pos['bottom'])
				return;
		}
		_this.PopupHide();
	}

	this.OnKeyPress = function(e)
	{
		if(!e) e = window.event
		if(!e) return;
		if(e.keyCode == 27)
			_this.PopupHide();
	},

	this.BuildItems = function(items)
	{
		if(!items || items.length == 0)
			return;

		var div = document.getElementById(this.menu_id);
		div.style.left='0px';
		div.style.top='0px';
		div.style.width='auto';

		var tbl = document.getElementById(this.menu_id+'_items');
		while(tbl.rows.length>0)
			tbl.deleteRow(0);

		var n = items.length;
		for(var i=0; i<n; i++)
		{
			var row = tbl.insertRow(-1);
			var cell = row.insertCell(-1);
			if(items[i]['SEPARATOR'])
			{
				cell.innerHTML =
					'	<table cellpadding="0" cellspacing="0" border="0" class="popupseparator">\n'+
					'		<tr><td><div class="empty"></div></td></tr>\n'+
					'	</table>\n';
			}
			else
			{
				cell.innerHTML =
					'	<table cellpadding="0" cellspacing="0" border="0" class="popupitem"'+(items[i]['DISABLED']!=true? ' onMouseOver="this.className=\'popupitem popupitemover\';" onMouseOut="this.className=\'popupitem\';" onClick="'+items[i]['ONCLICK']+'"':'')+'>\n'+
					'		<tr>\n'+
					'			<td class="gutter"'+(items[i]['ID']? ' id="'+items[i]['ID']+'"' : '')+'><div class="'+(items[i]['ICONCLASS']? items[i]['ICONCLASS']:'empty')+'"></div></td>\n'+
					'			<td class="item'+(items[i]['DISABLED'] == true? ' disabled' : '')+(items[i]['DEFAULT'] == true? ' default' : '')+'"'+(items[i]["TITLE"]? ' title="'+items[i]["TITLE"]+'"' : '')+'>'+items[i]['TEXT']+'</td>\n'+
					'		</tr>\n'+
					'	</table>\n';
			}
		}

		div.style.width = tbl.parentNode.offsetWidth;
	}
	
	this.IsVisible = function()
	{
		return (document.getElementById(this.menu_id).style.visibility != 'hidden');
	}
}

/************************************************/

function TabControl(name, unique_name, aTabs)
{
	var _this = this;
	this.name = name;
	this.unique_name = unique_name;
	this.aTabs = aTabs;
	this.aTabsDisabled = {};
	this.bExpandTabs = false;

	this.SelectTab = function(tab_id)
	{
		var div = document.getElementById(tab_id);
		if(div.style.display != 'none')
			return;

		for(var i in this.aTabs)
		{
			var tab = document.getElementById(this.aTabs[i]["DIV"])
			if(tab.style.display != 'none')
			{
				this.ShowTab(this.aTabs[i]["DIV"], false);
				tab.style.display = 'none';
				break;
			}
		}

		this.ShowTab(tab_id, true);
		div.style.display = 'block';
		document.getElementById(this.name+'_active_tab').value = tab_id;

		for(var i in this.aTabs)
			if(this.aTabs[i]["DIV"] == tab_id)
			{ 
				this.aTabs[i]["_ACTIVE"] = true;
				if(this.aTabs[i]["ONSELECT"])
					eval(this.aTabs[i]["ONSELECT"]);
				break;
			}
	}

	this.ShowTab = function(tab_id, on)
	{
		var sel = (on? '-selected':'');
		document.getElementById('tab_cont_'+tab_id).className = 'tab-container'+sel;
		document.getElementById('tab_left_'+tab_id).className = 'tab-left'+sel;
		document.getElementById('tab_'+tab_id).className = 'tab'+sel;
		if(tab_id != this.aTabs[this.aTabs.length-1]["DIV"])
			document.getElementById('tab_right_'+tab_id).className = 'tab-right'+sel;
		else
			document.getElementById('tab_right_'+tab_id).className = 'tab-right-last'+sel;
	}

	this.HoverTab = function(tab_id, on)
	{
		var tab = document.getElementById('tab_'+tab_id);
		if(tab.className == 'tab-selected')
			return;

		document.getElementById('tab_left_'+tab_id).className = (on? 'tab-left-hover':'tab-left');
		tab.className = (on? 'tab-hover':'tab');
		var tab_right = document.getElementById('tab_right_'+tab_id);
		if(tab_id != this.aTabs[this.aTabs.length-1]["DIV"])
			tab_right.className = (on? 'tab-right-hover':'tab-right');
		else
			tab_right.className = (on? 'tab-right-last-hover':'tab-right-last');
	}

	this.InitEditTables = function()
	{
		for(var tab in this.aTabs)
		{
			var div = document.getElementById(this.aTabs[tab]["DIV"]);
			var tbl = jsUtils.FindChildObject(div, 'table', 'edit-table');
			if(!tbl)
				continue;

			var n = tbl.rows.length;
			for(var i=0; i<n; i++)
				if(tbl.rows[i].cells.length > 1)
					tbl.rows[i].cells[0].className = 'field-name';
		}
	}

	this.DisableTab = function(tab_id)
	{
		this.aTabsDisabled[tab_id] = true;
		this.ShowDisabledTab(tab_id, true);
		if(this.bExpandTabs)
		{
			var div = document.getElementById(tab_id);
			div.style.display = 'none';
		}
	}

	this.EnableTab = function(tab_id)
	{
		this.aTabsDisabled[tab_id] = false;
		this.ShowDisabledTab(tab_id, this.bExpandTabs);
		if(this.bExpandTabs)
		{
			var div = document.getElementById(tab_id);
			div.style.display = 'block';
		}
	}
	
	this.ShowDisabledTab = function(tab_id, disabled)
	{
		var tab = document.getElementById('tab_cont_'+tab_id);
		if(disabled)
		{
			tab.className = 'tab-container-disabled';
			tab.onclick = null;
			tab.onmouseover = null;
			tab.onmouseout = null;
		}
		else
		{
			tab.className = 'tab-container';
			tab.onclick = function(){_this.SelectTab(tab_id);};
			tab.onmouseover = function(){_this.HoverTab(tab_id, true);};
			tab.onmouseout = function(){_this.HoverTab(tab_id, false);};
		}
	}
	
	this.ToggleTabs = function()
	{
		this.bExpandTabs = !this.bExpandTabs;

		var a = document.getElementById(this.name+'_expand_link');
		a.title = (this.bExpandTabs? phpVars.messCollapseTabs : phpVars.messExpandTabs);
		a.className = (this.bExpandTabs? a.className.replace(/\s*down/ig, ' up') : a.className.replace(/\s*up/ig, ' down'));

		for(var i in this.aTabs)
		{
			var tab_id = this.aTabs[i]["DIV"];
			this.ShowTab(tab_id, false);
			this.ShowDisabledTab(tab_id, (this.bExpandTabs || this.aTabsDisabled[tab_id]));
			var div = document.getElementById(tab_id);
			div.style.display = (this.bExpandTabs && !this.aTabsDisabled[tab_id]? 'block':'none');
			if(i > 0)
			{
				var tbl = jsUtils.FindChildObject(div, 'table', 'edit-tab-title');
				if(this.bExpandTabs)
				{
					try{
						tbl.rows[0].style.display = 'table-row';
					}
					catch(e){
						tbl.rows[0].style.display = 'block';
					}
				}
				else
					tbl.rows[0].style.display = 'none';
			}
		}
		if(!this.bExpandTabs)
		{
			this.ShowTab(this.aTabs[0]["DIV"], true);
			var div = document.getElementById(this.aTabs[0]["DIV"]);
			div.style.display = 'block';
		}
		jsUserOptions.SaveOption('edit', this.unique_name, 'expand', (this.bExpandTabs? 'on':'off'));
	}

	this.ShowWarnings = function(form_name, warnings)
	{
		var form = document.forms[form_name];
		if(!form)
			return;
		for(var i in warnings)
		{
			var e = form.elements[warnings[i]['name']];
			if(!e)
				continue;

			var type = (e.type? e.type.toLowerCase():'');
			var bBefore = false;
			if(e.length > 1 && type != 'select-one' && type != 'select-multiple')
			{
				e = e[0];
				bBefore = true;
			}
			if(type == 'textarea' || type == 'select-multiple')
				bBefore = true;

			var td = e.parentNode;
			var img;
			if(bBefore)
			{
				img = td.insertBefore(new Image(), e);
				td.insertBefore(document.createElement("BR"), e);
			}
			else
			{
				img = td.insertBefore(new Image(), e.nextSibling);
				img.hspace = 2;
				img.vspace = 2;
				img.style.verticalAlign = 'bottom';
			}
			img.src = '/bitrix/themes/'+phpVars.ADMIN_THEME_ID+'/images/icon_warn.gif';
			img.title = warnings[i]['title'];
		}
	}
}

/************************************************/

function ViewTabControl(aTabs)
{
	var _this = this;
	this.aTabs = aTabs;

	this.SelectTab = function(tab_id)
	{
		var div = document.getElementById(tab_id);
		if(div.style.display != 'none')
			return;

		for(var i in this.aTabs)
		{
			var tab_div = document.getElementById(this.aTabs[i]["DIV"]);
			if(tab_div.style.display != 'none')
			{
				var tab = document.getElementById('view_tab_'+this.aTabs[i]["DIV"]);
				tab.innerHTML = this.aTabs[i]["HTML"];
				tab.className = 'view-tab';
				this.ToggleDelimiter(tab, true);
				tab_div.style.display = 'none';
				break;
			}
		}

		var active_tab = document.getElementById('view_tab_'+tab_id);
		active_tab.className = 'view-tab view-tab-active';
		this.ToggleDelimiter(active_tab, false);
		div.style.display = 'block';

		this.RebuildTabs();

		for(var i in this.aTabs)
		{
			if(this.aTabs[i]["DIV"] == tab_id)
			{
				this.ReplaceAnchor(this.aTabs[i]);
				if(this.aTabs[i]["ONSELECT"])
					eval(this.aTabs[i]["ONSELECT"]);
				break;
			}
		}
	}

	this.ToggleDelimiter = function(tab, on)
	{
		var d;
		if((d = jsUtils.FindNextSibling(tab, 'div')) && d.className.indexOf('view-tab-delimiter') != -1)
			d.className = 'view-tab-delimiter'+(on? '':' view-tab-hide-delimiter');
		if((d = jsUtils.FindPreviousSibling(tab, 'div')) && d.className.indexOf('view-tab-delimiter') != -1)
			d.className = 'view-tab-delimiter'+(on? '':' view-tab-hide-delimiter');
	}

	this.DisableTab = function(tab_id)
	{
	}

	this.EnableTab = function(tab_id)
	{
	}

	this.ReplaceAnchor = function(tab)
	{
		var tab_div = document.getElementById('view_tab_'+tab["DIV"]);
		tab["HTML"] = tab_div.innerHTML;
		var a = jsUtils.FindChildObject(tab_div, "a");
		tab_div.innerHTML = a.innerHTML;
	}

	this.RebuildTabs = function()
	{
		var container = jsUtils.FindParentObject(document.getElementById('view_tab_'+_this.aTabs[0]["DIV"]), "div");
		var aPos = [0];
		var selectedIndex = -1;
		var prevTop = -1;
		var last;
		var n = container.childNodes.length;
		for(var i=0; i<n; i++)
		{
			var div = container.childNodes[i];
			if(!div.id)
				continue;

			if(prevTop > -1 && div.offsetTop > prevTop)
				aPos[aPos.length] = i;
			prevTop = div.offsetTop;

			if(selectedIndex == -1 && div.className.indexOf('view-tab-active') != -1)
				selectedIndex = aPos.length-1;
			last = div;
		}
		
		if(selectedIndex < aPos.length && selectedIndex > -1)
		{
			var aDiv = new Array();
			var div = container.childNodes[aPos[selectedIndex]];
			for(var i = aPos[selectedIndex]; i<aPos[selectedIndex+1]; i++)
			{
				aDiv[aDiv.length] = div;
				div = div.nextSibling;
			}
			if(aDiv.length > 0)
			{
				for(var i in aDiv)
					container.removeChild(aDiv[i]);

				while(last.nextSibling)
				{
					last = last.nextSibling;
					if(last.tagName && last.tagName.toUpperCase() == 'BR' && last.className && last.className == 'tab-break')
						break;
				}

				var br = document.createElement("BR");
				br.style.clear='both';
				container.insertBefore(br, last);

				for(var i in aDiv)
				{
					if(aDiv[i].tagName && aDiv[i].tagName.toUpperCase() == 'BR')
						continue;
					container.insertBefore(aDiv[i], last);
				}
			}
		}
	}

	this.Init = function()
	{
		if(this.aTabs.length == 0)
			return;
		for(var i in this.aTabs)
		{
			var div = document.getElementById(this.aTabs[i]["DIV"]);
			if(div.style.display != 'none')
			{
				this.ReplaceAnchor(this.aTabs[i]);
				this.ToggleDelimiter(document.getElementById('view_tab_'+this.aTabs[i]["DIV"]), false);
				break;
			}
		}
		setTimeout(this.RebuildTabs, 10);
		window.onresize = this.RebuildTabs;
	}

	this.Init();
}

/************************************************/

var jsAdminChain =
{
	_chain: '',

	AddItems: function(divId)
	{
		var main_chain = document.getElementById("main_navchain");
		if(!main_chain)
			return;

		if(this._chain == '')
			this._chain = main_chain.innerHTML;
		else
			main_chain.innerHTML = this._chain;

		var div = document.getElementById(divId);
		if(!div)
			return;

		main_chain.innerHTML += '<img src="/bitrix/themes/'+phpVars.ADMIN_THEME_ID+'/images/chain_arrow.gif" alt="" border="0" class="arrow">';
		main_chain.innerHTML += div.innerHTML;
	}
}

/************************************************/

function JCHttpRequest()
{
	this.Action = null; //function(result){}

	this.httpRequest = null;
	this._span = null;

	this.OnDataReady = function(result)
	{
		if(this.Action)
			this.Action(result);
		this._RemoveScript();
		this.httpRequest = null;
	}

	this._CreateHttpObject = function()
	{
		var obj = null;
		if(window.XMLHttpRequest)
		{
			try {obj = new XMLHttpRequest();} catch(e){}
		}
        else if(window.ActiveXObject)
        {
            try {obj = new ActiveXObject("Microsoft.XMLHTTP");} catch(e){}
            if(!obj)
            	try {obj = new ActiveXObject("Msxml2.XMLHTTP");} catch (e){}
        }
        return obj;
	}

	this._CreateScript = function(href)
	{
		var span = null;
		span = document.body.appendChild(document.createElement("SPAN"));
		span.style.display = 'none';
		span.innerHTML = '...<s'+'cript></' + 'script>';
		this._span = span;
		setTimeout(
			function()
			{
			    var s = span.getElementsByTagName("script")[0];
			    s.language = "JavaScript";
			    if(s.setAttribute)
			    	s.setAttribute('src', href);
			    else
			    	s.src = href;
			}, 10
		);
	}

	this._RemoveScript = function()
	{
		if(this._span)
		{
			this._span.parentNode.removeChild(this._span);
			this._span = null;
		}
	}

	this.Send = function(url, acync)
	{
		if(acync != true && acync != false)
			acync = true;
		
		var httpRequest = this._CreateHttpObject();
		if(httpRequest)
		{
			this.httpRequest = httpRequest;
			var _this = this;
			httpRequest.onreadystatechange = function()
			{
				if(httpRequest.readyState == 4)
				{
					try 
					{
						var s = httpRequest.responseText;
						var code = '';
						var start = s.indexOf('<script>');
						if(start != -1)
						{
							var end = s.indexOf('</script>', start);
							if(end != -1)
							{
								code = s.substr(start+8, end-start-8);
								s = s.substr(0, start) + s.substr(end+9);
							}
						}
						_this.OnDataReady(s);
						if(code != '')
							eval(code);
					} 
					catch (e)
					{
						var w = window.open("about:blank");
						w.document.write(httpRequest.responseText);
						w.document.close();
					}
				}
			}
			httpRequest.open("GET", url, acync);
			return httpRequest.send("");
  		}
  		else
  		{
  			this._CreateScript(url);
  		}
	}
}
var CHttpRequest = new JCHttpRequest();

/************************************************/

var jsSelectUtils =
{
	addNewOption: function(select_id, opt_value, opt_name, do_sort)
	{
		var oSelect = document.getElementById(select_id);
		if(oSelect)
		{
			var n = oSelect.length;
			for(var i=0;i<n;i++)
				if(oSelect[i].value==opt_value)
					return;
			var newoption = new Option(opt_name, opt_value, false, false);
			oSelect.options[n]=newoption;
		}
		if(do_sort != false)
			this.sortSelect(select_id);
	},

	deleteOption: function(select_id, opt_value)
	{
		var oSelect = document.getElementById(select_id);
		if(oSelect)
		{
			for(var i=0;i<oSelect.length;i++)
				if(oSelect[i].value==opt_value)
				{
					oSelect.remove(i);
					break;
				}
		}
	},

	deleteSelectedOptions: function(select_id)
	{
		var oSelect = document.getElementById(select_id);
		if(oSelect)
		{
			var i=0;
			while(i<oSelect.length)
				if(oSelect[i].selected)
				{
					oSelect[i].selected=false;
					oSelect.remove(i);
				}
				else
					i++;
		}
	},

	optionCompare: function(record1, record2)
	{
		var value1 = record1.optText.toLowerCase();
		var value2 = record2.optText.toLowerCase();
		if (value1 > value2) return(1);
		if (value1 < value2) return(-1);
		return(0);
	},

	sortSelect: function(select_id)
	{
		var oSelect = document.getElementById(select_id);
		if(oSelect)
		{
			var myOptions = [];
			var n = oSelect.options.length;
			for (var i=0;i<n;i++)
			{
				myOptions[i] = {
					optText:oSelect[i].text,
					optValue:oSelect[i].value
				};
			}
			myOptions.sort(this.optionCompare);
			oSelect.length=0;
			n = myOptions.length;
			for(var i=0;i<n;i++)
			{
				var newoption = new Option(myOptions[i].optText, myOptions[i].optValue, false, false);
				oSelect[i]=newoption;
			}
		}
	},

	selectAllOptions: function(select_id)
	{
		var oSelect = document.getElementById(select_id);
		if(oSelect)
		{
			var n = oSelect.length;
			for(var i=0;i<n;i++)
				oSelect[i].selected=true;
		}
	},
	
	addSelectedOptions: function(oSelect, to_select_id)
	{
		if(!oSelect)
			return;
		var n = oSelect.length;
		for(var i=0; i<n; i++)
			if(oSelect[i].selected)
				this.addNewOption(to_select_id, oSelect[i].value, oSelect[i].text, false);
	},
		
	moveOptionsUp: function(oSelect)
	{
		if(!oSelect)
			return;
		var n = oSelect.length;
		for(var i=0; i<n; i++)
			if(oSelect[i].selected && i>0 && oSelect[i-1].selected == false)
			{
				var option1 = new Option(oSelect[i].text, oSelect[i].value);
				var option2 = new Option(oSelect[i-1].text, oSelect[i-1].value);
				oSelect[i] = option2;
				oSelect[i].selected = false;
				oSelect[i-1] = option1;
				oSelect[i-1].selected = true;
			}
	},

	moveOptionsDown: function(oSelect)
	{
		if(!oSelect)
			return;
		var n = oSelect.length;
		for(var i=n-1; i>=0; i--)
			if(oSelect[i].selected && i<n-1 && oSelect[i+1].selected == false)
			{
				var option1 = new Option(oSelect[i].text, oSelect[i].value);
				var option2 = new Option(oSelect[i+1].text, oSelect[i+1].value);
				oSelect[i] = option2;
				oSelect[i].selected = false;
				oSelect[i+1] = option1;
				oSelect[i+1].selected = true;
			}
	}

}
/************************************************/
function JCUserOptions()  
{
	var _this = this;
	this.options = null;
	this.bSend = false;
	this.request = new JCHttpRequest();
		
	this.GetParams = function()
	{
		var sParam = '';
		var n = -1;
		var prevParam = '';
		for(var i in _this.options)
		{
			var aOpt = _this.options[i];
			if(prevParam != aOpt[0]+'.'+aOpt[1])
			{
				n++;
				sParam += '&p['+n+'][c]='+jsUtils.urlencode(aOpt[0]);
				sParam += '&p['+n+'][n]='+jsUtils.urlencode(aOpt[1]);
				if(aOpt[4] == true)
					sParam += '&p['+n+'][d]=Y';
				prevParam = aOpt[0]+'.'+aOpt[1];
			}
			sParam += '&p['+n+'][v]['+jsUtils.urlencode(aOpt[2])+']='+jsUtils.urlencode(aOpt[3]);
		}
		
		return sParam.substr(1);
	}

	this.SaveOption = function(sCategory, sName, sValName, sVal, bCommon)
	{
		if(!this.options)
			this.options = new Object();

		if(bCommon != true)
			bCommon = false;
		this.options[sCategory+'.'+sName+'.'+sValName] = [sCategory, sName, sValName, sVal, bCommon];

		var sParam = this.GetParams();
		if(sParam != '')
			document.cookie = phpVars.cookiePrefix+"_LAST_SETTINGS=" + sParam + "; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/;";

		if(!this.bSend)
		{
			this.bSend = true;
			setTimeout(function(){_this.SendData(null)}, 5000);
		}
	}
	
	this.SendData = function(callback)
	{
		var sParam = _this.GetParams();
		_this.options = null;
		_this.bSend = false;
		if(sParam != '')
		{
			document.cookie = phpVars.cookiePrefix+"_LAST_SETTINGS=; path=/;";
			_this.request.Action = callback; 
			_this.request.Send('/bitrix/admin/user_options.php?'+sParam);
		}
	}

	this.DeleteOption = function(sCategory, sName, bCommon, callback)
	{
		_this.request.Action = callback; 
		_this.request.Send('/bitrix/admin/user_options.php?action=delete&c='+sCategory+'&n='+sName+(bCommon == true? '&common=Y':''));
	}
}
var jsUserOptions = new JCUserOptions();
/************************************************/
