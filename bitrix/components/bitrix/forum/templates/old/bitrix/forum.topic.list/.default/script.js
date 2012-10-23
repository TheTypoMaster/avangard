function SelectTopics(oObj)
{
	if (typeof oObj != "object")
		return;

	var items = document.getElementsByName('topic_id[]');
	if (typeof items == "object")
	{
		if (!items.length || (typeof(items.length) == 'undefined'))
			items = [items];
		
		for (i = 0; i < items.length; i++)
		{
			if (items[i].type && (items[i].type=="checkbox"))
			{
				items[i].checked = oObj.checked;
				items[i].onclick = function(){oObj.checked = false;}
			}
		}
	}
	return;
}