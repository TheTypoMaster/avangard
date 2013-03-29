function getHint(e, cityUrl) {
    if (!$('#hint').is(':hidden'))
    {
        closeHint();
    }
    $("#hint_content").load(cityUrl,{},
        function() {
            $('#hint').css('top' , document.documentElement.scrollTop + document.documentElement.clientHeight/2 - getHintHeight()/2);
            $('#hint').css('left', document.documentElement.scrollLeft + document.documentElement.clientWidth/2 - 300);
            $('#hint').fadeIn('slow');
        });
}
function getHintHeight() {
    return $("#hint").height();
}
function closeHint() {
    $('#hint').fadeOut(0);
}

function shiftSubDiv(n)
// —крывает/раскрывает подразделы меню с ID вида subDiv1, subDiv2 и т.д.
// Ќомер подраздела передаетс€ в качестве аргумента.
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


;   