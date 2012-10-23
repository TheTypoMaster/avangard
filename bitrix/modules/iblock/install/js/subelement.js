function JCAdminSubList(table_id,list_url)
{
	var _this = this;
	this.table_id = table_id;
	this.list_url = list_url;

	this.InitTable = function()
	{
		var tbl = BX(this.table_id);
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
			var sort_table = jsUtils.FindChildObject(cell_sort, "table", "subsorting");

			for(j=0; j<2; j++)
			{
				var cell = tbl.rows[j].cells[i];

				cell.onmouseover = function(){_this.HighlightGutter(this, true);};
				cell.onmouseout = function(){_this.HighlightGutter(this, false);};

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
							cell.className += ' subsorted';
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
			row.cells[0].className += ' subleft';
	 		row.cells[row.cells.length-1].className += ' subright';

	 		if(row.className && row.className == 'footer')
	 			continue;

			/*sorted column*/
			if(sortedIndex != -1 && sortedIndex < row.cells.length)
				row.cells[sortedIndex].className += ' subsorted';

			if(i>=2)
			{
				/*first column checkbox action*/
				var checkbox = row.cells[0].childNodes[0];
				if(checkbox && checkbox.tagName && checkbox.tagName.toUpperCase() == "INPUT" && checkbox.type.toUpperCase() == "CHECKBOX")
				{
					checkbox.onclick = function(){_this.SelectRow(this); _this.EnableActions();};
					jsUtils.addEvent(row, "click", _this.OnClickRow);
				}

				/*rows mousover action*/
				row.onmouseover = function(){_this.HighlightRow(this, true);};
				row.onmouseout = function(){_this.HighlightRow(this, false);};

				if(i%2 == 0)
				{
					row.className += ' subodd';
				}
				else
				{
					row.className += ' subeven';
				}

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
							menu.SetItems(targetElement.oncontextmenu());
							menu.BuildItems();
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
	};

	this.Destroy = function(bLast)
	{
		var tbl = BX(this.table_id);
		if(!tbl || tbl.rows.length<1 || tbl.rows[0].cells.length<1)
			return;

		var i;
		var nCols = tbl.rows[0].cells.length;
		for(i=0; i<nCols; i++)
		{
			var j;
			for(j=0; j<2; j++)
			{
				var cell = tbl.rows[j].cells[i];
				cell.onmouseover = null;
				cell.onmouseout = null;
				cell.onclick = null;
			}
		}
		var n = tbl.rows.length;
		for(i=0; i<n; i++)
		{
			var row = tbl.rows[i];
			var checkbox = row.cells[0].childNodes[0];
			if(checkbox && checkbox.onclick)
				checkbox.onclick = null;
			row.onmouseover = null;
			row.onmouseout = null;
			jsUtils.removeAllEvents(row);
		}
		if(bLast == true)
			_this = null;
	};

	this.HighlightGutter = function(cell, on)
	{
		var table = cell.parentNode.parentNode.parentNode;
		var gutter = table.rows[0].cells[cell.cellIndex];
		if(on)
			gutter.className += ' subover';
		else
			gutter.className = gutter.className.replace(/\s*subover/i, '');
	};

	this.HighlightRow = function(row, on)
	{
		if(on)
			row.className += ' subover';
		else
			row.className = row.className.replace(/\s*subover/i, '');
	};

	this.SelectRow = function(checkbox)
	{
		var row = checkbox.parentNode.parentNode;
		var tbl = row.parentNode.parentNode;
		var span = BX(tbl.id+'_selected_span');
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

		var checkAll = BX(tbl.id+'_check_all');
		if(selCount == tbl.rows.length-2)
			checkAll.checked = true;
		else
			checkAll.checked = false;
	};

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
	};

	this.SelectAllRows = function(checkbox)
	{
		var tbl = checkbox.parentNode.parentNode.parentNode.parentNode;
		var bChecked = checkbox.checked;
		var i;
		var n = tbl.rows.length;
		for(i=2; i<n; i++)
		{
			var box = tbl.rows[i].cells[0].childNodes[0];
			if(box && box.tagName && box.tagName.toUpperCase() == 'INPUT' && box.type.toUpperCase() == "CHECKBOX")
			{
				if(box.checked != bChecked && !box.disabled)
				{
					box.checked = bChecked;
					this.SelectRow(box);
				}
			}
		}
		this.EnableActions();
	};

	this.EnableActions = function()
	{
		var form = BX('form_'+this.table_id);
		if (!form) return;

		var bEnabled = this.IsActionEnabled();
		var bEnabledEdit = this.IsActionEnabled('edit');

		var apply = BX(this.table_id+'_apply_sub_button');
		if (apply) apply.disabled = !bEnabled;
		var b = BX(this.table_id+'_action_edit_button');
		if(b) b.className = 'context-button icon action-edit-button'+(bEnabledEdit? '':'-dis');
		b = BX(this.table_id+'_action_delete_button');
		if(b) b.className = 'context-button icon action-delete-button'+(bEnabled? '':'-dis');
		b = BX(this.table_id+'_action');
		if (b) b.disabled = !bEnabled;
	};

	this.IsActionEnabled = function(action)
	{
		var form = BX('form_'+this.table_id);
		if (!form) return;

		var bChecked = false;
		var span = BX(this.table_id+'_selected_span');
		if(span && parseInt(span.innerHTML)>0)
			bChecked = true;

		var action_target = BX(this.table_id+'_action_sub_target');
		if(action == 'edit')
			return !(action_target && action_target.checked) && bChecked;
		else
			return (action_target && action_target.checked) || bChecked;
	};

	this.SetActiveResult = function(callback, url)
	{
		CHttpRequest.Action = function(result)
		{
			BX.closeWait();
			_this.Destroy(false);
			BX(_this.table_id+"_result_div").innerHTML = result;
			_this.InitTable();
			_this.ActivateMainForm();
			/*jsAdminChain.AddItems(_this.table_id+"_navchain_div"); */
			if(callback)
				callback(url);
		};
	};

	this.GetAdminList = function(url, callback)
	{
		BX.showWait();
		var re = new RegExp('&mode=list&table_id='+escape(_this.table_id), 'g');
		url = url.replace(re, '');

		var link = BX('navchain-link');
		if(link)
			link.href = url;

		if(url.indexOf('?')>=0)
			url += '&mode=list&table_id='+escape(_this.table_id);
		else
			url += '?&mode=list&table_id='+escape(_this.table_id);

		_this.SetActiveResult(callback, url);
		CHttpRequest.Send(url);
	};

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
	};

	this.PostAdminList = function(url)
	{
		if(url.indexOf('?')>=0)
			url += '&mode=frame&table_id='+escape(this.table_id);
		else
			url += '?mode=frame&table_id='+escape(this.table_id);

		var frm = BX('form_'+this.table_id);

		try{frm.action.act.parentNode.removeChild(frm.action);}catch(e){}

		frm.action = url;
		frm.onsubmit();
		frm.submit();
	};

	this.ShowSettings = function(url)
	{
		if(BX("settings_float_div"))
			return;

		CHttpRequest.Action = function(result)
		{
			BX.closeWait();

			if(result == '')
				return;

			var div = document.body.appendChild(document.createElement("DIV"));
			div.id = "settings_float_div";
			div.className = "settings-float-form";
			div.style.position = 'absolute';
			div.style.zIndex = 1000;
			div.innerHTML = result;

			var left = parseInt(document.body.scrollLeft + document.body.clientWidth/2 - div.offsetWidth/2);
			var top = parseInt(document.body.scrollTop + document.body.clientHeight/2 - div.offsetHeight/2);
			jsFloatDiv.Show(div, left, top);

			jsUtils.addEvent(document, "keypress", _this.SettingsOnKeyPress);
		};
		BX.showWait();
		CHttpRequest.Send(url);
	};

	this.CloseSettings =  function()
	{
		BX.closeWait();
		jsUtils.removeEvent(document, "keypress", _this.SettingsOnKeyPress);
		var div = BX("settings_float_div");
		jsFloatDiv.Close(div);
		div.parentNode.removeChild(div);
	};

	this.SettingsOnKeyPress = function(e)
	{
		if(!e) e = window.event;
		if(!e) return;
		if(e.keyCode == 27)
			_this.CloseSettings();
	};

	this.SaveSettings =  function()
	{
		BX.showWait();

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

		var url = this.list_url;
		jsUserOptions.SendData(function(){_this.GetAdminList(url, _this.CloseSettings);});
	};

	this.DeleteSettings = function(bCommon)
	{
		BX.showWait();
		var url = this.list_url;
		jsUserOptions.DeleteOption('list', this.table_id, bCommon, function(){_this.GetAdminList(url, _this.CloseSettings);});
	};
	
	this.FormSubmit = function()
	{
		var form = BX('form_'+this.table_id);
		if (!form) return;
		
		var obj = form.getElementsByTagName('input');
		if (!obj) return;
		var ln = obj.length;
		var reqdata = '';
	
		if (0 < ln)
		{
			var count = 0;
			for (i = 0; i < ln; i++)
			{
				if ('SUB_ID[]' == obj[i].name)
				{
					if (obj[i].checked)
					{
						if (reqdata.length > 0) reqdata += '&';
						reqdata += 'SUB_ID[]='+obj[i].value;
					}
				}
				else if ('action_button' == obj[i].name)
				{
					if (reqdata.length > 0) reqdata += '&';
					reqdata += 'action_button='+obj[i].value;
				}
				else if ('sessid' == obj[i].name)
				{
					if (reqdata.length > 0) reqdata += '&';
					reqdata += 'sessid='+obj[i].value;
				}
			}
			var res = BX.ajax.prepareData(reqdata);
			if (0 < res.length)
			{
				BX.showWait();
				BX.ajax.post(this.list_url+'&mode=frame',res,this.GetRes);
			}
		}
	};

	this.ExecuteFormAction = function(id)
	{
		if (!id)
			return;
		var obButton = BX(id);
		if (!obButton)
			return;

		var obActionCode = BX(this.table_id+'_action_button');
		if (!obActionCode)
			return;
		var obSessionId = BX('sessid');
		var reqdata = '';
		
		if (this.table_id+'_apply_sub_button' == obButton.id)
		{
			var obAction = BX(this.table_id+'_action');
			if (obAction)
			{
				obActionCode.value = obAction[obAction.selectedIndex].value; 
				if(obAction[obAction.selectedIndex].getAttribute('custom_action'))
				{
					eval(obAction[obAction.selectedIndex].getAttribute('custom_action'));
				}
				var form = BX('form_'+this.table_id);
				if (!form) return;
				
				var obj = BX.findChildren(form,{'attr': {'name' : 'SUB_ID[]'}},true);

				if (!obj) return;
				var ln = obj.length;

				if (0 < ln)
				{
					for (var i = 0; i < ln; i++)
					{
						if (obj[i].checked)
						{
							if (reqdata.length > 0) reqdata += '&';
							reqdata += 'SUB_ID['+i+']='+obj[i].value;
						}
					}
					reqdata += '&action_button='+obActionCode.value;
					reqdata += '&sessid='+obSessionId.value;
				}

			}
		}
		else if (this.table_id+'_save_sub_button' == obButton.id)
		{
			var form = BX('form_'+this.table_id);
			if (!form) return;
			var form_info = BX.findChildren(form,{},true);
			if (0 < form_info.length)
			{
				for (var i = 0; i < form_info.length; i++)
				{
					if (form_info[i].name)
					{
						var bAttr = true;
						if (('radio' == form_info[i].type) || ('checkbox' == form_info[i].type))
						{
							if (!form_info[i].checked)
								bAttr = false;
						}
						else if ('file' == form_info[i].type)
						{
							bAttr = false;
						}
						if (bAttr)
						{
							if ('select-multiple' == form_info[i].type)
							{
								if (0 < form_info[i].length)
									for (var j = 0; j < form_info[i].length; j++)
									{
										if (form_info[i].options[j].selected)
										{
											if (reqdata.length > 0) reqdata += '&';
											reqdata += form_info[i].name+'='+form_info[i].options[j].value;
										}
									}
							}
							else
							{
								if (reqdata.length > 0) reqdata += '&';
								reqdata += form_info[i].name+'='+form_info[i].value;
							}
						}
					}
				}
				reqdata += '&save=yes';
				reqdata += '&sessid='+obSessionId.value;
			}
		}

		var res = BX.ajax.prepareData(reqdata);
		if (0 < res.length)
		{
			BX.showWait();
			BX.ajax.post(this.list_url+'&mode=frame',res,this.GetRes);
		}

		/*
		 * id="'.$this->table_id.'_action_edit_button"
		 * id="'.$this->table_id.'_action_delete_button"
		 * id="'.$this->table_id.'_apply_sub_button"
		 * 
		 * id="'.$this->table_id.'_action"
		 * 
		 */
	};
	
	this.GetRes = function(result)
	{
		BX.closeWait();
		_this.Destroy(false);
		BX(_this.table_id+"_result_div").innerHTML = result;
		_this.InitTable();
		/*jsAdminChain.AddItems(_this.table_id+"_navchain_div"); */
	};

	this.DeActivateMainForm = function()
	{
		var btnsave = BX('savebtn');
		if (btnsave)
			btnsave.disabled = true;
		var dontsave = BX('dontsave');
		if (dontsave)
			dontsave.disabled = true;
		var save = BX('save');
		if (save)
			save.disabled = true;
		var apply = BX('apply');
		if (apply)
			apply.disabled = true;
		var cancel = BX('cancel');
		if (cancel)
			cancel.disabled = true;
		if (top.BX.WindowManager)
		{
			var obWin = top.BX.WindowManager.Get();
			if (obWin)
				obWin.DenyClose();
		}
	};

	this.ActivateMainForm = function()
	{
		var btnsave = BX('savebtn');
		if (btnsave)
			btnsave.disabled = false;
		var dontsave = BX('dontsave');
		if (dontsave)
			dontsave.disabled = false;
		var save = BX('save');
		if (save)
			save.disabled = false;
		var apply = BX('apply');
		if (apply)
			apply.disabled = false;
		var cancel = BX('cancel');
		if (cancel)
			cancel.disabled = false;
		if (top.BX.WindowManager)
		{
			var obWin = top.BX.WindowManager.Get();
			if (obWin)
				obWin.AllowClose();
		}
	};
}