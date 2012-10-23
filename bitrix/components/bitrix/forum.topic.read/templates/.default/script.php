<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
function SelectAllCheckBox(form_id, elements_name, control_checkbox_id)
{ 
	var checkbox_handle = document.getElementById(control_checkbox_id);
	for (i = 0; i < document.forms[form_id].elements.length; i++)
	{
         var item = document.forms[form_id].elements[i];
	     if (item.name == elements_name) 
	     {
		     item.checked = checkbox_handle.checked;
		 }
	}
	 return;
}
function SelectCheckBox(control_checkbox_id)
{
	var checkbox_handle = document.getElementById(control_checkbox_id);
	checkbox_handle.checked = false;
	return;
}
function quoteMessageEx(theAuthor)
{
	var selection;
	if (document.getSelection)
	{
		selection = document.getSelection();
		selection = selection.replace(/\r\n\r\n/gi, "_newstringhere_");
		selection = selection.replace(/\r\n/gi, " ");
		selection = selection.replace(/  /gi, "");
		selection = selection.replace(/_newstringhere_/gi, "\r\n\r\n");
	}
	else
	{
		selection = document.selection.createRange().text;
	}
	if (selection!="")
	{
		document.REPLIER.POST_MESSAGE.value += "[quote]"+theAuthor+" <?=GetMessage("JQOUTE_AUTHOR_WRITES");?>:\n"+selection+"[/quote]\n";
	}
}

function reply2author(name)
{
	document.REPLIER.POST_MESSAGE.value += "[b]"+name+"[/b]"+" \n";
}
