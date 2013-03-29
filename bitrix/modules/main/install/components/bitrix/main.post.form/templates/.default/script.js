;(function(window){
if (window.LHEPostForm) return;
window.LHEPostForm = function(formID, params)
{
	this.Inited = this.Init(formID, params);

	this.formID = formID;
	this.classID = 'PlEditor' + formID;
	this.eID = (params['eID'] ? params['eID'] : 'oPostFormLHE_' + formID);
	this.pLEditor = window[this.eID];

	this.IsBlog = !!params["IsBlog"];
	this.IsWebdavInstalled = !!params["IsWebdavInstalled"];
	this.arFiles = (typeof params["arFiles"] == "object" && params["arFiles"] != null ? params["arFiles"] : {});
	this.sNewFilePostfix = (params["sNewFilePostfix"] ? params["sNewFilePostfix"] : '');
	this.arActions = (typeof params["arActions"] == "object" && params["arActions"] != null ? params["arActions"] : {});

	this.Image = {
		'thumb_width' : 800,
		'code' : '[IMG ID=#FILE_ID##ADDITIONAL#]',
		'html' : '<img id="#FILE_ID#" src="#SRC#" title=""#ADDITIONAL# />'
	};
	this.File = {
		'code' : '[FILE ID=#FILE_ID##ADDITIONAL#]',
		'html' : '<span style="color: #2067B0; border-bottom: 1px dashed #2067B0;" id="#FILE_ID#"#ADDITIONAL#>#FILE_NAME#</span>'
	};
}

window.LHEPostForm.prototype = {
	Init : function(formID, params)
	{
		var eID = this.eID;

		params['LoadFormController'] = (params['WDLoadFormController'] === true ? "WDLoadFormController" : "BFileDLoadFormController");
		BX.ready(
			function()
			{
				BX.bind(
					BX.findChild(BX(formID), {'attr': {id: 'bx-b-uploadimage'}}, true, false),
					'click',
					function(){
						BX.onCustomEvent(
							BX.findParent(BX.findChild(BX(formID), {'className': 'file-selectdialog'}, true, false)),
							'BFileDLoadFormController'
						);
					}
				);
				BX.bind(
					BX.findChild(BX(formID), {'attr': {id: 'bx-b-uploadfile'}}, true, false),
					'click',
					function(){
						BX.onCustomEvent(
							BX.findParent(BX.findChild(BX(formID), {'className': 'file-selectdialog'}, true, false)),
							params['LoadFormController']);}
				);
			}
		);

		BX.addCustomEvent(
			BX.findParent(BX.findChild(BX(formID), {'className': 'file-selectdialog'}, true, false)),
			'OnFileUploadSuccess',
			function(result, obj){window['PlEditor' + formID]['OnFileUploadSuccess'](result, obj);}
		);
		BX.addCustomEvent(
			BX.findParent(BX.findChild(BX(formID), {'className': 'file-selectdialog'}, true, false)),
			'OnFileUploadRemove',
			function(result, obj){window['PlEditor' + formID]['OnFileUploadRemove'](result, obj);}
		);

		return true;
	},

	OnFileUploadSuccess : function(result, obj)
	{
		window[this.eID].SaveContent();
		window[this.eID]['arFiles'].push(result.element_id);

		if (this.IsBlog && this.IsWebdavInstalled && result.storage == 'bfile' && (!result.element_content_type ||result.element_content_type.substr(0,6) != 'image/'))
		{
			obj.agent.StopUpload(BX('wd-doc'+result.element_id));
		}
		else if (!window[this.eID]['oSpecialParsers']['postfile'] && !window[this.eID]['oSpecialParsers']['postimage'])
		{
			return ;
		}
		else if (result.storage == 'bfile')
		{
			if(BX.findChild(BX(this.formID), {'attr': {id: 'upload-cid'}}, true, false))
				BX.findChild(BX(this.formID), {'attr': {id: 'upload-cid'}}, true, false).value = obj.CID;

			if(result.element_content_type && result.element_content_type.substr(0,6) == 'image/')
			{

				var
					img = BX.findChild(BX('wd-doc'+result.element_id), {'tagName': 'img'}, true, false),
					img_wrap = BX.findChild(BX('wd-doc'+result.element_id), {'className': 'feed-add-img-wrap'}, true, false),
					img_title = BX.findChild(BX('wd-doc'+result.element_id), {'className': 'feed-add-img-title'}, true, false);

				BX.bind(img_wrap, "click", new Function("window['" + this.classID + "'].insertFile('" +
					result.element_id + this.sNewFilePostfix + "');"));
				BX.bind(img_title, "click", new Function("window['" + this.classID + "'].insertFile('" +
					result.element_id + this.sNewFilePostfix + "');"));

				img_wrap.style.cursor = img_title.style.cursor = "pointer";
				img_wrap.title = img_title.title = BX.message('MPF_IMAGE');

				this.checkFile(result.element_id, result);
				this.insertFile((result.element_id + this.sNewFilePostfix));
			}
			else if (window[this.eID]['oSpecialParsers']['postfile'])
			{
				var
					name_wrap = BX.findChild(BX('wd-doc'+result.element_id), {'className': 'f-wrap'}, true, false);
				BX.bind(name_wrap, "click", new Function("window['" + this.classID + "'].insertFile('" + result.element_id + this.sNewFilePostfix + "');"));

				name_wrap.style.cursor = "pointer";
				name_wrap.title = BX.message('MPF_FILE');

				this.checkFile(result.element_id, result);
				this.insertFile((result.element_id + this.sNewFilePostfix));
			}
		}
	},

	OnFileUploadRemove : function(result)
	{
		if(BX.findChild(BX(this.formID), {'attr': {id: 'wd-doc'+result}}, true, false))
		{
			window[this.classID].deleteFile(result + this.sNewFilePostfix);
		}
	},

	showPanelEditor : function(show, pEditor)
	{
		formHeaders = BX.findChild(BX(this.formID), {'className': /bxlhe-editor-buttons/ }, true, true);
		var p = ((formHeaders && formHeaders.length >= 1) ? formHeaders[formHeaders.length-1].parentNode : null);
		pEditor = (typeof pEditor == "object" && pEditor != null ? pEditor : window[this.eID]);

		if(show || (p && p.style.display == "none"))
		{
			if(p) { p.style.display = "table-row"; }
			pEditor.buttonsHeight = 34;
			pEditor.ResizeFrame();
		}
		else
		{
			if(p) { BX.hide(p); }
			pEditor.buttonsHeight = 0;
			pEditor.ResizeFrame();
		}
	},

	checkFile : function(id, result)
	{
		if (typeof result == "object" && result != null)
		{
			id = parseInt(id);
			id = id + this.sNewFilePostfix;

			this.arFiles[id] = {
				id : id,
				name : result.element_name,
				size: result.element_size,
				url: result.element_url,
				type: result.element_content_type,
				src: result.element_thumbnail,
				thumbnail: result.element_image,
				isImage: (result.element_content_type && result.element_content_type.substr(0,6) == 'image/')};
		}
		return (typeof this.arFiles[id] == "object" && this.arFiles[id] != null);
	},

	insertFile : function (id, width)
	{
		if (
			!window[this.eID] ||
				!this.checkFile(id) ||
				(!window[this.eID]['oSpecialParsers']['postfile'] && !window[this.eID]['oSpecialParsers']['postimage']) ||
				(!window[this.eID]['oSpecialParsers']['postfile'] && !this.arFiles[id]['isImage']))
		{
			return false;
		}

		var fileID = this.arFiles[id]['id'],
			params = '',
			res = (
				window[this.eID].sEditorMode == 'html'
					?
					( this.arFiles[id]['isImage'] ? this.Image.html : this.File.html )
					:
					( window[this.eID]['oSpecialParsers']['postimage'] && this.arFiles[id]['isImage'] ? this.Image.code : this.File.code )
				);

		if (this.arFiles[id]['isImage'] && width > 0)
		{
			var widthC = ((window[this.eID].arConfig.width && window[this.eID].arConfig.width.indexOf('%') <= 0) ?
				parseInt(window[this.eID].arConfig.width)*0.8 : this.config.thumb_width);
			params = (width > widthC ? ' width="80%"' : '');
		}

		if (window[this.eID].sEditorMode == 'code' && window[this.eID].bBBCode) // BB Codes
			window[this.eID].WrapWith("", "", res.
				replace("\#FILE_ID\#", fileID).
				replace("\#ADDITIONAL\#", ""));
		else if(window[this.eID].sEditorMode == 'html') // WYSIWYG
		{
			window[this.eID].InsertHTML(res.
				replace("\#FILE_ID\#", window[this.eID].SetBxTag(false,
				{'tag': (window[this.eID]['oSpecialParsers']['postimage'] && this.arFiles[id]['isImage'] ? "postimage" : "postfile"), params: {'value' : fileID}})).
				replace("\#SRC\#", this.arFiles[id].src).
				replace("\#URL\#", this.arFiles[id].url).
				replace("\#FILE_NAME\#", this.arFiles[id].name).
				replace("\#ADDITIONAL\#", params)
			);
			setTimeout(new Function('window["' + this.eID + '"].AutoResize();'), 500);
		}
	},

	deleteFile: function(id, url, el)
	{
		if (typeof url == "string")
		{
			BX.remove(el.parentNode);
			BX.ajax.get(url, function(data){});
		}
		window[this.eID].SaveContent();
		var content = window[this.eID].GetContent();
		content = content.
			replace(new RegExp('\\[IMG ID='+ id +'\\]','g'), '').
			replace(new RegExp('\\[FILE ID='+ id +'\\]','g'), '');
		window[this.eID].SetContent(content);
		window[this.eID].SetEditorContent(window[this.eID].content);
		window[this.eID].SetFocus();
		window[this.eID].AutoResize();
		this.arFiles[id] = false;
	},

	makeButton : function (oldb, newb)
	{
		var el = BX.findChild(BX(this.formID), {'attr': {'id': oldb}}, true, false);
		BX.remove(BX.findParent(el), true);
		BX.findChild(BX(this.formID), {'attr': {'id': newb}}, true, false).appendChild(el);
		el.style.backgroundImage = 'url(/bitrix/images/1.gif)';
		el.src = '/bitrix/images/1.gif';
		el.style.width = '25px';
		el.style.height = '25px';
		el.onmouseout = '';
		el.onmouseover = '';
		el.className = '';
	}
}
window["mentionText"] = '';
window.BXfpdSelectCallbackMent = function (item, type, search, formID, editorName)
{
	if(type == 'users')
	{
		if(item.entityId > 0)
		{
			if(window[editorName])
			{
				if (window[editorName].sEditorMode == 'code' && window[editorName].bBBCode) // BB Codes
				{
					window[editorName].WrapWith("", "", "[USER=" + item.entityId + "]" + item.name + "[/USER]");
				}
				else if(window[editorName].sEditorMode == 'html') // WYSIWYG
				{
					window[editorName].SetFocus();

					r = window[editorName].GetSelectionRange();

					win = window[editorName].pEditorWindow;
					if(win.document.selection) // IE8 and below
					{
						r = BXfixIERangeObject(r, win);
						if (r.endContainer)
							txt = r.endContainer.nodeValue;
					}
					else if (!r)
					{
						return true;
					}
					else
					{
						txt = r.endContainer.textContent;
					}

					lastS = r.endOffset+2;
					if(lastS > r.endOffset)
						lastS = r.endOffset;
					if(bPlus)
						txtPos = txt.lastIndexOf("+", lastS);
					else
						txtPos = txt.lastIndexOf("@", lastS);

					txt2 = txt.substr(0, txtPos);
					txtleng = txt2.length;

					var rng = window[editorName].pEditorDocument.createRange();

					if(txtPos < 0)
						txtPos = r.endContainer.length;

					txtPosEnd = txtPos+1;
					if(window["mentionText"].length > 0)
						txtPosEnd = window["mentionText"].length + txtPosEnd;
					else
					if(txtPosEnd > r.endContainer.length)
						txtPosEnd = r.endContainer.length;

					rng.setStart(r.endContainer, txtPos);
					rng.setEnd(r.endContainer, txtPosEnd);
					window[editorName].SelectRange(rng);

					adit = '&nbsp;';
					if(txtleng <= 0)
						adit = '';

					window[editorName].InsertHTML(adit + '<span id="' + window[editorName].SetBxTag(false, {'tag': "postuser", 'params': {'value' : item.entityId}}) + '" style="color: #2067B0; border-bottom: 1px dashed #2067B0;">' + item.name + '</span>&nbsp;');
					window[editorName].SetFocus();
				}
			}
			BX.SocNetLogDestination.obItemsSelected[window['BXSocNetLogDestinationFormNameMent' + formID]] = {};
			window['BXfpdStopMent' + formID]();
			window["mentionText"] = '';

		}
	}
}

window.deleteTag = function(val, el)
{
	BX.remove(el, true);
	BX('tags-hidden').value = BX('tags-hidden').value.replace(val+',', '');
	BX('tags-hidden').value = BX('tags-hidden').value.replace('  ', ' ');
}

window.addTag = function()
{
	setTimeout(function(){
		tagInput = BX.findChild(BX('post-tags-input'), {'tag': 'input' });
		var tags = tagInput.value.split(",");
		for (var i = 0; i < tags.length; i++ )
		{
			var tag = BX.util.trim(tags[i]);
			if(tag.length > 0)
			{
				var allTags = BX('tags-hidden').value.split(",");
				if(!BX.util.in_array(tag, allTags))
				{
					el = BX.create('SPAN', {'html': BX.util.htmlspecialchars(tag) + '<span class="feed-add-post-del-but" onclick="deleteTag(\'' + BX.util.htmlspecialchars(tag) + '\', this.parentNode)"></span>', 'attrs' : {'class': 'feed-add-post-tags'}});
					BX('post-tags-container').insertBefore(el, BX('bx-post-tag'));
					BX('tags-hidden').value += tag + ',';
				}
			}
		}

		tagInput.value = '';
		popupTag.close();
	}, 10);
}

window.bxPFParser = function(e, eID, formID)
{
	var pEditor = (typeof(eID) == 'string' ? window[eID] : eID);
	if(((e.keyCode == 187 || e.keyCode == 50 || e.keyCode == 107 || e.keyCode == 43 || e.keyCode == 61) && (e.shiftKey || e.modifiers > 3)) || e.keyCode == 107)
	{
		bPlus = false;
		setTimeout(function(){
			var r = pEditor.GetSelectionRange();

			win = pEditor.pEditorWindow;
			if(win.document.selection) // IE8 and below
			{
				r = BXfixIERangeObject(r, win);
				txt = r.endContainer.nodeValue;
			}
			else
			{
				txt = r.endContainer.textContent;
			}

			if(txt.length > 0 && (txt.slice(r.endOffset-1, r.endOffset) == "@" || txt.slice(r.endOffset-1, r.endOffset) == "+"))
			{
				if(txt.slice(r.endOffset-1, r.endOffset) == "+")
					bPlus = true;
				prevS = txt.slice(r.endOffset-2, r.endOffset-1);
				if(prevS == "+" || prevS == "@" || prevS == "," || (prevS.length == 1 && BX.util.trim(prevS) == 0) || prevS == "" || prevS== "(")
				{
					window['bMentListen'] = true;
					window["mentionText"] = '';
					if(!BX.SocNetLogDestination.isOpenDialog())
						BX.SocNetLogDestination.openDialog(window['BXSocNetLogDestinationFormNameMent' + formID]);
				}
			}
		}, 10);
	}

	if(window['bMentListen'] === true)
	{
		if(e.keyCode == 8) // backspace
		{
			setTimeout(function(){
				r = pEditor.GetSelectionRange();

				win = pEditor.pEditorWindow;
				if(win.document.selection) // IE8 and below
				{
					r = BXfixIERangeObject(r, win);
					if(r.endContainer)
						txt = r.endContainer.nodeValue;
				}
				else
				{
					txt = r.endContainer.textContent;
				}
				if(txt === undefined || txt == null || txt.length == 0 || (txt.lastIndexOf("+", r.endOffset) == -1 && txt.lastIndexOf("@", r.endOffset) == -1))
				{
					window['bMentListen'] = false;
					window['BXfpdStopMent' + formID]();
				}
			}, 50);
		}
	}
	if(window['bMentListen'] === true)
	{
		if(e.keyCode == 27) //ESC
		{
			window['BXfpdStopMent' + formID]();
		}
		else if(e.keyCode == 13) // enter
		{
			BX.PreventDefault(e);
			BX.SocNetLogDestination.selectFirstSearchItem(window['BXSocNetLogDestinationFormNameMent' + formID]);
		}
		else
		{
			setTimeout(function(){
				r = pEditor.GetSelectionRange();

				win = pEditor.pEditorWindow;
				if(win.document.selection) // IE8 and below
				{
					r = BXfixIERangeObject(r, win);
					if(r.endContainer)
						txt = r.endContainer.nodeValue;
				}
				else
				{
					txt = r.endContainer.textContent;
				}
				if(txt !== null)
				{
					if(bPlus)
						txtPos = txt.lastIndexOf("+", r.endOffset)+1;
					else
						txtPos = txt.lastIndexOf("@", r.endOffset)+1;
					txt2 = txt.substr(txtPos, (r.endOffset - txtPos));

					if(txt2.length == 1 && BX.util.trim(txt2).length == 0)
					{
						window['BXfpdStopMent' + formID]();
					}
					else
					{
						window["mentionText"] = txt2;
						BX.SocNetLogDestination.search(txt2, true, window['BXSocNetLogDestinationFormNameMent' + formID], BX.message("MPF_NAME_TEMPLATE"));
						if(BX.util.trim(txt2).length == 0)
						{
							window['BXfpdStopMent' + formID]();
							window['bMentListen'] = true;
							BX.SocNetLogDestination.openDialog(window['BXSocNetLogDestinationFormNameMent' + formID]);
						}
					}
				}
			}, 10);
		}
	}
}

window.BXfixIERangeObject = function(range, win) //Only for IE8 and below.
{
	win = win || window;

	if(!range)
		return null;
	if(!range.startContainer && win.document.selection) //IE8 and below
	{
		var _findTextNode = function(parentElement,text)
		{
			var container=null,
				offset=-1;
			for(var node = parentElement.firstChild; node; node = node.nextSibling)
			{
				if(node.nodeType == 3)
				{
					var find = node.nodeValue,
						pos = text.indexOf(find);
					if(pos == 0 && text != find)
					{
						text = text.substring(find.length);
					}
					else
					{
						container = node;
						offset = text.length-1;
						break;
					}
				}
			}
			return {node: container, offset: offset};
		}

		var rangeCopy1 = range.duplicate(),
			rangeCopy2 = range.duplicate(),
			rangeObj1 = range.duplicate(),
			rangeObj2 = range.duplicate();

		rangeCopy1.collapse(true);
		rangeCopy1.moveEnd('character', 1);
		rangeCopy2.collapse(false);
		rangeCopy2.moveStart('character', -1);

		var parentElement1 = rangeCopy1.parentElement(),
			parentElement2 = rangeCopy2.parentElement();

		rangeObj1.moveToElementText(parentElement1);
		rangeObj1.setEndPoint('EndToEnd', rangeCopy1);
		rangeObj2.moveToElementText(parentElement2);
		rangeObj2.setEndPoint('EndToEnd', rangeCopy2);

		var text1 = rangeObj1.text,
			text2 = rangeObj2.text,
			nodeInfo1 = _findTextNode(parentElement1, text1),
			nodeInfo2 = _findTextNode(parentElement2, text2);

		range.startContainer = nodeInfo1.node;
		range.startOffset = nodeInfo1.offset;
		range.endContainer = nodeInfo2.node;
		range.endOffset = nodeInfo2.offset+1;
	}
	return range;
}

window.__onKeyTags = function(event)
{
	if (!event)
		event = window.event;
	var key = (event.keyCode ? event.keyCode : (event.which ? event.which : null));
	if (key == 13)
		addTag();
}

window.BXfpdSetLinkName = function(name)
{
	if (BX.SocNetLogDestination.getSelectedCount(name) <= 0)
		BX('bx-destination-tag').innerHTML = BX.message("BX_FPD_LINK_1");
	else
		BX('bx-destination-tag').innerHTML = BX.message("BX_FPD_LINK_2");
}

window.BXfpdSelectCallback = function(item, type, search)
{
	var type1 = type;
	prefix = 'S';
	if (type == 'sonetgroups')
		prefix = 'SG';
	else if (type == 'groups')
	{
		prefix = 'UA';
		type1 = 'all-users';
	}
	else if (type == 'users')
		prefix = 'U';
	else if (type == 'department')
		prefix = 'DR';

	BX('feed-add-post-destination-item').appendChild(
		BX.create("span", { attrs : { 'data-id' : item.id }, props : { className : "feed-add-post-destination feed-add-post-destination-"+type1 }, children: [
			BX.create("input", { attrs : { 'type' : 'hidden', 'name' : 'SPERM['+prefix+'][]', 'value' : item.id }}),
			BX.create("span", { props : { 'className' : "feed-add-post-destination-text" }, html : item.name}),
			BX.create("span", { props : { 'className' : "feed-add-post-del-but"}, events : {'click' : function(e){BX.SocNetLogDestination.deleteItem(item.id, type, BXSocNetLogDestinationFormName);BX.PreventDefault(e)}, 'mouseover' : function(){BX.addClass(this.parentNode, 'feed-add-post-destination-hover')}, 'mouseout' : function(){BX.removeClass(this.parentNode, 'feed-add-post-destination-hover')}}})
		]})
	);

	BX('feed-add-post-destination-input').value = '';
	BXfpdSetLinkName(BXSocNetLogDestinationFormName);
}

// remove block
window.BXfpdUnSelectCallback = function(item, type, search)
{
	var elements = BX.findChildren(BX('feed-add-post-destination-item'), {attribute: {'data-id': ''+item.id+''}}, true);
	if (elements != null)
	{
		for (var j = 0; j < elements.length; j++)
			BX.remove(elements[j]);
	}
	BX('feed-add-post-destination-input').value = '';
	BXfpdSetLinkName(BXSocNetLogDestinationFormName);
}
window.BXfpdOpenDialogCallback = function()
{
	BX.style(BX('feed-add-post-destination-input-box'), 'display', 'inline-block');
	BX.style(BX('bx-destination-tag'), 'display', 'none');
	BX.focus(BX('feed-add-post-destination-input'));
}

window.BXfpdCloseDialogCallback = function()
{
	if (!BX.SocNetLogDestination.isOpenSearch() && BX('feed-add-post-destination-input').value.length <= 0)
	{
		BX.style(BX('feed-add-post-destination-input-box'), 'display', 'none');
		BX.style(BX('bx-destination-tag'), 'display', 'inline-block');
		BXfpdDisableBackspace();
	}
}

window.BXfpdCloseSearchCallback = function()
{
	if (!BX.SocNetLogDestination.isOpenSearch() && BX('feed-add-post-destination-input').value.length > 0)
	{
		BX.style(BX('feed-add-post-destination-input-box'), 'display', 'none');
		BX.style(BX('bx-destination-tag'), 'display', 'inline-block');
		BX('feed-add-post-destination-input').value = '';
		BXfpdDisableBackspace();
	}

}
window.BXfpdDisableBackspace = function(event)
{
	if (BX.SocNetLogDestination.backspaceDisable || BX.SocNetLogDestination.backspaceDisable != null)
		BX.unbind(window, 'keydown', BX.SocNetLogDestination.backspaceDisable);

	BX.bind(window, 'keydown', BX.SocNetLogDestination.backspaceDisable = function(event){
		if (event.keyCode == 8)
		{
			BX.PreventDefault(event);
			return false;
		}
	});
	setTimeout(function(){
		BX.unbind(window, 'keydown', BX.SocNetLogDestination.backspaceDisable);
		BX.SocNetLogDestination.backspaceDisable = null;
	}, 5000);
}

window.BXfpdSearchBefore = function(event)
{
	if (event.keyCode == 8 && BX('feed-add-post-destination-input').value.length <= 0)
	{
		BX.SocNetLogDestination.sendEvent = false;
		BX.SocNetLogDestination.deleteLastItem(BXSocNetLogDestinationFormName);
	}

	return true;
}
window.BXfpdSearch = function(event)
{
	if (event.keyCode == 16 || event.keyCode == 17 || event.keyCode == 18 || event.keyCode == 20 || event.keyCode == 244 || event.keyCode == 224 || event.keyCode == 91)
		return false;

	if (event.keyCode == 13)
	{
		BX.SocNetLogDestination.selectFirstSearchItem(BXSocNetLogDestinationFormName);
		return true;
	}
	if (event.keyCode == 27)
	{
		BX('feed-add-post-destination-input').value = '';
		BX.style(BX('bx-destination-tag'), 'display', 'inline');
	}
	else
	{
		BX.SocNetLogDestination.search(BX('feed-add-post-destination-input').value, true, BXSocNetLogDestinationFormName);
	}

	if (!BX.SocNetLogDestination.isOpenDialog() && BX('feed-add-post-destination-input').value.length <= 0)
	{
		BX.SocNetLogDestination.openDialog(BXSocNetLogDestinationFormName);
	}
	else
	{
		if (BX.SocNetLogDestination.sendEvent && BX.SocNetLogDestination.isOpenDialog())
			BX.SocNetLogDestination.closeDialog();
	}
	if (event.keyCode == 8)
	{
		BX.SocNetLogDestination.sendEvent = true;
	}
	return true;
}

window.__LHE_OnBeforeParsersInit = function(pEditor)
{
	if (!(window[pEditor.id + 'Settings'] && window[pEditor.id + 'Settings']['parsers']))
		return false;

	pEditor.formID = window[pEditor.id + 'Settings']['formID'];
	var parsers = window[pEditor.id + 'Settings']['parsers'];

	if (BX.util.in_array('postimage', parsers))
	{
		pEditor.AddParser(
			{
				name: 'postimage',
				obj: {
					Parse: function(sName, sContent, pLEditor)
					{
						sContent = sContent.replace(
							/\[IMG ID=((?:\s|\S)*?)(?:\s*?WIDTH=(\d+)\s*?HEIGHT=(\d+))?\]/ig,
							function(str, id, width, height)
							{
								var classID = 'PlEditor' + pLEditor.formID;
								var res = "";
								if (window[classID]['checkFile'](id))
								{
									width = parseInt(width); height = parseInt(height);
									var strSize = ((width && height && pLEditor.bBBParseImageSize) ?
										(" width=\"" + width + "\" height=\"" + height + "\"") : "");
									return window[classID].Image.html.
										replace("\#FILE_ID\#", pLEditor.SetBxTag(false, {tag: "postimage", params: {value : id}})).
										replace("\#SRC\#",  window[classID].arFiles[id]['src']).
										replace("\#ADDITIONAL\#", strSize);
								}
								return str;
							}
						);
						return sContent;
					},
					UnParse: function(bxTag, pNode, pLEditor)
					{
						var
							classID = 'PlEditor' + pLEditor.formID,
							res = "";
						if (bxTag.tag == 'postimage')
						{
							var
								width = parseInt(pNode.arAttributes['width']),
								height = parseInt(pNode.arAttributes['height']),
								strSize = "";

							if (width && height  && pLEditor.bBBParseImageSize)
								strSize = ' WIDTH=' + width + ' HEIGHT=' + height;

							res = window[classID].Image.code.
								replace("\#FILE_ID\#", bxTag.params.value).
								replace("\#ADDITIONAL\#", strSize);

						}
						return res;
					}
				}
			}
		);
	}
	if (BX.util.in_array('postfile', parsers))
	{
		pEditor.AddParser(
			{
				name: 'postfile',
				obj: {
					Parse: function(sName, sContent, pLEditor)
					{
						sContent = sContent.replace(
							/\[FILE ID=((?:\s|\S)*?)(?:\s*?WIDTH=(\d+)\s*?HEIGHT=(\d+))?\]/ig,
							function(str, id, width, height)
							{
								var
									edId = 'PlEditor' + pLEditor.formID,
									res = "",
									strAdditional = "",
									template = window[edId].File.html;

								if (window[edId].checkFile(id))
								{
									if (window[edId].arFiles[id]['isImage'])
									{
										width = parseInt(width); height = parseInt(height);
										strAdditional = ((width && height && pLEditor.bBBParseImageSize) ?
											(" width=\"" + width + "\" height=\"" + height + "\"") : "");
										template = window[edId].Image.html;
									}

									return template.
										replace("\#FILE_ID\#", pLEditor.SetBxTag(false, {tag: "postfile", params: {value : id}})).
										replace("\#FILE_NAME\#", window[edId].arFiles[id]['name']).
										replace("\#SRC\#",  window[edId].arFiles[id]['src']).
										replace("\#ADDITIONAL\#", strAdditional);
								}
								return str;
							}
						);
						return sContent;
					},
					UnParse: function(bxTag, pNode, pLEditor)
					{
						var
							edId = 'PlEditor' + pLEditor.formID,
							res = "";
						if (bxTag.tag == 'postfile')
						{
							var
								width = parseInt(pNode.arAttributes['width']),
								height = parseInt(pNode.arAttributes['height']),
								strSize = "";

							if (width && height  && pLEditor.bBBParseImageSize)
								strSize = ' WIDTH=' + width + ' HEIGHT=' + height;

							res = window[edId].File.code.
								replace("\#FILE_ID\#", bxTag.params.value).
								replace("\#ADDITIONAL\#", strSize);
						}
						return res;
					}
				}
			}
		);
	}
	if (BX.util.in_array('postuser', parsers))
	{
		pEditor.AddParser(
			{
				name: 'postuser',
				obj: {
					Parse: function(sName, sContent, pLEditor)
					{
						sContent = sContent.replace(/\[USER\s*=\s*(\d+)\]((?:\s|\S)*?)\[\/USER\]/ig, function(str, id, name)
						{
							var
								id = parseInt(id),
								name = BX.util.trim(name);

							return '<span id="' + pLEditor.SetBxTag(false, {tag: "postuser", params: {value : id}}) +
								'" style="color: #2067B0; border-bottom: 1px dashed #2067B0;">' + name + '</span>';
						});
						return sContent;
					},
					UnParse: function(bxTag, pNode, pLEditor)
					{
						if (bxTag.tag == 'postuser')
						{
							var name = '';
							for (var i = 0; i < pNode.arNodes.length; i++)
								name += pLEditor._RecursiveGetHTML(pNode.arNodes[i]);
							name = BX.util.trim(name);
							return "[USER=" + bxTag.params.value + "]" + name +"[/USER]";
						}
						return "";
					}
				}
			}
		);
	}
	window[pEditor.id + 'Settings']['parsers'] = false;
}
window.__LHE_OnInit = function(pEditor)
{
	if (!window[pEditor.id + 'Settings'])
		return false;
	pEditor.arFiles = window[pEditor.id + 'Settings']['arFiles'];
	pEditor.arFiles = (!!pEditor.arFiles && pEditor.arFiles.length > 0 ? pEditor.arFiles : []);

	var
		formID = window[pEditor.id + 'Settings']['formID'],
		objName = window[pEditor.id + 'Settings']['objName'];

	if(!/MSIE 8/.test(navigator.userAgent))
	{
		BX.addCustomEvent(
			pEditor,
			'OnDocumentKeyDown',
			function(e){bxPFParser(e, pEditor, formID);});
	}

	if (BX.util.in_array("CreateLink", window[pEditor.id + 'Settings']['buttons']))
		window[objName]['makeButton']('lhe_btn_createlink', 'bx-b-link');

	if (BX.util.in_array("InputVideo", window[pEditor.id + 'Settings']['buttons']))
		window[objName]['makeButton']('lhe_btn_inputvideo', 'bx-b-video');

	if (BX.util.in_array("Quote", window[pEditor.id + 'Settings']['buttons']))
		window[objName]['makeButton']('lhe_btn_quote', 'bx-b-quote');

	if (BX.util.in_array("SmileListHide", window[pEditor.id + 'Settings']['buttons']))
	{
		if(el = BX.findChild(BX(formID), {'attr': {id: 'lhe_btn_smilelist'}}, true, false))
			BX.remove(BX.findParent(el), true);
	}

	var el = BX.findChild(BX(formID), {'attr': {id: 'bx-panel-close'}}, true, false);
	if (el)
	{
		BX.findChild(BX(formID), {'className': /lhe-stat-toolbar-cont/ }, true, false).appendChild(el);
	}
	window[objName].showPanelEditor(window[pEditor.id + 'Settings']['showEditor'], pEditor);
	window[pEditor.id + 'Settings'] = false;
}

window.CustomizeLightEditor = function(editorId, params)
{
	// Rename image button and change Icon
	LHEButtons['Image'].id = 'ImageLink';
	LHEButtons['Image'].src = params.path + '/images/lhelink_image.gif';
	LHEButtons['Image'].name = params.imageLinkText;

	if (!(window[editorId + 'Settings'] && window[editorId + 'Settings']['parsers']))
		return false;

	LHEButtons['Spoiler'] = {
		id : 'Spoiler',
		name : params.spoilerText,
		src : params.path + '/images/lhespoiler.png',
		OnBeforeCreate: function(pLEditor, pBut)
		{
			// Disable in non BBCode mode in html
			pBut.disableOnCodeView = !pLEditor.bBBCode || pLEditor.arConfig.bConvertContentFromBBCodes;

			pLEditor.systemCSS += "blockquote.bx-spoiler {border: 1px solid #C0C0C0!important; background: #fff4ca url(" + params.path + "/images/spoiler.png) 4px 4px no-repeat; padding: 4px 4px 4px 24px; color: #373737!important;}\n";
			return pBut;
		},
		handler: function(pBut)
		{
			if (pBut.pLEditor.arConfig.bQuoteFromSelection)
			{
				if (document.selection && document.selection.createRange)
					res = document.selection.createRange().text;
				else if (window.getSelection)
					res = window.getSelection().toString();
				res = res.replace(/\n/g, '<br />');

				var strId = '';
				if (!pBut.pLEditor.bBBCode)
					strId = " id\"=" + pBut.pLEditor.SetBxTag(false, {tag: "spoiler"}) + "\"";

				if (res && res.length > 0)
					return pBut.pLEditor.InsertHTML('<blockquote class="bx-spoiler"' + strId + ">" + res + "</blockquote><br/>");
			}

			// Catch all blockquotes
			var
				arBQ = pBut.pLEditor.pEditorDocument.getElementsByTagName("blockquote"),
				i, l = arBQ.length;

			// Set specific name to nodes
			for (i = 0; i < l; i++)
				arBQ[i].name = "__bx_temp_spoiler";

			// Create new qoute
			pBut.pLEditor.executeCommand('Indent');

			// Search for created node and try to adjust new style end id
			setTimeout(function(){
				var
					arNewBQ = pBut.pLEditor.pEditorDocument.getElementsByTagName("blockquote"),
					i, l = arNewBQ.length;

				for (i = 0; i < l; i++)
				{
					if (arBQ[i].name == "__bx_temp_spoiler")
					{
						arBQ[i].removeAttribute("name");
					}
					else
					{
						arBQ[i].className = "bx-spoiler";
						arBQ[i].id = pBut.pLEditor.SetBxTag(false, {tag: "spoiler"});
					}
					try{arBQ[i].setAttribute("style", '');}catch(e){}

					if (!arBQ[i].nextSibling)
						arBQ[i].parentNode.appendChild(BX.create("BR", {}, pBut.pLEditor.pEditorDocument));

					if (arBQ[i].previousSibling && arBQ[i].previousSibling.nodeName && arBQ[i].previousSibling.nodeName.toLowerCase() == 'blockquote')
						arBQ[i].parentNode.insertBefore(BX.create("BR", {}, pBut.pLEditor.pEditorDocument), arBQ[i]);
				}
			}, 10);
		},
		bbHandler: function(pBut)
		{
			if (pBut.pLEditor.arConfig.bQuoteFromSelection)
			{
				if (document.selection && document.selection.createRange)
					res = document.selection.createRange().text;
				else if (window.getSelection)
					res = window.getSelection().toString();

				if (res && res.length > 0)
					return pBut.pLEditor.WrapWith('[SPOILER]', '[/SPOILER]', res);
			}

			pBut.pLEditor.FormatBB({tag: 'SPOILER', pBut: pBut});
		},
		parser: {
			name: 'spoiler',
			obj: {
				Parse: function(sName, sContent, pLEditor)
				{
					if (/\[(cut|spoiler)(([^\]])*)\]/gi.test(sContent))
					{
						sContent = sContent.
							replace(/[\001-\006]/gi, '').
							replace(/\[cut(((?:\=)[^\]]*)|)\]/gi, '\001$1\001').
							replace(/\[\/cut]/gi, '\002').
							replace(/\[spoiler([^\]]*)\]/gi, '\003$1\003').
							replace(/\[\/spoiler]/gi, '\004');
						var
							reg1 = /(?:\001([^\001]*)\001)([^\001-\004]+)\002/gi,
							reg2 = /(?:\003([^\003]*)\003)([^\001-\004]+)\004/gi,
							__replace_reg = function(title, body){
								title = title.replace(/^(\=\"|\=\'|\=)/gi, '').replace(/(\"|\')?$/gi, '');
								return '<blockquote class="bx-spoiler" id="' + pLEditor.SetBxTag(false, {tag: "spoiler"}) + '" title="' + title + '">' + body + '</blockquote>';
							};
						while (sContent.match(reg1) || sContent.match(reg2))
						{
							sContent = sContent.
								replace(reg1, function(str, title, body){return __replace_reg(title, body);}).
								replace(reg2, function(str, title, body){return __replace_reg(title, body);});
						}
					}
					sContent = sContent.
						replace(/\001([^\001]*)\001/gi, '[cut$1]').
						replace(/\003([^\003]*)\003/gi, '[spoiler$1]').
						replace(/\002/gi, '[/cut]').
						replace(/\004/gi, '[/spoiler]');
					return sContent;
				},
				UnParse: function(bxTag, pNode, pLEditor)
				{
					if (bxTag.tag == 'spoiler')
					{
						var i, l = pNode.arNodes.length, res = "[SPOILER" + (pNode.arAttributes["title"] ? "="+pNode.arAttributes["title"] : "") + "]";
						for (i = 0; i < l; i++)
							res += pLEditor._RecursiveGetHTML(pNode.arNodes[i]);
						res += "[/SPOILER]";
						return res;
					}
					return "";
				}
			}
		}
	};
	LHEButtons['InputVideo'] = {
		id : 'InputVideo',
		src : params.path + '/images/lhevideo.gif',
		name : params.videoText,
		handler: function(pBut)
		{
			pBut.pLEditor.OpenDialog({id : 'InputVideo', obj: false});
		},
		OnBeforeCreate: function(pLEditor, pBut)
		{
			// Disable in non BBCode mode in html
			pBut.disableOnCodeView = !pLEditor.bBBCode || pLEditor.arConfig.bConvertContentFromBBCodes;
			return pBut;
		},
		parser: {
			name: 'postvideo',
			obj: {
				Parse: function(sName, sContent, pLEditor)
				{
					sContent = sContent.replace(/\[VIDEO\s*?width=(\d+)\s*?height=(\d+)\s*\]((?:\s|\S)*?)\[\/VIDEO\]/ig, function(str, w, h, src)
					{
						var
							w = parseInt(w) || 400,
							h = parseInt(h) || 300,
							src = BX.util.trim(src);

						return '<img id="' + pLEditor.SetBxTag(false, {tag: "postvideo", params: {value : src}}) +
							'" src="/bitrix/images/1.gif" class="bxed-video" width=' + w + ' height=' + h + ' title="' + LHE_MESS.Video + ": " + src + '" />';
					});
					return sContent;
				},
				UnParse: function(bxTag, pNode, pLEditor)
				{
					if (bxTag.tag == 'postvideo')
					{
						return "[VIDEO WIDTH=" + pNode.arAttributes["width"] + " HEIGHT=" + pNode.arAttributes["height"] + "]" + bxTag.params.value + "[/VIDEO]";
					}
					return "";
				}
			}
		}
	};

	window.LHEDailogs['InputVideo'] = function(pObj)
	{
		var str = '<table width="100%"><tr>' +
			'<td class="lhe-dialog-label lhe-label-imp"><label for="' + pObj.pLEditor.id + 'lhed_post_video_path"><b>' + params.videoUploadText + ':</b></label></td>' +
			'<td class="lhe-dialog-param">' +
			'<input id="' + pObj.pLEditor.id + 'lhed_post_video_path" value="" size="30"/>' +
			'</td>' +
			'</tr><tr>' +
			'<td></td>' +
			'<td style="padding: 0!important; font-size: 11px!important;">' + params.videoUploadText1 + '</td>' +
			'</tr><tr>' +
			'<td class="lhe-dialog-label lhe-label-imp"><label for="' + pObj.pLEditor.id + 'lhed_post_video_width">' + params.videoUploadText3 + ':</label></td>' +
			'<td class="lhe-dialog-param">' +
			'<input id="' + pObj.pLEditor.id + 'lhed_post_video_width" value="" size="4"/>' +
			' x ' +
			'<input id="' + pObj.pLEditor.id + 'lhed_post_video_height" value="" size="4" />' +
			'</td>' +
			'</tr></table>';

		return {
			title: params.videoUploadText2,
			innerHTML : str,
			width: 480,
			OnLoad: function()
			{
				pObj.pPath = BX(pObj.pLEditor.id + "lhed_post_video_path");
				pObj.pWidth = BX(pObj.pLEditor.id + "lhed_post_video_width");
				pObj.pHeight = BX(pObj.pLEditor.id + "lhed_post_video_height");

				pObj.pLEditor.focus(pObj.pPath);
			},
			OnSave: function()
			{
				var
					src = BX.util.trim(pObj.pPath.value),
					w = parseInt(pObj.pWidth.value) || 400,
					h = parseInt(pObj.pHeight.value) || 300;

				if (src == "")
					return;

				if (pObj.pLEditor.sEditorMode == 'code' && pObj.pLEditor.bBBCode) // BB Codes
				{
					pObj.pLEditor.WrapWith("", "", "[VIDEO WIDTH=" + w + " HEIGHT=" + h + "]" + src + "[/VIDEO]");
				}
				else if(pObj.pLEditor.sEditorMode == 'html') // WYSIWYG
				{
					pObj.pLEditor.InsertHTML('<img id="' + pObj.pLEditor.SetBxTag(false, {tag: "postvideo", params: {value : src}}) + '" src="/bitrix/images/1.gif" class="bxed-video" width=' + w + ' height=' + h + ' title="' + LHE_MESS.Video + ": " + src + '" />');
					pObj.pLEditor.AutoResize();
				}
			}
		};
	};
}

	var lastWaitElement = null;
	window.MPFbuttonShowWait = function(el)
	{
		if (el && !BX.type.isElementNode(el))
			el = null;
		el = el || this;

		if (BX.type.isElementNode(el)

			)
		{
			BX.defer(function(){el.disabled = true})();

			var
				waiter_parent = BX.findParent(el, BX.is_relative),
				pos = BX.pos(el, !!waiter_parent);

			el.bxwaiter = (waiter_parent || document.body).appendChild(BX.create('DIV', {
				props: {className: 'mpf-load-img'},
				style: {
					top: parseInt((pos.bottom + pos.top)/2 - 9) + 'px',
					left: parseInt((pos.right + pos.left)/2 - 9) + 'px'
				}
			}));


			BX.addClass(el, 'mpf-load');
			BX.hide(el.nextSibling);
			BX.hide(el.previousSibling);

			lastWaitElement = el;

			return el.bxwaiter;
		}
	}

	window.MPFbuttonCloseWait = function(el)
	{
		if (el && !BX.type.isElementNode(el))
			el = null;
		el = el || lastWaitElement || this;

		if (BX.type.isElementNode(el))
		{
			if (el.bxwaiter && el.bxwaiter.parentNode)
			{
				el.bxwaiter.parentNode.removeChild(el.bxwaiter);
				el.bxwaiter = null;
			}

			el.disabled = false;
			BX.removeClass(el, 'mpf-load');
			BX.show(el.nextSibling);
			BX.show(el.previousSibling);

			if (lastWaitElement == el)
				lastWaitElement = null;
		}
	}
})(window);