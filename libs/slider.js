function shiftSubDiv(n)
// Скрывает/раскрывает подразделы меню с ID вида subDiv1, subDiv2 и т.д.
// Номер подраздела передается в качестве аргумента.
{
  var el = document.getElementById('subDiv'+n);
  var plusminus = document.getElementById('ic_'+n);

  if ( el.style.display == 'none' )
    {
    el.style.display = 'block';
    plusminus.innerHTML = '-';
    }
   else
   
    el.style.display = 'none';
    plusminus.innerHTML = '+';
  
 };

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


var isdrag=false;
var x;
var dobj;
var ie=document.all?1:0
n=document.layers?1:0
var nn6=document.getElementById&&!document.all?1:0

if(ie) document.write("<link rel='stylesheet' href='/libs/slider_new.css' type='text/css'>");
else document.write("<link rel='stylesheet' href='/libs/slider.css' type='text/css'>"); 



if(ie) var first_pos = -384; else  var first_pos = 34; 
if(ie) var last_pos = 384; else  var last_pos = 804;
if(ie) var left_pos_str = '-384px'; else  var left_pos_str = '34px';;
if(ie) var delta = 384; else  var delta = 0;;
var trig=1;

function movemouse(e)
{

  if (isdrag)
  {
         if((dobj.style.left.slice(0,-2) < last_pos) && (dobj.style.left.slice(0,-2) > (first_pos+527))) dobj.innerHTML = "Mix'Line";
         else if((dobj.style.left.slice(0,-2) < (first_pos+528)) && (dobj.style.left.slice(0,-2) > (first_pos+422))) dobj.innerHTML = "Le Roi";
         else if((dobj.style.left.slice(0,-2) < (first_pos+423)) && (dobj.style.left.slice(0,-2) > (first_pos+90)))  dobj.innerHTML = "Искусства&nbsp;&&nbsp;Ремесла";
         else if((dobj.style.left.slice(0,-2) < (first_pos+91)) && (dobj.style.left.slice(0,-2) > (first_pos)))  dobj.innerHTML = "EKKA";
		 
		 
   
     	
        var smeshenie = nn6 ? tx + e.clientX - x : tx + event.clientX - x;
    	smeshenie =  smeshenie - delta;
	    if(smeshenie>=first_pos && smeshenie<last_pos){
       
         dobj.style.left = smeshenie;
		
	  	 document.getElementById("scroll_div_id_one").scrollLeft = Math.ceil(((dobj.style.left.slice(0,-2)-first_pos)*4400)/(last_pos - first_pos) - 12);
     
		}    
	trig = trig+1;
	if((delta == 384) && (trig>2)) delta = 0;
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
    if((x >= first_pos) && (x < last_pos))
	{
	document.onmousemove=movemouse;
	}
    return false;
  }
}

document.onmousedown=selectmouse;
document.onmouseup=new Function("isdrag=false");

