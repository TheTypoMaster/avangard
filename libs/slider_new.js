DOM = document.getElementById;
Netscape4 = document.layer;
Netscape6 = Mozilla = (navigator.appName == "Netscape") && DOM;
Netscape7 = navigator.userAgent.indexOf("Netscape/7") >= 0;
Opera5 = window.opera && DOM;
Opera6 = Opera5 && window.print;
Opera7 = Opera5 && navigator.userAgent.indexOf("Opera 7") >= 0;
Opera8 = navigator.userAgent.indexOf("Opera/8") >= 0;
Opera9 = navigator.userAgent.indexOf("Opera/9") >= 0;
IE = document.all && !Opera5;
Firefox = navigator.userAgent.indexOf("Firefox") >= 0; 
if(IE) document.write("<link rel='stylesheet' href='/libs/slider_new.css' type='text/css'>");
else if(Firefox) document.write("<link rel='stylesheet' href='/libs/slider.css' type='text/css'>"); 
else if(Opera9) document.write("<link rel='stylesheet' href='/libs/slider.css' type='text/css'>"); 
else document.write("<link rel='stylesheet' href='/libs/slider.css' type='text/css'>"); 




function getPosition(objid)
{
obj = document.getElementById(objid);
var left=0,top=0; while(obj)
{ left+=obj.offsetLeft-obj.scrollLeft;
top+=obj.offsetTop-obj.scrollTop;
if(obj.style.borderTopWidth!='')
top+=parseInt(obj.style.borderTopWidth);
if(obj.style.borderLeftWidth!='')
left+=parseInt(obj.style.borderLeftWidth);
obj=obj.offsetParent;
 }
return left;
}

var ie=document.all;
var nn6=document.getElementById&&!document.all;

var isdrag=false;
var x;
var dobj;
if(Firefox) var first_pos = 38; else if(IE)   var first_pos = -280;  else  var first_pos = 38; 

if(Firefox) var first_pos_set = '38px'; else if(IE)   var first_pos_set = '-280px';  else  var first_pos_set = '38px'; 

if(Firefox) var last_pos =804; else if(IE)   var last_pos = 480;  else  var last_pos = 804; 

function movemouse(e)
{

  if (isdrag)
  {
         if((dobj.style.left.slice(0,-2) < last_pos) && (dobj.style.left.slice(0,-2) > last_pos*0.7)) dobj.innerHTML = "Mix'Line";
         else if((dobj.style.left.slice(0,-2) < last_pos*0.7) && (dobj.style.left.slice(0,-2) > last_pos*0.5)) dobj.innerHTML = "Le Roi";
         else if((dobj.style.left.slice(0,-2) < last_pos*0.5) && (dobj.style.left.slice(0,-2) > last_pos*0.1))  dobj.innerHTML = "Искусства&nbsp;&&nbsp;Ремесла";
         else dobj.innerHTML = "EKKA";
		 
		 
		

         if(dobj.style.left == 0) { dobj.style.left = first_pos_set} 
	  else if(dobj.style.left.slice(0,-2) < first_pos) {dobj.style.left=first_pos; }
	  else if(dobj.style.left.slice(0,-2) > last_pos) { dobj.style.left=last_pos; }
	  else  
	{
     	
        var smeshenie = nn6 ? tx + e.clientX - x : tx + event.clientX - x;
		if(smeshenie>first_pos && smeshenie<last_pos){
        dobj.style.left = smeshenie;
		
	    if((dobj.style.left == 0) || (dobj.style.left==first_pos)) 
		{
		document.getElementById("scroll_div_id_one").scrollLeft = 0;
       }
		else 
		{
	    document.getElementById("scroll_div_id_one").scrollLeft = Math.ceil((dobj.style.left.slice(0,-2)*766)/135)-240;
        }
		}    
	}
	
	return false;
  }
}

function selectmouse(e) 
{
  var fobj       = nn6 ? e.target : event.srcElement;
  var topelement = nn6 ? "HTML" : "BODY";

  while (fobj.tagName != topelement && fobj.className != "dragme")
  {
    fobj = nn6 ? fobj.parentNode : fobj.parentElement;
  }

  if (fobj.className=="dragme")
  {
    isdrag = true;
    dobj = fobj;
    tx = parseInt(dobj.style.left+0);
    x = nn6 ? e.clientX : event.clientX;
    if((x > first_pos) && (x < last_pos))
	{
	document.onmousemove=movemouse;
	}
    return false;
  }
}

document.onmousedown=selectmouse;
document.onmouseup=new Function("isdrag=false");


