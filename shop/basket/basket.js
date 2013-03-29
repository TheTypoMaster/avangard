function Inint_AJAX() {
	try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch(e) {} //IE
	try { return new ActiveXObject("Microsoft.XMLHTTP"); } catch(e) {} //IE
	try { return new XMLHttpRequest(); } catch(e) {} //Native Javascript
	alert("XMLHttpRequest not supported");
	return null;
};

function countSubject() {
	//alert(mt_url);
	var req = Inint_AJAX();
	req.onreadystatechange = function () {
		if (req.readyState==4) {
			if (req.status==200) {
				//alert(val);
				var regText = req.responseText;
				//alert('regTextMetro - '+regText);
				//if (regText) document.getElementById('basket_text').innerHTML="&nbsp; <a href='/shop/basket' style=\"color:#78A40E\">Товаров в корзине - "+regText+"</a>"; //return value
			}
		}
	};
	req.open("GET", "/shop/basket/basket_ajax.php?count=1");
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8"); // set Header
	req.send(null); //send value
	return false;
} 


function addSubject(subj_id, combinac_id, recommend_id) {
	//alert(mt_url);
	var req = Inint_AJAX();
	req.onreadystatechange = function () {
		if (req.readyState==4) {
			if (req.status==200 && callback) {
				//alert(val);
				var regText = req.responseText;
				//alert('regTextMetro - '+regText);
				//document.getElementById('basket_text').innerHTML="&nbsp; <a href='/shop/basket' style=\"color:#78A40E\">Товаров в корзине - "+regText+"</a>"; //return value
				//alert('1 - '+document.getElementById('check_city').innerHTML);
				//alert('2 - '+document.getElementById('city_name').value);
				//document.getElementById('check_metro').innerHTML=document.getElementById('metro_name').value;
				callback();
			}
		}
	};
	req.open("GET", "/shop/basket/basket_ajax.php?subj_id="+subj_id+'&combinac_id='+combinac_id+'&recommend_id='+recommend_id);
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8"); // set Header
			
	
	req.send(null); //send value
	//return false;
} 

function callback(){
	document.location.href = "/shop/basket";
}
function delSubject(key) {
	//alert(key);
	var req = Inint_AJAX();
	req.onreadystatechange = function () {
		if (req.readyState==4) {
			if (req.status==200) {
				//alert(val);
				var regText = req.responseText;
				//alert('regTextMetro - '+regText);
				//document.getElementById('basket_text').innerHTML="&nbsp; <a href='/shop/basket' style=\"color:#78A40E\">Товаров в корзине "+regText+"</a>"; //return value
				
				document.getElementById('tr_'+key).innerHTML=""; //return value
				//alert('1 - '+document.getElementById('check_city').innerHTML);
				//alert('2 - '+document.getElementById('city_name').value);
				//document.getElementById('check_metro').innerHTML=document.getElementById('metro_name').value;
			}
		}
	};
	req.open("GET", "/shop/basket/basket_ajax.php?del_key="+key);
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8"); // set Header
	req.send(null); //send value
	//return false;
} 

function delAllSubject() {
	//alert(mt_url);
	var req = Inint_AJAX();
	req.onreadystatechange = function () {
		if (req.readyState==4) {
			if (req.status==200) {
				//alert(val);
				var regText = req.responseText;
				//alert('regTextMetro - '+regText);
				document.getElementById('subject_count').innerHTML=regText; //return value
				//alert('1 - '+document.getElementById('check_city').innerHTML);
				//alert('2 - '+document.getElementById('city_name').value);
				//document.getElementById('check_metro').innerHTML=document.getElementById('metro_name').value;
			}
		}
	};
	req.open("GET", "/shop/basket/basket_ajax?del_all=1");
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8"); // set Header
	req.send(null); //send value
	return false;
} 

function winPop(uri, name, w, h){ // открывает поп-ап окно: ="return popupWin('/target.html', 'wnd', [600, 500]);"
	var ie = (document.all)? true : false; var nn6 = (!ie && document.getElementById)? true : false;
	var nn4 = (document.layers)? true : false; var posCode = ''; if (nn4 || nn6 || ie) {
		if ( (screen.height < 481) && (h > 400) ) { hgt = 400 }
		posX = Math.round((screen.width - w) / 2); posY = Math.round((screen.height - h) / 2);
		posCode = (nn4 || nn6)? ",screenX="+posX+",screenY="+posY : ",left="+posX+",top="+posY;
	} popupedWin = window.open(uri, name || "pop", "status=no, menubar=no, toolbar=no, resizable=yes, width=" + w + ", height=" + h + posCode); popupedWin.focus(); return false;
}
;   