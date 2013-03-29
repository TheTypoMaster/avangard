function shiftSubDiv(n)
{
  var el = document.getElementById('subDiv'+n);
  var plusminus = document.getElementById('ic_'+n);

  if ( el.style.display == 'none' )
    {
    el.style.display = 'block';
    plusminus.innerHTML = '-';
    }
   else
    {
    el.style.display = 'none';
    plusminus.innerHTML = '+';
    }
 };

var jshover = function() {
	var sfEls = document.getElementById("horizontal-multilevel-menu").getElementsByTagName("li");
	for (var i=0; i<sfEls.length; i++) 
	{
		sfEls[i].onmouseover=function()
		{
			this.className+=" jshover";
		}
		sfEls[i].onmouseout=function() 
		{
			this.className=this.className.replace(new RegExp(" jshover\\b"), "");
		}
	}
}

if (window.attachEvent) 
	window.attachEvent("onload", jshover);

;   