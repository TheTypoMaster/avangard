function SectionSH(id)
{
	var section = document.getElementById('forum_group_'+id);
	var switcher = document.getElementById('forum_switch_'+id);

	if (section.style.display != 'none')
	{
		document.cookie = 'forum_group_'+id+"=N; expires=Thu, 31 Dec 2030 23:59:59 GMT; path=/;";
		section.style.display = 'none';
		switcher.className = switcher.className.replace(/-hide/gi, "");
	}
	else
	{
		section.style.display = '';
		document.cookie = 'forum_group_'+id+"=Y; expires=Sun, 31 Dec 2000 23:59:59 GMT; path=/;";
		switcher.className = switcher.className.replace(/-hide/gi, "")+'-hide';
	}
	return false;
}

