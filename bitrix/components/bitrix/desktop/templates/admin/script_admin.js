function __ShowDesktopSettingsDialog(e)
{
	if(!e)
		e = window.event;

		(new BX.CAdminDialog({
			'title': BX.message('langGDSettingsDialogTitle'),
			'content_url': '/bitrix/components/bitrix/desktop/admin_settings.php?lang='+language_id+'&bxpublic=Y', 
			'content_post': 'sessid='+bxsessid+'&type=desktop&desktop_page='+desktopPage,
			'draggable': true,
			'resizable': true,
			'buttons': [BX.CAdminDialog.btnSave, BX.CAdminDialog.btnCancel]
		})).Show();
}

function __ShowDesktopAllSettingsDialog(e)
{
	if(!e)
		e = window.event;

		(new BX.CDialog({
			'title': BX.message('langGDSettingsAllDialogTitle'),
			'content_url': '/bitrix/components/bitrix/desktop/admin_settings_all.php?lang='+language_id+'&bxpublic=Y', 
			'content_post': 'sessid='+bxsessid+'&desktop_backurl='+desktopBackurl,
			'draggable': true,
			'resizable': true,
			'buttons': [BX.CDialog.btnSave, BX.CDialog.btnCancel]
		})).Show();
}

function __ShowDesktopAddDialog(e)
{
	if(!e)
		e = window.event;

		(new BX.CAdminDialog({
			'title': BX.message('langGDSettingsDialogTitle'),
			'content_url': '/bitrix/components/bitrix/desktop/admin_settings.php?lang='+language_id+'&bxpublic=Y', 
			'content_post': 'sessid='+bxsessid+'&type=desktop&desktop_page='+desktopPage+'&action=new&desktop_backurl='+desktopBackurl,
			'draggable': true,
			'resizable': true,
			'buttons': [BX.CAdminDialog.btnSave, BX.CAdminDialog.btnCancel]
		})).Show();
}

function __RecalcDesktopSettingsDialog(e)
{
	if(!e)
		e = window.event;

	col_count = this.value;
	if (e.type == 'blur' && col_count.length <= 0)
	{
		col_count = current_col_count;
		BX('SETTINGS_COLUMNS').value = col_count;
	}
	else if (e.type == 'keyup' && (parseInt(col_count) <= 0	|| parseInt(col_count) >= 10))
	{
		current_col_count = col_count = 2;
		BX('SETTINGS_COLUMNS').value = col_count;
	}
	else if (e.type == 'keyup' && col_count.length > 0)
		current_col_count = col_count;

	var tableNode = BX.findParent(this, {'tag':'tbody'})

	var arItems = BX.findChildren(tableNode, {'tag':'tr', 'class':'bx-gd-admin-settings-col'}, true);
	if (!arItems)
		arItems = [];

	for (var i = 0; i < arItems.length; i++)
	{
		if (i >= col_count)
			arItems[i].parentNode.removeChild(arItems[i]);
	}

	var col_add = col_count - i;

	for (var i = 0; i < col_add; i++)
	{
		tableNode.appendChild(BX.create('tr', {
			props: {
				'className': 'bx-gd-admin-settings-col'
			},
			children: [
				BX.create('td', {
					attrs: {
						'width': '40%'
					},
					html: BX.message('langGDSettingsDialogRowTitle') + (parseInt(arItems.length) + parseInt(i) + 1)
				}),
				BX.create('td', {
					attrs: {
						'width': '60%'
					},
					children: [
						BX.create('input', {
							attrs: {
								'type': 'text',
								'size': '5',
								'maxlength': '6'
							},
							props: {
								'id': 'SETTINGS_COLUMN_WIDTH_' + (arItems.length + i),
								'name': 'SETTINGS_COLUMN_WIDTH_' + (arItems.length + i),
								'value': ''
							}
						})
					]
				})
			]
		}));
	}
}

var allAdminGagdgetHolders = [];
function getAdminGadgetHolder(id)
{
	return allAdminGagdgetHolders[id];
}

BX.AdminGadget = function(gadgetHolderID, allGadgets, settingsMenuItems)
{
	BX.AdminGadget.superclass.constructor.apply(this, arguments);
	this.settingsMenuItems = settingsMenuItems;
	allAdminGagdgetHolders[this.gadgetHolderID] = this;
}

BX.extend(BX.AdminGadget, BXGadget);

BX.AdminGadget.prototype.ShowSettings = function(id, title)
{
	(new BX.CAdminDialog({
		'title': title,
		'content_url': '/bitrix/components/bitrix/desktop/admin_settings.php?lang='+language_id+'&bxpublic=Y', 
		'content_post': 'sessid='+bxsessid+'&type=gadget&gd_ajax='+this.gadgetHolderID+'&gid='+id+'&desktop_page='+desktopPage,
		'draggable': true,
		'resizable': true
	})).Show();
	
	return false;
}

BX.AdminGadget.prototype.ShowSettingsMenu  = function(a)
{
	this.menu = new PopupMenu('settings_float_menu');
	this.menu.Create(110);

	if(this.menu.IsVisible())
		return;

	this.menu.SetItems(this.settingsMenuItems);
	this.menu.BuildItems();

	var pos = jsUtils.GetRealPos(a);
	pos["bottom"]+=1;

	this.menu.PopupShow(pos);
}


gdTabControl = function(id)
{
	this.id = id;
	this.aTabs = BX.findChildren(BX(this.id), {'tag':'span', 'class':'bx-gadgets-tab-wrap'}, true);
}

gdTabControl.prototype.SelectTab = function(tab)
{
	var content_div = BX(tab.id+'_content');
	if(!content_div || content_div.style.display != 'none')
		return;

	var t = false;
	for (var i = 0, cnt = this.aTabs.length; i < cnt; i++)
	{
		t = BX(this.aTabs[i]);
		BX.removeClass(t, 'bx-gadgets-tab-active');			
		if(t.style.display != 'none')
			BX(t.id+'_content').style.display = 'none';
	}

	content_div.style.display = 'block';
	BX.addClass(tab, 'bx-gadgets-tab-active');		
}

gdTabControl.prototype.LoadTab = function(tab, url)
{
	var content_div = BX(tab.id+'_content');
	if(!content_div || content_div.style.display != 'none')
		return;
	
	var node_div = BX(tab.id+'_content_node');
	if(node_div && node_div.innerHTML.length <= 0)
	{
		BX.ajax.get(url, function(result)
		{
			BX.closeWait();
			node_div.innerHTML = result;
			window.arTabsLoaded[tab] = true;
		})
		BX.showWait();
	}
	
	var t = false;
	for (var i = 0, cnt = this.aTabs.length; i < cnt; i++)
	{
		t = BX(this.aTabs[i]);
		BX.removeClass(t, 'bx-gadgets-tab-active');			
		if(t.style.display != 'none')
			BX(t.id+'_content').style.display = 'none';
	}

	content_div.style.display = 'block';
	BX.addClass(tab, 'bx-gadgets-tab-active');
	
}