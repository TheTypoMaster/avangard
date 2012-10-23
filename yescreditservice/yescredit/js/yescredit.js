function yescreditdialog (data,typecredit) {

    var summa = 0;
    var args = '';
    for (i = 0; i < data.length; i++) {
        args += '&ITEMS[]=';
        var j = 0;
        for (var key in data[i]) {
            j++;
            args += key + ':' + data[i][key];
            if (j < 3) args += '|';
            if (j == 3) summa += parseFloat(data[i][key]);
        }
    }
	
	args = '?SUMMA='+summa+'&TYPECREDIT='+typecredit+args;
	
    var page = '/yescreditservice/index.php' + encodeURI(args);
    var $dialog = $('<div title="Заявка на кредит, не выходя из дома" id="Divbody"></div>')
    .html('<iframe style="border: 0px; " src="' + page + '" width="100%" height="100%"></iframe>')
    .dialog({
        autoOpen: false,
        modal: true,
        height: 700,
        width: 500,
    });
    $dialog.dialog('open');
}


