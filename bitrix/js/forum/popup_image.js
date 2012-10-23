(function(window){
if (window.onForumImageLoad) return;
window.onForumImageLoad = function(oImg, w, h, family, oImg1)
{
	if (typeof oImg == "string")
	{
		oImg = document.getElementById(oImg);
	}
	if (oImg == null || typeof oImg != "object")
	{
		return false;
	}
	
	family = (family && family.length > 0 ? family : "");
    var img = {'width' : 0, 'height' : 0};
    
	if (oImg.naturalWidth)
	{
		img['width'] = oImg.naturalWidth;
		img['height'] = oImg.naturalHeight;
	}
	else
	{
		img['width'] = oImg.width;
		img['height'] = oImg.height;
	}
	var k = 1;
	w = parseInt(w);
	w = (w > 0 ? w : 100);
	h = parseInt(h);
	
	
	if (h <= 0 && img['width'] > w)
	{
    	k = w/img['width'];
	}
	else if (h > 0 && (img['width'] > w || h > img['height']))
	{
		if (img['width'] <= 0)
			k = h/img['height'];
		else
			k = Math.min(w/img['width'], h/img['height']);
	}
	
	if (0 < k && k < 1)
	{
        oImg.style.cursor = 'pointer';
        oImg.onclick = new Function("onForumImageClick(this, '" + img['width'] + "', '" + img['height'] + "', '" + family +"')");
        if (h > 0)
        {
	        var width = parseInt(img['width'] * k);
	        var height = parseInt(img['height'] * k);
	        oImg.width = width;
	        oImg.height = height;
        }
	}
}
window.onForumImageClick = function(oImg, w, h, family)
{
	if (oImg == null || typeof oImg != "object")
		return false;

	w = (w <= 0 ? 100 : w);
	h = (h <= 0 ? 100 : h);
	family = (family && family.length > 0 ? family : "");
	var div = null;
	var id = 'div_image' + (family.length > 0 ? family : oImg.id);
	if (family.length > 0)
	{
		div = document.getElementById(id);
		if (div != null && typeof div == "object")
			div.parentNode.removeChild(div);
	}
	div = document.createElement('div');
	div.id = id;
	div.className = 'forum-popup-image';
	div.style.position = 'absolute';
	div.style.width = w + 'px';
	div.style.height = h + 'px';
	div.style.zIndex = 80;
	div.onclick = function(){
		jsFloatDiv.Close(this);
		this.parentNode.removeChild(this);};
	
	var pos = {};
	var res = jsUtils.GetRealPos(oImg);
	var win = jsUtils.GetWindowScrollPos();
	var win_size = jsUtils.GetWindowInnerSize();
	var img = new Image();
	var div1 = document.createElement('div');
	
	pos['top'] = parseInt(res['top'] + oImg.offsetHeight/2 - h/2);
	if ((parseInt(pos['top']) + parseInt(h)) > (win['scrollTop'] + win_size['innerHeight']))
	{
		pos['top'] = (win['scrollTop'] + win_size['innerHeight'] - h - 10);
	}
	if (pos['top'] <= win['scrollTop'])
	{
		pos['top'] = win['scrollTop'] + 10;
	}
	
	pos['left'] = parseInt(res['left'] + oImg.offsetWidth/2 - w/2);
	pos['left'] = (pos['left'] <= 0 ? 10 : pos['left']);
	
	div1.style.left = (w - 14) + "px";
	div1.style.top = "0px";
	
	div1.className = 'empty';
	div1.style.zIndex = 82;
	div1.style.position = 'absolute';
	div1.style
	div.appendChild(div1);
	
	img.width = w;
	img.height = h;
	img.style.cursor = 'pointer';
	img.src = oImg.src;
	
	div.appendChild(img);
	document.body.appendChild(div);
	jsFloatDiv.Show(div, pos['left'], pos['top']);
}

window.onForumImagesLoad = function()
{
	if (window.oForumForm && window.oForumForm['images_for_resize'] && window.oForumForm['images_for_resize'].length > 0)
	{
		for (var ii = 0; ii < window.oForumForm['images_for_resize'].length; ii++)
		{
			var img = document.getElementById(window.oForumForm['images_for_resize'][ii]);
			if (img != 'null' && img && img.tagName == "IMG")
			{
				img.onload();
			}
		}
	}
}

window.addForumImagesShow = function(id)
{
	if (typeof window.oForumForm != "object")
		window.oForumForm = {};
	if (!window.oForumForm['images_for_resize'])
		window.oForumForm['images_for_resize'] = [];
	window.oForumForm['images_for_resize'].push(id);
}

if (jsUtils.IsIE())
{
	jsUtils.addEvent(window, "load", window.onForumImagesLoad);
}
})(window)
