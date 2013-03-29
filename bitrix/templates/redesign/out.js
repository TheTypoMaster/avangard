document.write(unescape("%3Cscript src='" + (("https:" == document.location.protocol) ? "https" : "http") + "://a.mouseflow.com/projects/7ccdfd23-7475-49c4-b767-dcf9da14fd83.js' type='text/javascript'%3E%3C/script%3E"));

function movebox(id) {
    var div = document.getElementById(id);
	if(div.style.left == '0') {
		div.style.left = '100px';
                div.style.display = 'none';
	}
	else {
		div.style.display = '0';
	}
};   