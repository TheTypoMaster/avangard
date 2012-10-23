function SubTabControl(name, unique_name, aTabs, url_link, post_params)
{
	SubTabControl.superclass.constructor.apply(this,[ name, unique_name, aTabs]);
	this.url_link = url_link;
	this.post_params = post_params;
	this.url_settings = '';
	
	this.SaveSettings = function()
	{
		BX.showWait();

		var sTabs='', s='';

		var oFieldsSelect;
		var oSelect = BX('selected_tabs');
		if(oSelect)
		{
			var k = oSelect.length;
			for(var i=0; i<k; i++)
			{
				s = oSelect[i].value + '--#--' + oSelect[i].text;
				oFieldsSelect = BX('selected_fields[' + oSelect[i].value + ']');
				if(oFieldsSelect)
				{
					var n = oFieldsSelect.length;
					for(var j=0; j<n; j++)
					{
						s += '--,--' + oFieldsSelect[j].value + '--#--' + jsUtils.trim(oFieldsSelect[j].text);
					}
				}
				sTabs += s + '--;--';
			}
		}

		var bCommon = (document.form_settings.set_default && document.form_settings.set_default.checked);

		this.CloseSettings();
		var url_link = this.url_link;
		var post_params = this.post_params;
		var request = new JCHttpRequest;
		request.Action = function () {
			BX.WindowManager.Get().AllowClose(); BX.WindowManager.Get().Close();
			BX.closeWait();
			(new BX.CAdminDialog({
			    'content_url': url_link,
			    'content_post': post_params,
				'draggable': true,
				'resizable': true,
				'buttons': [BX.CAdminDialog.btnSave, BX.CAdminDialog.btnCancel]
				})).Show();
		};

		var sParam = '';
		sParam += '&p[0][c]=form';
		sParam += '&p[0][n]='+encodeURIComponent(this.name);
		if(bCommon)
			sParam += '&p[0][d]=Y';
		sParam += '&p[0][v][tabs]=' + encodeURIComponent(sTabs);

		var options_url = '/bitrix/admin/user_options.php?lang='+phpVars.LANGUAGE_ID+'&sessid='+phpVars.bitrix_sessid;
		options_url += '&action=delete&c=form&n='+this.name+'_disabled';

		request.Post(options_url, sParam);
	};
	
	this.DeleteSettings = function(bCommon)
	{
		BX.showWait();
		this.CloseSettings();
		
		var url_link = this.url_link;
		var post_params = this.post_params;

		jsUserOptions.DeleteOption('form', this.name, bCommon, function () {
			BX.WindowManager.Get().AllowClose(); BX.WindowManager.Get().Close();
			BX.closeWait();
			(new BX.CAdminDialog({
			    'content_url': url_link,
			    'content_post': post_params,
				'draggable': true,
				'resizable': true,
				'buttons': [BX.CAdminDialog.btnSave, BX.CAdminDialog.btnCancel]
				})).Show();
		});		
	};
	
	this.DisableSettings = function()
	{
		BX.showWait();
		
		var url_link = this.url_link;
		var post_params = this.post_params;
		var request = new JCHttpRequest;
		request.Action = function () {
			BX.WindowManager.Get().AllowClose(); BX.WindowManager.Get().Close();
			BX.closeWait();
			(new BX.CAdminDialog({
			    'content_url': url_link,
			    'content_post': post_params,
				'draggable': true,
				'resizable': true,
				'buttons': [BX.CAdminDialog.btnSave, BX.CAdminDialog.btnCancel]
				})).Show();
		};
		var sParam = '';
		sParam += '&p[0][c]=form';
		sParam += '&p[0][n]='+encodeURIComponent(this.name+'_disabled');
		sParam += '&p[0][v][disabled]=Y';
		request.Send('/bitrix/admin/user_options.php?lang=' + phpVars.LANGUAGE_ID + sParam + '&sessid='+phpVars.bitrix_sessid);
	};

	this.EnableSettings = function()
	{
		BX.showWait();
		
		var url_link = this.url_link;
		var post_params = this.post_params;
		var request = new JCHttpRequest;
		request.Action = function () {
			BX.WindowManager.Get().AllowClose(); BX.WindowManager.Get().Close();
			BX.closeWait();
			(new BX.CAdminDialog({
			    'content_url': url_link,
			    'content_post': post_params,
				'draggable': true,
				'resizable': true,
				'buttons': [BX.CAdminDialog.btnSave, BX.CAdminDialog.btnCancel]
				})).Show();
		};
		var sParam = '';
		sParam += '&c=form';
		sParam += '&n='+encodeURIComponent(this.name)+'_disabled';
		sParam += '&action=delete';
		request.Send('/bitrix/admin/user_options.php?lang=' + phpVars.LANGUAGE_ID + sParam + '&sessid='+phpVars.bitrix_sessid);
	};
	
	this.btnSettingsShow = function (obj,url_link,title,off)
	{
		off = !!off;
		var set_lnk = obj.PARTS.FOOT.appendChild(BX.create('A', {
			attr: {href: 'javascript:void(0);'},
			style: {
				position: 'absolute', top: '11px', left: (off ? '40px' : '11px')
			},
			props: {
				id: 'btn_subsettings',
				className: 'context-button icon',
				title: title
			}
		}));
		this.url_settings = url_link;
		BX.bind(set_lnk, 'click', BX.proxy(this.btnSettingsHandler, this));
	};
	
	this.btnSettingsHandler = function()
	{
		this.ShowSettings(this.url_settings);
	};
	
	this.btnSettingsOnOff = function(obj,title,enable,off)
	{
		enable = !!enable;
		off = !!off;
		var set_lnk = obj.PARTS.FOOT.appendChild(BX.create('A', {
			attr: {href: 'javascript:void(0);'},
			style: {
				position: 'absolute', top: '11px', left: (off ? '69px' : '40px')
			},
			props: {
				id: (enable ? 'btn_subsettings_enable' : 'btn_subsettings_disable'),
				className: 'context-button icon',
				title: title
			}
		}));
		BX.bind(set_lnk, 'click', BX.proxy(this.btnSettingsOnOffHandler, this));
	};
	
	this.btnSettingsOnOffHandler = function()
	{
		if (BX.proxy_context.id == 'btn_subsettings_enable' || BX.proxy_context.id == 'btn_subsettings_disable')
		{
			if (BX.proxy_context.id == 'btn_subsettings_enable')
			{
				this.EnableSettings();
			}
			else
			{
				this.DisableSettings();
			}
		}
	};
}

BX.extend(SubTabControl,TabControl);