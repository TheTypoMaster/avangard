/**
	���� ��� ������: BPCSourse, BPCSlider, BPCPlayer
	
	BPCSourse - ����� "��������� ������", ���������� �� ���� ��������-
	��� ����������. 
	
	data - ����� �������������� ������
	id - ����� ������� �������� ������ � ����� ������ (���� �� ������, �� ������ (1))
	count - ���������� ��������� � ����� ������ (���� �� ��������, �� 0)
*/
function BPCSourse(data, id, count) {
	id = parseInt(id);
	id = (id > 0 ? id : 1);
	count = parseInt(count);
	this.Data = new Array(id); // ������ ������ "���������� �����" => ������
	this.iCountData = (count > 0 ? count : 0); // ������������ ���������� ���������
	this.iFirstNumber = id; // ������ ������� ����������� �������
	this.iForceElementCount = 5; // ����� ����������� �� ������ ��������� 
	this.loaded = false; // ��������� �������� �������
	this.arParams = {'attempt' : {}}; // ��������� ����������
	this.events = {}; 
//	try {
		for (var ii = 0; ii < data.length; ii++)
		{
			if (this.checkEvent('OnBeforeItemAdd', data[ii], this.Data.length))
			{
				this.Data.push(data[ii]);
				this.checkEvent('OnAfterItemAdd', (this.Data.length - 1));
			}
		}
		this.loaded = true;
//	} catch (e){}
	this.url = false;
	return this.loaded;
}
/**
	getData - ���������� ����� ������
	
	iLastNumber - ���������� ����� � ������� 
	bDirection - ����������� �������� (true - ������, false - �����)
	
	����������: false | 'wait' | ����� ������
*/
BPCSourse.prototype.getData = function(iLastNumber, bDirection) 
{
	if (!this.loaded)
		return false;
	bDirection = !!bDirection; // true = next, false = prev
	iLastNumber = parseInt(iLastNumber);
// 	���� � ��� �� ���������� ����� 
	if (iLastNumber < 1)
		return false;
//	���� ������ ������������ ���������� � ����� ������ 
//	����� ����������, �� ����� �������, ��� ������ ������ �� �����
	else if (this.iCountData > 0 && iLastNumber > this.iCountData)
		return false;

//	���� ��� ���� ��� ������
	if (bDirection && (iLastNumber < this.Data.length)) 
	{
		if ((this.Data.length - iLastNumber) < this.iForceElementCount)
			this.checkData(bDirection);
		return this.Data.slice(iLastNumber);
	}
	else if (!bDirection && this.iFirstNumber < iLastNumber)
	{
		if ((iLastNumber - this.iFirstNumber) < this.iForceElementCount)
			this.checkData(bDirection);
		return this.Data.slice(this.iFirstNumber, iLastNumber);
	}
	
	return this.checkData(bDirection);
}
/**
	checkData - ��������� ������� ������. ���� ������ ���, �� �������� ������
	
	bDirection - ����������� �������� (true - ������, false - �����)
	
	����������: false | 'wait' | true
*/
BPCSourse.prototype.checkData = function(bDirection) 
{
	bDirection = !!bDirection;
	if (!this.loaded)
		return false;
	else if (this.busy == true)
		return 'wait';
	else if ((bDirection && this.iCountData > 0 && (this.Data.length - 1) >= this.iCountData) || 
		(!bDirection && this.iFirstNumber <= 1))
		return true;
	else if (this.busy != true && !this.checkSendData(bDirection) && this.busy != true)
	{
		this.addData(bDirection, (bDirection ? 
			'{"status" : "end"}' : 
			'{"start_number" : ' + (this.iFirstNumber - 1) + ', "elements" : ' + 
				'{"src" : "/bitrix/components/bitrix/photogallery.detail.list/templates/slide_show/images/error.gif"}}'));
	}
	
	__this_source = this;
	setTimeout(new Function("__this_source.sendData(" + (bDirection ? "true" : "false") + ")"), 100);
	return 'wait';
}
/**
	checkSendData - ��������� ���������� �������� �������� �������� � ����������� ���������
	
	bDirection - ����������� �������� (true - ������, false - �����)
	
	����������: false | true
*/
BPCSourse.prototype.checkSendData = function(bDirection)
{
	if (this.busy == true)
		return false;
	bDirection = !!bDirection;
	var res = (bDirection ? 'next:' + this.Data.length : 'prev:' + this.Data.length);
	this.arParams['attempt'][res] = (this.arParams['attempt'][res] ? this.arParams['attempt'][res] : 0);
	if (parseInt(this.arParams['attempt'][res]) > 20)
		return false;
	this.arParams['attempt'][res]++;
	return true;
}
/**
	sendData - ���������� ������ �� ��������� ������
	
	bDirection - ����������� �������� (true - ������, false - �����)
	
	����������: false | 'wait' | true
*/
BPCSourse.prototype.sendData = function(bDirection)
{
	if (this.busy == true)
		return 'wait';
	this.busy = true;
	
	bDirection = !!bDirection;
	
	var current = (bDirection ? this.Data.slice(-1) : this.Data.slice(this.iFirstNumber, this.iFirstNumber+1));
	var url;
	if (!this.url) { url = window.location.href; } 
	url = url.replace(/PAGEN\_([\d]+)\=([\d]+)/gi, '').replace(/\#(.*)/gi, '');
	var res = {'current' : {'id' : current[0]['id']}, 
		'return_array' : 'Y', 
		'direction' : (bDirection ? 'next' : 'prev'), 
		'ELEMENT_ID' : current[0]['id']};
	var result_events = this.checkEvent('OnBeforeSendData', res);
	if (result_events === false)
		return false; 
	else if (typeof result_events == "object")
		res = result_events; 
	var TID = jsAjax.InitThread();
	__this_source = this;
	eval("jsAjax.AddAction(TID, function(data){__this_source.addData(" + (bDirection ? "true" : "false") + ", data);});");

	jsAjax.Send(TID, url, res);
}
/**
	addData - ��������� ������ � ������
	
	bDirection - ����������� �������� (true - ������, false - �����)
	data - ������
*/
BPCSourse.prototype.addData = function(bDirection, data)
{
	bDirection = !!bDirection;
	
	try
	{
		eval("var result=" + data + ";");
		result['start_number'] = parseInt(result['start_number']);
		if (result['start_number'] > 0)
		{
			if (result['elements'] && result['elements'].length > 0)
			{
				if (this.Data.length < result['start_number'])
				{
					var res = this.Data.length;
					for (var ii = res; ii < result['start_number']; ii++)
						this.Data[ii] = false;
				}
				for (var ii = 0; ii < result['elements'].length; ii++)
				{
					var jj = result['start_number'] + ii;
					if ((!this.Data[jj] || this.Data[jj] == null) && this.checkEvent('OnBeforeItemAdd', result['elements'][ii], jj))
					{
						this.Data[jj] = result['elements'][ii];
						this.checkEvent('OnAfterItemAdd', jj);
					}
				}
			}
			if (result['start_number'] < this.iFirstNumber)
				this.iFirstNumber = result['start_number'];
		}
		
		if (result['start_number'] <= 0 || !(result['elements'] && result['elements'].length > 0) || 
			result['status'] == 'end')
		{
			this.iCountData = (this.Data.length - 1);
		}
	}
	catch (e) {}
	this.checkEvent('OnAfterSendData');
	this.busy = false;
}
/**
	checkItem - ��������� ������������ �������� � �������
	
	item_id - ���������� ����� � ������� 
	bDirection - ����������� �������� (true - ������, false - �����)
	
	����������: false | 'wait' | ����� ������
*/
BPCSourse.prototype.checkItem = function(item_id, bDirection)
{
	return true;
}
/**
	�������� �������
*/
BPCSourse.prototype.checkEvent = function()
{
	eventName = arguments[0];
//	if (arguments && arguments.shift) {arguments.shift()};
	if (this.events[eventName]) { return this.events[eventName](arguments); } 
	if (this[eventName]) { return this[eventName](arguments); } 
	return true;
}
/********************************************************************
	BPCSlider - ����� ��������
	
	data - ����� �������������� ������
	active - �������� ������� �������
	count - ���������� ��������� � ����� ������ (���� �� ��������, �� 0)
	position - ����� ������� �������� ������ � ����� ������ (���� �� ������, �� ������ (1))
	
********************************************************************/
function BPCSlider(data, active, count, position) 
{
	if (count <= 0)
		return false;
	this.oSource = new BPCSourse(data, position, count);
	if (!this.oSource)
		return false;
	this.windowsize = 1;
	this.oSource.iForceElementCount = this.windowsize * 3;
	this.active = this.oSource.iFirstNumber;
	this.item_params = {'width' : 800, 'height' : 600};
	this.events = {}; 
	for (var ii = this.oSource.iFirstNumber; ii < this.oSource.Data.length; ii++)
	{
		if (active == this.oSource.Data[ii]['id'])
			this.active = ii;
	}
}
/** 
	ShowSlider - ������������� ��������
	
	����������: true || false || 'wait'
*/
BPCSlider.prototype.ShowSlider = function(data) 
{
	for (var ii = 1; ii <= this.windowsize; ii++) 
	{
		var item_id = (this.active - 1 + ii);
		
		if (!this.oSource.Data[item_id])
		{
			var res = this.oSource.checkItem(item_id);
			if (!res || res == 'wait')
				return res;
		}
		if (!this.oSource.Data[item_id] || (this.oSource.Data[item_id]['loaded'] != true && !this.checkEvent('OnBeforeItemShow', item_id)))
		{
			return 'wait';
		}
	}
	
	for (var ii = 0; ii < this.windowsize; ii++)
	{
		var item_id = (this.active + ii);
		this.MakeItem(item_id, (ii + 1));
	}
	return true;
}
/** 
	MakeItem ������� �������
	
	item_id - ����� ���������
 	number - ���������� ����� � ����
*/
BPCSlider.prototype.MakeItem = function(item_id, number) 
{
	this.checkEvent('OnBeforeItemShow', item_id);
	this.ShowItem(item_id, number);
}
/** 
	ShowItem ���������� ������� (������ ���� ��������������, ��� ��� ��������� � �������� ��������)
	
	item_id - ����� ���������
 	number - ���������� ����� � ����
*/
BPCSlider.prototype.ShowItem = function(item_id, number) 
{
}

/** 
	CreateItem - ������� ������� (������� � ���� �����, ��� �������� ����� ������������� � ����������� ��������������, 
		�� � ������� ����� �� �������)

	item_id - ����� ���������
	
	����������: ������ ��� false
*/
BPCSlider.prototype.CreateItem = function(item_id)
{
	var koeff = Math.min(this.item_params['width']/this.oSource.Data[item_id]['width'], this.item_params['height']/this.oSource.Data[item_id]['height']);
	var res = {'width' : this.oSource.Data[item_id]['width'], 'height' : this.oSource.Data[item_id]['height']};
	if (koeff < 1)
	{
		res['width'] = parseInt(this.oSource.Data[item_id]['width']*koeff);
		res['height'] = parseInt(this.oSource.Data[item_id]['height']*koeff);
	}
	
	var div = document.createElement('div');
	div.className = "bx-slider-image-container";
	div.style.overflow = 'hidden';
//	div.style.visibility = 'hidden';
	div.style.width = res['width'] + "px";
	div.style.height = res['height'] + "px";
	div.id = this.oSource.Data[item_id]['id'];
	
	var image = new Image();
	image.id = 'image_' + item_id;
	__this_slider = this;
	image.onload = function(){
		__this_slider.oSource.Data[this.id.replace('image_', '')]['loaded'] = true;
		__this_slider.checkEvent('OnAfterItemLoad', this);
	}
	image.style.width = res['width'] + "px";
	image.style.height = res['height'] + "px";
	image.title = image.alt = this.oSource.Data[item_id]['title'];
	div.appendChild(image);
	image.src = this.oSource.Data[item_id]['src'];
	return div;
}

/** 
	PreloadItems - ��������������� ��������
	
	item_id - ����� ���������
*/
BPCSlider.prototype.PreloadItems = function(item_id) 
{
	item_id = parseInt(item_id);
	var images = new Array();
	var res = [(item_id - 1), (item_id + 1)];
	for (var jj in res)
	{
		var ii = res[jj];
		if (this.oSource.Data[ii] && !this.oSource.Data[ii]['loaded'])
		{
			images[ii] = new Image();
			images[ii].id = 'preload_image_' + ii;
			images[ii].onload = function(){ __this_slider.oSource.Data[this.id.replace('preload_image_', '')]['loaded'] = true; };
			images[ii].src = this.oSource.Data[ii]['src'];
		}
	}
	return true;
}

/** 
	GoToNext - ������� ������� �� ������
	
	����������: true || false || 'wait'
*/
BPCSlider.prototype.GoToNext = function()
{
	res = this.oSource.getData((this.active + this.windowsize), true);
	if (!res || res == 'wait')
		return res;
	this.active++;
	return true;
}
/** 
	GoToNext - ������� ������� � �����
	
	����������: true || false || 'wait'
*/
BPCSlider.prototype.GoToLast = function()
{
	res = this.oSource.getData((this.oSource.iCountData - this.windowsize + 1), true);
	if (!res || res == 'wait')
		return res;
	this.active = (this.oSource.iCountData - this.windowsize + 1);
	return true;
}
/** 
	GoToNext - ������� ������� �����
	
	����������: true || false || 'wait'
*/
BPCSlider.prototype.GoToPrev = function()
{
	res = this.oSource.getData((this.active - 1), false);
	if (!res || res == 'wait')
	{
		return res;
	}
	this.active--;
	return true;
}
/** 
	GoToNext - ������� ������� � ������
	
	����������: true || false || 'wait'
*/
BPCSlider.prototype.GoToFirst = function()
{
	res = this.oSource.getData(1, false);
	if (!res || res == 'wait')
		return res;
	this.active = 1;
	return true;
}
/**
	�������� �������
*/
BPCSlider.prototype.checkEvent = function()
{
	eventName = arguments[0];
	if (this.events[eventName]) { return this.events[eventName](arguments); } 
	if (this[eventName]) {return this[eventName](arguments); } 
	return true;
}

/********************************************************************
	BPCPlayer - ����� �������
	
	oSlider - ������ ��������
********************************************************************/
BPCPlayer = function(oSlider)
{
	if (!oSlider)
		return false;
	this.oSlider = oSlider;
	this.events = {};
	this.params = {
		'period' : 5, 
		'status' : 'paused'};
}
/** 
	step - ��������� ��� (������, �����, �����, ������)
	
	����������: false || 'wait' || ����� ������
*/
BPCPlayer.prototype.step = function(name_step)
{
	var res = '';
	this.stop();
	
	if (name_step == 'next')
	{
		res = this.oSlider.GoToNext();
		if (!res)
			res = this.oSlider.GoToFirst();
	}
	else if (name_step == 'prev')
	{
		res = this.oSlider.GoToPrev();
		if (!res)
			res = this.oSlider.GoToLast();
	}
	else if (name_step == 'last')
		res = this.oSlider.GoToLast();
	else
		res = this.oSlider.GoToFirst();
		
	if (res == 'wait')
	{
		this.checkEvent('OnWaitItem');
		__this_player = this;
		setTimeout(new Function("__this_player.step('" + name_step + "');"), 200);
	}
	else if (res != false)
	{
		this.checkEvent('OnShowItem');
		this.oSlider.ShowSlider();
	}
	return res;
}
/** 
	play - �����-��� (������ �����)
	
	����������: false || 'wait' || ����� ������
*/
BPCPlayer.prototype.play = function(status)
{
	status = (status == true ? true : false);
	this.checkEvent('OnStartPlay');	
	if (this.params['period'] <= 0 || this.params['status'] != 'play')
	{
		this.checkEvent('OnStopPlay');
		return false;
	}
	// ���� ��� ������ ���, �� ������ ���� ��������
	else if (!status)
	{
		__this_player = this;
		setTimeout(function(){__this_player.play(true);}, this.params['period'] * 1000);
	}
	else
	{
		// ����������� ������
		var res = this.oSlider.GoToNext();
		// ���� �� �������� �����, �� ������������� �����-���
		if (res == false) 
		{
			this.checkEvent('OnStopPlay');
			return false;
		}
		// ���� ������ �� ������������, �� ���������� ����� 200 �������.
		else if (res == 'wait') 
		{
			this.checkEvent('OnWaitItem');
			__this_player = this;
			setTimeout(function(){__this_player.play(true);}, 200);
		}
		else
		{
			this.checkEvent('OnShowItem');
			__this_player = this;
			// ���� ���� ���� ��������, �� ��������� ����. ���
			if (this.oSlider.ShowSlider({'slideshow' : true}))
			{
				setTimeout(function(){__this_player.play(true);}, this.params['period'] * 1000);
			}
			// ���� ���, �� ����������� ������ �����, ����, ����� ����� ����������.
			else
			{
				this.oSlider.GoToPrev();
				setTimeout(function(){__this_player.play(true);}, 200);
			}
		}
		return res;
	}
}
/** 
	stop - ������������� �����-���
*/
BPCPlayer.prototype.stop = function(cycle)
{
	this.params['status'] = 'paused';
	this.checkEvent('OnStopPlay');
}
/**
	�������� �������
*/
BPCPlayer.prototype.checkEvent = function()
{
	eventName = arguments[0];
	if (this.events[eventName]) { return this.events[eventName](arguments); } 
	if (this[eventName]) { return this[eventName](arguments); } 
	return true;
}
/**
	�������� ������� ������
*/
BPCPlayer.prototype.checkKeyPress = function(e)
{
	if(!e) e = window.event
	if(!e) return;
	if(e.keyCode == 39)
		__this_player.step('next');
	else if(e.keyCode == 37)
		__this_player.step('prev');
}

window.bPhotoPlayerLoad = true;