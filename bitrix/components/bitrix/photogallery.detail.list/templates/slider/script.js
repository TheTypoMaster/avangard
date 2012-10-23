/********************************************************************
	BPCStretchSlider - класс резинового слайдера
	
	data - набор первоначальных данных
	position - номер первого элемента набора в ленте данных (если не указан, то первый (1))
	count - количество элементов в выборке
	active - активный элемент в выборке

********************************************************************/
function BPCStretchSlider(data, position, count, active) 
{
	if (count <= 0 && data)
		count = data.length; 
	else if (count <= 0 )
		return false;
	this.oSource = new BPCSourse(data, position, count);
	if (!this.oSource)
		return false;
	this.oSource.iForceElementCount = 10;
	this.active = this.oSource.iFirstNumber;
	this.params = {'height' : 250, 'width' : 250, 'first_created' : 0, 'last_created' : 0, 'step_size' : 100};
	this.events = {}; 
	for (var ii = this.oSource.iFirstNumber; ii < this.oSource.Data.length; ii++)
	{
		if (active == this.oSource.Data[ii]['id'])
			this.active = ii;
	}
	this.oSource.OnAfterItemAdd = function()
	{
		try
		{
			var element_id = arguments[0][1]; 
			var item_data = this.Data[element_id]; 
			var item_id = item_data["id"]; 
			var item = document.getElementById('item_' + item_id); 
			if (!item)
			{
				var div = BX.create("DIV", {"props" : {"id" : "item_" + item_id}, "attrs" : {"class" : "photo-slider-item"}, 
					"html" : (
					'<table class="photo-slider-thumb" cellpadding="0">' + 
						'<tr>' + 
							'<td>' + 
								'<a href="' + item_data['url'] + '">' + 
									this.parentObject.CreateItem(element_id) + 
								'</a>' + 
							'</td>' + 
						'</tr>' + 
					'</table>')}); 
				
				if (element_id < this.iFirstNumber)
				{
					var pointer = document.getElementById('item_' + this.Data[this.iFirstNumber]["id"]);
					if (pointer)
						pointer.parentNode.insertBefore(div, pointer); 
				}
				else
					this.parentObject.tape.appendChild(div); 
	
				pos = BX.pos(div); 
				this.parentObject.tape.__int_width += parseInt(pos['width']); 
				if (element_id < this.iFirstNumber)
				{
					this.parentObject.tape.style.left = (parseInt(this.parentObject.tape.style.left) - parseInt(pos['width'])) + 'px'; 
					this.parentObject.prev.className = this.parentObject.prev.className.replace("-disabled", "-enabled").replace("-wait", "-enabled");
				}
				else
				{
					this.parentObject.next.className = this.parentObject.next.className.replace("-disabled", "-enabled").replace("-wait", "-enabled");
				}
			}
		} 
		catch (e) { } 
	}
	this.oSource.OnBeforeSendData = function() 
	{
		arguments[0][1]['package_id'] = this.parentObject.pack_id; 
		return arguments[0][1]; 
	}
	
	return true;
}
/** 
	CreateSlider Создает слайдер, перемещает курсор на активный элемент
*/
BPCStretchSlider.prototype.CreateSlider = function() 
{
	this.checkEvent('OnBeforeSliderCreate');
	this.params['first_created'] = this.params['last_created'] = this.oSource.iFirstNumber; 
	for (var item_id = this.oSource.iFirstNumber; item_id < this.oSource.Data.length; item_id++)
	{
		this.params['last_created'] = item_id;
		var res = this.oSource.checkItem(item_id);
		if (!res || res == 'wait')
			return res;
		this.MakeItem(item_id, (this.active == item_id));
	}
	this.checkEvent('OnAfterSliderCreate');
	return true;
}
/** 
	MakeItem Создает элемент
	
	item_id - номер эелемента
 	number - порядковый номер в окне
*/
BPCStretchSlider.prototype.MakeItem = function(item_id, active_id) 
{
	this.checkEvent('OnBeforeItemMake', item_id, active_id);
	this.ShowItem(item_id, active_id);
	this.checkEvent('OnAfterItemMake', item_id, active_id);
}
/** 
	ShowItem Отображает элемент (должна быть переопределена, так как привязана к объектам страницы)
	
	item_id - номер элемента
 	active_id - активный элемент
*/
BPCStretchSlider.prototype.ShowItem = function(item_id, active_id) 
{
}

/** 
	CreateItem - Создает элемент (внесена в этот класс, как наиболее часто повторяющаяся и практически неизменяющаяся, 
		но с классом никак не связана)

	item_id - номер эелемента
	
	Возвращает: объект или false
*/
BPCStretchSlider.prototype.CreateItem = function(item_id)
{
	var koeff = Math.min(this.params['width']/this.oSource.Data[item_id]['width'], this.params['height']/this.oSource.Data[item_id]['height']);
	var res = {'width' : this.oSource.Data[item_id]['width'], 'height' : this.oSource.Data[item_id]['height']};
	if (koeff < 1)
	{
		res['width'] = parseInt(this.oSource.Data[item_id]['width']*koeff);
		res['height'] = parseInt(this.oSource.Data[item_id]['height']*koeff);
	}
	__this_slider = this;
	
	var image = new Image();
	image.src = this.oSource.Data[item_id]['src']; 
	return '<img id="image_' + item_id + '" border="0" ' + 
		'onload="__this_slider.oSource.Data[this.id.replace(\'image_\', \'\')][\'loaded\'] = true; __this_slider.checkEvent(\'OnAfterItemLoad\', this);" ' + 
		'style="width:' + res['width'] + 'px;height:' + res['height'] + 'px;" ' + 
		'title="' + this.oSource.Data[item_id]['title'] + '" alt="' + this.oSource.Data[item_id]['title'] + '" ' + 
		'src="' + this.oSource.Data[item_id]['src'] + '" />';
}
/** 
	GoToNext - Перевод курсора на вправо
	
	Возвращает: true || false || 'wait'
*/
BPCStretchSlider.prototype.GoToNext = function()
{
	var pos_window = BX.pos(this.window); 
	var tape_right_width = parseInt(this.tape.__int_width) + parseInt(this.tape.style.left) - pos_window['width']; 
	
	var leftward = (tape_right_width > this.params['step_size'] ? this.params['step_size'] : tape_right_width); 
	if (leftward > 0)
	{
		this.tape.style.left = parseInt(parseInt(this.tape.style.left) - leftward) + 'px'; 
		this.prev.className = this.prev.className.replace("-disabled", "-enabled").replace("-wait", "-enabled");
	}

	if (this.oSource.Data.length <= this.oSource.iCountData && tape_right_width <= this.params['step_size'] * 10)
		this.oSource.getData(this.oSource.Data.length, true);

	if (tape_right_width > this.params['step_size'])
		this.next.className = this.next.className.replace("-disabled", "-enabled").replace("-wait", "-enabled");
	else if (this.oSource.busy === true || this.oSource.Data.length < this.oSource.iCountData)
		this.next.className = this.next.className.replace("-enabled", "-wait").replace("-disabled", "-wait");
	else
		this.next.className = this.next.className.replace("-enabled", "-disabled").replace("-wait", "-disabled");
	
	return true;
}
/** 
	GoToPrev - Перевод курсора влево
	
	Возвращает: true || false || 'wait'
*/
BPCStretchSlider.prototype.GoToPrev = function()
{
	var tape_left_width = parseInt(this.tape.style.left) * (-1); 
	var rightward = (tape_left_width > this.params['step_size'] ? this.params['step_size'] : tape_left_width); 

	if (rightward > 0)
	{
		this.tape.style.left = parseInt(parseInt(this.tape.style.left) + rightward) + 'px'; 
		var pos_window = BX.pos(this.window); 
		var tape_right_width = parseInt(this.tape.__int_width) + parseInt(this.tape.style.left) - pos_window['width']; 
		if (tape_right_width > 0)
			this.next.className = this.next.className.replace("-disabled", "-enabled").replace("-wait", "-enabled");
	}

	if (this.oSource.iFirstNumber > 1 && rightward <= this.params['step_size'] * 10)
		this.oSource.getData(this.oSource.iFirstNumber, false);

	if (tape_left_width > this.params['step_size'])
		this.prev.className = this.prev.className.replace("-disabled", "-enabled").replace("-wait", "-enabled");
	else if (this.oSource.busy === true || this.oSource.iFirstNumber > 1)
		this.prev.className = this.prev.className.replace("-enabled", "-wait").replace("-disabled", "-wait");
	else
		this.prev.className = this.prev.className.replace("-enabled", "-disabled").replace("-wait", "-disabled");

	return true;
}/**
	Проверка событий
*/
BPCStretchSlider.prototype.checkEvent = function()
{
	eventName = arguments[0];
	if (this.events[eventName]) { return this.events[eventName](arguments); } 
	if (this[eventName]) {return this[eventName](arguments); } 
	return true;
}

BPCStretchSlider.prototype.OnBeforeSliderCreate = function(image) 
{
	this.prev = document.getElementById('prev_' + this.pack_id); 
	this.next = document.getElementById('next_' + this.pack_id); 
	this.window = document.getElementById('slider_window_' + this.pack_id); 
	this.tape = this.window.firstChild; 
	this.oSource.parentObject = this; 
	this.__leftward = 0;
	this.__width = 0;
	this.__active_element_founded = false; 
	__this = this; 
	if (this.window.addEventListener)
		this.window.addEventListener('DOMMouseScroll', __this.OnMouseWheel, false);
	__this = this;
	BX.bind(this.window, 'mousewheel', new Function('__this.OnMouseWheel(event);'));
}
BPCStretchSlider.prototype.OnMouseWheel = function(event)
{
	if (!event) 
		event = window.event;
	
	var wheelDelta = 0;
	
	if (event.wheelDelta) 
		wheelDelta = event.wheelDelta / 120;
	else if (event.detail) 
		wheelDelta = -event.detail / 3;
	BX.PreventDefault(event); 
	var steps = (wheelDelta > 0 ? wheelDelta : wheelDelta * (-1)); 
	for (var ii = 1; ii <= steps; ii++)
	{
		if (wheelDelta < 0)
			__this.GoToNext();
		else
			__this.GoToPrev();
	}
}
BPCStretchSlider.prototype.OnAfterSliderCreate = function()
{
	this.tape.style.left = (this.__leftward > 0 ? ('-' + this.__leftward + 'px') : '0px'); 
	this.tape.__int_width = this.__width; 
	
	delete this.__leftward; 
	delete this.__width; 
	delete this.__active_element_founded; 
	
	__this = this;
	BX.bind(this.next, 'click', new Function('__this.GoToNext(arguments);'));
	BX.bind(this.prev, 'click', new Function('__this.GoToPrev(arguments);'));
}
BPCStretchSlider.prototype.OnAfterItemMake = function()
{
	arguments = arguments[0]; 
	var item_id = arguments[1]; 
	var is_active = (arguments[2] === false || arguments[2] === true ? arguments[2] : (arguments[2] === 'false' ? false : (arguments[2] === 'true' ? true : null))); 

	var item = document.getElementById('item_' + this.oSource.Data[item_id]['id']); 
	var pos = BX.pos(item); 

	this.__width += parseInt(pos['width']); 
	if (!this.__active_element_founded && (is_active === false || is_active === true))
	{
		if (is_active === false)
		{
			this.__leftward += parseInt(pos['width']); 
		}
		else
		{
			this.__active_element_founded = true; 
		}
	}
}
window.bPhotoSliderStretchLoad = true;