function show_special()
{
	o = document.getElementById('special_perms');
	if (document.getElementById('blog_perms_1').checked==true)
		o.style.display='block';
	else
		o.style.display='none';
}

function changeDate()
{
	document.getElementById('date-publ').style.display = 'block';
	document.getElementById('date-publ-text').style.display = 'none';
	document.getElementById('DATE_PUBLISH_DEF').value = '';
}

BlogPostAutoSaveIcon = function () {
	var formId = 'POST_BLOG_FORM';
	var form = BX(formId);
	if (!form) return;
	
	auto_lnk = BX('post-form-autosave-icon');
	formHeaders = BX.findChild(form, {'className': /lhe-stat-toolbar-cont/ }, true, true);
	if (formHeaders.length < 1)
		return false;
	formHeader = formHeaders[formHeaders.length-1];
	formHeader.insertBefore(auto_lnk, formHeader.children[0]);
}

BlogPostAutoSave = function () {
	var formId = 'POST_BLOG_FORM';
	var form = BX(formId);
	if (!form) return;

	var controlID = "POST_MESSAGE_HTML";
	var titleID = 'POST_TITLE';
	var sonetID = 'SONETGROUP';
	title = BX(titleID);
	tags = BX(formId).TAGS;
	sonetgroup = BX(sonetID);
	
	var	iconClass = "blogPostAutoSave";
	var	actionClass = "blogPostAutoRestore";
	var	actionText = BX.message('AUTOSAVE_R');
	var recoverMessage = BX.message('BLOG_POST_AUTOSAVE');
	var recoverNotify = null;
	
	var auto_lnk = BX.create('A', {
		'attr': {'href': 'javascript:void(0)'},
		'props': {
			'className': iconClass+' bx-core-autosave bx-core-autosave-ready',
			'title': BX.message('AUTOSAVE_T'),
			'id': 'post-form-autosave-icon'
		}
	});
	
	BX('blog-post-autosave-hidden').appendChild(auto_lnk);
	
	var bindLHEEvents = function(_ob)
	{
		if (window.oBlogLHE)
		{
			window.oBlogLHE.fAutosave = _ob;
			BX.bind(window.oBlogLHE.pEditorDocument, 'keydown', BX.proxy(_ob.Init, _ob));
			BX.bind(window.oBlogLHE.pTextarea, 'keydown', BX.proxy(_ob.Init, _ob));
			BX.bind(title, 'keydown', BX.proxy(_ob.Init, _ob));
			BX.bind(tags, 'keydown', BX.proxy(_ob.Init, _ob));
		}
	}

	BX.addCustomEvent(form, 'onAutoSavePrepare', function (ob, h) {
		ob.DISABLE_STANDARD_NOTIFY = true;
		BX.bind(auto_lnk, 'click', BX.proxy(ob.Save, ob));
		_ob=ob;
		setTimeout(function() { bindLHEEvents(_ob) },1500);
	});

	BX.addCustomEvent(form, 'onAutoSave', function(ob, form_data) {
		BX.removeClass(auto_lnk,'bx-core-autosave-edited');
		BX.removeClass(auto_lnk,'bx-core-autosave-ready');
		BX.addClass(auto_lnk,'bx-core-autosave-saving');

		if (! window.oBlogLHE) return;

		form_data[controlID+'_type'] = window.oBlogLHE.sEditorMode;
		var text = "";
		if (window.oBlogLHE.sEditorMode == 'code')
			text = window.oBlogLHE.GetCodeEditorContent();
		else
			text = window.oBlogLHE.GetEditorContent();
		form_data[controlID] = text;
		form_data[titleID] = BX(titleID).value;
		form_data[tags] = BX(formId).TAGS.value;
		if(BX(sonetID))
			form_data[sonetID] = BX(sonetID).value;
	});

	BX.addCustomEvent(form, 'onAutoSaveFinished', function(ob, t) {
		t = parseInt(t);
		if (!isNaN(t))
		{
			setTimeout(function() {
				BX.removeClass(auto_lnk,'bx-core-autosave-saving');
				BX.addClass(auto_lnk,'bx-core-autosave-ready');
			}, 1000);
			auto_lnk.title = BX.message('AUTOSAVE_L').replace('#DATE#', BX.formatDate(new Date(t * 1000)));
		}
	});

	BX.addCustomEvent(form, 'onAutoSaveInit', function() {
		BX.removeClass(auto_lnk,'bx-core-autosave-ready');
		BX.addClass(auto_lnk,'bx-core-autosave-edited');
	});

	BX.addCustomEvent(form, 'onAutoSaveRestoreFound', function(ob, data) {
		if (BX.util.trim(data[controlID]).length < 1 && BX.util.trim(data[titleID]).length < 1) return;
		_ob = ob;
		
		formHeaders = BX.findChild(form, {'className': /blog-post-edit/ }, true, true);
		var w = "100%";
		if(formHeaders.length > 0)
			w = formHeaders[0].clientWidth + 'px';

		var id = form.name || Math.random();
		recoverNotify = BX.create('DIV', {
			'props': {
				'className': 'blog-notify-bar',
				'id' : 'post-form-autosave-not'
			},
			'children': [
				BX.create('DIV', {
					'props': { 'className': 'blog-notify-close' },
					'children': [
						BX.create('A', {
							'events':{
								'click': function() {
									if (!! recoverNotify)
										BX.remove(recoverNotify);
									return false;
								}
							}
						})
					]
				}),
				BX.create('DIV', {
					'props': { 'className': 'blog-notify-text' },
					'children': [
						BX.create('SPAN', { 'text': recoverMessage }),
						BX.create('A', {
							'attr': {'href': 'javascript:void(0)'},
							'props': {'className': actionClass},
							'text': actionText,
							'events':{
								'click': function() { _ob.Restore(); return false;}
							}
						})
					]
				})
			],
			'style': {'width':w}
		});
		
		form.insertBefore(recoverNotify, form.children[1]);
	});

	BX.addCustomEvent(form, 'onAutoSaveRestore', function(ob, data) {
		if (!window.oBlogLHE || !data[controlID]) return;

		window.oBlogLHE.SetView(data[controlID+'_type']);

		if (!!window.oBlogLHE.sourseBut)
			window.oBlogLHE.sourseBut.Check((data[controlID+'_type'] == 'code'));
		if (data[controlID+'_type'] == 'code')
			window.oBlogLHE.SetContent(data[controlID]);
		else
			window.oBlogLHE.SetEditorContent(data[controlID]);
		BX(titleID).value = data[titleID];
		BX(formId).TAGS.value = data[tags];
		
		if(BX(sonetID))
		{
			BX(sonetID).value = data[sonetID];
			
			if(groupsPopup)
			{
				for(var i = 0, count = groupsPopup.myGroups.length; i < count; i++)
				{
					if(groupsPopup.myGroups[i].id == data[sonetID])
						onGroupBlogSelect([groupsPopup.myGroups[i]]);
				}
			}
		}
		
		bindLHEEvents(ob);
	});

	BX.addCustomEvent(form, 'onAutoSaveRestoreFinished', function(ob, data) {
		if (!! recoverNotify)
			BX.remove(recoverNotify);
	});
}

if(window.BX)
{
	BX.ready(function() {
		BX.bind(BX.findChild(BX("blog-post-group-selector"), {tag: "span", className: "blog-post-group-value"}), "click", function(e) {
					if(!e) e = window.event;
					groupsPopup.show();
					BX.PreventDefault(e);
				});
		});
}
function onGroupBlogSelect(groups)
{
	if (groups[0])
	{
		BX.adjust(BX.findChild(BX("blog-post-group-selector"), {tag: "span", className: "blog-post-group-value"}), {
			text: groups[0].title
		});
	
		var deleteIcon = BX.findChild(BX("blog-post-group-selector"), {tag: "span", className: "blog-post-group-delete"});
		if (deleteIcon)
		{
			BX.adjust(deleteIcon, {
				events: {
					click: function(e) {
						if (!e) e = window.event;
						deleteGroup(groups[0].id);
					}
				}
			})
		}
		else
		{
			BX("blog-post-group-selector").appendChild(
				BX.create("span", {
					props: {className: "blog-post-group-delete"},
					events: {
						click: function(e)
						{
							if (!e) e = window.event;
							deleteGroup(groups[0].id);
						}
					}
				})
			);
		}
		
		var input = BX.findNextSibling(BX("blog-post-group-selector"), {tag: "input", name: "SONETGROUP"});
		if (input)
		{
			BX.adjust(input, {props: {value: groups[0].id}})
		}
		else
		{
			BX("blog-post-group-selector").parentNode.appendChild(
				BX.create("input", {
					props: {
						name: "SONETGROUP",
						type: "hidden",
						value: groups[0].id
					}
				})
			);
		}
	}
}

function deleteGroup(groupId)
{
	BX.adjust(BX.findChild(BX("blog-post-group-selector"), {tag: "span", className: "blog-post-group-value"}), {
		text: BX.message("SONET_GROUP_BLOG_NO")
	});
	var deleteIcon = BX.findChild(BX("blog-post-group-selector"), {tag: "span", className: "blog-post-group-delete"});
	if (deleteIcon)
	{
		BX.cleanNode(deleteIcon, true);
	}
	var input = BX.findNextSibling(BX("blog-post-group-selector"), {tag: "input", name: "SONETGROUP"});
	if (input)
	{
		input.value = 0;
	}
	groupsPopup.deselect(groupId);
}
