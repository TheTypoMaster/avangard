(function(window){
if (BX.admin) return;

BX.admin = {
	/* settings */
	__border_style: 'solid 1px #777f8c', // 'dashed 1px orange',
	__bg_style: '#777f8c', // 'dashed 1px orange',
	__border_dx: 0,
	__border_min_height: 12,
	__border_menu_timeout: 500,

	__borders_last_comp_pos: {},

	/* borders cache */
	__borders: null,

	dynamic_mode: false,
	dynamic_mode_show_borders: false,

	timer: null,

	/* method */
	createComponentBorder: function()
	{
		BX.admin.__borders = {};
		BX.admin.__borders.cont = document.body.appendChild(BX.create('DIV', {style: {
			display: 'none',
			height: '0px',
			width: '0px'
		}}));

		BX.admin.__borders.top = BX.admin.__borders.cont.appendChild(BX.create('DIV', {style: {
			position: 'absolute',
			height: '1px',
			fontSize: '1px',
			overflow: 'hidden',
			zIndex: 990,
			//borderTop: BX.admin.__border_style
			background: BX.admin.__bg_style
		}}));
		BX.admin.__borders.right = BX.admin.__borders.cont.appendChild(BX.create('DIV', {style: {
			position: 'absolute',
			width: '1px',
			fontSize: '1px',
			overflow: 'hidden',
			zIndex: 990,
			//borderRight: BX.admin.__border_style
			background: BX.admin.__bg_style
		}}));
		BX.admin.__borders.bottom = BX.admin.__borders.cont.appendChild(BX.create('DIV', {style: {
			position: 'absolute',
			height: '1px',
			fontSize: '1px',
			overflow: 'hidden',
			zIndex: 990,
			//borderTop: BX.admin.__border_style
			background: BX.admin.__bg_style
		}}));
		BX.admin.__borders.left = BX.admin.__borders.cont.appendChild(BX.create('DIV', {style: {
			position: 'absolute',
			width: '1px',
			fontSize: '1px',
			overflow: 'hidden',
			zIndex: 990,
			//borderLeft: BX.admin.__border_style
			background: BX.admin.__bg_style
		}}));
	},

	__borders_adjust: function()
	{
		var pos = BX.pos(this),
			dx = BX.admin.__border_dx;

		var db = BX.browser.IsIE() && !BX.browser.IsDoctype() ? 2 : 0

		BX.adjust(BX.admin.__borders.top, {style: {
			'top': (pos.top - dx - db) + 'px',
			'left': (pos.left - dx - db) + 'px',
			'width': (pos.width + dx*2) + 'px'
		}});
		BX.adjust(BX.admin.__borders.right, {style: {
			'top': (pos.top - dx - db) + 'px',
			'left': (pos.right + dx - 1 - db) + 'px',
			'height': (pos.height + dx*2) + 'px'
		}});
		BX.adjust(BX.admin.__borders.bottom, {style: {
			'top': (pos.bottom + dx - db) + 'px',
			'left': (pos.left - dx - db) + 'px',
			'width': (pos.width + dx*2) + 'px'
		}});
		BX.adjust(BX.admin.__borders.left, {style: {
			'top': (pos.top - dx - db) + 'px',
			'left': (pos.left - dx - db) + 'px',
			'height': (pos.height + dx*2) + 'px'
		}});

		BX.admin.__borders_last_comp_pos = pos;
	},

	setComponentBorder: function(comp)
	{
		if (!BX.isReady)
			return BX.ready(function() {BX.admin.setComponentBorder(comp)});

		if (null == BX.admin.__borders)
			BX.admin.createComponentBorder();

		comp = BX(comp);
		if (!comp) return;

		if (comp.children.length > 0)
		{
			var c = comp.firstChild, new_comp = null, cnt = 0;
			while (c)
			{
				if (BX.type.isElementNode(c) && c.tagName.toUpperCase() != 'SCRIPT')
				{
					cnt++;
					if (cnt > 1 || !BX.is_relative(c) && !BX.is_float(c))
					{
						cnt = -1;
						break;
					}
					new_comp = c;
				}
				c = c.nextSibling;
			}

			if (cnt == 1 && new_comp)
			{
				if (comp.OPENER)
				{
					new_comp.OPENER = comp.OPENER;
					new_comp.OPENER.PARAMS.parent = new_comp;
				}

				comp = new_comp;
			}
		}

		if (BX.admin.dynamic_mode)
		{
			BX.addCustomEvent(window, 'onDynamicModeChange', BX.delegate(BX.admin.__empty_comp_onmodechange, comp));
		}

		BX.admin.__empty_comp_onmodechange.apply(comp, [!BX.admin.dynamic_mode || BX.admin.dynamic_mode_show_borders]);

		BX.bind(comp, 'mouseover', BX.admin.__borders_show);
		BX.bind(comp, 'mouseout', BX.admin.__borders_hide);

		if (comp.OPENER && comp.OPENER.defaultAction)
		{
			comp.title = BX.message('ADMIN_INCLAREA_DBLCLICK') + ' - ' + comp.OPENER.defaultActionTitle;
			BX.bind(comp, 'dblclick', BX.admin.__borders_dblclick);
		}
	},

	removeComponentBorder: function(comp)
	{
		comp = BX(comp);
		if (!comp) return;

		BX.unbind(comp, 'mouseover', BX.admin.__borders_show);
		BX.unbind(comp, 'mouseout', BX.admin.__borders_hide);

		if (comp.bx_msover)
		{
			BX.admin.__borders_hide.apply(comp);
		}
	},

	__empty_comp_onmodechange: function(val)
	{
		if (this.offsetHeight <= BX.admin.__border_min_height)
		{
			if (val)
			{
				if (BX.browser.IsIE() && !BX.browser.IsDoctype())
					this.style.height = BX.admin.__border_min_height + 'px';
				else
					this.style.minHeight = BX.admin.__border_min_height + 'px';

				BX.addClass(this, 'bx-context-toolbar-empty-area');
			}
			else
			{
				if (BX.browser.IsIE() && !BX.browser.IsDoctype())
					this.style.height = null;
				else
					this.style.minHeight = null;

				BX.removeClass(this, 'bx-context-toolbar-empty-area');
			}
		}
	},

	__borders_dblclick: function(e)
	{
		if (
			(!BX.admin.dynamic_mode || BX.admin.dynamic_mode_show_borders)
			&& this.OPENER && this.OPENER.defaultAction
		)
		{
			this.OPENER.executeDefaultAction();
			return BX.PreventDefault(e);
		}
		return true;
	},

	__borders_show: function(e)
	{
		e = e || window.event;

		var q = BX.is_relative(this) ? this.parentNode : this;
		if (BX.admin.dynamic_mode && !BX.admin.dynamic_mode_show_borders)
		{
			if (q.title) {q._title = q.title; q.title = '';}

			return;
		}

		if (q._title) {q.title = q._title;}

		if (!BX.admin.__borders_adjusted)
		{
			BX.admin.__borders.cont.style.display = 'block';
			BX.admin.__borders_adjust.apply(this);
			BX.admin.__borders_adjusted = true;
		}

		this.bx_msover = true;

		if (this.OPENER)
		{
			if (this.bxtimer) clearTimeout(this.bxtimer);
			this.bxtimer = setTimeout(BX.proxy(BX.admin.__borders_menu_show, this), this.OPENER.timeout || BX.admin.__border_menu_timeout);
			if (!this.__opener_events_set)
			{
				BX.bind(this.OPENER.Get(), 'mouseover', BX.proxy(BX.admin.__borders_show, this));
				BX.bind(this.OPENER.Get(), 'mouseout', BX.proxy(BX.admin.__borders_hide, this));
				this.__opener_events_set = true;
			}
		}

		//return BX.PreventDefault(e);
	},

	__borders_menu_show: function()
	{
		if (this.bx_msover && this.OPENER)
		{
			this.OPENER.UnHide();
		}
	},

	__borders_hide: function()
	{
		if (BX.admin.dynamic_mode && !BX.admin.dynamic_mode_show_borders)
			return;

		if (this.OPENER && this.OPENER.isMenuVisible())
		{
			setTimeout(BX.admin.__borders_hide, 3*BX.admin.__border_menu_timeout);
			return;
		}

		BX.admin.__borders.cont.style.display = 'none';
		BX.admin.__borders_adjusted = false;

		this.bx_msover = false;

		if (this.OPENER)
		{
			var to = BX.admin.__get_hide_timeout(this.OPENER);
			if (this.bxtimer) clearTimeout(this.bxtimer);
			this.bxtimer = setTimeout(BX.proxy(BX.admin.__borders_menu_hide, this), to);
		}
	},

	__borders_menu_hide: function(e)
	{
		if (!this.bx_msover && this.OPENER)
		{
			this.OPENER.Hide();
		}
	},

	__get_hide_timeout: function(opener)
	{
		var to = BX.admin.__border_menu_timeout;
		return to;
		if (BX.admin.__borders_last_comp_pos.top)
		{
			var pos = {top: parseInt(opener.Get().style.top), left: parseInt(opener.Get().style.left)}
			var bpos = BX.admin.__borders_last_comp_pos;

			if (pos.top <= bpos.bottom && pos.top >= bpos.top && pos.left <= bpos.right && pos.left >= bpos.left)
			{
				return to;
			}

			var dist = {
				top: Math.min(Math.abs(BX.admin.__borders_last_comp_pos.top - pos.top), Math.abs(BX.admin.__borders_last_comp_pos.bottom - pos.top)),
				left: Math.min(Math.abs(BX.admin.__borders_last_comp_pos.top - pos.left), Math.abs(BX.admin.__borders_last_comp_pos.bottom - pos.left))
			}

			dist = Math.sqrt(dist.top*dist.top + dist.left*dist.left);

			to += 2*dist;
			return to;
		}

	}
};

BX.admin.panel = {
	state: {
		fixed: false,
		collapsed: false
	},

	DIV: null,
	BACKDIV: null,
	BACKFRAME: null,
	NOTIFY: null,

	buttons: [],

	Init: function()
	{
		BX.browser.addGlobalClass();

		var q;

		BX.admin.panel.DIV = BX('bx-panel');

		if (BX.admin.panel.DIV)
		{
			BX.setUnselectable(BX.admin.panel.DIV);

			q = BX('bx-panel-toggle');
			if (q)
			{
				q.onclick = function(event)
				{
					BX.admin.toggle.toggleStatus();
					event = event || window.event;
					BX.PreventDefault(event);
				}
			}

			q = BX('bx-panel-toggle-icon');
			if (q)
			{
				BX.bind(q, "mousedown", BX.proxy(BX.admin.toggle.start, BX.admin.toggle));
				BX.bind(q, "click", BX.PreventDefault);
			}

			q = BX('bx-panel-hider');
			if (q)
			{
				BX.admin.panel.DIV.ondblclick = BX('bx-panel-expander').onclick = q.onclick = BX.admin.panel.Collapse;

				BX('bx-panel-tabs').ondblclick = BX.PreventDefault;
				var sw = BX('bx-panel-switcher');
				if (sw) sw.ondblclick = BX.PreventDefault;
			}

			q = BX('bx-panel-pin');
			if (q)
			{
				BX.bind(q, 'click', function() {
					var bFixed = BX.hasClass(this, 'bx-panel-pin-fixed');
					if (bFixed)
						BX.removeClass(this, 'bx-panel-pin-fixed');
					else
						BX.addClass(this, 'bx-panel-pin-fixed');

					BX.userOptions.save('admin_panel', 'settings', 'fix', (bFixed? 'off':'on'));
				});

				BX.bind(q, 'click', BX.admin.panel.Fix);

				if (BX.admin.panel.state.fixed) BX.admin.panel.Fix();
			}

			for (var i=0,len=BX.admin.panel.buttons.length; i<len; i++)
			{
				var btn = BX(BX.admin.panel.buttons[i]['ID']);

				if (btn)
				{
					if (BX.admin.panel.buttons[i].HOVER_CSS)
					{
						btn.bx_hover_class = BX.admin.panel.buttons[i].HOVER_CSS;
						if (BX.admin.panel.buttons[i].ACTIVE_CSS)
							btn.bx_active_class = BX.admin.panel.buttons[i].ACTIVE_CSS;

						BX.bind(btn, 'mouseover', BX.admin.panel.__btn_hover);
						BX.bind(btn, 'mouseout', BX.admin.panel.__btn_hout);
						BX.bind(btn, 'mousedown', BX.admin.panel.__btn_down);
					}

					if (BX.admin.panel.buttons[i].MENU)
					{
						var opener = new BX.COpener({
							DIV: btn,
							ATTACH:btn.parentNode.parentNode,
							MENU: BX.admin.panel.buttons[i].MENU,
							TYPE: 'click'
						});

						BX.addCustomEvent(opener, 'onOpenerMenuOpen', BX.delegate(BX.admin.panel.__btn_menuopen, btn));
						BX.addCustomEvent(opener, 'onOpenerMenuClose', BX.delegate(BX.admin.panel.__btn_menuclose, btn));
					}

					if (BX.admin.panel.buttons[i].HINT)
					{
						var target = BX.admin.panel.buttons[i].HINT.TARGET ? btn.parentNode.parentNode : btn;
						if (BX.admin.panel.buttons[i].HINT.ID)
						{
							BX.hint(target, BX.admin.panel.buttons[i].HINT.TITLE, BX.admin.panel.buttons[i].HINT.TEXT, BX.admin.panel.buttons[i].HINT.ID)
						}
						else
						{
							target.BXHINT = new BX.CHint({
								parent: target, hint: BX.admin.panel.buttons[i].HINT.TEXT, title: BX.admin.panel.buttons[i].HINT.TITLE, id: BX.admin.panel.buttons[i].HINT.ID
							});
						}
					}

					btn.ondblclick = BX.PreventDefault;

					if (BX.browser.IsIE())
						btn.setAttribute('hideFocus', 'hidefocus');
				}
			}
		}

		BX.admin.panel.buttons = []; q = null;
	},

	__view_mode_toggle: function(e)
	{
		var this1 = BX('bx-panel-toggle');

		var captiontext = BX('bx-panel-toggle-caption-mode');
		if (this1.className=='bx-panel-toggle-on')
		{
			this1.className='bx-panel-toggle-off';
			captiontext.innerHTML=BX.message('ADMIN_SHOW_MODE_OFF');
			BX.admin.dynamic_mode_show_borders = false;
			this1.href = this1.href.replace('bitrix_include_areas=N', 'bitrix_include_areas=Y');
		}
		else
		{
			this1.className = 'bx-panel-toggle-on';
			captiontext.innerHTML=BX.message('ADMIN_SHOW_MODE_ON');
			BX.admin.dynamic_mode_show_borders = true;
			this1.href = this1.href.replace('bitrix_include_areas=Y', 'bitrix_include_areas=N');
		}

		if (null != this.BXHINT)
			this.BXHINT.Destroy();

		this.BXHINT = new BX.CHint({
			parent: this,
			title: BX.message('AMDIN_SHOW_MODE_TITLE'),
			hint: BX.admin.dynamic_mode_show_borders
					? BX.message('ADMIN_SHOW_MODE_ON_HINT')
					: BX.message('ADMIN_SHOW_MODE_OFF_HINT'),
			showOnce: true,
			preventHide: true,
			show_timeout: 0,
			hide_timeout: 2000
		});

		BX.userOptions.save('admin_panel', 'settings', 'edit', (BX.admin.dynamic_mode_show_borders ? 'on' : 'off'));

		BX.onCustomEvent(window, 'onDynamicModeChange', [BX.admin.dynamic_mode_show_borders]);

		return BX.eventReturnFalse(e);
	},

	__btn_hover: function() {
		this.bx_hover = true;
		if (!BX.admin.panel._menu_open) BX.addClass(this.parentNode.parentNode, this.bx_hover_class);
	},
	__btn_hout: function()
	{
		this.bx_hover = false;
		if (!BX.admin.panel._menu_open) BX.removeClass(this.parentNode.parentNode, this.bx_hover_class);
		BX.admin.panel.__btn_inactive.apply(this);
	},

	__btn_down: function()
	{
		//BX.bind(document, "mouseup", BX.proxy(BX.admin.panel.__btn_up, this));
		BX.admin.panel.__btn_active.apply(this);
	},

	__btn_up : function()
	{
		BX.unbind(document, "mouseup", BX.proxy(BX.admin.panel.__btn_up, this));
		BX.admin.panel.__btn_inactive.apply(this);
	},

	__btn_active: function()
	{
		this.bx_active = true;
		if (!BX.admin.panel._menu_open)
			BX.addClass(this.parentNode.parentNode, this.bx_active_class);
	},

	__btn_inactive: function()
	{
		this.bx_active = false;
		if (!BX.admin.panel._menu_open)
			BX.removeClass(this.parentNode.parentNode, this.bx_active_class);
	},

	__btn_menuopen: function()
	{
		if (this.bx_hover)
			BX.admin.panel.__btn_hover.apply(this);

		if (this.bx_active)
			BX.admin.panel.__btn_active.apply(this);

		BX.admin.panel._menu_open = true;
	},

	__btn_menuclose: function()
	{
		BX.admin.panel._menu_open = false;
		if (!this.bx_hover)
			BX.admin.panel.__btn_hout.apply(this);

		//if (!this.bx_active)
		BX.admin.panel.__btn_inactive.apply(this);
	},

	RegisterButton: function(btn)
	{
		BX.admin.panel.buttons[BX.admin.panel.buttons.length] = btn;
	},

	Collapse: function(e)
	{
		e = e || window.event;

		BX.admin.panel.state.collapsed = !(BX.admin.panel.DIV.className.indexOf('bx-panel-folded')>-1);
		var y_start = BX.admin.panel.DIV.offsetHeight;

		var hider = BX("bx-panel-hider", true);
		var expander = BX("bx-panel-expander", true);
		var toggle = BX("bx-panel-toggle");

		if (BX.admin.panel.state.collapsed)
		{
			BX.admin.toggle.unset();
			BX("bx-panel-userinfo").insertBefore(toggle.parentNode.removeChild(toggle), expander);
			BX.addClass(BX.admin.panel.DIV, "bx-panel-folded");
		}
		else
		{
			BX.admin.toggle.unset();
			BX("bx-panel-switcher").insertBefore(toggle.parentNode.removeChild(toggle), hider);
			BX.removeClass(BX.admin.panel.DIV, "bx-panel-folded");
		}

		var dy = BX.admin.panel.DIV.offsetHeight - y_start;

		BX.userOptions.save('admin_panel', 'settings', 'collapsed', (BX.admin.panel.state.collapsed ? 'on':'off'));

		BX.admin.panel.__adjustBackDiv();

		BX.onCustomEvent('onTopPanelCollapse', [BX.admin.panel.state.collapsed, dy]);

		return BX.PreventDefault(e);
	},

	isFixed: function()
	{
		return BX.admin.panel.DIV.className.indexOf('bx-panel-fixed') > -1;
	},

	Fix: function()
	{
		if (null == BX.admin.panel.BACKDIV)
			BX.admin.panel.BACKDIV = BX('bx-panel-back');
		var bFixed = BX.admin.panel.isFixed();

		var bIE = BX.browser.IsIE();
		if(bIE)
		{
			try {BX.admin.panel.DIV.style.removeExpression("top");} catch(e) {bIE = false;}
		}

		if(bFixed)
		{
			BX.removeClass(BX.admin.panel.DIV, bIE ? 'bx-panel-fixed-ie' : 'bx-panel-fixed');
			BX.admin.panel.BACKDIV.style.display = 'none';
			if(bIE)
			{
				BX.admin.panel.DIV.style.cssText = "position: static !important;";

				if(BX.admin.panel.BACKFRAME)
					BX.admin.panel.BACKFRAME.style.visibility = 'hidden';
			}
		}
		else
		{
			if(bIE)
			{
				try{BX.admin.panel.DIV.style.setExpression("top", "0");} catch(e) {bIE = false;}
			}

			if (bIE)
				BX.admin.panel.DIV.style.cssText = "";

			BX.addClass(BX.admin.panel.DIV, bIE ? 'bx-panel-fixed-ie' : 'bx-panel-fixed');

			if(bIE)
			{
				if(document.body.currentStyle.backgroundImage == 'none')
				{
					document.body.style.backgroundImage = "url(/bitrix/images/1.gif)";
					document.body.style.backgroundAttachment = "fixed";
					document.body.style.backgroundRepeat = "no-repeat";
				}
				BX.admin.panel.DIV.style.setExpression("top", "eval((document.documentElement && document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop)");
				BX.admin.panel.DIV.style.setExpression("left", "eval((document.documentElement && document.documentElement.scrollLeft) ? document.documentElement.scrollLeft : document.body.scrollLeft)");
				BX.admin.panel.DIV.style.setExpression("width", "eval((document.documentElement && document.documentElement.clientWidth) ? document.documentElement.clientWidth : document.body.clientWidth)");
			}

			BX.admin.panel.__adjustBackDiv();
			BX.admin.panel.BACKDIV.style.display = 'block';

			if(bIE)
			{
				if(BX.admin.panel.BACKFRAME)
					BX.admin.panel.BACKFRAME.style.visibility = 'visible';
				else
					BX.admin.panel.CreateFrame();
			}
		}

		BX.onCustomEvent('onTopPanelFix', [!bFixed]);
	},

	__adjustBackDiv: function()
	{
		if (BX.admin.panel.BACKDIV)
		{
			var h = BX.admin.panel.DIV.offsetHeight+'px';
			BX.admin.panel.BACKDIV.style.height = h;

			var frame = BX("bx-panel-frame");
			if (BX.admin.panel.BACKFRAME)
				BX.admin.panel.BACKFRAME.style.height = h;
		}
	},

	CreateFrame: function()
	{
		BX.admin.panel.BACKFRAME = document.body.appendChild(BX.create('IFRAME', {
			props: {
				id: "bx-panel-frame"
			},
			style: {
				position: 'absolute',
				overflow: 'hidden',
				zIndex: parseInt(BX.admin.panel.DIV.currentStyle.zIndex)-1,
				height: BX.admin.panel.DIV.offsetHeight + "px"
			}
		}));

		BX.admin.panel.BACKFRAME.style.setExpression("top", "eval(document.body.scrollTop)");
		BX.admin.panel.BACKFRAME.style.setExpression("left", "eval(document.body.scrollLeft)");
		BX.admin.panel.BACKFRAME.style.setExpression("width", "eval(document.body.clientWidth)");
	},

	Notify: function(str)
	{
		if (!BX.isReady)
		{
			var _args = arguments;
			BX.ready(function() {BX.admin.panel.Notify.apply(this, _args);});
			return;
		}

		if (!BX.admin.panel.DIV) return;

		if (null == BX.admin.panel.NOTIFY)
		{
			BX.admin.panel.NOTIFY = BX.admin.panel.DIV.appendChild(BX.create('DIV', {
				props: {className: 'adm-warning-block'},
				html:
					'<span class="adm-warning-text">'+(str||'&nbsp;')+'</span><span onclick="BX.admin.panel.hideNotify(this.parentNode)" class="adm-warning-close"></span>'
			}));

		}

		BX.removeClass(BX.admin.panel.NOTIFY, 'adm-warning-animate');

		BX.admin.panel.__adjustBackDiv();
	},


	hideNotify: function(element)
	{
		var element = BX.type.isDomNode(element)? element: this;

		if (!!element && !!element.parentNode && !!element.parentNode.parentNode)
		{
			BX.addClass(element, 'adm-warning-animate');
		}

		if (BX.type.isDomNode(element) && element.getAttribute('data-ajax') == "Y")
		{
			var notifyId = parseInt(element.getAttribute('data-id'));
			if (notifyId > 0)
			{
				BX.ajax({
					url: '/bitrix/admin/admin_notify.php',
					method: 'POST',
					dataType: 'json',
					data: {'ID' : notifyId, 'sessid': BX.bitrix_sessid()}
				});
			}
		}

		(BX.defer(BX.admin.panel.__adjustBackDiv, this))();
		setTimeout(BX.proxy(BX.admin.panel.__adjustBackDiv, this), 310);
	}

	/*,
	setZIndex: function()
	{
		var zIndex = BX.WindowManager.GetZIndex()-6;
		BX.admin.panel.DIV.setAttribute('style', 'z-index: ' + zIndex + ' !important;');
	}
	*/
};

BX.admin.toggle = {

	icon : null,
	indicator : null,
	toggle : null,
	caption : null,

	pageX : 0,
	initIconPos : 0,
	initIndicatorPos : 0,

	minLeft : -3,
	maxLeft : 17,

	unset : function()
	{
		this.icon = this.indicator = this.toggle = this.caption = null;
	},

	start : function(event)
	{
		event = event || window.event;

		if (!this._init() || !event)
			return;

		BX.fixEventPageX(event);
		this.pageX = event.pageX;
		this.initIconPos = parseInt(BX.style(this.icon, "left"));
		this.initIndicatorPos = BX.hasClass(this.toggle, "bx-panel-toggle-on") ? -270 : -290;

		BX.removeClass(this.toggle, "bx-panel-toggle-animate");

		BX.bind(document, "mousemove", BX.proxy(this._onMouseMove, this));
		BX.bind(document, "mouseup", BX.proxy(this._onMouseUp, this));

		document.body.onselectstart = BX.False;
		document.body.ondragstart = BX.False;
		document.body.style.MozUserSelect = "none";
	},

	_init : function()
	{
		if (this.toggle)
			return true;

		this.toggle = BX("bx-panel-toggle");
		this.icon = BX("bx-panel-toggle-icon");
		this.indicator = BX("bx-panel-toggle-indicator");
		this.caption = BX("bx-panel-toggle-caption-mode");

		return (this.toggle && this.icon && this.indicator && this.caption);
	},

	_onMouseMove : function(event)
	{
		event = event || window.event;
		BX.fixEventPageX(event);
		this._moveToggle(event.pageX - this.pageX);
	},

	_onMouseUp : function()
	{
		var pos = parseInt(BX.style(this.icon, "left"));
		if (this.initIconPos == pos)
		{
			this.toggleStatus();
		}
		else
		{
			var half = this.minLeft + Math.floor((this.maxLeft - this.minLeft) / 2);
			if (pos >= half)
			{
				BX.addClass(this.toggle, "bx-panel-toggle-on bx-panel-toggle-animate");
				BX.removeClass(this.toggle, "bx-panel-toggle-off");
				this._changePosition(true);
			}
			else
			{
				BX.addClass(this.toggle, "bx-panel-toggle-off bx-panel-toggle-animate");
				BX.removeClass(this.toggle, "bx-panel-toggle-on");
				this._changePosition(false);
			}
		}

		this.icon.style.cssText = "";
		this.indicator.style.cssText = "";

		BX.unbind(document, "mousemove", BX.proxy(this._onMouseMove, this));
		BX.unbind(document, "mouseup", BX.proxy(this._onMouseUp, this));

		document.body.onselectstart = null;
		document.body.ondragstart = null;
		document.body.style.MozUserSelect = "";
	},

	_changePosition : function(on)
	{
		var firstNode = this.caption.childNodes[0];

		if ( (on && firstNode.id == "bx-panel-toggle-caption-mode-on") || (!on && firstNode.id == "bx-panel-toggle-caption-mode-off"))
			return;
		this.caption.appendChild(this.caption.removeChild(firstNode));

		if (BX.admin.dynamic_mode)
		{
			if (on)
			{
				BX.admin.dynamic_mode_show_borders = true;
				this.toggle.href = this.toggle.href.replace('bitrix_include_areas=Y', 'bitrix_include_areas=N');
			}
			else
			{
				BX.admin.dynamic_mode_show_borders = false;
				this.toggle.href = this.toggle.href.replace('bitrix_include_areas=N', 'bitrix_include_areas=Y');
			}

			if (null != BX.admin.panel.BXHINT)
				BX.admin.panel.BXHINT.Destroy();

			BX.admin.panel.BXHINT = new BX.CHint({
				parent: this.toggle,
				title: BX.message('AMDIN_SHOW_MODE_TITLE'),
				hint: BX.admin.dynamic_mode_show_borders
					? BX.message('ADMIN_SHOW_MODE_ON_HINT')
					: BX.message('ADMIN_SHOW_MODE_OFF_HINT'),
				showOnce: true,
				preventHide: true,
				show_timeout: 0,
				hide_timeout: 2000
			});

			BX.userOptions.save('admin_panel', 'settings', 'edit', (BX.admin.dynamic_mode_show_borders ? 'on' : 'off'));
			BX.onCustomEvent(window, 'onDynamicModeChange', [BX.admin.dynamic_mode_show_borders]);
		}
		else
		{
			BX.reload(this.toggle.href);
		}
	},

	_moveToggle : function(offset)
	{
		var newPos = this.initIconPos + offset;
		newPos = Math.min(this.maxLeft, Math.max(newPos, this.minLeft));
		this.icon.style.cssText = "left:" + newPos + "px !important";
		this.indicator.style.cssText = "background-position: " + ( this.initIndicatorPos + newPos - this.initIconPos) + "px -1751px !important";

	},

	toggleStatus : function()
	{
		if (!this._init())
			return;

		if (BX.hasClass(this.toggle, "bx-panel-toggle-off"))
		{
			BX.addClass(this.toggle, "bx-panel-toggle-on bx-panel-toggle-animate");
			BX.removeClass(this.toggle, "bx-panel-toggle-off");
			this._changePosition(true);
		}
		else
		{
			BX.addClass(this.toggle, "bx-panel-toggle-off bx-panel-toggle-animate");
			BX.removeClass(this.toggle, "bx-panel-toggle-on");
			this._changePosition(false);
		}
	}
};

BX.admin.startMenuRecent = function(itemInfo)
{
	BX.ajax.get('/bitrix/admin/get_start_menu.php', {
		mode: 'save_recent',
		url: itemInfo['LINK'],
		text: itemInfo['TEXT'],
		title: itemInfo['TITLE'],
		icon: itemInfo['GLOBAL_ICON'],
		sessid:BX.bitrix_sessid()
	});
}

BX.admin.startMenuFavAdd = function(back_url)
{
	window.location.href = '/bitrix/admin/favorite_edit.php?lang='+BX.message('LANGUAGE_ID')+'&name='+BX.util.urlencode(document.title)+'&addurl='+BX.util.urlencode(window.location.href)+'&encoded=Y' + (!!back_url ? '&back_url_pub=' + BX.util.urlencode(back_url) : '');
}

/************************** init admin panel **********************************/
BX.ready(function() {
	BX.admin.panel.Init();
});
//BX.addCustomEvent('onWindowRegister', BX.admin.panel.setZIndex);
//BX.addCustomEvent('onWindowUnRegister', BX.admin.panel.setZIndex);

})(window);
