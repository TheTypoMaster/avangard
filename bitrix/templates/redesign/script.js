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